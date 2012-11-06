<?php

/*
	iptvlist created by Roman Lut aka hax.
*/
define("last_playlist","/tmp/iptv_lastlist.dat");

$res="";
$count=0;
$index=0;


if (file_exists(last_playlist)) 
{
	$lastPlaylist = file_get_contents(last_playlist);
	$lastPlaylist = trim( str_replace('\r', '', str_replace('\n', '', $lastPlaylist)) );
} 
else 
{
	$lastPlaylist="";
} 


foreach (glob("*.m3u") as $filename) 
{
    $res .= $filename . "\n";
	
	$name= str_replace( ".m3u", "", $filename );
	$name= str_replace( ".m3U", "", $name );
	$name= str_replace( ".M3u", "", $name );
	$name= str_replace( ".M3U", "", $name );

    $res .= $name . "\n";
    $count++;

	if ( $filename == $lastPlaylist )
    {
     	$index = $count - 1;
    }
}

$res .= "Recordings\n";
$res .= "Recordings\n";
$count++;

if ( "Recordings" == $lastPlaylist )
{
   	$index = $count - 1;
}


file_put_contents( "/tmp/iptv_lists.txt", $count."\n".$index."\n".$res );

echo "done";

?>
