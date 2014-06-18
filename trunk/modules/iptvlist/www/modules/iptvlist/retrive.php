#!/tmp/www/cgi-bin/php
<?php

//==================================================================
//==================================================================
function gtcallback($buffer)
{
  return "";
}

//==================================================================
//==================================================================

define("settings","/usr/local/etc/mos/www/modules/iptvlist/settings.conf");

ob_start("gtcallback");
require_once 'iptvlist_timezone.php';
ob_end_flush();


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

$tz = date_default_timezone_get();

$doc = new DomDocument('1.0');
$root = $doc->createElement('root');
$root = $doc->appendChild($root);

$child = $doc->createElement( "version" );
$child = $root->appendChild($child);
$child->setAttribute('value', "3");

$child = $doc->createElement( "UDPProxy" );
$child = $root->appendChild($child);
$child->setAttribute('value', $udpproxy);

$child = $doc->createElement( "Timezone" );
$child = $root->appendChild($child);
$child->setAttribute('value', $tz );

$child = $doc->createElement( "Recordings" );
$child = $root->appendChild($child);
$child->setAttribute('value', $path);

$child = $doc->createElement( "playlists" );
$child = $root->appendChild($child);


if ( file_exists("Favorites.m3u") == false ) 
{
	$fv = "#EXTM3U\n";
	$fv .= "#EXTINF:0,Favorites.m3u - dummy link\n";
	$fv .= "none\n";

	file_put_contents( "Favorites.m3u", $fv );
} 

	foreach (glob( "*.m3u" ) as $filename) 
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
		
		$child1 = $doc->createElement( "playlist" );
		$child1 = $child->appendChild($child1);
		$child1->setAttribute('name', $name);

		$content = file_get_contents($filename);
		$child1->setAttribute('value', $content);

		$urlFileName = $filename.".url";
		
		if ( file_exists( $urlFileName ) == true ) 
		{
			$content = file_get_contents( $urlFileName );
			$child1->setAttribute( 'url', $content );
		}
 
	}


echo $doc->saveXML();

?>