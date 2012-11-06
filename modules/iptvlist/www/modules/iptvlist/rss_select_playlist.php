<?php

function rss_select_playlist_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<ReloadList>
	path = "/tmp/iptv_lists.txt";
	favLinkArray = readStringFromFile( path );
	favLinkCount = 0 + getStringArrayAt( favLinkArray, 0 );
	currentIndex = 0 + getStringArrayAt( favLinkArray, 1 );
   	redrawDisplay();
</ReloadList>

<BuildFavList>
	getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/loadplaylists.php" );
    executeScript("ReloadList");
</BuildFavList>

<onEnter>
setParentFade(128);
executeScript("BuildFavList");
setFocusItemIndex(currentIndex);
pressOK = "false";
cancelIdle();
redrawDisplay();
</onEnter>


<item_template>
    <displayTitle>
        <script>
            linkName = getStringArrayAt( favLinkArray, 2 + getQueryItemIndex() * 2 + 1 );
            linkName;
        </script>
    </displayTitle>
    <onClick>
	       	focusIndex = 0 + getFocusItemIndex();
            if (favLinkCount &gt; 0)
            {
				path = "/tmp/iptv_lastlist.dat";

				listName = getStringArrayAt(favLinkArray, 2 + focusIndex * 2 );		

				writeStringToFile(path,listName);

            	pressOK = "true";
            	setReturnString("true");
            	postMessage("return");
            	null; 
            }
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

	viewAreaXPC = 25.16 
	viewAreaYPC = 13.83
	viewAreaWidthPC = 49.61 
	viewAreaHeightPC = 70
    sideLeftWidthPC = 0

	itemXPC = 5
    itemYPC = 19 
	itemWidthPC = 90
	itemHeightPC = 9
    itemImageXPC = 0
    itemImageYPC=0
	itemImageWidthPC = 0
    itemImageHeightPC = 0
    itemPerPage=7

    forceFocusOnItem=yes

	
	imageFocus = ""
	imageUnFocus=""
		
	showHeader="no"
	showDefaultInfo="no"
	slidingItemText="no"

	rollItems="no"
	forceRedrawItems="yes"	
>

<backgroundDisplay>
	<image offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/usr/local/etc/mos/www/modules/iptvlist/images/select_playlist_bg.png
	</image>
</backgroundDisplay>

<text offsetXPC=0 offsetYPC=1 widthPC=100 heightPC=11.24 align=center fontSize=18 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                hint = "<?= getMsg( 'iptvSelectPlaylist' ) ?>";
	</script>
</text>

<text offsetXPC=28 offsetYPC=89 widthPC=96 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                hint = "<?= getMsg( 'iptvSelect' ) ?>";
	</script>
</text>

<text offsetXPC=61 offsetYPC=89 widthPC=96 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                hint = "<?= getMsg( 'iptvCancel' ) ?>";
	</script>
</text>


<!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->
<itemDisplay>
    <image offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100 useBackgroundSurface=yes>
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

	<text align=center fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255
		offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
        <script>
               itemT = getItemInfo("displayTitle");
               itemT;
        </script>
	</text>
</itemDisplay>

<onUserInput>
	handle = "false";
	userInput = currentUserInput();
    if ("return" == userInput)
	{
    	if(pressOK == "false") 
		{
    		setReturnString("false");
    	}
    }
    else if ("menu" == userInput)
	{
    	setReturnString("false");
        postMessage("return");
		handle = "true";
    }
    else if ("left" == userInput || "right" == userInput)
    {
    	handle = "true";
    }
	handle;
</onUserInput>

</mediaDisplay>


<channel>
	<title>
		<script>
                "Select playlist";
		</script>
	</title>
	<link><?= getMosUrl().'?page=' ?>rss_select_playlist</link>


<itemSize>
<script>
    favLinkCount;
</script>
</itemSize>

</channel>


</rss>
<?php

}

?>
