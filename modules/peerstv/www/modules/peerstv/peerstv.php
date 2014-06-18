<?php

// set timezone
$db = new SQLite3('/usr/local/etc/dvdplayer/Setup');
$res = $db->query('SELECT key, value FROM SetupKeyValue where key="SETUP_TIME_ZONE"');

if ($row = $res->fetchArray())
{
	$numb = $row[1];
	$my_offset = ( $numb - 25 ) * 1800;
	$tz = timezone_name_from_abbr( '', $my_offset, 1 );
}
else
{
	$my_offset = 4 * 3600;
	 $tz = 'Europe/Moscow';
}
date_default_timezone_set( $tz );

// diffrence between tz of server and current time zone
$peerstv_offset = 7 * 3600 - $my_offset;

// get config e.t.c.
include 'peerstv.init.php';

//
// ------------------------------------
function peerstvChannelCacheImages( &$chs )
{
	$p = dirname( __FILE__ ) .'/cached/';

	foreach( $chs as $id => $ch )
	{
		$f = $p . $id .'.png';
		if( ! file_exists( $f ))
		{
			$s = $ch['image'];
			copy( $s, $f );
		}
		$chs[ $id ]['image'] = $f;
	}
}
//
// ------------------------------------
function peerstv_get_content()
{
global $peerstv_config;

	if( ! isset( $_REQUEST['cid'] )) return;
	$cid = $_REQUEST['cid'];

if( isset( $_REQUEST['debug'])) header( "Content-type: text/plain" );

	$url = 'http://hls.cn.ru/streaming/'. $cid .'/tvrec/playlist.m3u8';

	if( $peerstv_config['cast'] == 'list' )
	{
		if( isset( $_REQUEST['debug'])) echo 'Location: ' .$url ."\n" ;
		else header ( 'Location: ' .$url );
		return;
	}

	// m3u8 patch

	$s = file_get_contents( $url );

if( isset( $_REQUEST['debug'])) print_r( $http_response_header );

	// get location
	$loc = $url;
	if( preg_match( '|Location: (.+)|', implode( "\n", $http_response_header ), $a ) > 0 ) $loc = $a[1];
	$loc = dirname( $loc );

if( isset( $_REQUEST['debug'])) echo $s;

	// get duration
	if( preg_match( '|#EXT-X-TARGETDURATION:(\d+)|', $s, $a ) > 0 ) $dur = $a[1];
	else return;

	// get sequence
	if( preg_match( '|#EXT-X-MEDIA-SEQUENCE:(\d+)|', $s, $a ) > 0 ) $seq = $a[1];
	else return;

	// get segment
	if( preg_match( '|(segment-\d+)-'.$seq.'.ts|', $s, $a ) > 0 ) $seg = $a[1];
	else return;


if( isset( $_REQUEST['debug'])) echo "duration=$dur\nsequence=$seq\nsegment=$seg\n";

	// one hour
	$n = (integer)60*60/$dur;

if( isset( $_REQUEST['debug'])) echo "number=$n\nlocation=$loc\n";

	$s  = '';
	$s .= '#EXTM3U'.PHP_EOL;
	$s .= '#EXT-X-TARGETDURATION:' . $dur .PHP_EOL;
	$s .= '#EXT-X-MEDIA-SEQUENCE:' . $seq .PHP_EOL;
	$s .= '#EXT-X-ENDLIST'.PHP_EOL;

	for( $i == 0; $i < $n; $i++ )
	{
		$s .= '#EXTINF:' .$dur .', no desc' .PHP_EOL;
		$s .= "$loc/$seg-". sprintf('%1$06d',( $seq + $i )) .".ts\n";
	}


	if( isset( $_REQUEST['debug'])) echo "playlist=$s\n";
	else
	{
		header( 'Content-Type: application/vnd.apple.mpegurl' );
		header( 'Content-Length: ' .strlen( $s ) );
		echo $s;
	}
}
//
// ====================================
function peerstv_channels_content()
{
global $peerstv_config;
global $peerstv_session;

	header( "Content-type: text/plain" );

	$channel = $peerstv_config['channel'];
	if( isset( $_REQUEST['channel'] ))
	{
		$channel = $_REQUEST['channel'];
		$peerstv_config['channel'] = $channel;
	}

	// get channels list
	$s = file_get_contents( 'http://peers.tv' );

	$channels = array();
	if( preg_match_all( '|<a href="/program/(.*?)/" data-id="(.*?)".*?<img src="(.*?)".*?<b>(.*?)</b></a>|s', $s, $a ) > 0 )
	 foreach( $a[1] as $i => $name )
	 {
		if( strpos( $a[0][ $i ], 'class="locked"' ) > 0 ) continue;

		$channels[ $name ] = array(
			'id' => $a[ 2 ][ $i ],
			'image' => 'http://peers.tv'. $a[ 3 ][ $i ],
			'title' => $a[ 4 ][ $i ],
		);
	 }

if( isset( $_REQUEST['debug'])) print_r( $channels );

	// cache images
	peerstvChannelCacheImages( $channels );

	$peerstv_session['channels'] = $channels;
	peerstvSaveSession();

	// find focused channel
	reset( $channels );
	$f = key( $channels );

	if( $channel == '' && isset( $channels[ $f ] ))
	{
		$channel = $f;
		$peerstv_config['channel'] = $channel;
	}

	peerstvSaveConfig();

	// generate list
	$s = '';

	// number of channels
	$i = 0;
	$n = 0;
	$s .= count( $channels ) . PHP_EOL;

	foreach( $channels as $id => $item )
	{
		$s .= $id .PHP_EOL;
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['image'] .PHP_EOL;

		if( $id == $channel ) $n = $i;
		$i++;
	}
	// focused channel
	$s .= $n .PHP_EOL;

	// focused id channel
	$s .= $channel .PHP_EOL;

	// put data
	if( isset( $_REQUEST['debug']))
	{
		echo $s;
	}
	else
	{
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}
}

