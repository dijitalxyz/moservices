<?php
//
// ====================================

$sovok_session = array(
	'sid'     => '',
	'sid_name'=> '',
	'message' => '',
	'gid'     => '',
	'groups'  => array(),
);

if( is_file( '/tmp/sovok.session.php' ) )
{
	include( '/tmp/sovok.session.php' );
}

//
// ------------------------------------
function rss_sovok_player_content()
{
global $sovok_session;

	class rssSkinSovokPlayer extends rssSkin
	{
	const titleBackgroundColor = '200:200:200';
	const itemBackgroundColor  = '20:20:20';

//	const titleBackgroundColor = '245:173:29';
//	const itemBackgroundColor  = '0:9:39';

		//
		// ------------------------------------
		function showOnUserInput()
		{

?>
    <onUserInput>
	input = currentUserInput();
	ret = "true";

	if( input == "<?= getRssCommand('enter') ?>" )
	{
		if( showInfo == 0 ) showInfo = 1;
		else showInfo = 0;
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
		ret = "true";
	}
	else
	{
		ret = "false";
	}
	ret;
    </onUserInput>
<?php
		}
		//
		// ------------------------------------
		public $cItem;

		public function showScripts()
		{

?>
  <onEnter>

	pagePrompt = "";

	itemCount = getPageInfo( "itemCount" );
	currentItem = <?= $this->cItem ?>;

	startVideo = 1;
	cPlayPause = 1;

	setRefreshTime(10);

	cTitle = "No channels";
	cDesc1 = "Call to developers http://www.moservices.org/forum";

	setRefreshTime(100);
  </onEnter>
	
  <onRefresh>
	setRefreshTime(-1);

	if (startVideo == 1)
	{
		playItemURL(-1, 1);

		if( itemCount == 0 )
		{
			/* error */
			startVideo = 0;
			showInfo = 1;
		}
		else
		{
			showIdle;

			cTitle = getItemInfo( currentItem, "title" );
			cImg   = getItemInfo( currentItem, "image" );
			cId    = getItemInfo( currentItem, "id" );

			cUrl = getUrl( "<?= getMosUrl().'?page=get_sovok' ?>&amp;cid=" + cId );
			cancelIdle;

			startVideo = 0;
			showInfo = 1;
			playItemURL(cUrl, 0, "mediaDisplay", "previewWindow");
		}
	}

	if (startVideo == 0)
	{
		if( showInfo == 1 )
		{
			showIdle;
			dlok = getUrl( "<?= getMosUrl().'?page=get_sovok_epg' ?>&amp;cid=" + cId );
			if (dlok != null)
			{
				cDesc1 = getStringArrayAt(dlok, 0);
				cDesc2 = getStringArrayAt(dlok, 1);
			}
			else
			{
				cDesc1 = "";
				cDesc2 = "";
			}
			cancelIdle;

			startVideo = 2;
			statusTimeout = 50;
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
	if( showInfo == 1 ) redrawDisplay();

	setRefreshTime(100);
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
	playItemURL(-1, 1);
  </onExit>

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

?>
  <mediaDisplay name="threePartsView"
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

   idleImageXPC="88"
   idleImageYPC="80"
   idleImageWidthPC="4.4118"
   idleImageHeightPC="7.8125"
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

    <text redraw="yes" offsetXPC="13" offsetYPC="65" widthPC="81" heightPC="10" 
     align="left" lines="1" fontSize="22"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::topFontColor ?>">
      <script>
	if( showInfo == 1 ) cTitle;
	else null;
      </script>
    </text>

    <text redraw="yes" offsetXPC="6" offsetYPC="75.5" widthPC="88" heightPC="6" 
     align="left" lines="1" fontSize="16"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	if( showInfo == 1 ) cDesc1;
	else null;
      </script>
    </text>

    <text redraw="yes" offsetXPC="6" offsetYPC="81.5" widthPC="88" heightPC="6" 
     align="left" lines="1" fontSize="16"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	if( showInfo == 1 ) cDesc2;
	else null;
      </script>
    </text>
<?php
/*
    <text redraw="yes" offsetXPC="6" offsetYPC="86" widthPC="88" heightPC="6" 
     align="left" fontSize="12.5" foregroundColor="200:200:200">
      <backgroundColor>
        <script>
	  if( showInfo == 1 ) "40:40:40";
	  else "-1:-1:-1";
	</script>
      </backgroundColor>
      <script>
	if( showInfo == 1 ) "<?= $this->Prompt ?>";
	else null;
      </script>
    </text>
*/

			$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
		}
	}

	$gid = $sovok_session['gid'];

	$items = array();

	foreach( $sovok_session['groups'][ $gid ]['channels'] as $item )
	 $items[] = array(
		'id'   => $item['id'],
		'title' => $item['name'],
		'image' => 'http://sovok.tv'. $item['icon']
	 );

	if( count( $items ) == 0 ) return;

	$view = new rssSkinSovokPlayer;

	$view->items = $items;
	$view->cItem = 0;
	if( isset( $_REQUEST['cid'] )) $view->cItem = $_REQUEST['cid'];

	$view->showRss();
}

?>
