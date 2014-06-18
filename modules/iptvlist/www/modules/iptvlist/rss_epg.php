<?php

require_once 'detectfw.php';

function rss_epg_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<script>
	path = "/tmp/iptv_url.dat";
	tmp = readStringFromFile(path);

	iptvTitle1 = getStringArrayAt( tmp, 0 );
	iptvTitle = getStringArrayAt( tmp, 1 );
	content_original = getStringArrayAt( tmp, 2 );
	content = getStringArrayAt( tmp, 3 );
	listName = getStringArrayAt( tmp, 4 );

	listName = listName + ".m3u";

	items = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/epg.php?listName=" + urlEncode( listName ) + "&amp;title=" + urlEncode( iptvTitle1 ) + "&amp;link=" + urlEncode( content_original ) );

	itemsBaseIndex = 5;

        setReturnString("false");

</script>

<onEnter>
gindex = 0 + getStringArrayAt( items, 1 );
if ( gindex &gt;= 0 )
{
	setFocusItemIndex( gindex ); 
	selectMatch = "false";
}
else
{
	setFocusItemIndex( -1 - gindex ); 
	selectMatch = "true";
}

pressOK = "false";
cancelIdle();
redrawDisplay();
</onEnter>

<item_template>
    <displayTitle>
        <script>
            s = getStringArrayAt( items, ( itemsBaseIndex + getQueryItemIndex() ) );
            s;
        </script>
    </displayTitle>
    <onClick>
	null;
    </onClick>
</item_template>

<!-- mediaDisplay of parent will be merged into the mediaDisplay here, include all child elements, ex: onEnter, text, image, etc. -->
<mediaDisplay
	name=onePartView

	sideColorRight=-1:-1:-1
	sideColorLeft=-1:-1:-1
	sideColorTop=-1:-1:-1
	sideColorBottom=-1:-1:-1
	backgroundColor=-1:-1:-1
	focusBorderColor=-1:-1:-1
	unFocusBorderColor=-1:-1:-1
	itemBackgroundColor=-1:-1:-1

	viewAreaXPC = 10
	viewAreaYPC = 10
	viewAreaWidthPC = 80 
	viewAreaHeightPC = 80
    sideLeftWidthPC = 0

	itemXPC = 3
    itemYPC = 14 
	itemWidthPC = 94
	itemHeightPC = 5.7
    itemImageXPC = 0
    itemImageYPC=0
	itemImageWidthPC = 0
    itemImageHeightPC = 0
    itemPerPage=13

	
	imageFocus = ""
	imageUnFocus=""
		
	showHeader="no"
	showDefaultInfo="no"
	slidingItemText="no"

	rollItems="no"
	forceRedrawItems="yes"	

>

    <itemDisplay>
        <image offsetXPC=0.5 offsetYPC=0 widthPC=99 heightPC=100 useBackgroundSurface=yes>
            <script>
                status = getDrawingItemState();
                if (status == "focus")
                {
                	"/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_FAVORITS_FOC.png";
                }
                else
                	"";
            </script>
        </image>
        <text offsetXPC=<?= detectFirmware( '0.5', '1.0' ) ?> offsetYPC=4 widthPC=99 heightPC=92 fontSize=12 backgroundColor=-1:-1:-1 tailDots=no> 
        	<foregroundColor>
	            <script>
	            	color = "250:250:250";
	                color;
	            </script>
        	</foregroundColor>
            <script>
                itemT = getItemInfo("displayTitle");
                itemT;
            </script>
            <rolling>
    	        <script>
        	    if (getDrawingItemState() == "focus")
        	    {
        	  	    "yes";
        	    }
        	    else
        	    {
        	  	    "no";
        	    }
        	    </script>
        	</rolling>
        </text>
    </itemDisplay>

<backgroundDisplay>
	<image offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/usr/local/etc/mos/www/modules/iptvlist/images/epg.png
	</image>
</backgroundDisplay>

<text offsetXPC=<?= detectFirmware( '8', '10.5' ) ?> offsetYPC=<?= detectFirmware( '88.5', '88.9' ) ?> widthPC=96 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	"<?= getMsg( 'iptvEPGReturn' ) ?>";
		}
		else
		{
                	"<?= getMsg( 'iptvEPGAssign' ) ?>";
		}
	</script>
</text>

<text offsetXPC=<?= detectFirmware( '71', '73.5' ) ?> offsetYPC=<?= detectFirmware( '88.5', '88.9' ) ?> widthPC=96 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	"";
		}
		else
		{
                	"<?= getMsg( 'iptvEPGCancel' ) ?>";
		}
	</script>
</text>

<text offsetXPC=2 offsetYPC=0.8 widthPC=96 heightPC=11.24 align=center fontSize=18 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	"";
		}
		else
		{
                	"<?= getMsg( 'iptvEPGSelect' ) ?>";
		}
	</script>
</text>

<text offsetXPC=4 offsetYPC=1.6 widthPC=96 heightPC=11.24 align=left fontSize=18 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	getStringArrayAt( items, 3 );
		}
		else
		{
                	"";
		}
	</script>
