<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

header("Content-Type:application/vnd.apple.mpegurl");

mb_internal_encoding("UTF-8"); 



if( isset( $_GET["link"]) ) 
{
	$link = $_GET["link"];

	loop:

	$count = 5;

	while( true )
	{
		$contents = file_get_contents( $link );

		if ( $contents != FALSE )
		{

			$tpos = strpos( $contents, "#EXT-X-DISCONTINUITY" );	
			if ( $tpos === false )
			{
				break;
			}
		}

		$count--;
		if ( $count == 0 )
		{
			break;
		}
	}

	if ( $contents != FALSE )
	{

		$tpos = strpos( $contents, "#EXT-X-MEDIA-SEQUENCE:" );	

		//---------------------------------------

        $contents2 = str_replace("\n", "\r", $contents);  

		$i1 = mb_strlen( $contents2 ) - 1;
		
		while ( 
				( $i1 > 0 ) && 
				( 
					( mb_substr( $contents2, $i1, 1 ) === "\r" ) || 
					( mb_substr( $contents2, $i1, 1 ) === " " )
				)
			)
		{
			$i1--;
		}

		$i2 = $i1 - 1;

		while ( 
				( $i2 > 0 ) && 
				( mb_substr( $contents2, $i2, 1 ) != "\r" ) && 
				( mb_substr( $contents2, $i2, 1 ) != " " )
			)
		{
			$i2--;
		}

		$lastFileName = mb_substr( $contents2, $i2+1, $i1-$i2+1 );

       	$lastFileName = str_replace("\r", "", $lastFileName);  

		$i1 = mb_strlen( $lastFileName );

//echo $lastFileName."\n";

		//---------------------------------------

		if ( $tpos === false )
		{
			//maybe list is reference to other m3u list?

			if ( strtoupper( mb_substr( $lastFileName, $i1 - 5, 5 ) ) == ".M3U8" )
			{
				//list has reference to other list...
				$ph = $link;

				$phi =  strrpos( $ph, "/" );
				$ph =  mb_substr( $ph, 0, $phi + 1 );
		
				$link = $ph.$lastFileName;
				goto loop;
			}
			else
			{
				echo $contents;
			}
		}
		else
		{

			while ( 
					( $i1 > 0 ) && 
					( is_numeric( mb_substr( $lastFileName, $i1, 1 ) ) == false ) 
				)
			{
				$i1--;
			}


			$count = 1;
			$contentsLen = mb_strlen( $contents2 );

			while ( ( $count < 1000 ) && ( $contentsLen < 30000 ) )

			{
			 	$contents2 .= "\r#EXTINF:c,\r";
				$contentsLen += 12;				
				
				$i2 = $i1;

				$add = 1;

				while ( 
						( $i2 > 0 ) &&
						is_numeric( mb_substr( $lastFileName, $i2, 1 ) )
					)
				{
					$c = mb_substr( $lastFileName, $i2, 1 );

//echo "\n----\nc=".$c."\n----\n";

					$c1 = intval( $c );

					$c1 += $add;

//echo "\n----\nc1=".$c1."\n----\n";

					if ( $c1 >= 10 )
					{
						$add = 1;
						$c1=0;
					}
					else
					{
						$add = 0;
					}

//echo "\n----\nadd=".$add."\n----\n";

					$lastFileName = substr_replace( $lastFileName, $c1, $i2, 1 );

//echo "\n----\nf=".$lastFileName."\n----\n";


					$i2--;
					
				}

				if ( $add > 0 )
				{
					$lastFileName = substr( $lastFileName, 0, $i2 + 1 ).$add.substr( $lastFileName, $i2 + 1, 1000 );
					$i1++;
				}

				$contents2.=$lastFileName;

				$contentsLen += mb_strlen( $lastFileName );

				$count++;
				
			}
			
			$ph = $link;
//"http://50.7.129.202/stream/TEST/";

			$phi =  strrpos( $ph, "/" );
			$ph =  mb_substr( $ph, 0, $phi + 1 );

			$b = false;

			$contents3 = "";

	        $contents2 = str_replace("\r\r", "\r", $contents2);  

//echo $contents2;

			$contents2 = explode( "\r", $contents2 );
			
			foreach( $contents2 as $key => $line ) 
			{
				if( strtoupper(substr($line, 0, 15)) == "#EXT-X-VERSION:") 
				{
					//skip line
				}
				else if( strtoupper(substr($line, 0, 22)) == "#EXT-X-TARGETDURATION:") 
				{
					//skip line
				}
				else if( strtoupper(substr($line, 0, 22)) == "#EXT-X-MEDIA-SEQUENCE:") 
				{
					//skip line
				}
				else if ( $b == true )
				{
					$b = false;
					$contents3 .= $ph.$line."\n";
				}
				else
				{
				
					$contents3 .= $line."\n";
					if( strtoupper(substr($line, 0, 8)) == "#EXTINF:") 
					{
//						$contents3 .= $line."test\n";
						$b = true;
					}
					else
					{
//						$contents3 .= $line."\n";
					}
				}
			}


			echo $contents3;
		}
	}
	else
	{	
		echo "Unable to download link";
	}

}
else
{
		echo "link not specified"; 
}

?>
