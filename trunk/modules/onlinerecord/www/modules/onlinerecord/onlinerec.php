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
$onlinerec_offset = 4 * 3600 - $my_offset;

// get config e.t.c.
include 'onlinerec.init.php';

//
// ------------------------------------
function onlinerecChannelCacheImages( &$chs )
{
	$p = dirname( __FILE__ ) .'/cached/';

	foreach( $chs as $id => $ch )
	{
		$f = $p . $id .'.png';
		if( ! file_exists( $f ))
		{
			$s = 'http://www.moservices.org/modules/tvlogo/' .urlencode( $ch['title'] ) .'.png';
			copy( $s, $f );
		}
		$chs[ $id ]['image'] = $f;
	}
}
//
// ------------------------------------
function onlinerec_get_content()
{
global $onlinerec_config;

	if( ! isset( $_REQUEST['cid'] )) return;

	$u = 'http://online-record.ru:8000/'
	. $_REQUEST['cid'] .'_'. $onlinerec_config['quality'] .'/';

	if( $onlinerec_config['cast'] == 'stream' ) $u .= 'mpegts';
	else  $u .= 'index.m3u8';

	if( isset( $_REQUEST['debug'])) echo 'Location: ' .$u ."\n" ;
	else header ( 'Location: ' .$u );
}
//
// ====================================
function onlinerec_channels_content()
{
global $onlinerec_config;
global $onlinerec_session;

	header( "Content-type: text/plain" );

	$channel = $onlinerec_config['channel'];
	if( isset( $_REQUEST['channel'] ))
	{
		$channel = $_REQUEST['channel'];
		$onlinerec_config['channel'] = $channel;
	}

	// get channels list
	$request = 'http://xml.online-record.ru/channel.xml';

	// get content
if( isset( $_REQUEST['debug'])) echo "request=$request\n";
	$s = file_get_contents( $request );
if( isset( $_REQUEST['debug'])) echo "respond=$s\n";

	// get channels
	$channels = array();

	if( preg_match_all( '|<item id="(.*?)".*?<title>(.*?)</title>|s', $s, $a ) > 0 )
	 foreach( $a[1] as $i => $id )
	  $channels[ $id ] = array(
			'title' => trim( $a[2][ $i ] ),
	  );

if( isset( $_REQUEST['debug'])) print_r( $channels );

	// cache images
	onlinerecChannelCacheImages( $channels );

	$onlinerec_session['channels'] = $channels;
	onlinerecSaveSession();

	// find focused channel
	reset( $channels );
	$f = key( $channels );

	if( $channel == '' && isset( $channels[ $f ] ))
	{
		$channel = $f;
		$onlinerec_config['channel'] = $channel;
	}

	onlinerecSaveConfig();

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
	$t = date_parse_from_format( 'Y-m-d H:i:s', $s );
	return mktime ( $t['hour'], $t['minute'], $t['second'], $t['month'], $t['day'], $t['year']);
}
//
// ------------------------------------
function getEpgList( $channel, $date )
{
global $onlinerec_config;
global $onlinerec_offset;

	$request = 'http://online-record.ru/?channel='. $channel .'&date='. $date;

if( isset( $_REQUEST['debug'])) echo "request=$request\n";

	$s = file_get_contents( $request );

//if( isset( $_REQUEST['debug'])) echo "respond=$s\n";

	$tt = 0;

	$items = array();
	if( preg_match_all( "|<tr>\\s*<td style='vertical-align:top;'>.*?</tr>|s", $s, $a ) > 0 )
	 foreach( $a[0] as $ss )
	 {
		$time = 0;
		if( preg_match( "|<td style='vertical-align:top;'>(.*?)</td>|s", $ss, $aa ) > 0 )
		{
			$time = getUnixTime( $date .' '. $aa[1] );

			if( $time < $tt ) $time += 60*60*24;
			$tt = $time;

			$time -= $onlinerec_offset;
		}

		$title = '';
		if( preg_match( "|<span>(.*?)<|s", $ss, $aa ) > 0 )
		 $title = $aa[1];

		$url = '';
		if( preg_match( "|<div.*? class='tvstream'.*? host='(.*?)'.*? ps='(.*?)'.*? pd='(.*?)'>|s", $ss, $aa ) > 0 )
		 $url = $aa[1] .'/'. $channel .'_'. $onlinerec_config['quality'] .'/index-'. $aa[2] .'-'. $aa[3] .'.m3u8';

		$items[] = array(
			'tm'   => $time,
			'time' => date( 'H:i', $time ),
			'title'=> $title,
			'url'  => $url,
		);
	 }

//if( isset( $_REQUEST['debug'])) var_export( $items );

	return $items;
}
//
// ====================================
function onlinerec_list_content()
{
global $onlinerec_config;
global $onlinerec_session;

	header( "Content-type: text/plain" );

	$channel = $onlinerec_config['channel'];
	if( isset( $_REQUEST['channel'] ))
	{
		$channel = $_REQUEST['channel'];
		$onlinerec_config['channel'] = $channel;
		onlinerecSaveConfig();
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
	if( $date == '' ) $date = date( 'Y-m-d', $tm );
	$tm = getUnixTime( $date .' '. $time );
	$tp = $tm;

if( isset( $_REQUEST['debug'])) echo "\ntime=$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";
if( isset( $_REQUEST['debug'])) echo "date=$date\nchannel=$channel\n";

	$items = getEPGList( $channel, $date );

	if( count( $items ) == 0 
	|| $items[0]['tm'] > $tm )
	{
		// tomorrow is here
		$tp -= 60*60*24;
		$date = date( 'Y-m-d', $tp );

		$items = getEPGList( $channel, $date );
	}

	// find current time
	$cItem = 0;

	foreach( $items as $i => $item )
	{
		$tb = $item['tm'];
		if( isset( $items[ $i + 1 ] )) $te = $items[ $i + 1 ]['tm'];
		else $te = $tb;

if( isset( $_REQUEST['debug'])) echo "\nbeg=$tb (" .date( 'Y-m-d H:i:s', $tb) .")\n";
if( isset( $_REQUEST['debug'])) echo "end=$te (" .date( 'Y-m-d H:i:s', $te) .")\n";
if( isset( $_REQUEST['debug'])) echo "title=" .$item['title'] ."\n";

		if( $tm >= $tb && $tm < $te )
		{
if( isset( $_REQUEST['debug'])) echo "\nok =$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";

			$cItem = $i;
			break;
		}
	}

if( isset( $_REQUEST['debug'] )) echo "\ncItem=$cItem\n";
//if( isset( $_REQUEST['debug'] )) var_export( $items );

	// find next and prev days
	$pDate = date( 'Y-m-d', $tp - 60*60*24 );
	$nDate = date( 'Y-m-d', $tp + 60*60*24 );

if( isset( $_REQUEST['debug'])) echo "prev=$pDate\nnext=$nDate\n";

	// generate list
	$s = '';

	// top title
	$t = $tp;
	$a = getMsg('onlinerecMonths');
	$b = getMsg('onlinerecDays');

	$s .= sprintf( $a[ date( 'n', $t ) ], date( 'd', $t )) .', '. $b[ date( 'w', $t ) ] .PHP_EOL;

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
function onlinerec_get_epg_content()
{
global $onlinerec_config;
global $onlinerec_session;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['cid'] )) return;
	$channel = $_REQUEST['cid'];

	// get current date
	$tm = time();
	$date = date( 'Y-m-d', $tm );

	$tp = getUnixTime( $date );

if( isset( $_REQUEST['debug'])) echo "\ntime=$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";
if( isset( $_REQUEST['debug'])) echo "channel=$channel\ndate=$date\n";

	$items = getEPGList( $channel, $date );

	if( count( $items ) == 0 
	|| $items[0]['tm'] > $tm )
	{
		// tomorrow is here
		$tp -= 60*60*24;
		$date = date( 'Y-m-d', $tp );

		$items = getEPGList( $channel, $date );
	}

	// get items
	$cItem = NULL;

	foreach( $items as $i => $item )
	{
		$tb = $item['tm'];
		if( isset( $items[ $i + 1 ] )) $te = $items[ $i + 1 ]['tm'];
		else $te = $tb;


if( isset( $_REQUEST['debug'])) echo "\nbeg=$tb (" .date( 'Y-m-d H:i:s', $tb) .")\n";
if( isset( $_REQUEST['debug'])) echo "end=$te (" .date( 'Y-m-d H:i:s', $te) .")\n";
if( isset( $_REQUEST['debug'])) echo "title=" .$item['title'] ."\n";

		if( $tm >= $tb && $tm < $te )
		{
if( isset( $_REQUEST['debug'])) echo "\nok =$tm (" .date( 'Y-m-d H:i:s', $tm) .")\n";
			 $cItem = $i;
			break;
		}
	}

	if( $cItem == NULL ) return;

	echo $items[ $cItem ]['time'] .' '. $items[ $cItem ]['title'] .PHP_EOL;

	if( isset( $items[ $cItem + 1 ] ))
	 echo $items[ $cItem + 1 ]['time'] .' '. $items[ $cItem + 1 ]['title'];

	echo  PHP_EOL;
}

?>