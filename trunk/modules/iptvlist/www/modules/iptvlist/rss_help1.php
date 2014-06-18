<?php

function rss_help1_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<onEnter>
setParentFade(128);
setFocusItemIndex(0);
pressOK = "false";
cancelIdle();
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
	viewAreaYPC = 10
	viewAreaWidthPC = 49.61 
	viewAreaHeightPC = 74
    sideLeftWidthPC = 0

	itemXPC = 5
    itemYPC = 10 
	itemWidthPC = 90
	itemHeightPC = 7.3
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
			/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_HDD_FORMAT_WARNING1.png
	</image>
</backgroundDisplay>

<text offsetXPC=2 offsetYPC=80 widthPC=96 heightPC=11.24 align=center fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 >
	<script>
                "<?= getMsg( 'iptvPressOk' ) ?>";
	</script>
</text>


<!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->
<itemDisplay>
<image offsetXPC=2 offsetYPC=11  widthPC=6.6 heightPC=89 useBackgroundSurface=yes>
        <script>
        	getItemInfo("icon");
        </script>
</image>	
<image offsetXPC=12 offsetYPC=11  widthPC=6.6 heightPC=89 useBackgroundSurface=yes>
        <script>
        	getItemInfo("icon2");
        </script>
</image>	
<text align=left fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255
		offsetXPC=22 offsetYPC=0 widthPC=100 heightPC=100>
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
    }
    else if ("left" == userInput || "right" == userInput)
    {
    	handle = "true";
    }
    else if ("enter" == userInput || "display" == userInput)
    {
		postMessage("return");
    	handle = "true";
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
    <title><?= getMsg( 'iptvShowActionsMenu' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/pd.png</icon>
	<icon2></icon2>
</item>

<item>
    <title><?= getMsg( 'iptvSelPrevNextIt' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/up.png</icon>
	<icon2>/usr/local/etc/mos/www/modules/iptvlist/images/dn.png</icon2>
</item>

<item>
    <title><?= getMsg( 'iptvShowPrevNextPg' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/left.png</icon>
	<icon2>/usr/local/etc/mos/www/modules/iptvlist/images/right.png</icon2>
</item>

<item>
	<title><?= getMsg( 'iptvPlay' ) ?> / <?= getMsg( 'iptvPlaySt' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/ok.png</icon>
	<icon2>/usr/local/etc/mos/www/modules/iptvlist/images/play.png</icon2>
</item>

<item>
    <title><?= getMsg( 'iptvMoveUpDn' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/fr.png</icon>
	<icon2>/usr/local/etc/mos/www/modules/iptvlist/images/ff.png</icon2>
	<icon2></icon2>
</item>

<item>
	<title><?= getMsg( 'iptvMoveUpPg' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/pu.png</icon>
</item>

<item>
	<title><?= getMsg( 'iptvProgramGuide' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/display.png</icon>
</item>

<item>
	<title><?= getMsg( 'iptvSelectPlaylist' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/notes.png</icon>
</item>

<item>
	<title><?= getMsg( 'iptvAddToFavorites' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/stop.png</icon>
</item>

<item>
	<title><?= getMsg( 'iptvDelSel' ) ?></title>
	<icon>/usr/local/etc/mos/www/modules/iptvlist/images/option.png</icon>
</item>


</channel>
</rss>
<?php

}

?>