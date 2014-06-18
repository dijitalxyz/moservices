<?php

require_once( dirname( __FILE__ ) .'/di.php' );
//
// ------------------------------------
function rss_di_content()
{
	class rssDiView extends rssSkinHTile
	{
		const itemWidth		= 400;
		const itemHeight	= 100;

		const itemRows		= 5;
		const itemOffsetY	= 8;

		const itemAddFgColor	 = '160:160:160';
		const itemUnFocusBgColor = '0:0:0';

		//
		// ------------------------------------
		public $topTitle =
'
	<script>
	  topTitle;
	</script>';
		//
		// ------------------------------------
		public $bottomTitle =
'
	<script>
	  btmTitle;
	</script>';
		//
		// ------------------------------------
		public $fields = array(
			array(				// image
				'type'    => 'image',
				'posX'    => 10,
				'posY'    => 10,
				'width'   => 80,
				'height'  => 80,
				'content' => '
	<script>
	  getStringArrayAt(imageArray, idx);
	</script>'
			),
			array(				// channel name
				'type'    => 'text',
				'posX'    => 100,
				'posY'    => 10,
				'width'   => 295,
				'height'  => 24,
				'lines'   => 1,
				'fontSize'=> 12,
				'align'   => 'left',
//				'bgColor' => '"100:0:0"',
				'content' => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
			array(				// description
				'type'    => 'text',
				'posX'    => 100,
				'posY'    => 34,
				'width'   => 295,
				'height'  => 40,
				'lines'   => 2,
				'fontSize'=> 9,
				'align'   => 'left',
//				'bgColor' => '"0:100:0"',
				'fgColor' => 'acolor',
				'content' => '
	<script>
	  getStringArrayAt(descArray, idx);
	</script>'
			),
			array(				// track title
				'type'    => 'text',
				'posX'    => 100,
				'posY'    => 74,
				'width'   => 295,
				'height'  => 16,
				'lines'   => 1,
				'fontSize'=> 9,
				'align'   => 'left',
				'rolling' => 'yes',
//				'bgColor' => '"0:0:100"',
				'content' => '
	<script>
	  getStringArrayAt(titleArray, idx);
	</script>'
			),
		);
		//
		// ------------------------------------
		public function showItemDisplay()
		{

?>
    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();
	if (drawState == "unfocus")
	{
		color = "<?= static::unFocusFontColor ?>";
		acolor = "<?= static::itemAddFgColor ?>";
		bgcolor = "<?= static::itemUnFocusBgColor ?>";
	}
	else
	{
		color = "<?= static::focusFontColor ?>";
		acolor = "<?= static::focusFontColor ?>";
		bgcolor = "<?= static::itemFocusBgColor ?>";
	}
      </script>
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="6">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<?php
			foreach( $this->fields as $info )
			{
				$px = round( $info['posX'] / static::itemWidth * 100, 4);
				$py = round( $info['posY'] / static::itemHeight * 100, 4);
				$pw = round( $info['width']  / static::itemWidth  * 100, 4);
				$ph = round( $info['height'] / static::itemHeight * 100, 4);

				if( $info['type'] == 'image' )
				{

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $info['content'] ?> 
      </image>
<?php
				}
				else
				{
					$pa = isset( $info['align'] )    ? $info['align']     : static::itemTextAlign;
					$ps = isset( $info['fontSize'] ) ? $info['fontSize']  : static::itemTextFontSize;
					$pb = isset( $info['bgColor'] )  ? $info['bgColor']   : '"'. static::itemTextBackgroundColor .'"';
					$pf = isset( $info['fgColor'] )  ? $info['fgColor']   : 'color';

					$pl = isset( $info['lines'] ) ? $info['lines'] : static::itemTextLines;
					$pl = ( $pl > 0 ) ? ' lines="'. $pl .'"' : '';

					$pl .= isset( $info['rolling'] ) ? ' rolling="'. $info['rolling'] .'"' : '';

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="<?= $pa ?>"<?= $pl ?> fontSize="<?= $ps ?>">
	<backgroundColor>
          <script>
            <?= $pb ?>;
          </script>
	</backgroundColor>
	<foregroundColor>
          <script>
            <?= $pf ?>;
          </script>
	</foregroundColor><?= $info['content'] ?> 
      </text>
<?php
				}
			}

			// more item's fields
			$this->showMoreItemDisplay();

?>
    </itemDisplay>
<?
		}
		//
		// ------------------------------------
		function showMoreDisplay()
		{
?>
    <previewWindow windowColor="0:0:0" offsetXPC="99" widthPC="1" offsetYPC="99" heightPC="1"></previewWindow>

    <text redraw="yes" align="center" lines="1" fontSize="22"
     offsetXPC="18.2353" offsetYPC="15.625" widthPC="63.5294" heightPC="7.8125"
     backgroundColor="-1:-1:-1"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	msgText;
      </script>
    </text>
<!-- playing station -->
<?php
		$px = 0;
		$py = round( 583 / static::viewAreaHeight * 100, 4);
		$pw = 100;
		$ph = round( 3 / static::viewAreaHeight * 100, 4);

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
      <?= getSkinPath().static::topBackground ?> 
    </image>
<?php
		$px = round(  80 / static::viewAreaWidth  * 100, 4);
		$py = round( 596 / static::viewAreaHeight * 100, 4);
		$pw = round( 100 / static::viewAreaWidth  * 100, 4);
		$ph = round( 100 / static::viewAreaHeight * 100, 4);

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     redraw="yes" useBackgroundSurface="yes">
      <script>
	channelImage;
      </script>
    </image>
<?php
		$px = round(  190 / static::viewAreaWidth  * 100, 4);
		$py = round(  596 / static::viewAreaHeight * 100, 4);
		$pw = round( 1090 / static::viewAreaWidth  * 100, 4);
		$ph = round(   50 / static::viewAreaHeight * 100, 4);

?>
    <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     redraw="yes" align="left" lines="1" fontSize="14"
     backgroundColor="<?= static::itemUnFocusBgColor ?>"
     foregroundColor="<?= static::itemAddFgColor ?>">
      <script>
	channelName;
      </script>
    </text>
<?php
		$px = round(  190 / static::viewAreaWidth  * 100, 4);
		$py = round(  646 / static::viewAreaHeight * 100, 4);
		$pw = round( 1090 / static::viewAreaWidth  * 100, 4);
		$ph = round(   50 / static::viewAreaHeight * 100, 4);

?>
    <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     redraw="yes" align="left" lines="1" fontSize="18"
     backgroundColor="<?= static::itemUnFocusBgColor ?>"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	channelPlay;
      </script>
    </text>

<?php
		}
	//
	// ------------------------------------
	function showDisplay()
	{

		$sw = static::screenWidth;
		$sh = static::screenHeight;

		$kw = static::skinWidth;
		$kh = static::skinHeight;

		$sx = ($sw - $kw)/2;
		$sy = ($sh - $kh)/2;

		$vx = static::viewAreaX;
		$vy = static::viewAreaY;
		$vw = static::viewAreaWidth;
		$vh = static::viewAreaHeight;

		$th = static::topHeight + static::topY + static::itemOffsetY;

		$iw = static::itemWidth;
		$ih = static::itemHeight;

		$nc = floor( $vw / $iw );
		$nr = static::itemRows;

		$this->_columnCount = $nc;
		$this->_rowCount = $nr;

?>
  <mediaDisplay name="photoView"

   viewAreaXPC="<?= round(( $sx + $vx )/$sw*100, 4) ?>"
   viewAreaYPC="<?= round(( $sy + $vy )/$sh*100, 4) ?>"
   viewAreaWidthPC="<?= round(( $vw )/$sw*100, 4) ?>"
   viewAreaHeightPC="<?= round(( $vh )/$sh*100, 4) ?>"

   backgroundColor="<?= static::backgroundColor ?>"

   sideTopHeightPC="0"
   sideBottomHeightPC="0"

   sideColorBottom="<?= static::sideColorBottom ?>"
   sideColorTop="<?= static::sideColorTop ?>"

   rowCount="<?= $nr ?>"
   columnCount="<?= $nc ?>"

   itemOffsetXPC="<?= round(($vw - $iw * $nc)/2/$vw*100, 4) ?>"
   itemOffsetYPC="<?= round($th/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"

   itemGapXPC="0"
   itemGapYPC="0"

   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   sliding="no"
   rollItems="no"
   drawItemText="no"
   forceFocusOnItem="yes"

   showHeader="no"
   showDefaultInfo="no"

   idleImageXPC="<?= round( static::idleImageX/$vw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$vh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$vw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$vh*100, 4) ?>"
  >
<?php
		$this->showIdleBg();
		$this->showTop();
		$this->showBottom();

		$this->showMoreDisplay();

		$this->showItemDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
		//
		// ------------------------------------
		public function showOnUserInput()
		{
?>
    <onUserInput>

	ssTimer = ssIdle;

	ret = "false";
	i = getFocusItemIndex();
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		if( ( i % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		if( ( ( i - -1 ) % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		channelId   = getStringArrayAt(cidArray, i);
		channelName = getStringArrayAt(nameArray, i);
		stationId   = getStringArrayAt(stArray, i);
		action = "play";
		setRefreshTime(100);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		action = "stop";
		setRefreshTime(100);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		action = "load";
		savedItem = i;
		setRefreshTime(100);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('forward') ?>")
	{
		s = getURL( actionFwd
		 + "&amp;id="      + getStringArrayAt(cidArray, i)
		 + "&amp;name="    + urlEncode( getStringArrayAt(nameArray,  i))
		 + "&amp;desc="    + urlEncode( getStringArrayAt(descArray,  i))
		 + "&amp;image="   + urlEncode( getStringArrayAt(imageArray, i))
		 + "&amp;station=" + urlEncode( getStringArrayAt(stArray,    i))
		);

		action = "load";
		savedItem = i;
		setRefreshTime(100);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('display') ?>")
	{
		ssShow = 1;
		setRefreshTime(100);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "menu" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_di_menu' ?>";

		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			action = "load";
			setRefreshTime(100);
		}
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
		//
		// ------------------------------------
		public function showScripts()
		{
?>

  <onEnter>
	moUrl = "<?= getMosUrl().'?page=di_list' ?>";
	savedItem = 0;

	channelId   = "<?= diGetConfigParameter('cid') ?>";
	stationId   = "<?= diGetConfigParameter('sid') ?>";
	playOnStart = "<?= diGetConfigParameter('playOnStart') ?>";

	channelName  = "";
	channelPlay  = "";
	channelImage = null;

	ssIdle  = <?= diGetConfigParameter('screensaver') ?>;
	ssTimer = -1;
	ssShow  = 0;

	state = 0;

	action = "load";
	setRefreshTime(100);

  </onEnter>

  <onRefresh>
	setRefreshTime(-1);

	isRedraw = 0;

	/* screensaver */
	if( ssIdle != 0 )
	{
		if( ssTimer == -1 ) ssTimer = ssIdle;
		else if( ssTimer == 0 )
		{
			ssShow = 1;
			ssTimer = ssIdle;
		}
		else ssTimer -= 1;
	}

	if( ssShow == 1 )
	{
		s = null;
		s  = pushBackStringArray( s, stationId );
		s  = pushBackStringArray( s, channelId );
		writeStringToFile( "/tmp/di_channel.txt", s );

		url = doModalRss( "<?= getMosUrl().'?page=rss_di_screensaver' ?>" );

		ssShow = 0;
	}


	/* load list */
	if( action == "load" )
	{
		showIdle();
		dlok = getURL( moUrl );
		if ( dlok != null &amp;&amp; dlok != "" ) dlok = readStringFromFile( dlok );
		if ( dlok != null &amp;&amp; dlok != "" )
		{
			itemCount = 0;

			c = 0;
			topTitle = getStringArrayAt(dlok, c); c += 1;
			btmTitle = getStringArrayAt(dlok, c); c += 1;

			actionFwd = getStringArrayAt(dlok, c); c += 1;

			ssIdle = getStringArrayAt(dlok, c); c += 1;
			ssIdle = ssIdle * 2;

			itemCount = getStringArrayAt(dlok, c); c += 1;

			cidArray     = null;
			nameArray    = null;
			descArray    = null;
			imageArray   = null;
			titleArray   = null;
			stArray      = null;

			count = 0;
			while( count != itemCount )
			{
				cidArray   = pushBackStringArray( cidArray,   getStringArrayAt(dlok, c)); c += 1;
				nameArray  = pushBackStringArray( nameArray,  getStringArrayAt(dlok, c)); c += 1;
				descArray  = pushBackStringArray( descArray,  getStringArrayAt(dlok, c)); c += 1;
				imageArray = pushBackStringArray( imageArray, getStringArrayAt(dlok, c)); c += 1;
				titleArray = pushBackStringArray( titleArray, getStringArrayAt(dlok, c)); c += 1;
				stArray    = pushBackStringArray( stArray,    getStringArrayAt(dlok, c)); c += 1;

				count += 1;
			}
			isRedraw = 1;
		}
		msgText = "";
		if( itemCount == 0 )
		{
			msgText = "<?= getMsg('coreRssPromptNothing') ?>";
			setFocusItemIndex( 0 );
		}
		else
		{
			if( savedItem &gt; ( itemCount - 1 )) setFocusItemIndex( itemCount - 1 );
			else setFocusItemIndex( savedItem );
		}

		cancelIdle();

		if( playOnStart == "yes" )
		{
			action = "play";
			playOnStart == "no";
		}
	}

	if( action == "play" &amp;&amp; channelId != "" )
	{
		showIdle();
		s= getURL("<?= getMosUrl().'?page=di_get' ?>"
		+ "&amp;station=" + stationId
		+ "&amp;id="      + channelId
		);
		cancelIdle();
		if ( s != null &amp;&amp; s != "" )
		{
	    	playItemURL(-1, 1);

			channelUrl  = getStringArrayAt(s, 0);
			channelName = getStringArrayAt(s, 1);
			channelPlay = getStringArrayAt(s, 2);

			playItemURL(channelUrl, 5, "mediaDisplay", "previewWindow");
			isRedraw = 1;
		}
	}
	else if( action == "stop" )
	{
	    	playItemURL(-1, 1);

		channelId    = "";
		channelName  = "";
		channelPlay  = "";
		channelImage = null;

		s= getURL("<?= getMosUrl().'?page=di_get' ?>");

		isRedraw = 1;
	}
	action = "";

	s = getPlaybackStatus();
	st = getStringArrayAt( s, 3 );

	if( st != 0 &amp;&amp; channelId != "" )
	{
		/* get now playing */
		s = getURL("<?= getMosUrl().'?page=di_track' ?>"
		+ "&amp;station=" + stationId
		+ "&amp;id="      + channelId
		);

		if ( s != null &amp;&amp; s != "" )
		{
			c = getStringArrayAt(s, 0);
			if( c != channelPlay )
			{
				channelPlay  = c;
				isRedraw = 1;
			}
			c = getStringArrayAt(s, 1);
			if( c != channelImage )
			{
				channelImage  = c;
				isRedraw = 1;
			}
		}
	}

	if( isRedraw == 1 ) redrawDisplay();

	setRefreshTime( 30000 );
	null;
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
    	playItemURL(-1, 1);
  </onExit>

<?php
		}
		//
		// ------------------------------------
		public function showChannel()
		{
?>
  <channel>
    <itemSize>
      <script>
	itemCount;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}

	$view = new rssDiView;
	$view->showRss();
}

?>
