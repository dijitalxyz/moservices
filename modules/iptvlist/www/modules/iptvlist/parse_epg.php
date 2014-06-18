<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

/*

parseEPG() function in this cript has to retrive program guide for current day and store it in 
"/tmp/epg_channels.txt" and "/tmp/epg_data.txt", in UTF-8 encoding.
Module is catching parsed results, so script is called only once a day.
Program guide should contain programs starting from 06:00 current date and ending till 06:00 next date - this is important!

epg_channels.txt should contain \n separated lines:
- channel name
- starting position (in bytes) in epg_data.txt
- ending position (in bytes) in epg_data.txt

epg_data.txt should contain \n separated lines:
- start time, in format hour:minute
- program name

All echo output from script is ignored.
There are no error response codes.
If script is unable to retrive EPG due to network error,
it should not create output files.

It is important that timezone is not changed or set to one defined in iptv_timezone when script exits.
*/

header("Content-Type:text/plain");

mb_internal_encoding("UTF-8"); 

//==================================================================
//==================================================================
function adjustHour( $prtime, $prHourAdd )
{

//$prHourAdd = 1;

	$prtime1 = explode( ":", $prtime );
	$prHour = intval( $prtime1[ 0 ] ) + $prHourAdd;
	$prMinute = intval( $prtime1[ 1 ] );

	$prHourStr = "".$prHour;
	$prMinuteStr = "".$prMinute;

	if ( strlen( $prHourStr ) < 2 )
	{
		$prHourStr = "0".$prHourStr;	
	}

	if ( strlen( $prMinuteStr ) < 2 )
	{
		$prMinuteStr = "0".$prMinuteStr;	
	}

//echo $prHourStr.":".$prMinuteStr."\n";
	return $prHourStr.":".$prMinuteStr;
}

//==================================================================
//==================================================================
function pggcallback($buffer)
{
  return "";
}