</text>

<text offsetXPC=76 offsetYPC=1.9 widthPC=20 heightPC=11.24 align=right fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	getStringArrayAt( items, 4 );
		}
		else
		{
                	"";
		}
	</script>
</text>

<image offsetXPC=68.8 offsetYPC=91.5 heightPC=6 useBackgroundSurface=yes redraw=no>
		<widthPC>
			<script>
				if ( selectMatch == "false" )
				{
					0;
				}
				else
				{
					3.5;
				}
			</script>
		</widthPC>
		<script>
			if ( selectMatch == "false" )
			{
				"";
			}
			else
			{
				"/usr/local/etc/mos/www/modules/iptvlist/images/back.png";
			}
		</script>
</image>

<image offsetXPC=28.8 offsetYPC=91.5 widthPC=3.5 heightPC=6 useBackgroundSurface=yes redraw=no>
		<widthPC>
			<script>
				if ( selectMatch == "false" )
				{
					3.5;
				}
				else
				{
					0;
				}
			</script>
		</widthPC>
		<script>
			if ( selectMatch == "false" )
			{
				"/usr/local/etc/mos/www/modules/iptvlist/images/stop.png";
			}
			else
			{
				"";
			}
		</script>
</image>

<text offsetXPC=<?= detectFirmware( '30', '33.5' ) ?> offsetYPC=<?= detectFirmware( '88.5', '88.9' ) ?> widthPC=96 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
		if ( selectMatch == "false" )
		{
                	"<?= getMsg( 'iptvEPGReassign' ) ?>";
		}
		else
		{
                	"";
		}
	</script>
</text>


<!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->

<onUserInput>
	handle = "false";
	userInput = currentUserInput();
	if ("return" == userInput)
	{
    		handle = "false";		
  	}
	if ("video_stop" == userInput)
	{
		items = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/epg.php?match=1&amp;listName=" + urlEncode( listName ) + "&amp;title=" + urlEncode( iptvTitle1 ) + "&amp;link=" + urlEncode( content_original ) );
		executeScript( "onEnter" );
    		handle = "true";		
  	}
    	else if ( "enter" == userInput )
    	{
		if ( selectMatch == "false" )
		{
			postMessage("return");
		}
		else
		{
			focusIndex = 0 + getFocusItemIndex();
        
			if ( focusIndex &gt;= 0  )
			{
				getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/saveepgmatch.php?name1=" + urlEncode( getStringArrayAt( items, 2 ) ) + "&amp;name2=" + urlEncode( getStringArrayAt( items, focusIndex + itemsBaseIndex ) ) );
				items = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/epg.php?listName=" + urlEncode( listName ) + "&amp;title=" + urlEncode( iptvTitle1 ) + "&amp;link=" + urlEncode( content_original ) );
				executeScript( "onEnter" );
		            	setReturnString("true");
			}
			else
			{
				postMessage("return");
			}
		}
    		handle = "true";
    	}
    	else if ( "display" == userInput )
    	{
		postMessage("return");
    		handle = "true";
    	}
    	else if ( "pagedown" == userInput )
    	{
		setFocusItemIndex( 0 + getStringArrayAt( items, 0 ) - 1 ); 
		redrawDisplay();
    		handle = "true";
    	}
    	else if ( "pageup" == userInput )
    	{
		setFocusItemIndex( 0 ); 
		redrawDisplay();
    		handle = "true";
    	}
    	else if ( ( "video_frwd" == userInput ) || ( "left" == userInput ) )
    	{
		focusIndex = 0 + getFocusItemIndex();
		if ( focusIndex &gt;= 13 )
		{
			focusIndex = focusIndex - 13;
		}
		else
		{
			focusIndex = focusIndex = 0;
		}
		setFocusItemIndex( focusIndex ); 
		redrawDisplay();
    		handle = "true";
    	}
    	else if ( ( "video_ffwd" == userInput ) || ( "right" == userInput ) )
    	{
		count = 0 + getStringArrayAt( items, 0 );
		focusIndex = 0 + getFocusItemIndex();
		if ( ( focusIndex + 13 ) &lt; ( count - 1 ) )
		{
			focusIndex = focusIndex + 13;
		}
		else
		{
			focusIndex = focusIndex = count -  1;
		}
		setFocusItemIndex( focusIndex ); 
		redrawDisplay();
    		handle = "true";
    	}
    	else if ( "video_play" == userInput ) 
    	{
    		handle = "true";
    	}
    	else if ( "video_stop" == userInput ) 
    	{
    		handle = "true";
    	}
    	else if ( "video_pause" == userInput ) 
    	{
    		handle = "true";
    	}
	handle;
</onUserInput>

</mediaDisplay>


<channel>
	<title>
		<script>
                "help2";
		</script>
	</title>
	<link>rss_file:///usr/local/etc/mos/www/modules/iptvlist/help2.rss</link>


<itemSize>
<script>
	itemsCount = 0 + getStringArrayAt( items, 0 ); 
    itemsCount;
</script>
</itemSize>



</channel>
</rss>
<?php

}

?>