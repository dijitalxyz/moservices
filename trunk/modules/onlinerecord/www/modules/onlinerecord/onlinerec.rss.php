<?php

include 'onlinerec.init.php';

//
// ------------------------------------
function rss_lealta_content()
{
	$scrWidth  = 1280;
	$scrHeight = 720;

	$menuWidth  = 280;
	$menuHeight = 47;

	$itemWidth  = 862;
	$itemHeight = 47;

	$backgroundColor            = '0:9:39'; 	//'45:47:56';
	$backgroundMenuColor        = '86:91:103';
	$backgroundBarColor         = '53:55:66';


	$parentFocusFontColor       = '0:0:0';
	$parentFocusBackgroundColor = '160:165:180';

	$focusFontColor             = '0:0:0';
	$unFocusFontColor           = '255:255:255';
	$focusBackgroundColor       = '255:255:255';

	$focusUrlFontColor          = '0:0:86';
	$unFocusUrlFontColor        = '180:180:255';
	$parentFocusUrlFontColor    = '0:0:86';

	$path = dirname( __FILE__ ) .'/';

	header( "Content-type: text/plain" );

	echo '<?xml version="1.0" ?>' .PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' .PHP_EOL;

?>
  <mediaDisplay name="threePartsView"
	viewAreaXPC="0"
	viewAreaYPC="0"
	viewAreaWidthPC="100"
	viewAreaHeightPC="100"

	sideLeftWidthPC="<?= round( 348.5 / $scrWidth * 100, 4) ?>"
	sideColorLeft="<?= $backgroundMenuColor ?>"
	sideRightWidthPC="0"
	sideColorRight="<?= $backgroundColor ?>"

	backgroundColor="<?= $backgroundColor ?>"

	itemXPC="<?= round( 358.5 / $scrWidth  * 100, 4) ?>"
	itemYPC="<?= round(  75.5 / $scrHeight * 100, 4) ?>"
	itemWidthPC="<?=   round( $itemWidth  / $scrWidth  * 100, 4) ?>"
	itemHeightPC="<?=  round( $itemHeight / $scrHeight * 100, 4) ?>"

	itemImageXPC="<?= round( 358.5 / $scrWidth  * 100, 4) ?>"
	itemImageYPC="<?= round(  75.5 / $scrHeight * 100, 4) ?>"
	itemImageHeightPC="0"
	itemImageWidthPC="0"

	itemGap="0"
	itemPerPage="12"

	itemBackgroundColor="<?= $backgroundColor ?>"

	menuXPC="<?= round( 60.5 / $scrWidth  * 100, 4) ?>"
	menuYPC="<?= round( 75.5 / $scrHeight * 100, 4) ?>"
	menuWidthPC="<?=   round( $menuWidth  / $scrWidth  * 100, 4) ?>"
	menuHeightPC="<?=  round( $menuHeight / $scrHeight * 100, 4) ?>"

	menuImageXPC="<?= round( 60.5 / $scrWidth  * 100, 4) ?>"
	menuImageYPC="<?= round( 75.5 / $scrHeight * 100, 4) ?>"
	menuImageHeightPC="0"
	menuImageWidthPC="0"

	menuBackgroundColor="<?= $backgroundMenuColor ?>"

	menuGap="0"
	menuPerPage="12"

	focusBorderColor="-1:-1:-1"
	unFocusBorderColor="-1:-1:-1"

	autoSelectMenu="no"
	autoSelectItem="no"
	selectMenuOnRight="no"
	forceFocusOnItem="no"
	forceFocusOnMenu="yes"

	drawItemText="no"
	drawMenuText="no"

	showHeader="no"
	showDefaultInfo="no"
	showInfoPage="yes"


	idleImageXPC="45.2344"
	idleImageYPC="41.5278"
	idleImageWidthPC="9.4531"
	idleImageHeightPC="16.9444"
   >
    <idleImage><?= getRssImages() ?>idle01.png</idleImage>
    <idleImage><?= getRssImages() ?>idle02.png</idleImage>
    <idleImage><?= getRssImages() ?>idle03.png</idleImage>
    <idleImage><?= getRssImages() ?>idle04.png</idleImage>
    <idleImage><?= getRssImages() ?>idle05.png</idleImage>
    <idleImage><?= getRssImages() ?>idle06.png</idleImage>
    <idleImage><?= getRssImages() ?>idle07.png</idleImage>
    <idleImage><?= getRssImages() ?>idle08.png</idleImage>

<!-- top bar -->
<?php
		$px = round(    0 / $scrWidth  * 100, 4);
		$py = round(    0 / $scrHeight * 100, 4);
		$pw = round( $scrWidth / $scrWidth  * 100, 4);
		$ph = round( 67.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     backgroundColor="<?= $backgroundBarColor ?>">
      <script>
	"";
      </script>
    </text>

<?php
		$px = round(  60.5 / $scrWidth  * 100, 4);
		$py = round(  22.5 / $scrHeight * 100, 4);
		$pw = round( $menuWidth / $scrWidth  * 100, 4);
		$ph = round(  37.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="left" fontSize="14" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $unFocusFontColor ?>">
      <script>
	"Online-Record.ru";
      </script>
    </text>
<?php
		$px = round( 384.5 / $scrWidth  * 100, 4);
		$py = round(  22.5 / $scrHeight * 100, 4);
		$pw = round( 800.5 / $scrWidth  * 100, 4);
		$ph = round(  37.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="left" fontSize="14" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $unFocusFontColor ?>">
      <script>
	topTitle;
      </script> 
    </text>

<!-- buttom bar -->
<?php
		$px = round(    0   / $scrWidth  * 100, 4);
		$py = round(  653.5 / $scrHeight * 100, 4);
		$pw = round( 1280   / $scrWidth  * 100, 4);
		$ph = round(   67.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     backgroundColor="<?= $backgroundBarColor ?>">
      <script>
	"";
      </script>
    </text>

<?php
		$wi = 26;	// image width
		$hi = 26;	// image height
		$wd = 4;	// delimiter
		$wt = 163;	// text width
		$ht = 24;	// text height
		$fs = 10;	// font size

		$yi = 661.5;
		$yt = 664.5;

		$pyi  = round(( $yi + 0.5 )/ $scrHeight * 100, 4);
		$pyt  = round(( $yt + 0.5 )/ $scrHeight * 100, 4);

		$pwi = round(( $wi + 0.5 )/ $scrWidth  * 100, 4);
		$phi = round(( $hi + 0.5 )/ $scrHeight * 100, 4);
		$pwt = round(( $wt + 0.5 )/ $scrWidth  * 100, 4);
		$pht = round(( $ht + 0.5 )/ $scrHeight * 100, 4);

		$pos = 60.5;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>" widthPC="<?= $pwi ?>">
      <?= $path .'images/hint_fr.png' ?> 
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	"<?= getMsg('onlinerecSettings') ?>";
      </script>
    </text>
<?php
		$pos += $wt;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>">
      <?= $path .'images/hint_ok.png' ?> 

      <widthPC>
        <script>
	  majorContext = getPageInfo("majorContext");

	  str = "";
	  if( majorContext == "menu" )
	  {
		if( channelsCount &gt; 0 )
		{
			str = "<?= getMsg('onlinerecSelChannel') ?>";
			"<?= $pwi ?>";
		}
		else 0;
	  }
	  else
	  {
		if( channelsCount &gt; 0 )
		{
			idx = getFocusItemIndex();
			url = getStringArrayAt(urlArray, idx);
			if( url != "" )
			{
				str = "<?= getMsg('onlinerecWatchRecord') ?>";
				"<?= $pwi ?>";
			}
			else 0;
		}
		else 0;
	  }
        </script>
      </widthPC>
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	str;
      </script>
    </text>

<?php
		$pos += $wt;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>">
      <?= $path .'images/hint_right.png' ?> 

      <widthPC>
        <script>
	  str = "";
	  if( channelsCount &gt; 0 )
	  {
		str = "<?= getMsg('onlinerecWatchCast') ?>";
		"<?= $pwi ?>";
	  }
	  else 0;
        </script>
      </widthPC>
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	str;
      </script>
    </text>

<?php
		$pos += $wt;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>">
      <?= $path .'images/hint_prev.png' ?> 

      <widthPC>
        <script>
	  str = "";
	  if( prevDate != "" )
	  {
		str = "<?= getMsg('onlinerecPrevDay') ?>";
		"<?= $pwi ?>";
	  }
	  else 0;
        </script>
      </widthPC>
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	str;
      </script>
    </text>

<?php
		$pos += $wt;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>">
      <?= $path .'images/hint_next.png' ?> 

      <widthPC>
        <script>
	  if( nextDate != "" )
	  {
		str = "<?= getMsg('onlinerecNextDay') ?>";
		"<?= $pwi ?>";
	  }
	  else 0;
        </script>
      </widthPC>
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	str;
      </script>
    </text>

<?php
		$pos += $wt;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyi ?>" heightPC="<?= $phi ?>">
      <?= $path .'images/hint_stop.png' ?> 

      <widthPC>
        <script>
	  str = "";
	  if( itemsCount &gt; 0 )
	  {
		str = "<?= getMsg('onlinerecRefresh') ?>";
		"<?= $pwi ?>";
	  }
	  else 0;
        </script>
      </widthPC>
    </image>

<?php
		$pos += $wi + $wd;
		$px = round(   $pos / $scrWidth  * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $pyt ?>" widthPC="<?= $pwt ?>" heightPC="<?= $pht ?>"
     fontSize="<?= $fs ?>" backgroundColor="<?= $backgroundBarColor ?>" foregroundColor="<?= $parentFocusBackgroundColor ?>">
      <script>
	str;
      </script>
    </text>

<!-- Nothing found text -->
    <text redraw="yes" align="center" lines="1" fontSize="22"
     offsetXPC="30" offsetYPC="16" widthPC="64" heightPC="8"
     backgroundColor="-1:-1:-1" foregroundColor="<?= $unFocusFontColor ?>">
      <script>
	msgText;
      </script>
    </text>

    <menuDisplay>
      <script>
	majorContext = getPageInfo("majorContext");
	idx = getQueryMenuIndex();
	idf = getFocusMenuIndex();

	if( idx == idf )
	{
		if( majorContext == "menu" )
		{
			color   = "<?= $focusFontColor ?>";
			bgcolor = "<?= $focusBackgroundColor ?>";
		}
		else
		{
			color   = "<?= $parentFocusFontColor ?>";
			bgcolor = "<?= $parentFocusBackgroundColor ?>";
		}
	}
	else
	{
		color   = "<?= $unFocusFontColor ?>";
		bgcolor = "<?= $backgroundMenuColor ?>";
	}
      </script>
<!-- background -->
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<!-- icon -->
<?php
		$px = round( 10.5 / $menuWidth  * 100, 4);;
		$py = round( 10.5 / $menuHeight * 100, 4);
		$pw = round( 27.5 / $menuWidth  * 100, 4);;
		$ph = round( 27.5 / $menuHeight * 100, 4);

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
	<script>
	  getStringArrayAt(menuIconArray, idx);
	</script>
      </image>
<!-- title -->
<?php
		$px = round(  47.5 / $menuWidth  * 100, 4);;
		$py = round(  11.5 / $menuHeight * 100, 4);
		$pw = round( 235.5 / $menuWidth  * 100, 4);;
		$ph = round(  27.5 / $menuHeight * 100, 4);

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="left" lines="1" fontSize="14" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
	<script>
	  getStringArrayAt(menuTitleArray, idx);
	</script> 
      </text>
    </menuDisplay>

    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();

	url = getStringArrayAt(urlArray, idx);

	if (drawState == "unfocus")
	{
		if( url == "" ) color = "<?= $unFocusFontColor ?>";
		else color = "<?= $unFocusUrlFontColor ?>";

		bgcolor = "<?= $backgroundColor ?>";
	}
	else if (drawState == "inactive")
	{
		if( url == "" ) color = "<?= $parentFocusFontColor ?>";
		else color = "<?= $parentFocusUrlFontColor ?>";

		bgcolor = "<?= $parentFocusBackgroundColor ?>";
	}
	else
	{
		if( url == "" ) color = "<?= $focusFontColor ?>";
		else color = "<?= $focusUrlFontColor ?>";

		bgcolor = "<?= $focusBackgroundColor ?>";
	}
      </script>
<!-- background -->
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<!-- title -->
<?php
		$px = round(  10.5 / $itemWidth  * 100, 4);
		$py = round(  10.5 / $itemHeight * 100, 4);
		$pw = round( 854.5 / $itemWidth  * 100, 4);
		$ph = round(  27.5 / $itemHeight * 100, 4);

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="left" lines="1" fontSize="14" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
	<script>
	  getStringArrayAt(timeArray, idx) + " " + getStringArrayAt(titleArray, idx);
	</script> 
      </text>
    </itemDisplay>

    <onUserInput>
      <script>
	majorContext = getPageInfo("majorContext");

	i = getFocusItemIndex();
	curTime = getStringArrayAt( timeArray, i );

	userInput = currentUserInput();

print("UserInput=" + userInput);

	ret = "false";

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		if ( majorContext == "items" )
		{
			if( i == 0 &amp;&amp; prevDate != "" )
			{
				savedItem = "last";
				curDate = prevDate;
				curTime = "";
				setRefreshTime(100);
				ret = "true";
			}
		}
	}

	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		if ( majorContext == "items" )
		{
			if( i == ( itemsCount - 1 ) &amp;&amp; nextDate != "" )
			{
				savedItem = "first";
				curDate = nextDate;
				curTime = "";
				setRefreshTime(100);
				ret = "true";
			}
		}
	}

	if (userInput == "<?= getRssCommand('pageup') ?>")
	{
		if( prevDate != "" )
		{
			savedItem = i;
			curDate = prevDate;
			curTime = getStringArrayAt( timeArray , i );
			setRefreshTime(10);
			ret = "true";
		}
	}

	else if (userInput == "<?= getRssCommand('pagedown') ?>")
	{
		if( nextDate != "" )
		{
			savedItem = i;
			curDate = nextDate;
			curTime = getStringArrayAt( timeArray , i );
			setRefreshTime(100);
			ret = "true";
		}
	}

	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		if ( majorContext == "menu" ) ret = "true";
		else savedChannel = getFocusMenuIndex();
	}
		
	else if (userInput == "<?= getRssCommand('right') ?>")
	{
		if ( majorContext == "menu" )
		{
			setFocusMenuIndex( savedChannel );
		}
		else ret = "true";
	}

	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		curDate = "";
		curTime = "";
	        setRefreshTime(100);
		ret = "true";
	}

	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		if ( majorContext == "menu" )
		{
			savedChannel = getFocusMenuIndex();
			curChannel = getStringArrayAt( menuIdArray , savedChannel );
		        setRefreshTime(100);
		}
		else
		{
			url = getStringArrayAt( urlArray , i );
			if( url != "" )
			{
				showIdle();
				playItemUrl( url, 0 );
				cancelIdle();
			}
		}
		ret = "true";
	}

	else if (userInput == "<?= getRssCommand('play') ?>" || userInput == "<?= getRssCommand('forward') ?>")
	{
		if ( majorContext == "menu" )
		{
			savedChannel = getFocusMenuIndex();
			curChannel = getStringArrayAt( menuIdArray , savedChannel );
			doRefresh = 1;
		}

		if (userInput == "<?= getRssCommand('forward') ?>")
		{
			s = savedChannel;
			playItemURL( "<?= getMosUrl().'?page=onlinerec_get' ?>&amp;cid=" + curChannel, 0);
		}
		else
		{
			s = doModalRss( "<?= getMosUrl().'?page=rss_onlinerec_player' ?>" + "&amp;cid=" + curChannel );
		}

		if( s != savedChannel )
		{
			savedChannel = s;
			curChannel = getStringArrayAt( menuIdArray , savedChannel );
			doRefresh = 1;
		}

		if( doRefresh == 1 ) setRefreshTime(100);
		ret = "true";
	}

	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_onlinerec_sets' ?>";

		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			showIdle();
			s = getURL( url );
			cancelIdle();
		}
		ret = "true";
	}
	ret;
      </script>
    </onUserInput>
  </mediaDisplay>

  <onEnter>
	curItem    = 0;
	curChannel = "";
	curDate    = "";
	curTime    = "";

	prevDate   = "";
	nextDate   = "";

	channelsCount = 0;
	itemsCount    = 0;

	savedChannel = 0;
	savedItem    = "";

	savedDate = "";
	savedTime = "";

	showStream = "no";

	setRefreshTime(100);
  </onEnter>

  <onExit>
	setRefreshTime(-1);
	playItemURL(-1, 1);
  </onExit>

  <onRefresh>
	setRefreshTime(-1);

	showIdle();

	if( channelsCount == 0 )
	{
		/* get channels */

		url = "<?= getMosUrl().'?page=onlinerec_channels' ?>";

		if( curChannel != "" ) url += "&amp;channel=" + curChannel;
		if( curDate    != "" ) url += "&amp;date="    + curDate;

		dlok = getURL( url );
		if (dlok != null) dlok = readStringFromFile( dlok );
		if (dlok != null)
		{
			c = 0;

			channelsCount = getStringArrayAt(dlok, c); c += 1;

			menuTitleArray = null;
			menuIconArray = null;
			menuIdArray = null;

			count = 0;
			while( count != channelsCount )
			{
				menuIdArray    = pushBackStringArray( menuIdArray, getStringArrayAt(dlok, c)); c += 1;
				menuTitleArray = pushBackStringArray( menuTitleArray, getStringArrayAt(dlok, c)); c += 1;
				menuIconArray  = pushBackStringArray( menuIconArray, getStringArrayAt(dlok, c)); c += 1;

				count += 1;
			}
			savedChannel = getStringArrayAt(dlok, c); c += 1;
			curChannel   = getStringArrayAt(dlok, c); c += 1;
		}
	}

	/* get epg list */
	url = "<?= getMosUrl().'?page=onlinerec_list' ?>";
	if( curChannel != "" ) url += "&amp;channel=" + curChannel;
	if( curDate    != "" ) url += "&amp;date="    + curDate;
	if( curTime    != "" ) url += "&amp;time="    + curTime;

	dlok = getURL( url );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;

		topTitle = getStringArrayAt(dlok, c); c += 1;

		curDate  = getStringArrayAt(dlok, c); c += 1;

		prevDate = getStringArrayAt(dlok, c); c += 1;
		nextDate = getStringArrayAt(dlok, c); c += 1;

		itemsCount = getStringArrayAt(dlok, c); c += 1;

		titleArray = null;
		urlArray   = null;
		timeArray  = null;

		count = 0;
		while( count != itemsCount )
		{
			titleArray = pushBackStringArray( titleArray, getStringArrayAt(dlok, c)); c += 1;
			urlArray   = pushBackStringArray( urlArray,   getStringArrayAt(dlok, c)); c += 1;
			timeArray  = pushBackStringArray( timeArray,  getStringArrayAt(dlok, c)); c += 1;

			count += 1;
		}
		curItem = getStringArrayAt(dlok, c); c += 1;
	}

	setFocusMenuIndex( savedChannel );

	msgText = "";
	if( itemsCount == 0 )
	{
		msgText = "<?= getMsg('onlinerecNothingFound') ?>";
		setFocusItemIndex( 0 );
	}
	else
	{
		if( savedItem == "first" )
		{
			 savedItem = 0;
		}
		else if( savedItem == "last" )
		{
			savedItem = itemsCount - 1;
		}
		else
		{
			savedItem = curItem;
		}

		setFocusItemIndex( savedItem );
		setItemFocus( savedItem );
	}

	cancelIdle();

	doRefresh = 0;

<?php
	$s = getOnlinerecConfigParameter('vfd');
	if( $s == 'mele' )
	{
?>
	MeleVFDShow("Online-record.ru");

<?php
	}
	elseif( $s == 'inext' )
	{
?>
	sekatorSWF_vfdShowMessage("Online-record.ru");

<?php
	}

?>
	redrawDisplay();
  </onRefresh>

  <channel>
    <itemSize>
      <script>
	itemsCount;
      </script>
    </itemSize>

    <menuSize>
      <script>
	channelsCount;
      </script>
    </menuSize>
  </channel>
</rss>
<?php

}

?>