//==================================================================
//==================================================================
//Implementatin to retrive program guide from www.svetv.com
function doParseEPG( $destYear, $destMon, $destDay )
{

date_default_timezone_set( 'Europe/Kiev' );

$ctime_arr = getdate( time() );

$adjHour1 = intval( $ctime_arr[ "hours" ] ); 

ob_start("pggcallback");
require 'iptvlist_timezone.php';
ob_end_flush();

$ctime_arr = getdate( time() );

$adjHour2 = intval( $ctime_arr[ "hours" ] ); 

$prHourAdd = $adjHour2 - $adjHour1;  

//echo "adjust=".$prHourAdd."\n";

$context = stream_context_create(
array(
        'http'=>array(
                        'header' => "User-Agent: Brauzer 2/0\r\nConnection: Close\r\n\r\n",
                        'method' => 'GET',
                     )
    )
);

if ( strlen( $destMon ) < 2 )
{
	$destMon = "0".$destMon;
}

if ( strlen( $destMon ) < 2 )
{
	$destDay = "0".$destDay;
}

$epg_channels = "";
$epg_data = "";

$lstId=0;

$processedChannels = array();


while ( $lstId < 2 )
{

//$s = @file_get_contents("http://www.vsetv.com/schedule.html",false,$context); 
//echo "http://www.vsetv.com/schedule_package_ntvplusua_day_".$destYear."-".$destMon."-".$destDay.".html";

if( $lstId == 0 )
{
	$s = @file_get_contents("http://www.vsetv.com/schedule_package_maximumtv_day_".$destYear."-".$destMon."-".$destDay.".html",false,$context); 
}
else
{
	$s = @file_get_contents("http://www.vsetv.com/schedule_package_ntvplus_day_".$destYear."-".$destMon."-".$destDay.".html",false,$context); 
}

$lstId++;

$count = strpos($s, 'class="channeltitle">', 0);
while($count)
{
	$count2 = strpos($s, '<', $count+21);
	$title = substr($s, $count + 21, $count2 - $count - 21);
    	$title = iconv( "windows-1251", "utf-8", $title ); 
	
	//echo $title."\r\n";

	$bAdd = isset( $processedChannels[$title] ) ? 0 : 1;
	
	if ( $bAdd == 1 )
	{
		$processedChannels[$title] = 1;
		$epg_channels .= $title."\n";
		$epg_channels .= strlen( $epg_data )."\n";
	}

	$count5 = strpos($s, 'class="channeltitle">', $count2);

	$count7 = $count2;
	$count3 = $count2;
	while($count3 = strpos($s, 'class="pasttime">', $count3+1))
	{
		if ( $count5 !== false )
		{
			if ( $count3 > $count5 )
			{
				break;
			}
		}

		$count4 = strpos($s, '<', $count3+17);
		$prtime = substr($s, $count3 + 17, $count4 - $count3 - 17);
		
		if ( $bAdd == 1 )
		{
			$epg_data .= adjustHour( $prtime, $prHourAdd )."\n";
		}
//		echo $prtime."\r\n";

		$count6 = strpos($s, 'class="pastprname2">', $count4);
		$count7 = strpos($s, '<', $count6+20);

		$prname = substr($s, $count6 + 20, $count7 - $count6 - 20);
		$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
	        $prname = str_replace("\xa0", "", $prname);
	        $prname = str_replace("\n", " ", $prname);
	        $prname = str_replace("\r", " ", $prname);
		$prname = trim( $prname );

		$r=0;
		while ( ( $prname== "" ) && ( $r < 4 ) )
		{
			$count6 = strpos($s, '>', $count7+1);
			$count7 = strpos($s, '<', $count6+1);

			$prname = substr($s, $count6 + 1, $count7 - $count6 - 1);
			$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
	        	$prname = str_replace("\xa0", "", $prname);
			$prname = trim( $prname );

			$r++;
		}

	    	$prname = iconv( "windows-1251", "utf-8", $prname ); 
//		echo $prname."\r\n";
		if ( $bAdd == 1 )
		{
			$epg_data .= $prname."\n";
		}
	}

	$count3 = $count7;
	while($count3 = strpos($s, 'class="onair">', $count3+1))
	{
		if ( $count5 !== false )
		{
			if ( $count3 > $count5 )
			{
				break;
			}
		}

		$count4 = strpos($s, '<', $count3+14);
		$prtime = substr($s, $count3 + 14, $count4 - $count3 - 14);
		//echo "onair:".$prname;
		//echo "\r\n";
		if ( $bAdd == 1 )
		{
			$epg_data .= adjustHour( $prtime, $prHourAdd )."\n";
		}


		$count6 = strpos($s, 'class="prname2">', $count4);
		$count7 = strpos($s, '<', $count6+16);

		$prname = substr($s, $count6 + 16, $count7 - $count6 - 16);
		$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
        	$prname = str_replace("\xa0", "", $prname);
	        $prname = str_replace("\n", " ", $prname);
	        $prname = str_replace("\r", " ", $prname);
		$prname = trim( $prname );

		$r=0;
		while ( ( $prname== "" ) && ( $r < 4 ) )
		{
			$count6 = strpos($s, '>', $count7+1);
			$count7 = strpos($s, '<', $count6+1);

			$prname = substr($s, $count6 + 1, $count7 - $count6 - 1);
			$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
	        	$prname = str_replace("\xa0", "", $prname);
			$prname = trim( $prname );

			$r++;
		}

	    	$prname = iconv( "windows-1251", "utf-8", $prname ); 
		//echo $prname;
		//echo "\r\n";
		if ( $bAdd == 1 )
		{
			$epg_data .= $prname."\n";
		}
	}

	$count3 = $count7;
	while($count3 = strpos($s, 'class="time">', $count3+1))
	{
		if ( $count5 !== false )
		{
			if ( $count3 > $count5 )
			{
				break;
			}
		}

		$count4 = strpos($s, '<', $count3+13);
		$prtime = substr($s, $count3 + 13, $count4 - $count3 - 13);
		//echo $prtime;
		//echo "\r\n";
		if ( $bAdd == 1 )
		{
			$epg_data .= adjustHour( $prtime, $prHourAdd )."\n";
		}

		$count6 = strpos($s, 'class="prname2">', $count4);
		$count7 = strpos($s, '<', $count6+16);

		$prname = substr($s, $count6 + 16, $count7 - $count6 - 16);
		$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
        	$prname = str_replace("\xa0", "", $prname);
	        $prname = str_replace("\n", " ", $prname);
	        $prname = str_replace("\r", " ", $prname);
		$prname = trim( $prname );

		$r=0;
		while ( ( $prname== "" ) && ( $r < 4 ) )
		{
			$count6 = strpos($s, '>', $count7+1);
			$count7 = strpos($s, '<', $count6+1);

			$prname = substr($s, $count6 + 1, $count7 - $count6 - 1);
			$prname = html_entity_decode( $prname, ENT_QUOTES, 'windows-1251');
	        	$prname = str_replace("\xa0", "", $prname);
			$prname = trim( $prname );

			$r++;
		}


	    	$prname = iconv( "windows-1251", "utf-8", $prname ); 
		//echo $prname;
		//echo "\r\n";
		if ( $bAdd == 1 )
		{
			$epg_data .= $prname."\n";
		}
	}

	$count = $count5;

	if ( $bAdd == 1 )
	{	
		$epg_channels .= strlen( $epg_data )."\n";
	}
} //count
} //lstId

file_put_contents( "/tmp/epg_channels.txt", $epg_channels );
file_put_contents( "/tmp/epg_data.txt", $epg_data );

}

//==================================================================
//==================================================================
//only calls actual parser if cache is outdated or does not exist
function parseEPG()
{
	ob_start("pggcallback");
	require 'iptvlist_timezone.php';
	ob_end_flush();

	$ctime_arr = getdate( time() );
	$focusHour = $ctime_arr[ "hours"];

	$ctime_day = $ctime_arr["mday"];
	if ( $ctime_arr["hours"] < 5 ) 
	{
		$ctime_day--;
		$focusHour += 24;
	}

	if (!file_exists("/tmp/epg_channels.txt")) 
	{
		doParseEPG( $ctime_arr["year"],  $ctime_arr["mon"], $ctime_day ); 
	}
	else
	{
		//check if EPG still valid
		$ftime = filemtime( "/tmp/epg_channels.txt" );				
		$ftime_arr = getdate( $ftime );
		//echo "year:".$ftime_arr["year"]."\n";
		//echo "mon:".$ftime_arr["mon"]."\n";
		//echo "mday:".$ftime_arr["mday"]."\n";
		//echo "hours:".$ftime_arr["hours"]."\n";
		//echo "minutes:".$ftime_arr["minutes"]."\n";
		//echo "seconds:".$ftime_arr["seconds"]."\n";

		$ftime_day = $ftime_arr["mday"];  
		if ( $ftime_arr["hours"] < 5 ) 
		{
			$ftime_day--;
		}

		if ( ( $ftime_arr["year"] != $ctime_arr["year"] ) || ( $ftime_arr["mon"] != $ctime_arr["mon"] ) || ( $ftime_day != $ctime_day ) )
		{
			doParseEPG( $ctime_arr["year"],  $ctime_arr["mon"], $ctime_day ); 
			//echo "cache invalid";
		}
	}

}

?>
