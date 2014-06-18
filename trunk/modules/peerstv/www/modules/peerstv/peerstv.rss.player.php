<?php

include 'peerstv.init.php';

//
// ------------------------------------
function rss_peerstv_player_content()
{
global $peerstv_session;

	if( ! isset( $_REQUEST['cid'] )) return;
	$cid = $_REQUEST['cid'];

	$items = array();

	$cItem = 0;
	$i = 0;
	foreach( $peerstv_session['channels'] as $id => $item )
	{
		if( $id == $cid ) $cItem = $i;

		$items[ $i++ ] = array(
			'id' => $id,
			'title' => $item['title'],
			'image' => $item['image'],
		);
	}

	if( $i == 0 ) return;

	// show RSS

	$scrWidth  = 1280;
	$scrHeight = 720;

	$backgroundColor      = '0:9:39';	//'86:91:103';
	$titleBackgroundColor = '255:255:255';

	$fontColor      = '255:255:255';
	$titleFontColor = '0:0:0';

	header( "Content-type: text/plain" );

	echo '<?xml version="1.0" ?>' .PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' .PHP_EOL;

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
<?php
		$px = round(   60.5 / $scrWidth  * 100, 4);
		$py = round(  475.5 / $scrHeight * 100, 4);
		$pw = round( 1160.5 / $scrWidth  * 100, 4);
		$ph = round(  173.5 / $scrHeight * 100, 4);

//    <text redraw="yes" offsetXPC="6" offsetYPC="66" widthPC="88" heightPC="24"

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     cornerRounding="15">
      <backgroundColor>
        <script>
	  if( showInfo == 1 ) "<?= $titleBackgroundColor ?>";
	  else "-1:-1:-1";
	</script>
      </backgroundColor>
    </text>

<?php
		$px = round(   63.5 / $scrWidth  * 100, 4);
		$py = round(  533.5 / $scrHeight * 100, 4);
		$pw = round( 1154.5 / $scrWidth  * 100, 4);
		$ph = round(  112.5 / $scrHeight * 100, 4);

//    <text redraw="yes" offsetXPC="6.2" offsetYPC="74" widthPC="87.6" heightPC="15.6"

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     cornerRounding="15">
      <backgroundColor>
        <script>
	  if( showInfo == 1 ) "<?= $backgroundColor ?>";
	  else "-1:-1:-1";
	</script>
      </backgroundColor>
    </text>

<?php
		$px = round(  74.5 / $scrWidth  * 100, 4);
		$py = round( 490.5 / $scrHeight * 100, 4);
		$pw = round(  27.5 / $scrWidth  * 100, 4);
		$ph = round(  27.5 / $scrHeight * 100, 4);

?>
    <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
      <script>
	if( showInfo == 1 ) cImg;
	else null;
      </script>
    </image>

<?php
		$px = round(  106.5 / $scrWidth  * 100, 4);
		$py = round(  480.5 / $scrHeight * 100, 4);
		$pw = round( 1000.5 / $scrWidth  * 100, 4);
		$ph = round(   48.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="left" lines="1" fontSize="18"
     backgroundColor="-1:-1:-1" foregroundColor="<?= $titleFontColor ?>">
      <script>
	if( showInfo == 1 ) cTitle;
	else null;
      </script>
    </text>

<?php
		$px = round( 1106.5 / $scrWidth  * 100, 4);
		$py = round(  480.5 / $scrHeight * 100, 4);
		$pw = round(  100.5 / $scrWidth  * 100, 4);
		$ph = round(   48.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="right" lines="1" fontSize="14"
     backgroundColor="-1:-1:-1" foregroundColor="<?= $titleFontColor ?>">
      <script>
	if( showInfo == 1 ) cTime;
	else null;
      </script>
    </text>

<?php
		$px = round(   80.5 / $scrWidth  * 100, 4);
		$py = round(  544.5 / $scrHeight * 100, 4);
		$pw = round( 1120.5 / $scrWidth  * 100, 4);
		$ph = round(   43.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="left" lines="1" fontSize="14"
     backgroundColor="-1:-1:-1" foregroundColor="<?= $fontColor ?>">
      <script>
	if( showInfo == 1 ) cDesc1;
	else null;
      </script>
    </text>

<?php
		$px = round(   80.5 / $scrWidth  * 100, 4);
		$py = round(  587.5 / $scrHeight * 100, 4);
		$pw = round( 1120.5 / $scrWidth  * 100, 4);
		$ph = round(   43.5 / $scrHeight * 100, 4);

?>
    <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     align="left" lines="1" fontSize="14"
     backgroundColor="-1:-1:-1" foregroundColor="<?= $fontColor ?>">
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
		if( cVideoPaused == 0 )
		{
			postMessage( "<?= getRssCommand('pause') ?>" );
			cVideoPaused = 1;
		}
		else
		{
			postMessage( "<?= getRssCommand('play') ?>" );
			cVideoPaused = 0;	
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
		if( showInfo == 1 )
		{
			showInfo = 0;
			redrawDisplay();
		}

		setEnv( "peerstvChannelNumber", currentItem );
		setEnv( "peerstvChannelCommand", input );

		s = doModalRss( "<?= getMosUrl().'?page=rss_peerstv_channels' ?>" );
		if( s != null &amp;&amp; s != "" )
		{
			currentItem = s;
			startVideo = 1;
		}
	}

	else
	{
		ret = "false";
	}
	ret;
    </onUserInput>
  </mediaDisplay>

  <onEnter>

	pagePrompt = "";

	itemCount = getPageInfo( "itemCount" );
	currentItem = <?= $cItem ?>;

	startVideo = 1;
	cVideoPaused = 0;

	cTitle = "No channels";
	cDesc1 = "Call to developers http://www.moservices.org/forum";
	cDesc2 = "";
	cTime  = "";

	timerEpg = 0;

	vfd_time = "";

	setRefreshTime(100);
  </onEnter>
	
  <onRefresh>
	setRefreshTime(-1);

	/* get time */
	s = getTimeDate();
	t = getStringArrayAt(s, 3); if( t &lt; 10 ) t = "0" + t; cTime = t;
	t = getStringArrayAt(s, 4); if( t &lt; 10 ) t = "0" + t; cTime += ":" + t;

	videoProgress = getPlaybackStatus();
	playStatus  = getStringArrayAt(videoProgress, 3);
	print( "videoProgress={" + videoProgress + "}");

	if (playStatus == 0 )
	{
		/* Stream Got Dead You mazafaka... And we are tryin to restart it... */
		startVideo = 1;
	}

	/* start video */
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

			cUrl = "<?= getMosUrl().'?page=peerstv_get' ?>&amp;cid=" + cId;
			print("Start video: ", cUrl);

			playItemURL(cUrl, 0, "mediaDisplay", "previewWindow");

			cVideoPaused = 0;
			timerEpg = 0;
		}
	}

	/* get epg */
	if( timerEpg == 0 )
	{
		s = getUrl( "<?= getMosUrl().'?page=peerstv_get_epg' ?>&amp;cid=" + cId );
		if ( s != null &amp;&amp; s != "" )
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


	if (startVideo == 0)
	{
		if( showInfo == 1 )
		{
			startVideo = 2;
			showTimeout = 50;
			timerEpg = 0;

			redrawDisplay();
		}
	}
	else
	{
		if( showInfo == 0 ) showTimeout = 0;
		else showTimeout -= 1;

		if ( showTimeout == 0 )
		{
			showInfo = 0;
			startVideo = 0;

		}
		redrawDisplay();
	}

<?php
	if(( $s = getPeerstvConfigParameter('vfd')) != 'none' )
	{
?>
	/* VFD time */
	s = getTimeDate();
	t = getStringArrayAt(s, 3); if( t &lt; 10 ) t = "0" + t; d = t + ":";
	t = getStringArrayAt(s, 4); if( t &lt; 10 ) t = "0" + t; d = d + t + " ";

	if( d != vfd_time )
	{
		vfd_time = d;
<?php
		if( $s == 'mele' )
		{
?>
		MeleVFDShow(vfd_time);
<?php
		}
		elseif( $s == 'inext' )
		{
?>
		sekatorSWF_vfdShowMessage(vfd_time);
<?php
		}

?>
	}

<?php
	}

?>
	setRefreshTime(100);
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
	playItemURL(-1, 1);
	setReturnString( currentItem );
  </onExit>

  <channel>
<?php
	foreach( $items as $item )
	{
		echo "    <item>\n";
		foreach( $item as $tag => $val ) echo "      <$tag>$val</$tag>\n";
		echo "    </item>\n";
	}

?>
  </channel>
</rss>
<?php

}

?>
