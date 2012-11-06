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

		$playlists = $doc->getElementsByTagName('playlist');
		foreach ($playlists as $playlist) 
		{
			$pname = $playlist->getAttribute("name");
			echo $pname ."\n";
			$pdata = $playlist->getAttribute("value");
			
			if (file_exists($pname)) 
			{
				$pdata = file_get_contents($pname) . $pdata;
			}

			file_put_contents( $pname, $pdata );
		}	
	}
}
else
{
	echo "Parameters not specified.";
}

?>