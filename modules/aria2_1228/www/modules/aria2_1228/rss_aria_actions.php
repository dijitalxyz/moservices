<?php

function addFavorite( $path )
{
	// get drive
	$vols = '/tmp/ramfs/volumes/';

	if( ! $d = opendir( $vols )) return false;

	$vol = '';
	while (( $f = readdir( $d )) !== false )
	{
		if( ! is_link( $vols.$f )) continue;

		if( strpos( $path, $vols.$f ) !== false )
		{
			$vol = $f;
			$favUrl = str_replace( $vols.$f, '', $path );
			break;
		}
		if( strpos( $path, readlink( $vols.$f )) !== false )
		{
			$vol = $f;
			$favUrl = str_replace( readlink( $vols.$f ), '', $path );
			break;
		}
	}
	if( $vol == '' ) return false;
	$pathDev = $vols.$vol;

if( isset( $_REQUEST['debug'] ))
{
	echo "pathDev=$pathDev\n";
	echo "favUrl =$favUrl\n";
}

	# get type of drive
	$a = file( '/tmp/ramfs/labels/'. $vol );
	if( trim( $a[2] ) == 'usb' ) $typeDev = 1;
	elseif( trim( $a[2] ) == 'sata' ) $typeDev = 2;
	else return false;

if( isset( $_REQUEST['debug'] )) echo "typeDev=$typeDev\n";

	# get type file
	if( is_dir( $path )) $favType = 4;
	else
	{
		$a = pathinfo( $path, PATHINFO_EXTENSION );
		if( in_array( $a, array( 'jpg', 'jpeg', 'bmp', 'png', 'gif', 'tif', 'tiff'))) $favType = 1;
		elseif( in_array( $a, array( 'trp', 'mp4', 'mov', 'avi', 'asf', 'wmv', 'mkv', 'flv', 'ts', 'mts', 'm2ts', 'dat', 'mpg', 'vob', 'iso'))) $favType = 2;
		elseif( in_array( $a, array( 'mp3', 'wav', 'aac', 'ogg', 'flac', 'aiff'))) $favType = 3;
		else return false;
	}

if( isset( $_REQUEST['debug'] )) echo "favType=$favType\n";

	# find FavLink file
	$favName = $pathDev .'/FavLink_';
	if( $typeDev == 1 ) $favName .= 'USB';
	else $favName .= 'HDD';

	if( file_exists( $favName ))
	{
		$s = file_get_contents ( $favName );

		if( strpos( $s, '<Link />' ) !== false )
		{
			# empty link
			$s = str_replace( '    <Link />', '    <Link>
    </Link>', $s );
		}
		else
		{
			# remove previous trans items
			$s = preg_replace( '/\s*<Item module="trans">.*?<\/Item>/s', '', $s );
		}

		# add new item
		$s = str_replace( '    </Link>', '        <Item module="trans">
            <Hash>'. md5( $favUrl) .'</Hash>
            <Type>'. $favType .'</Type>
            <Timestamp>'. time() .'</Timestamp>
            <URL>'. $favUrl .'</URL>
        </Item>
    </Link>' , $s );

	}
	else
	{
		# make new favorites file
		$s = '<Storage>
    <Device>'. $typeDev .'</Device>
    <Link>
        <Item>
            <Hash>'. md5( $favUrl) .'</Hash>
            <Type>'. $favType .'</Type>
            <Timestamp>'. time() .'</Timestamp>
            <URL>'. $favUrl .'</URL>
        </Item>
    </Link>
</Storage>
';

	}

if( isset( $_REQUEST['debug'] ))
{
	echo "favName =$favName\n";
	echo $s;
}
	file_put_contents( $favName, $s );

	return true;
}

// ------------------------------------
//
function rss_aria_actions_content()
{
	if( ! isset( $_REQUEST['id'] )) return;
	$id = $_REQUEST['id'];

	$aria_dir = dirname( __FILE__ );
	require_once( $aria_dir . '/aria2.class.php' );

	$rpc = new aria2();

if( isset( $_REQUEST['debug'] ))
{
//	$rpc->debug = true;
	header( "Content-type: text/plain" );
}
	$keys = array(
		'gid',			// GID загрузки.
		'status',		// active - загружаемая/сидируемая в данный момент. waiting - ожидающая в очереди; загрузка не началась. paused - приостановленная. error - остановленная, т.к. произошла ошибка. complete - остановленная и завершенная загрузка. removed - удаленная пользователем загрузка.
		'totalLength',		// Общий объем загрузки в байтах.
		'completedLength',	// Загруженный объем загрузки в байтах.
		'errorCode',		// Последний код ошибки, которая произошла при загрузке. Значение имеет тип строки. Коды ошибок определены в разделе КОДЫ ЗАВЕРШЕНИЯ. Это значение доступно только для остановленных/завершенных загрузок.
		'dir',			// Каталог для сохранения файлов.
		'files',		// Возвращает список файлов. Элемент списка - это структура, такая же, что и в методе aria2.getFiles().
		'bittorrent',		// Структура, в которой содержится информация, извлеченная из .torrent-файла. Только для BitTorrent.
	);
	$result = $rpc->tellStatus( $id, $keys );

if( isset( $_REQUEST['debug'] ))
{
	print "status:";
	print_r( $result );
}

	if( count( $result['result'] ) == 0 ) return;

	$item = $result['result'];

	$st = $item['status'];

	if( $item['totalLength'] == $item['completedLength'] ) $isDone = true;
	else $isDone = false;

	if( isset( $item['bittorrent']['info']['name'] )) $name = $item['bittorrent']['info']['name'];
	else $name = basename( $item['files'][0]['path'] );

	// --------------------------
	// send RSS
	//
	include( 'modules/core/rss_view_popup.php' );

	$view = new rssSkinPopupView;

	$view->topTitle = $name;

	if( $isDone )
	if( file_exists( '/usr/local/bin/scripts/myfavorites_list.rss' ))
	{
		$l = $item['dir'] .'/';
		if( count( $item['files'] ) > 1 )
		 // folder
		 $l .= $item['bittorrent']['info']['name'] .'/';
		else
		 // file
		 $l = $item['files'][0]['path'];

		if( addFavorite( $l ) )
		 $view->items[] = array(
				'title'	=> getMsg( 'ariaOpen' ),
				'action'=> 'ret',
				'link'	=> 'open'
		 );
	}

	if( $st == 'paused' )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'ariaResume' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_aria&amp;id=$id&amp;act=resume"
		);
	}
	elseif( $st == 'active' )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'ariaPause' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_aria&amp;id=$id&amp;act=pause"
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'ariaRemove' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_aria&amp;id=$id&amp;act=remove"
	);
	$view->items[] = array(
		'title'	=> getMsg( 'ariaDelete' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_aria&amp;id=$id&amp;act=delete"
	);

/*
	$view->items[] = array(
		'title'	=> getMsg( 'transAnno' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=anno"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transCheck' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=check"
	);
*/
	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}

?>