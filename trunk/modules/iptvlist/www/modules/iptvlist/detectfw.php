<?php

function detectFirmware( $forPOHD, $forIconbit )
{
	if( ! is_dir('/usr/local/bin/home_menu/')) return $forPOHD;
	if( file_exists('/usr/local/bin/home_menu/scripts/LatestAddMovie.rss')) return $forPOHD;
	return $forIconbit;
}

?>