//
// ------------------------------------
function replaceHtmlEntity( $s )
{
	$s = str_replace(
	 array( '&nbsp;','&mdash;','&ndash;','&laquo;','&raquo;','&bdquo;','&ldquo;',"\n","\r"),
	 array( ' ',     '-',      '-',      '"',      '"',      '"',      '"',      ' ', ''),
	 $s );

	return $s;
}

//
// ------------------------------------
function getUnixTime( $s )
{
	$t = date_parse_from_format( 'm/d/Y H:i:s', $s );
	return mktime ( $t['hour'], $t['minute'], $t['second'], $t['month'], $t['day'], $t['year']);
}
//
// ------------------------------------
function getPeersTime( $s )
{
global $peerstv_offset;

	return getUnixTime( $s ) - $peerstv_offset;
}
//
// ====================================
function peerstv_list_content()
{
global $peerstv_config;
global $peerstv_session;

	header( "Content-type: text/plain" );

	$channel = $peerstv_config['channel'];
	if( isset( $_REQUEST['channel'] ))
	{
		$channel = $_REQUEST['channel'];
		$peerstv_config['channel'] = $channel;
		peerstvSaveConfig();
	}

	if( isset( $_REQUEST['date'] ))
	{
		$date = $_REQUEST['date'];
		if( $date == 'now' ) $date = '';
	}
	else $date = '';

	if( isset( $_REQUEST['time'] ))
	{
		$time = $_REQUEST['time'];
		if( $time == 'now' ) $time = '';
	}
	else $time = '';

	// get current date
	$tm = time();
	if( $time == '' ) $time = date( 'H:i',   $tm );
	if( $date == '' ) $date = date( 'm/d/Y', $tm );
	$tm = getUnixTime( $date .' '. $time );
	$tp = $tm;

if( isset( $_REQUEST['debug'])) echo "\ntime=$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";
if( isset( $_REQUEST['debug'])) echo "channel=$channel\ndate=$date\n";

	// get epg for channel
	$request = 'http://peers.tv/ajax/program/'
	. $peerstv_session['channels'][ $channel ]['id'] .'/'
	. date( 'Y-m-d', $tp ) .'/';

if( isset( $_REQUEST['debug'])) echo "request=$request\n";

	$s = file_get_contents( $request );
	$a = json_decode($s, true);

	$tb = getUnixTime( $a['telecasts'][0]['time'] );
	if( $tb > $tm )
	{
		// tomorrow is here
		$tp -= 60*60*24;
		$date = date( 'm/d/Y', $tp );

		// get epg for channel
		$request = 'http://peers.tv/ajax/program/'
		. $peerstv_session['channels'][ $channel ]['id'] .'/'
		. date( 'Y-m-d', $tp ) .'/';

if( isset( $_REQUEST['debug'])) echo "request=$request\n";

		$s = file_get_contents( $request );
		$a = json_decode($s, true);
	}

//if( isset( $_REQUEST['debug'] )) var_export( $a );

	// get items
	$cItem = 0;
	$i = 0;

	$items = array();

	foreach( $a['telecasts'] as $item )
	{
		$tb = getPeersTime( $item['time'] );
		$te = getPeersTime( $item['ends'] );

if( isset( $_REQUEST['debug'])) echo "\nbeg=$tb (" .date( 'Y-m-d H:i:s', $tb) .")\n";
if( isset( $_REQUEST['debug'])) echo "end=$te (" .date( 'Y-m-d H:i:s', $te) .")\n";
if( isset( $_REQUEST['debug'])) echo "tit=" .replaceHtmlEntity( $item['title'] ) ."\n";

		if( $tm >= $tb && $tm < $te )
		{

if( isset( $_REQUEST['debug'])) echo "\nok =$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";

			$cItem = $i;
		}

		if( isset( $item['files']) && count( $item['files'] ) > 0 )
		 $url = $item['files'][ 0 ]['movie'];
		else $url = '';

		 $items[ $i++ ] = array(
			'title' => replaceHtmlEntity( $item['title'] ),
			'time'  => date( 'H:i ', $tb ),
			'url'   => $url,
		);
	}

if( isset( $_REQUEST['debug'] )) echo "cItem=$cItem\n";

//if( isset( $_REQUEST['debug'] )) var_export( $items );

	// found next and prev days
	$nDate = '';
	$pDate = '';
	for( $i = 0; $i < count($a['week']); $i++ )
	{
		if( $a['week'][ $i ]['date'] != $date ) continue;
		if( isset( $a['week'][ $i - 1 ] )) $pDate = $a['week'][ $i - 1 ]['date'];
		if( isset( $a['week'][ $i + 1 ] )) $nDate = $a['week'][ $i + 1 ]['date'];
		break;
	}

if( isset( $_REQUEST['debug'])) echo "prev=$pDate\nnext=$nDate\n";

	// generate list
	$s = '';

	// top title
	$a = getMsg('peerstvMonths');
	$b = getMsg('peerstvDays');

	$s .= sprintf( $a[ date( 'n', $tp ) ], date( 'd', $tp )) .', '. $b[ date( 'w', $tp ) ] .PHP_EOL;

	// current date
	$s .= $date .PHP_EOL;

	// next prev days
	$s .= $pDate .PHP_EOL;
	$s .= $nDate .PHP_EOL;

	// list
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['url'  ] .PHP_EOL;
		$s .= $item['time' ] .PHP_EOL;
	}

	// focused item
	$s .= $cItem .PHP_EOL;

	// put datas
	if( isset( $_REQUEST['debug']))
	{
		echo "\n$s\n";
	}
	else
	{
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}
}
//
// ====================================
function peerstv_get_epg_content()
{
global $peerstv_config;
global $peerstv_session;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['cid'] )) return;
	$channel = $_REQUEST['cid'];

	// get current date
	$tm = time();

