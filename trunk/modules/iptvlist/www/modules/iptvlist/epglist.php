<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

/*
Build EPG data for all programs in m3u list
Script has to create file /tmp/epg_list.txt 
Line 0: m3u filename
Line 1: validHour
Line 2: validMinute
 - hour and minute when list has to be rebuilt.
  In practice it is time when soonest program on any channel from list will start.
  
Line3: m3u filename

Rest of the file should contain programs list - current program and 6 next programs, if any.
So total number of lines in file should be 3 + channelsCount * 7

*/

//==================================================================
//==================================================================
function elcallback($buffer)
{
  return "";
}

//==================================================================
//==================================================================
function loadm3u( $fileName )
{
	$m3uFileName = $fileName;
	if (file_exists($m3uFileName) == false ) 
	{
		$m3uFileName = "Favorites.m3u";
	} 

	$m3uFile = file_get_contents( $m3uFileName );
    $m3uFile = str_replace("\n", "\r", $m3uFile);  
    $m3uFile = str_replace("\r\r", "\r", $m3uFile);  
	$m3uFile = explode( "\r", $m3uFile );

	$count = 0;

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
			$res[ $count++ ] = $r;
		}
	}
	return $res;
}

//==================================================================
//==================================================================

function createEPGList( $listName )
{

		//todo: if EPG list is not expired yet - do not reparse

ob_start("elcallback");
require_once 'iptvlist_timezone.php';
ob_end_flush();

ob_start("elcallback");
require_once 'iptvlist_common.php';
ob_end_flush();

ob_start("elcallback");
require_once 'parse_epg.php';
ob_end_flush();

		if( isset( $_GET["msgEPGNotAvail"] ) )
		{
			$msgEPGNotAvail = $_GET["msgEPGNotAvail"];
		}
		else
		{
			$msgEPGNotAvail = "EPG not available";
		}

 
		$programNames = loadm3u( $listName );
		$res = "";

		//$programNames is array which contains list of programs from m3u

/*
		for( $i = 0; $i < count($programNames); $i++) 
		{ 
			$res .= $programNames[ $i ]."\n"."\r";
		}
*/
         
		$prMatch = loadEPGMatch();

		$ctime_arr = getdate( time() );

		$focusHour = $ctime_arr[ "hours"];

		if ( $focusHour < 5 )
		{
			$focusHour += 24;
		}

		parseEPG();

		$epg_channels = file_get_contents("/tmp/epg_channels.txt");
		$epg_data = file_get_contents("/tmp/epg_data.txt");

		$channels = explode( "\n", $epg_channels );
		              
		$expireHour = 100;
		$expireMinute = 0;


		//------------------------------------------------------

		for ( $i = 0; ( $i + 2 ) < count( $channels ); $i += 3 )
		{
			$title2 = str_replace( " ", "", $channels[$i] );
			$title2 = mb_convert_case( $title2, MB_CASE_LOWER, "UTF-8" );

			$programNames1[ $title2 ] = $i;
		}


		//------------------------------------------------------
		$prCount = count( $programNames );

		for( $prIndex = 0; $prIndex < $prCount; $prIndex++ ) 
		{ 
			$title = $programNames[ $prIndex ];

			if ( array_key_exists( $title, $prMatch ) )
			{
				$title1 = str_replace( " ", "", $prMatch[ $title ] );  
			} 
			else
			{
				$title1 = str_replace( " ", "", $title );  
			}

			$title1 = mb_convert_case( $title1, MB_CASE_LOWER, "UTF-8" );

			$found = 0;

			$count = 0;

			$foundName = "";

			$i = $programNames1[ $title1 ];
		
//			for ( $i = 0; ( $i + 2 ) < count( $channels ); $i += 3 )
			if ( isset( $i ) == true )
			{
				 
//					$title2 = str_replace( " ", "", $channels[$i] );
//					$title2 = mb_convert_case( $title2, MB_CASE_LOWER, "UTF-8" );

//					if ( $title1 == $title2 )
					{
						$foundName = $channels[$i];
						$found = 1;

						$startData = intval( $channels[ $i + 1 ] );
						$endData = intval( $channels[ $i + 2 ] );
	
						$data = substr( $epg_data, $startData, $endData - $startData );
						
						$data1 = explode( "\n", $data );
	
						$passed12 = 0;
	
						//echo "focusHour:".$focusHour."\n";\\

						$focusPrName = "";

						for ( $j = 0; ( $j + 1 ) < count( $data1 ); $j += 2 )
						{
							$prtime = $data1[ $j ];
							$prtime1 = explode( ":", $prtime );
							$prHour = intval( $prtime1[ 0 ] );
							$prMinute = intval( $prtime1[ 1 ] );
	
							if ( $prHour > 12 )
							{
								$passed12 = 1;
							}
	
							if ( ( $prHour < 12 ) && ( $passed12 != 0 ) )
							{
								$prHour += 24;
							}
	
							//echo "prHour:".$prHour."\n";

							$prname = $data1[ $j + 1 ];
							$prname = str_replace(","," ", $prname );

							if ( 
								( $prHour < $focusHour ) ||
								(
									( $prHour == $focusHour ) &&
									( $prMinute <= $ctime_arr["minutes"] ) 
								)
							)
							{
								$focusPrName = $prtime."   ".$prname;
								$count = 1;
							}
							else
							{
								if (
										( $prHour < $expireHour ) ||
										(
											( $prHour == $expireHour ) &&
											( $prMinute <= $expireMinute ) 
										)
								)
								{
									$expireHour = $prHour;
									$expireMinute = $prMinute;
								}

								//$focusPrName == " means no programs before current time is found
								if ( ( $count == 1 ) && ( $focusPrName != "" ) )
								{
									$res .= $focusPrName."\n";
								}

								$res .= $prtime."   ".$prname."\n";
								$count++; 
							}

							if ( $count == 7 )
							{
								break;
							}
						}

					
						if ( ( $count == 1 ) && ( $focusPrName != "" ) )
						{
							$res .= $focusPrName."\n";
						}

				
						//break;
					}
			}

			if ( $found == 0 )
			{
				$res .= $msgEPGNotAvail."\n";
				$count++;
			}

			while ( $count < 7 )
			{
				$res .= "\n";
				$count++;
			}		

			//$res .= "-----------------------\n";
			
		}

		if ( $expireHour >= 24 )
		{
			$expireHour -= 24;
		}

		if ( $programNames >= 100 )
		{
			//for long lists, make sure expire time is not too close to current time
			//make at least 5 min pause to prevent too frequent updates
			$dist = $expireHour * 60 + $expireMinute - $ctime_arr[ "hours"] * 60 - $ctime_arr["minutes"];

			if ( $dist < 5 )
			{
				$expireMinute += 5;
				if ( $expireMinute >= 60 )
				{
					$expireMinute -= 60;
				        $expireHour++;
				}	
			}
		}

		$res = $listName."\n".$expireHour."\n".$expireMinute."\n".$res;

		file_put_contents( "/tmp/epg_list.txt", $res );

		return array( $expireHour, $expireMinute );		
}
?>
