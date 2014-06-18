<?php

$aria_dir = dirname( __FILE__ );
require_once( $aria_dir .'/aria2.class.php' );

$rpc = new aria2();

if( isset( $_REQUEST['debug']))
{
	error_reporting ('E_ALL');
	ini_set('display_errors', '1' );

//	$rpc->debug = true;
	header( "Content-type: text/plain" );
}

// ------------------------------------
// Actions
//
if( isset( $_REQUEST['act'] ))
{
	$act = $_REQUEST['act'];
	if( $act == 'resume_all' )
	{
		$result = $rpc->unpauseAll();
	}
	elseif( $act == 'pause_all' )
	{
		$result = $rpc->pauseAll();
	}

	else
	if( isset( $_REQUEST['id'] ))
	{
		$id = $_REQUEST['id'];

		if( $act == 'resume' )
		{
			$result = $rpc->unpause( $id );
		}
		elseif( $act == 'pause' )
		{
			$result = $rpc->pause( $id );
		}
		elseif( $act == 'remove' )
		{
			$result = $rpc->remove( $id );
		}
		elseif( $act == 'delete' )
		{
			$result = $rpc->removeDownloadResult( $id );
		}
	}
}
// ------------------------------------
//
function getPeers( $peers_active, $peers_total, $s )
{
	if( ! isset( $peers_total )) return '';
	return ' '.$s.sprintf("%01u",$peers_active).getMsg( 'ariaOf' ).sprintf("%01u",$peers_total);
}

// ------------------------------------
// List of torrents
//

header( "Content-type: text/plain" );

$keys = array(

'gid',			// GID загрузки.
'status',		// active - загружаемая/сидируемая в данный момент. waiting - ожидающая в очереди; загрузка не началась. paused - приостановленная. error - остановленная, т.к. произошла ошибка. complete - остановленная и завершенная загрузка. removed - удаленная пользователем загрузка.
'totalLength',		// Общий объем загрузки в байтах.
'completedLength',	// Загруженный объем загрузки в байтах.
'uploadLength',		// Выгруженный объем загрузки в байтах.
'downloadSpeed',	// Скорость загрузки в байт/сек.
'uploadSpeed',		// Скорость выгрузки в байт/сек.
'numSeeders',		// Количество сидов, к которым подключен клиент. Только для BitTorrent.
'connections',		// Количество пиров/серверов, к которым подключен клиент.
'errorCode',		// Последний код ошибки, которая произошла при загрузке. Значение имеет тип строки. Коды ошибок определены в разделе КОДЫ ЗАВЕРШЕНИЯ. Это значение доступно только для остановленных/завершенных загрузок.
'dir',			// Каталог для сохранения файлов.
'files',		// Возвращает список файлов. Элемент списка - это структура, такая же, что и в методе aria2.getFiles().
'bittorrent',		// Структура, в которой содержится информация, извлеченная из .torrent-файла. Только для BitTorrent.

);

$dls = array();

$result = $rpc->tellActive( $keys );
foreach( $result['result'] as $d )
 $dls[ $d['gid'] ] = $d;

$result = $rpc->tellWaiting( 0, 100, $keys );
foreach( $result['result'] as $d )
 $dls[ $d['gid'] ] = $d;

$result = $rpc->tellStopped( 0, 100, $keys );
foreach( $result['result'] as $d )
 $dls[ $d['gid'] ] = $d;

ksort( $dls );

if( isset( $_REQUEST['debug']))
{
	print "downloads:";
	print_r( $dls );
}

$sput = count( $dls ) . PHP_EOL;

