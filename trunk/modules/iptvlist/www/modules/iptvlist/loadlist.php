<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

define("last_playlist","/tmp/iptv_lastlist.dat");
define("udpsettings","/usr/local/etc/mos/www/modules/iptvlist/udp_proxy.conf");
define("settings","/usr/local/etc/mos/www/modules/iptvlist/settings.conf");

if (file_exists("Favorites.m3u") == false) 
{
	$fv = "#EXTM3U\n";
	$fv .= "#EXTINF:0,Favorites.m3u - dummy link\n";
	$fv .= "none\n";

	file_put_contents( "Favorites.m3u", $fv );
} 

if (file_exists(udpsettings)) 
{
	$udpproxy = file_get_contents(udpsettings);
	$udpproxy = trim( str_replace('\r', '', str_replace('\n', '', $udpproxy)) );
	if ( substr( $path, -1) <> "/" ) 
	{
		$udpproxy.= "/";
	}
} 
else 
{
	$udpproxy="http://127.0.0.1:8080/";
} 



$m3uFileName = "";
if (file_exists(last_playlist)) 
{
	$m3uFileName = file_get_contents(last_playlist);
	$m3uFileName = trim( str_replace('\r', '', str_replace('\n', '', $m3uFileName)) );
	if ( $m3uFileName != "Recordings" )
	{
		if (file_exists($m3uFileName) == false ) 
		{
			$m3uFileName = "";		
		}
	}
} 

if ( $m3uFileName == "" )
{
	$m3uFileName = "Favorites.m3u";
} 

/*
$m3uFile = file( "/usr/local/etc/mos/www/modules/iptvlist/playlist.m3u" );
*/

$res="";
$count=0;

if ( $m3uFileName == "Recordings" )
{
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

	$path .= "*.mpg";


	foreach (glob( $path ) as $filename) 
	{  	
		$slpos = strrpos($filename, "/");
		if ($slpos === false) 
		{ 
			$slpos = 0;
		}       	
		else
		{
			$slpos += 1;
		}  	
		$name = substr( $filename, $slpos ); 
		$name= str_replace( ".mpg", "", $name );
		$name= str_replace( "_", " ", $name );
	    $res .= $name . "\n";	
	    $res .= $filename . "\n";	
	    $res .= $filename . "\n";	
		$count++;
	}

}
else
{
	$m3uFile = file_get_contents( $m3uFileName );
        $m3uFile = str_replace("\n", "\r", $m3uFile);  
        $m3uFile = str_replace("\r\r", "\r", $m3uFile);  
	$m3uFile = explode( "\r", $m3uFile );

	foreach( $m3uFile as $key => $line ) 
	{
		if( strtoupper(substr($line, 0, 8)) == "#EXTINF:") 
		{
	        $line = substr_replace($line, "", 0, 8);
	        $line = explode(",", $line, 2);
			$r = trim( $line[1] );
			if ( $r == "" )
			{
				continue;
			}
			$res .= $r."\n";
			
			$link = trim( $m3uFile[$key + 1]);
	
			$res .= $link."\n";
	
			$mmsPos = strpos( $link, "mms://" );
			if ( $mmsPos === false )
			{
				$mmsPos = strpos( $link, "mmsh://" );
			}
			if ( $mmsPos === false )
			{
				$mmsPos = strpos( $link, "rtsp://" );
			}

			if ( $mmsPos !== false )
			{
				if ( $mmsPos == 0 )
				{
					$link = "http://127.0.0.1:88/cgi-bin/translate?stream,".urlencode( $link );
				}
			}
	
			$rtmpPos = strpos( $link, "rtmp://" );
			if ( $rtmpPos === false )
			{
				$rtmpPos = strpos( $link, 'rtmpt://' );
			}
			if ( $rtmpPos === false )
			{
				$rtmpPos = strpos( $link, 'rtmpe://' );
			}
			if ( $rtmpPos === false )
			{
				$rtmpPos = strpos( $link, 'rtmpte://' );
			}
			if ( $rtmpPos === false )
			{
				$rtmpPos = strpos( $link, 'rtmps://' );
			}
	
			if ( $rtmpPos !== false )
			{
				if ( $rtmpPos == 0 )
				{
			 		$link = "http://127.0.0.1/modules/iptvlist/rtmp.cgi?rtmp-raw=".$link;
	
				}
			}
	
			$udpPos = strpos( $link, "udp://@" );
			if ( $udpPos === 0 )
			{
				$link = $udpproxy."udp/".substr( $link, 7 );		
			}
	
			$res .= $link."\n";
			$count++;
	      	}      	          
	}
}

$name= str_replace( ".m3u", "", $m3uFileName );
$name= str_replace( ".m3U", "", $name );
$name= str_replace( ".M3u", "", $name );
$name= str_replace( ".M3U", "", $name );

file_put_contents( "/tmp/iptv_playlist.txt", $count."\n".$name."\n".$res );

echo "done";

?>
