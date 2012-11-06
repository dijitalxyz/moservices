#!/tmp/www/cgi-bin/php
<?php

function apply_filters($tag, $value) 
{
        global $wp_filter, $merged_filters, $wp_current_filter;

        $args = array();

        // Do 'all' actions first
        if ( isset($wp_filter['all']) ) {
                $wp_current_filter[] = $tag;
                $args = func_get_args();
                _wp_call_all_hook($args);
        }

        if ( !isset($wp_filter[$tag]) ) {
                if ( isset($wp_filter['all']) )
                        array_pop($wp_current_filter);
                return $value;
        }

        if ( !isset($wp_filter['all']) )
                $wp_current_filter[] = $tag;

        // Sort
        if ( !isset( $merged_filters[ $tag ] ) ) {
                ksort($wp_filter[$tag]);
                $merged_filters[ $tag ] = true;
        }

        reset( $wp_filter[ $tag ] );

        if ( empty($args) )
                $args = func_get_args();

        do {
                foreach( (array) current($wp_filter[$tag]) as $the_ )
                        if ( !is_null($the_['function']) ){
                                $args[1] = $value;
                                $value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
                        }

        } while ( next($wp_filter[$tag]) !== false );

        array_pop( $wp_current_filter );

        return $value;
}


function sanitize_file_name( $filename ) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $special_chars = apply_filters('sanitize_file_name_chars', $special_chars, $filename_raw);
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    return apply_filters('sanitize_file_name', $filename, $filename_raw);
}                               

function transformRtmp( $link )
{
	$s = "";
	$pairs = explode( "&", $link );
	$first = 1;
    foreach ( $pairs as $pair ) 
	{
		if ( $first == 1 )
		{
			$first = 0;
			$s = $pair;
		}
		else
		{
        	$nv = explode( "=", $pair );

			$s1 = urldecode( $nv[0] );
			$s1 = strtolower( $s1 );

			if ( $s1 == "swfurl" )
			{
				$s1 = "swfUrl";
			}

			if ( $s1 == "tcurl" )
			{
				$s1 = "tcUrl";
			}

			if ( $s1 == "pageurl" )
			{
				$s1 = "pageUrl";
			}

			if ( $s1 == "swfvfy" )
			{
				$s1 = "swfVfy";
			}

			if ( $s1 == "swfage" )
			{
				$s1 = "swfAge";
			}

			if ( $s1 == "flashVer" )
			{
				$s1 = "flashVer";
			}

			$s = $s." --".$s1." ".'"'.urldecode( $nv[1] ).'"';
		}
    }
	return "/usr/local/etc/mos/www/modules/iptvlist/rtmpdump -r ".$s." --quiet --live";
}

@date_default_timezone_set(@date_default_timezone_get()); 

define("settings","/usr/local/etc/mos/www/modules/iptvlist/settings.conf");
if (file_exists(settings)) 
{
	$path = file_get_contents(settings);
	$path = trim( str_replace('\r', '', str_replace('\n', '', $path)) );
	if ( substr( $path, -1 ) <> "/" ) 
	{
		$path.= "/";
	}
} 
else 
{
	$path="/tmp/usbmounts/sda1/";
} 
//echo $path;

define("udpsettings","/usr/local/etc/mos/www/modules/iptvlist/udp_proxy.conf");
if (file_exists(udpsettings)) 
{
	$udpproxy = file_get_contents(udpsettings);
	$udpproxy = trim( str_replace('\r', '', str_replace('\n', '', $udpproxy)) );
	if ( substr( $udpproxy, -1 ) <> "/" ) 
	{
		$udpproxy.= "/";
	}
} 
else 
{
	$udpproxy="http://127.0.0.1:8080/";
} 


define("logfile","/tmp/iptv_downl");

if(isset( $_GET["downloadlink"]) ) 
{
		$fileName=sanitize_file_name( $_GET["title"] )."_".date( "d.m.Y_H.i.s", time() ).".mpg";
		$outputfile = "'".$path . $fileName . "'";
		$downloadfile = $_GET["downloadlink"];				


		$mmsPos = strpos( $downloadfile, 'mms://' );
		if ( $mmsPos === false )
		{
			$mmsPos = strpos( $downloadfile, 'mmsh://' );
		}
		if ( $mmsPos === false )
		{
			$mmsPos = strpos( $downloadfile, 'rtsp://' );
		}

		$rtmpPos = strpos( $downloadfile, 'rtmp://' );
		if ( $rtmpPos === false )
		{
			$rtmpPos = strpos( $downloadfile, 'rtmpt://' );
		}
		if ( $rtmpPos === false )
		{
			$rtmpPos = strpos( $downloadfile, 'rtmpe://' );
		}
		if ( $rtmpPos === false )
		{
			$rtmpPos = strpos( $downloadfile, 'rtmpte://' );
		}
		if ( $rtmpPos === false )
		{
			$rtmpPos = strpos( $downloadfile, 'rtmps://' );
		}

		$udpPos = strpos( $downloadfile, "udp://@" );
		if ( $udpPos === 0 )
		{
			$downloadfile = $udpproxy."udp/".substr( $downloadfile, 7 );		
		}

		if ( $mmsPos !== false )
		{
			$link = substr( $downloadfile, $mmsPos );
			$command="nice -n -11 /usr/local/etc/mos/rssex2/bin/msdl -q -o ".$outputfile." ".$link." > /dev/null 2>&1 & echo $!";
		}
		else if ( $rtmpPos !== false )
		{
//			$rtmpCommand="/usr/local/etc/mos/www/modules/iptvlist/rtmpdump -r rtmp://live.simplestreamcdn.net/jewellerychannel_live/ --pageUrl http://www.thejewellerychannel.tv/TJC_LiveAuctionHome.aspx --swfUrl http://simplestream.com/players/thejewellerychannel/jwplayer/player.swf --playpath jewellerychannel2";
			$rtmpCommand = transformRtmp( $downloadfile );
			$command="nice -n -11 ".$rtmpCommand." -o ".$outputfile." > /dev/null 2>&1 & echo $!";
//echo $command;
//$command="ls";
		}
		else
		{
			// starting download using wget with low priority
			$command ="nice -n -11 wget -O ".$outputfile." ".$downloadfile." > /dev/null 2>&1 & echo $!";
		}

        unset($op);
		exec($command, $op);
		$pids=$op[0];
		file_put_contents(logfile, $pids );
		echo $fileName;
}
else if ( isset( $_GET["stop"]) )
{
	if ( file_exists( logfile ) )
	{
    	$temp= file_get_contents(logfile);
		unset($op);
		exec("kill ".$temp, $op);
		unlink( logfile );
		echo "download stopped";	
	}
}
else if ( isset( $_GET["streamrtmp"]) )
{
		//expecting rtmp:// or rtsp:// link here
		$link = $_GET["streamrtmp"];
		$rtmpString = transformRtmp( $link );
//		echo $rtmpString;
//		echo"<br>";
		header("Content-type: video/mp4");					
        passthru( $rtmpString );
}
else
{
	echo "parameters not set. ";
	echo "date=".date( "H.i.s_d.m.Y", time() );
}
?>