if( count( $dls ) > 0 )
foreach( $dls as $item )
{
	// status
	$id = $item['gid'];
	$st = 'error';
	if( isset( $item['status'] )) $st = $item['status'];

	$progress_bg = $aria_dir . '/images/progress_white.png';
	if( $item['totalLength'] > 0 ) $progress_ratio = round( $item['completedLength']/$item['totalLength'], 2 );
	else $progress_ratio = 0;

	if( $st == 'error' )
	{
		$item_left = getMsg( 'ariaError' ) . getMsg( 'ariaError'. $item['errorCode'] );
		$progress_bg  = $aria_dir . '/images/progress_red.png';
		$progress_bar = $aria_dir . '/images/progress_deep_green.png';
	}
	elseif( $st == 'removed' )
	{
		$item_left = getMsg( 'ariaRemoved' );
		$progress_bg  = $aria_dir . '/images/progress_red.png';
		$progress_bar = $aria_dir . '/images/progress_deep_green.png';
	}
	elseif( $st == 'active' )
	{
		if( $progress_ratio == 1 )
		{
			$item_left = getMsg( 'ariaSeeding' ) . getPeers( $item['numSeeders'], $item['connections'], getMsg( 'ariaTo' ) );
			$progress_bar = $aria_dir . '/images/progress_green.png';
		}
		else
		{
			$item_left = getMsg('ariaDownload') . getPeers( $item['numSeeders'], $item['connections'], getMsg( 'ariaFrom' ) );
			$progress_bar = $aria_dir . '/images/progress_blue.png';
		}
	}
	else	// paused, waiting, complete
	{
		$progress_bar = $aria_dir . '/images/progress_grey.png';

		if( $st == 'paused'   ) $item_left = getMsg( 'ariaPaused' );
		elseif( $st == 'complete' ) $item_left = getMsg( 'ariaComplete' );
		else $item_left = getMsg( 'ariaWaiting' );
	}
	// DL & UL
	$s = '';
	if( $item['downloadSpeed'] > 0 )
		$s .= getMsg( 'ariaDL' ) . getHumanValue( $item['downloadSpeed'] ) . getMsg( 'ariaPSec' );
	if( $item['uploadSpeed'] > 0 )
		$s .= getMsg( 'ariaUL' ) . getHumanValue( $item['uploadSpeed'] ) . getMsg( 'ariaPSec' );
	if( $s <> '' ) $item_left .= ' -'.$s;


	$item_right = '';

	if( isset( $item['totalLength'] ))
	{
		$ts = $item['totalLength'];
		$vs = 0;
		if( isset( $item['completedLength'] )) $vs = $item['completedLength'];

		if( $ts !== $vs )
		{
			// incomplete
			$item_right .= getHumanValue( $vs ).getMsg( 'ariaOf' ).getHumanValue( $ts );
			$item_right .= ' ('.sprintf( '%01.2f', $progress_ratio ).'%)';
			if( $item['downloadSpeed'] > 0 )
			$item_right .= ' - '. getHumanPeriod( ($ts-$vs)/$item['downloadSpeed'] ).getMsg( 'ariaRemain' );
		}
		else
		{
			// complete
			$item_right .= getHumanValue( $ts );
			$item_right .= getMsg( 'ariaUploaded' ).getHumanValue( $item['uploadLength'] );
			if( $ts > 0 )
			$item_right .= getMsg( 'ariaRatio' ).sprintf( '%01.2f', round( $item['uploadLength']/$ts * 100, 2 )).')';
		}
	}

	$url = getMosUrl() ."?page=rss_aria_actions&id=$id";

	if( isset( $item['bittorrent']['info']['name'] ))
	 $sput .= $item['bittorrent']['info']['name'] . PHP_EOL;

	elseif( count( $item['files']) > 1 )
	{
		$s = str_replace( $item['dir'].'/', '', $item['files'][0]['path'] );
		$s = preg_replace('/(.*?)\/.*/', '$1', $s);
		$sput .= $s . PHP_EOL;
	}
	else $sput .= basename( $item['files'][0]['path'] ) . PHP_EOL;

	$sput .= $item_left . PHP_EOL;
	$sput .= $item_right . PHP_EOL;
	$sput .= $progress_bg . PHP_EOL;
	$sput .= $progress_bar . PHP_EOL;
	$sput .= ($progress_ratio * 100) . PHP_EOL;
	$sput .= $url. PHP_EOL;
}

if( isset( $_REQUEST['debug']))
{
	echo $sput;
}
else
{
	file_put_contents( '/tmp/put.dat', $sput );
	echo "/tmp/put.dat";
}

exit;

?>