<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

header("Content-Type:text/plain");
mb_internal_encoding("UTF-8"); 

ob_start("eecallback");
require_once 'iptvlist_timezone.php';
ob_end_flush();

ob_start("eecallback");
require_once 'iptvlist_common.php';
ob_end_flush();

ob_start("eecallback");
require_once 'parse_epg.php';
ob_end_flush();

//==================================================================
//==================================================================
function eecallback($buffer)
{
  return "";
}

//==================================================================
//==================================================================


if( isset( $_GET["listName"] ) ) 
{
	if( isset( $_GET["title"] ) ) 
	{
		if( isset( $_GET["link"] ) ) 
		{
			$listName = $_GET["listName"];  //list name, f.e. Favorites.m3u
			$title = $_GET["title"];        //program title as displayed in in interface, f.e. "Discovery Channel"
			$link = $_GET["link"];         	//unmodified progam link(without proxy), f.e. udp://@230.33.0.16:1234
			              
			$prMatch = loadEPGMatch();

			$ctime_arr = getdate( time() );
//			$ctime_arr[ "hours"] = 12;

			$focusHour = $ctime_arr[ "hours"];

			if ( $focusHour < 5 )
			{
				$focusHour += 24;
			}

			parseEPG();

			$epg_channels = file_get_contents("/tmp/epg_channels.txt");
			$epg_data = file_get_contents("/tmp/epg_data.txt");

			//script has to return lines separated with \n:
			//1) Number of items in list
			//2) Item index to set focus on ( selected by current time to point to current program )
			//3) channel name as requested
			//4) channel name returned ( after matching )
			//5) current date
			//6) items itself

			//if there is no matching channel found, script has to return channels list and focusindex=-1
			
			$channels = explode( "\n", $epg_channels );

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

			$focusIndex = 0;

			$foundName = "";

			if ( isset( $_GET["match"] ) == false ) 
			{
				for ( $i = 0; ( $i + 2 ) < count( $channels ); $i += 3 )
				{
					$title2 = str_replace( " ", "", $channels[$i] );
					$title2 = mb_convert_case( $title2, MB_CASE_LOWER, "UTF-8" );

					if ( $title1 == $title2 )
					{
						$foundName = $channels[$i];
						$found = 1;

						$startData = intval( $channels[ $i + 1 ] );
						$endData = intval( $channels[ $i + 2 ] );
	
						$data = substr( $epg_data, $startData, $endData - $startData );
						
						$data1 = explode( "\n", $data );

	
						$passed12 = 0;
	
						//echo "focusHour:".$focusHour."\n";
	
						for ( $j = 0; ( $j + 1 ) < count( $data1 ); $j += 2 )
						{
							$prtime = $data1[ $j ];
							$prtime1 = explode( ":", $prtime );
							$prHour = intval( $prtime1[ 0 ] );
							$prMinute = intval( $prtime1[ 1 ] );
	
//echo $prHour.":".$prMinute."\n";
							if ( $prHour > 12 )
							{
								$passed12 = 1;
							}
	
							if ( ( $prHour < 12 ) && ( $passed12 != 0 ) )
							{
								$prHour += 24;
							}
	
							//echo "prHour:".$prHour."\n";
	
							if ( 
								( $prHour < $focusHour ) ||
								(
									( $prHour == $focusHour ) &&
									( $prMinute <= $ctime_arr["minutes"] ) 
								)
							)
							{
								$focusIndex = $count;
							}

							$prname = $data1[ $j + 1 ];
							$prname = str_replace(","," ", $prname );
							$res .= $prtime."    ".$prname."\n";
							$count++; 
						}
			
						break;
					}
				}
			}

			//do not return more then 100 entries. rss can't display it.
			if ( ( $found == 1 ) && ( $count > 100 ) )
			{
				$resa = explode( "\n", $res );

				$deleteAfterIndex = $focusIndex + 50; 					
				if ( $deleteAfterIndex > ( count( $resa ) - 1 ) )
				{
					$deleteAfterIndex = count( $resa ) - 1;
				}		

				$deleteBeforeIndex = $focusIndex - 50; 					
				if ( $deleteBeforeIndex < 0 )
				{
					$deleteBeforeIndex = 0;
				}		

				$focusIndex -= $deleteBeforeIndex;
				$count = 0;

				unset( $res );
				$res = "";

//echo "deleteBeforeIndex:".$deleteBeforeIndex."\n";

				for ( $i = $deleteBeforeIndex; $i <= $deleteAfterIndex; $i++ )
				{
					$res .= $resa[ $i ]."\n";
					$count++;
				}


			}

			if ( $found == 0 )
			{
				unset( $res );

				$focusIndex = -1; 

				for ( $i = 0; ( $i + 2 ) < count( $channels ); $i += 3 )
				{
					$title2 = str_replace( ",", " ", $channels[$i] );
					$res[ $count ] .= $title2;
					$count++;
				}

				uasort($res, 'utf_8_sort::cmp'); 

				$firstChar = mb_substr( $title, 0, 1 );

				$i = 0;
				foreach ( $res as $title2 )
				{
					$firstChar2 = mb_substr( $title2, 0, 1 );
					if ( $firstChar2 == $firstChar )
					{
						$focusIndex = -1 - $i;	
						break;
					}

					if ( $firstChar2 < $firstChar )
					{
						$focusIndex = -1 - $i;	
					}

					$i++;
				}

				$res = implode( "\n", $res );
				
			}

		
			$res = $count."\n".$focusIndex."\n".$title."\n".$foundName."\n".date("D M j H:i")."\n".$res;
				
			echo $res;
		}
		else
		{
			echo "link not specified";
        	}
	}
	else
	{
		echo "title not specified";
	}
}
else
{
	echo "listName not specified";
}
?>