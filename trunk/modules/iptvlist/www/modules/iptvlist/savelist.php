<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

define("last_playlist","/tmp/iptv_lastlist.dat");

$start = 2;

$list = file( "/tmp/iptv_playlist.txt" );

$swap1 = 0;
$swap2 = 0;

if( isset( $_GET["swap1"] ) ) 
{
	$swap1 = $_GET["swap1"];
}

if( isset( $_GET["swap2"] ) ) 
{
	$swap2 = $_GET["swap2"];
}

if( isset( $_GET["delete"] ) ) 
{
	$delete = $_GET["delete"];
	$s1 = $list[ $delete * 3 + $start ];
	$s2 = $list[ $delete * 3 + 1+ $start ];
	$s3 = $list[ $delete * 3 + 2 + $start ];
    array_splice($list, $delete * 3 + $start, 3);
	$list[ 0 ] = ($list[ 0 ] - 1)."\n";
}

if( isset( $_GET["insert"] ) ) 
{
	$insert = $_GET["insert"];
    array_splice($list, $insert * 3 + $start, 0, array( $s1, $s2, $s3 ) );
	$list[ 0 ] = ( $list[ 0 ] + 1 )."\n";
}

$favorite = -1;
if( isset( $_GET["favorite"] ) ) 
{
	$favorite = $_GET["favorite"];
}


$s1 = $list[ $swap1 * 3 + $start ];
$s2 = $list[ $swap1 * 3 + 1 + $start ];
$s3 = $list[ $swap1 * 3 + 2 + $start ];

$list[ $swap1 * 3 + $start ] = $list[ $swap2 * 3 + $start ];      
$list[ $swap1 * 3 + 1 + $start ] = $list[ $swap2 * 3 + 1 + $start ];
$list[ $swap1 * 3 + 2 + $start ] = $list[ $swap2 * 3 + 2 + $start ];

$list[ $swap2 * 3 + $start ] = $s1;
$list[ $swap2 * 3 + 1 + $start ] = $s2;
$list[ $swap2 * 3 + 2 + $start ] = $s3;

if ( $favorite == -1 )
{
	file_put_contents( "/tmp/iptv_playlist.txt", $list );
}


/*
 now save m3u
*/

$resStr = "#EXTM3U\n";

$processed = 0;

$count = $list[ 0 ] * 3 + $start;
for( $i = $start; $i < $count; $i++ )
{
	$resStr .= "#EXTINF:0,".$list[ $i ];
	$i++;
	$resStr .= $list[ $i ];
	$i++;
}

$m3uFileName = "";
if (file_exists(last_playlist)) 
{
	$m3uFileName = file_get_contents(last_playlist);
	$m3uFileName = trim( str_replace('\r', '', str_replace('\n', '', $m3uFileName)) );
	if (file_exists($m3uFileName) == false ) 
	{
		$m3uFileName = "";		
	}
} 

if ( $m3uFileName == "" )
{
	$m3uFileName = "Favorites.m3u";
} 

if ( $favorite == -1 )
{
	file_put_contents( $m3uFileName, $resStr );
}

if ( $favorite != -1 )
{
	$fv = file_get_contents( "Favorites.m3u" );
	$fv .= "#EXTINF:0,".$list[ $favorite * 3 + $start ];
	$fv .= $list[ $favorite * 3 + 1 + $start ];
	file_put_contents( "Favorites.m3u", $fv );
}

include_once("epglist.php");
createEPGList( $m3uFileName );

echo "done";
?>