if( isset( $_REQUEST['debug'])) echo "\ntime=$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";

	$date = date( 'm/d/Y', $tm );

	$tp = getUnixTime( $date );

if( isset( $_REQUEST['debug'])) echo "channel=$channel\ndate=$date\n";

	// get epg for channel
	$request = 'http://peers.tv/ajax/program/'
	. $peerstv_session['channels'][ $channel ]['id'] .'/'
	. date( 'Y-m-d', $tp ) .'/';

if( isset( $_REQUEST['debug'])) echo "request=$request\n";

	$s = file_get_contents( $request );
	$a = json_decode($s, true);

	$tb = getUnixTime( $a['telecasts'][0]['time'] );
	if( $tb > $tm )
	{
		// tomorrow is here
		$tp -= 60*60*24;
		$date = date( 'm/d/Y', $tp );

		// get epg for channel
		$request = 'http://peers.tv/ajax/program/'
		. $peerstv_session['channels'][ $channel ]['id'] .'/'
		. date( 'Y-m-d', $tp ) .'/';

if( isset( $_REQUEST['debug'])) echo "request=$request\n";

		$s = file_get_contents( $request );
		$a = json_decode($s, true);
	}

//if( isset( $_REQUEST['debug'] )) var_export( $a );

	// get items
	$cItem = NULL;
	$i = 0;

	$items = array();

	foreach( $a['telecasts'] as $item )
	{
		$tb = getPeersTime( $item['time'] );
		$te = getPeersTime( $item['ends'] );

if( isset( $_REQUEST['debug'])) echo "\nbeg=$tb (" .date( 'Y-m-d H:i:s', $tb) .")\n";
if( isset( $_REQUEST['debug'])) echo "end=$te (" .date( 'Y-m-d H:i:s', $te) .")\n";
if( isset( $_REQUEST['debug'])) echo "tit=" .replaceHtmlEntity( $item['title'] ) ."\n";

		if( $tm >= $tb && $tm < $te )
		{

if( isset( $_REQUEST['debug'])) echo "\nok =$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";

			$cItem = $i;
		}

		 $items[ $i++ ] = array(
			'title' => date( 'H:i ', $tb ) . replaceHtmlEntity( $item['title'] ),
		);
	}

if( isset( $_REQUEST['debug'] )) echo "cItem=$cItem\n";

//if( isset( $_REQUEST['debug'] )) var_export( $items );

	if( $cItem == NULL ) return;

	echo $items[ $cItem ]['title'] .PHP_EOL;

	if( isset( $items[ $cItem + 1 ] ))
	 echo $items[ $cItem + 1 ]['title'];
}

?>