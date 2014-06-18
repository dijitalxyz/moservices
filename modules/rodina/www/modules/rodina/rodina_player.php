<?php

include( 'load_config.inc.php' );
//
// ------------------------------------
function rss_rodina_player_content()
{
global $rodina_session;

	class rssSkinRodinaPlayer extends rssSkin
	{
		const titleBackgroundColor = '200:200:200';
		const itemBackgroundColor  = '20:20:20';

		//
		// ------------------------------------
		public $cItem;
		public $cCode;
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

?>
  <mediaDisplay name="onePartView"
   viewAreaXPC="0"
   viewAreaYPC="0"
   viewAreaWidthPC="100"
   viewAreaHeightPC="100"

   backgroundColor="0:0:0"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="0:0:0"
   sideColorRight="0:0:0"

   showHeader="no"
   showDefaultInfo="no"

   idleImageXPC="45.5882"
   idleImageYPC="42.1875"
   idleImageWidthPC="8.8235"
   idleImageHeightPC="15.625"
  >
    <idleImage><?= getRssImages() ?>idle01.png</idleImage>
    <idleImage><?= getRssImages() ?>idle02.png</idleImage>
    <idleImage><?= getRssImages() ?>idle03.png</idleImage>
    <idleImage><?= getRssImages() ?>idle04.png</idleImage>
    <idleImage><?= getRssImages() ?>idle05.png</idleImage>
    <idleImage><?= getRssImages() ?>idle06.png</idleImage>
    <idleImage><?= getRssImages() ?>idle07.png</idleImage>
    <idleImage><?= getRssImages() ?>idle08.png</idleImage>

    <previewWindow 
     windowColor="0:0:0" 
     offsetXPC="0"
     offsetYPC="0"
     widthPC="100"
     heightPC="100"
    />

    <!-- bar -->
    <text redraw="yes" offsetXPC="6" offsetYPC="66" widthPC="88" heightPC="24"
     cornerRounding="15">
      <backgroundColor>
        <script>
	  if( showInfo == 1 ) "<?= static::titleBackgroundColor ?>";
	  else "-1:-1:-1";
	</script>
      </backgroundColor>
    </text>

    <text redraw="yes" offsetXPC="6.2" offsetYPC="74" widthPC="87.6" heightPC="15.6"
     cornerRounding="15">
      <backgroundColor>
        <script>
	  if( showInfo == 1 ) "<?= static::itemBackgroundColor ?>";
	  else "-1:-1:-1";
	</script>
      </backgroundColor>
    </text>

    <image redraw="yes" offsetXPC="8" offsetYPC="66" widthPC="5" heightPC="8">
      <script>
	if( showInfo == 1 ) cImg;
	else null;
      </script>
    </image>

    <text redraw="yes" offsetXPC="14" offsetYPC="65" widthPC="80" heightPC="10" 
     align="left" lines="1" fontSize="22"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::topFontColor ?>">
      <script>
	if( showInfo == 1 ) cTitle;
	else null;
      </script>
    </text>

    <text redraw="yes" offsetXPC="7" offsetYPC="75.5" widthPC="87" heightPC="6" 
     align="left" lines="1" fontSize="16"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	if( showInfo == 1 ) cDesc1;
	else null;
      </script>
    </text>

    <text redraw="yes" offsetXPC="7" offsetYPC="81.5" widthPC="87" heightPC="6" 
     align="left" lines="1" fontSize="16"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	if( showInfo == 1 ) cDesc2;
	else null;
      </script>
    </text>

    <onUserInput>
	input = currentUserInput();
	ret = "true";

	if( input == "<?= getRssCommand('enter') ?>" )
	{
		if( showInfo == 0 ) showInfo = 1;
		else showInfo = 0;
	}	

	else if( input == "<?= getRssCommand('stop') ?>" )
	{
		postMessage( "<?= getRssCommand('return') ?>" );
	}

	else if( input == "<?= getRssCommand('play') ?>" )
	{
		vidProgress = getPlaybackStatus();
		playStatus = getStringArrayAt(vidProgress, 3);

		if (playStatus == 0)
		{
			startVideo = 1;
		}
		else
		if( cPlayPause == 1 )
		{
			postMessage( "<?= getRssCommand('pause') ?>" );
			cPlayPause = 0;
		}
		else
		{
			postMessage( "<?= getRssCommand('play') ?>" );
			cPlayPause = 1;	
		}
		showInfo = 1;
	}

	else if( input == "<?= getRssCommand('left') ?>" )
	{
		if( itemCount != 1 &amp;&amp; currentItem != -1 )
		{
			if( currentItem == 0 ) currentItem = itemCount - 1;
			else currentItem -= 1;
			startVideo = 1;
		}
	}		

	else if( input == "<?= getRssCommand('right') ?>" )
	{
		if( itemCount != 1 &amp;&amp; currentItem != -1 )
		{
			currentItem -= -1;
			if( currentItem == itemCount ) currentItem = 0;
			startVideo = 1;
		}
	}

	else if( input == "<?= getRssCommand('up') ?>" || input == "<?= getRssCommand('down') ?>" )
	{
		setEnv( "rodinaKey", input );
		s = doModalRss( "<?= getMosUrl().'?page=rss_rodina_channels' ?>&amp;item=" + currentItem );
		if(( s != null )&amp;&amp;( s != "" ))
		{
			currentItem = s;
			startVideo = 1;
		}
		ret = "true";
	}
<?php
	if( getMosOption('sdk_version') > 3 )
	{

?>
	else if ( input == "<?= getRssCommand('zoom') ?>")
	{
		currentAspectRatio = getCurrentSetting("$[ASPECT_RATIO]");
		if (null == originalAspectRatio)
		{
			originalAspectRatio = currentAspectRatio;
		}
		if (currentAspectRatio=="$[PAN_SCAN_4_BY_3]") setAspectRatio("$[LETTER_BOX_4_BY_3]");
		else if (currentAspectRatio=="$[LETTER_BOX_4_BY_3]") setAspectRatio("$[WIDE_16_BY_9]");
		else if (currentAspectRatio=="$[WIDE_16_BY_9]") setAspectRatio("$[WIDE_16_BY_10]");
		else if (currentAspectRatio=="$[WIDE_16_BY_10]") setAspectRatio("$[PAN_SCAN_4_BY_3]");
		ret = "true";
	}
<?php
	}

?>
	else
	{
		ret = "false";
	}
	ret;
    </onUserInput>
  </mediaDisplay>
<?php
		}
		//
		// ------------------------------------
		public function showScripts()
		{

?>
  <onEnter>

	pagePrompt = "";

	itemCount = getPageInfo( "itemCount" );
	currentItem = <?= $this->cItem ?>;

	startVideo = 1;
	cPlayPause = 1;

	code = "<?= $this->cCode ?>";

	cTitle = "No channels";
	cDesc1 = "Call to developers http://www.moservices.org/forum";
	cDesc2 = "";

	timerEpg   = 0;
	timerToken = 0;

	setRefreshTime(100);
  </onEnter>
	
  <onRefresh>
	setRefreshTime(-1);

	if (startVideo == 1)
	{
		playItemURL(-1, 1);
		startVideo = 0;
		showInfo = 1;

		if( itemCount != 0 )
		{
			cTitle = getItemInfo( currentItem, "title" );
			cImg   = getItemInfo( currentItem, "image" );
			cId    = getItemInfo( currentItem, "id" );

			while( 1 )
			{
				showIdle();
				cUrl = getUrl( "<?= getMosUrl().'?page=get_rodina' ?>&amp;cid=" + cId + "&amp;code=" + code );
				cancelIdle();
				if( cUrl == "protected" )
				{
					s = doModalRss( "<?= getMosUrl().'?page=rss_keyboard' ?>" );
					if(( s != null )&amp;&amp;( s != "" ))
					{
						code = s;
					}
					else break;
				}
				else if( cUrl == "message" )
				{
					doModalRss( "<?= getMosUrl().'?page=rss_rodina_message' ?>" );
					cUrl = null;
					break;
				}
				else break;
			}

			playItemURL(cUrl, 0, "mediaDisplay", "previewWindow");
		}
	}

	if (startVideo == 0)
	{
		if( showInfo == 1 )
		{
			startVideo = 2;
			statusTimeout = 50;
			timerEpg = 0;
		}
	}
	else
	{
		if( showInfo == 0 ) statusTimeout = 0;
		else statusTimeout -= 1;

		if ( statusTimeout == 0 )
		{
			showInfo = 0;
			startVideo = 0;
			redrawDisplay();
		}
	}

	/* get token status */
	if( timerToken == 0 )
	{
		s = getUrl( "<?= getMosUrl().'?page=get_rodina_token' ?>" );
		if( s == "fail" )
		{
			doModalRss( "<?= getMosUrl().'?page=rss_rodina_message' ?>" );
			postMessage( "<?= getRssCommand('return') ?>" );
		}
		timerToken = 1800;
	}
	else timerToken -= 1;


	/* get epg */
	if( timerEpg == 0 )
	{
		s = getUrl( "<?= getMosUrl().'?page=get_rodina_epg' ?>&amp;cid=" + cId );
		if( s == "fail" )
		{
			doModalRss( "<?= getMosUrl().'?page=rss_rodina_message' ?>" );
			postMessage( "<?= getRssCommand('return') ?>" );
		}
		else if ( s != null &amp;&amp; s != "" )
		{
			cDesc1 = getStringArrayAt(s, 0);
			cDesc2 = getStringArrayAt(s, 1);
		}
		else
		{
			cDesc1 = "";
			cDesc2 = "";
		}
		timerEpg = 3000;
	}
	else timerEpg -= 1;

	if( showInfo == 1 ) redrawDisplay();

	setRefreshTime(100);
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
	playItemURL(-1, 1);
	setReturnString( currentItem );
  </onExit>

<?php

		}
	}

	$gid = $rodina_session['gid'];

	$items = array();

	foreach( $rodina_session['categories'][ $gid ]['channels'] as $cid )
	 $items[] = array(
		'id'    => $cid,
		'title' => $rodina_session['channels'][ $cid ]['name'],
		'image' => $rodina_session['channels'][ $cid ]['icon']
	 );

	if( count( $items ) == 0 ) return;

	$view = new rssSkinRodinaPlayer;

	$view->items = $items;
	$view->cItem = 0;
	if( isset( $_REQUEST['cid'] )) $view->cItem = $_REQUEST['cid'];

	$view->cCode = $rodina_session['code'];

	$view->showRss();
}

?>
