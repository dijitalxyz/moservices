#!/tmp/www/cgi-bin/php
<?php

define("settings","/usr/local/etc/mos/www/modules/iptvlist/settings.conf");
define("udpsettings","/usr/local/etc/mos/www/modules/iptvlist/udp_proxy.conf");

if( isset( $GLOBALS["HTTP_RAW_POST_DATA"] ) ) 
{
	echo $GLOBALS["HTTP_RAW_POST_DATA"];
	$doc = new DomDocument();
	$doc->loadXML( $GLOBALS["HTTP_RAW_POST_DATA"] );

	$elem = $doc->getElementsByTagName('version');
	if ( $elem->item(0)->getAttribute("value")  == "1" )
	{

		$elem = $doc->getElementsByTagName('Recordings');
		$path = $elem->item(0)->getAttribute("value");
		echo $path . "\n";
		file_put_contents( settings, $path );


		$elem = $doc->getElementsByTagName('UDPProxy');
		$udpproxy = $elem->item(0)->getAttribute("value");
		echo $udpproxy . "\n";
		file_put_contents( udpsettings, $udpproxy );
	
		echo "deleting .m3u..bak...\n";

		//delete all m3u.bak
		foreach (glob( "*.m3u.bak" ) as $filename) 
		{  	
			echo "delete:" . $filename . "\n";
			unlink( $filename );
		}
	
		echo "renaming .m3u to .m3u..bak...\n";
		//reame all m3u to m3u.bak
		foreach (glob( "*.m3u" ) as $filename) 
		{  	
			$newName = str_replace( ".m3u", ".m3u.bak", $filename );
			echo "rename:" . $filename . "->" . $newName . "\n";
			rename( $filename, $newName );
		}
	}
}
else
{
	echo "Parameters not specified.";
}

?>