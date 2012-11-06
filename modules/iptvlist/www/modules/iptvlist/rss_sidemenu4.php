<?php

function rss_sidemenu4_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<onEnter>
setParentFade(128);
setFocusItemIndex(0);
cancelIdle();
redrawDisplay();
setReturnString( "" );
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

	viewAreaXPC = 70 
	viewAreaYPC = 0
	viewAreaWidthPC = 30 
	viewAreaHeightPC = 100
    sideLeftWidthPC = 0

	itemXPC = 5
    itemYPC = 13 
	itemWidthPC = 95
	itemHeightPC = 5
    itemImageXPC = 0
    itemImageYPC=0
	itemImageWidthPC = 0
    itemImageHeightPC = 0
    itemPerPage=10

	
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
			/usr/local/etc/mos/www/modules/iptvlist/images/sidemenu.png
	</image>
</backgroundDisplay>

<text offsetXPC=5 offsetYPC=2 widthPC=100 heightPC=11.24 align=left fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                "<?= getMsg( 'iptvSelectAction' ) ?>";
	</script>
</text>


<!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->
<itemDisplay>
        <image offsetXPC=0 offsetYPC=0 widthPC=95 heightPC=100 useBackgroundSurface=yes>
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

<text align=left fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255
		offsetXPC=2 offsetYPC=0 widthPC=100 heightPC=100>
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
 handle = "false";
    	}
    	else if ("left" == userInput || "right" == userInput)
    	{
    		handle = "true";
    	}
    	else if ("enter" == userInput )
    	{
		setReturnString( getItemInfo("action") );
    		handle = "true";
		postMessage("return");
    	}
    	else if ("pagedown" == userInput)
    	{
    		handle = "true";
		postMessage("return");
    	}
	handle;
</onUserInput>

</mediaDisplay>


<channel>
	<title>
		<script>
                "Delete channel";
		</script>
	</title>
	<link><?= getMosUrl().'?page=' ?>rss_deleteconfirm</link>


<item>
	<title><?= getMsg( 'iptvPlay' ) ?></title>
	<action>enter</action>
</item>

<item>
	<title><?= getMsg( 'iptvPlaySt' ) ?></title>
	<action>video_play</action>
</item>

<item>
	<title><?= getMsg( 'iptvSelectPlaylist' ) ?>...</title>
	<action>menu</action>
</item>

<item>
	<title><?= getMsg( 'iptvAddToFavorites' ) ?></title>
	<action>video_stop</action>
</item>

<item>
	<title><?= getMsg( 'iptvDelSel' ) ?></title>
	<action>option_green</action>
</item>

<item>
	<title><?= getMsg( 'iptvRenVideo' ) ?></title>
	<action>zoom</action>
</item>

<item>
	<title><?= getMsg( 'iptvShowHelp' ) ?></title>
	<action>help</action>
</item>

<item>
	<title><?= getMsg( 'iptvBack' ) ?></title>
	<action></action>
</item>

</channel>
</rss>
<?php

}

?>
