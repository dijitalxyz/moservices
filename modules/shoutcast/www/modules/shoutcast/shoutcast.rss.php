<?php

require_once( dirname( __FILE__ ) .'/shoutcast.php' );
//
// ------------------------------------
function rss_shoutcast_content()
{
	class rssShoutcastView extends rssSkinHTile
	{
		const itemWidth		= 400;
		const itemHeight	= 84;

		const itemRows		= 6;
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
			0 => array(			// genre
				'type'    => 'text',
				'posX'    => 10,
				'posY'    => 10,
				'width'   => 320,
				'height'  => 18,
				'lines'   => 1,
				'fontSize'=> 9,
				'align'   => 'left',
				'fgColor' => 'acolor',
//				'bgColor' => '"100:0:0"',
				'text'    => '
	<script>
	  getStringArrayAt(genreArray, idx);
	</script>'
			),
			1 => array(			// bitrate
				'type'    => 'text',
				'posX'    => 330,
				'posY'    => 10,
				'width'   => 60,
				'height'  => 18,
				'lines'   => 1,
				'fontSize'=> 9,
				'align'   => 'right',
//				'bgColor' => '"0:100:0"',
				'fgColor' => 'acolor',
				'text'    => '
	<script>
	  getStringArrayAt(brArray, idx) + " Kbps";
	</script>'
			),
			2 => array(			// title
				'type'    => 'text',
				'posX'    => 10,
				'posY'    => 28,
				'width'   => 380,
				'height'  => 28, // 70
				'lines'   => 1,	// 3
				'fontSize'=> 13,
				'align'   => 'left',
//				'bgColor' => '"100:100:100"',
				'text'    => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
			3 => array(			// ct
				'type'    => 'text',
				'posX'    => 10,
				'posY'    => 56,
				'width'   => 380,
				'height'  => 18,
				'lines'   => 1,
				'fontSize'=> 10,
				'align'   => 'left',
//				'bgColor' => '"0:0:100"',
				'fgColor' => 'acolor',
				'text'    => '
	<script>
	  getStringArrayAt(ctArray, idx);
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
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="10">
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
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $info['image'] ?> 
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
	</foregroundColor><?= $info['text'] ?> 
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
    <image offsetXPC="0" offsetYPC="77" widthPC="100" heightPC="0.4" >
      <?= getSkinPath().static::topBackground ?> 
    </image>

    <text redraw="yes" align="center" lines="1" fontSize="16"
     offsetXPC="8" offsetYPC="78" widthPC="84" heightPC="6"
     backgroundColor="<?= static::itemUnFocusBgColor ?>"
     foregroundColor="<?= static::itemAddFgColor ?>">
      <script>
	stationName;
      </script>
    </text>

    <text redraw="yes" align="center" lines="1" fontSize="20"
     offsetXPC="8" offsetYPC="84" widthPC="84" heightPC="6"
     backgroundColor="<?= static::itemUnFocusBgColor ?>"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	stationPlay;
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
		stationId   = getStringArrayAt(sidArray, i);
		stationName = getStringArrayAt(nameArray, i);
		action = "play";
		setRefreshTime(10);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		action = "stop";
		setRefreshTime(10);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		action = "load";
		setRefreshTime(10);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('forward') ?>")
	{
		s = getURL( actionFwd
		 + "&amp;id="    + getStringArrayAt(sidArray, i)
		 + "&amp;name="  + urlEncode( getStringArrayAt(nameArray, i))
		 + "&amp;genre=" + urlEncode( getStringArrayAt(genreArray, i))
		 + "&amp;br="    + urlEncode( getStringArrayAt(brArray, i))
		);

		action = "load";
		setRefreshTime(10);
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
	        url = "<?= getMosUrl().'?page=rss_shoutcast_menu' ?>";
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			action = "load";
			setRefreshTime(10);
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
	moUrl = "<?= getMosUrl().'?page=shoutcast_list' ?>";
	savedItem = 0;

	stationId   = "<?= shoutcastGetConfigParameter('sid') ?>";
	playOnStart = "<?= shoutcastGetConfigParameter('playOnStart') ?>";

	stationUrl  = "";
	stationName = "";
	stationPlay = "";

	ssIdle  = <?= shoutcastGetConfigParameter('screensaver') ?>;
	ssTimer = -1;
	ssShow  = 0;

	state = 0;

	action = "load";
	setRefreshTime(10);

  </onEnter>

  <onRefresh>
	setRefreshTime(-1);

	Refresh = -1;
	isRedraw = 0;
print("bla 1");
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

print("bla 2");
	if( ssShow == 1 )
	{
		writeStringToFile( "/tmp/shoutcast_station.txt", stationUrl );

	        url = "<?= getMosUrl().'?page=rss_shoutcast_screensaver' ?>";
		url = doModalRss( url );

		ssShow = 0;
	}


print("bla 3");
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

			sidArray   = null;
			nameArray  = null;
			genreArray = null;
			brArray    = null;
			ctArray    = null;

			count = 0;
			while( count != itemCount )
			{
				sidArray   = pushBackStringArray( sidArray,   getStringArrayAt(dlok, c)); c += 1;
				nameArray  = pushBackStringArray( nameArray,  getStringArrayAt(dlok, c)); c += 1;
				genreArray = pushBackStringArray( genreArray, getStringArrayAt(dlok, c)); c += 1;
				brArray    = pushBackStringArray( brArray,    getStringArrayAt(dlok, c)); c += 1;
				ctArray    = pushBackStringArray( ctArray,    getStringArrayAt(dlok, c)); c += 1;

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

print("bla 4");
	if( action == "play" &amp;&amp; stationId != "" )
	{
		showIdle();
		s= getURL("<?= getMosUrl().'?page=shoutcast_get' ?>&amp;id=" + stationId );
		cancelIdle();
		if ( s != null &amp;&amp; s != "" )
		{
		    	playItemURL(-1, 1);

			stationName = getStringArrayAt(s, 0);
			stationUrl  = getStringArrayAt(s, 1);
			stationPlay = "";

			playItemURL(stationUrl, 5, "mediaDisplay", "previewWindow");
			isRedraw = 1;
		}
	}
	else if( action == "stop" )
	{
	    	playItemURL(-1, 1);

		stationId   = "";
		stationName = "";
		stationUrl  = "";
		stationPlay = "";

		s= getURL("<?= getMosUrl().'?page=shoutcast_get' ?>");

		isRedraw = 1;
	}
	action = "";

print("bla 5");
	s = getPlaybackStatus();
	st = getStringArrayAt( s, 3 );

	if( st != 0 &amp;&amp; stationUrl != "" )
	{
		/* get now playing */
		s = getURL("<?= getMosUrl().'?page=shoutcast_tags' ?>&amp;url=" + urlEncode( stationUrl ));
		if ( s != null &amp;&amp; s != "" )
		{
			if( s != stationPlay )
			{
				stationPlay = s;
				isRedraw = 1;
			}
		}
	}

print("bla 6");
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

	$view = new rssShoutcastView;
	$view->showRss();

}

?>
