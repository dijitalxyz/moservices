<?php

function rss_alreadyfavorites_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<onEnter>
setParentFade(128);
setFocusItemIndex(0);
pressOK = "false";
redrawDisplay();
</onEnter>

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
	viewAreaYPC = 25.83
	viewAreaWidthPC = 49.61 
	viewAreaHeightPC = 38.19
    sideLeftWidthPC = 0

	itemXPC = 50
    itemYPC = 90 
	itemWidthPC = 80
	itemHeightPC = 18
    itemImageXPC = 0
    itemImageYPC=0
	itemImageWidthPC = 0
    itemImageHeightPC = 0
    itemPerPage=1

	
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
			/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_CONFIRM_WINDOW_BG.png
	</image>
</backgroundDisplay>

<text offsetXPC=2 offsetYPC=16 widthPC=96 heightPC=11.24 align=center fontSize=16 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                "<?= getMsg( 'iptvAddToFavorites' ) ?>";
	</script>
</text>


<text offsetXPC=2 offsetYPC=38 widthPC=96  heightPC=12.1 align=center fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
        "<?= getMsg( 'iptvCurIsFavorites' ) ?>";
	</script>
</text>

<text offsetXPC=2 offsetYPC=49 widthPC=96  heightPC=12.1 align=center fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
        "<?= getMsg( 'iptvSelAnotherPlaylist' ) ?>";
	</script>
</text>


<!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->
<itemDisplay>
<image useBackgroundSurface="yes">
		<offsetXPC>
			<script>
				queryIndex = getQueryItemIndex();

				if(queryIndex == 0)
					offsetX = -20;
				else if(queryIndex == 1)
					offsetX = 20;
				offsetX;
			</script>
		</offsetXPC>
		<offsetYPC>
			<script>
				queryIndex = getQueryItemIndex();

				if(queryIndex == 0)
					offsetY = -50;
				else if(queryIndex == 1)
					offsetY = -148;
				offsetY;
			</script>
		</offsetYPC>		
		<widthPC>
			<script>
				width = 38;
				width;
			</script>
		</widthPC>			
		<heightPC>
			<script>
				height = 70;
				height;
			</script>
		</heightPC>				
        <script>
                queryIndex = getQueryItemIndex();
                focusIndex = getFocusItemIndex();
                if (queryIndex == focusIndex) 
				{
                    	 image = "/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_DIALOG_MENU_BTN_FOCUS.png";
                }
                else
		{
                    	 image = "/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_DIALOG_MENU_BTN_UNFOCUS.png";
                }
                image;
        </script>
</image>	
<text align=center fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		<offsetXPC>
			<script>
				queryIndex = getQueryItemIndex();

				if(queryIndex == 0)
					offsetX = -20;
				else if(queryIndex == 1)
					offsetX = 20;
				offsetX;
			</script>
		</offsetXPC>
		<offsetYPC>
			<script>
				queryIndex = getQueryItemIndex();

				if(queryIndex == 0)
					offsetY = -46;
				else if(queryIndex == 1)
					offsetY = -148;
				offsetY;
			</script>
		</offsetYPC>		
		<widthPC>
			<script>
				width = 38;
				width;
			</script>
		</widthPC>			
		<heightPC>
			<script>
				height = 70;
				height;
			</script>
		</heightPC>				

        <script>
        	getItemInfo("title");
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
                "<?= getMsg( 'iptvCurIsFavorites' ) ?>";
		</script>
	</title>
	<link><?= getMosUrl().'?page=' ?>rss_alreadyfavorites</link>


<item>
	<title>$[OK]</title>
	<onClick>
		<script>
		            pressOK = "true";
		            setReturnString("true");
		            postMessage("return");
		            null; 
		 </script>
	</onClick>	
</item>

</channel>
</rss>
<?php

}

?>