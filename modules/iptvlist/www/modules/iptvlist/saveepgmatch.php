<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

define("epgmatch","/usr/local/etc/mos/www/modules/iptvlist/epg_match.conf");

//==================================================================
//==================================================================
function loadEPGMatch()
{
	$prMatch = array();

	if ( file_exists( epgmatch ) ) 
	{
		$epgmatchfile = file_get_contents( epgmatch );
	        $epgmatchfile = str_replace( "\n", "\r", $epgmatchfile );  
	        $epgmatchfile = str_replace( "\r\r", "\r", $epgmatchfile );  
		$epgmatchfile = explode( "\r", $epgmatchfile );

		foreach( $epgmatchfile as $key => $line ) 
		{
        		$line = explode( "|", $line, 2);
			if ( count( $line ) == 2 )
			{
//				echo "prmatch: ".$line[0]."->".$line[1];
				$prMatch[ $line[0] ] = $line[ 1 ];
			}
		}
	} 

	return $prMatch;
}

//==================================================================
//==================================================================
if( isset( $_GET["name1"] ) ) 
{
	$name1 = $_GET["name1"];

	if( isset( $_GET["name2"] ) ) 
	{
		$name2 = $_GET["name2"];

		$prMatch = loadEPGMatch();

		$prMatch[ $name1 ] = $name2;

		foreach( $prMatch as $key => $line ) 
		{
			if ( $key != $line )
			{
				$res .= $key."|".$line."\n";
			}
		}

		file_put_contents( epgmatch, $res );

		echo "done";
	}
	else
	{
		echo "name1 not sepcified.";
	}
}
else
{
	echo "name1 not sepcified.";	
}

?>
