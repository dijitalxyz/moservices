<?php

function rss_weather_content()
{
global $nav_lang;
global $mos;

	$config = $mos .'/www/modules/weather/weather.conf';
	$player = '/usr/local/etc/dvdplayer/savedrss/scripts/map/myfavorites.rss';

	$code = 'RSXX0063';
	$temp = 'c';
	if( file_exists( $player ))
	{
		$lenta = simplexml_load_file($player,"SimpleXMLElement",LIBXML_NOERROR);
		$code = $lenta->channel->item[1]->cur;
	}
	else if( file_exists( $config ))
	{
		$table = file($config);
		$code = trim($table[1]);
		$temp = trim($table[3]);
	}


	header ('Location: http://www.moservices.org/modules/weather/weather.php?city='. $code .'&temp='. $temp .'&lang='. $nav_lang );
}

?>
