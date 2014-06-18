<?php

require_once 'detectfw.php';

define("epgenabledconf", '/usr/local/etc/mos/www/modules/iptvlist/epg_enabled.conf');

function rss_iptvplay_content()
{
	if ( file_exists( epgenabledconf )) 
	{
		$epgenabled = file_get_contents(epgenabledconf);
		$epgenabled = trim( str_replace(array( "\r","\n"), '', $epgenabled));
	} 
	else $epgenabled='1';

	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>
<!--
	iptvlist created by Roman Lut aka hax.
-->

<script>
/*
	1 - enable sound fix
	0 -disable sound fix;
*/
	ENABLE_SOUND_FIX = 0;

</script>

<initVars>
	msCounter = 0;
	seekPos = -1;

	firstBuf = 1;
	progressbarStatus = 0;

	lastFullnessLimit = 10;

	bufPoolUnit = 384 * 1024;
	bufPoolCount = 4;
	bufPoolSize = bufPoolUnit * bufPoolCount;

	GUIVisible = 1;
	GUIShow    = 4000;

	GUI2Visible = 0;
	GUI2Show    = 0;

	pendingUnpause = 0;

	startVideo = 1;
	pauseVideo = -1;

	period = 100;
</initVars>

<stopDownload>		
	if ( rec == 1 )
	{
		result = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/download.php?stop=1" );
		rec = 2;
		recSec = 0;
		recMin = 0;
		redrawDisplay();
	}
</stopDownload>		

<saveUrl>
       	focusIndex = 0 + getFocusItemIndex();

	tmp = null;
	tmp = pushBackStringArray( tmp, iptvTitle1 );	
	tmp = pushBackStringArray( tmp, iptvTitle );	
	tmp = pushBackStringArray( tmp, content_original );	
	tmp = pushBackStringArray( tmp, content );	
	tmp = pushBackStringArray( tmp, listName );	
	writeStringToFile("/tmp/iptv_url.dat",tmp);
</saveUrl>

<GetEPGName>
/* 
  do create actual EPG list 
*/
	getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/loadlist.php?msgEPGNotAvail=" + urlEncode( "<?= getMsg( 'iptvEPGNotAvailable' ) ?>" ) );

	EPGData = readStringFromFile("/tmp/epg_list.txt");
	EPGName = getStringArrayAt( EPGData, chIndex * 7 + 3 );

	writeStringToFile("/tmp/test.txt", EPGName);

</GetEPGName>

<loadList>
	if ( favLinkArray == null )
	{
		/* BuildFavList */
		favLinkArray = null;
		favLinkCount = 0;
/*    
		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/loadlist.php" );
*/
		favLinkArray = readStringFromFile("/tmp/iptv_playlist.txt");

		favLinkCount = 0 + getStringArrayAt( favLinkArray, 0 );
		favLinkArray = deleteStringArrayAt( favLinkArray, 0 );

		listName = getStringArrayAt( favLinkArray, 0 );
		favLinkArray = deleteStringArrayAt( favLinkArray, 0 );

		/* readIndex */
		chIndex = 0 + readStringFromFile("/tmp/iptv_index.dat");

		if ( chIndex &lt; 0 ) chIndex = favLinkCount - 1;
		else if ( chIndex &gt; ( favLinkCount - 1 ) ) chIndex = 0;

		chIndex2 = chIndex;

		redrawDisplay();
	}
</loadList>

<playIndex>
	/* writeIndex */
	writeStringToFile("/tmp/iptv_index.dat", chIndex);

	executeScript("stopDownload");

	playItemURL(-1, 1);
	setRefreshTime(-1);

	content = getStringArrayAt(favLinkArray , chIndex * 3 + 2);
	content_original = getStringArrayAt(favLinkArray , chIndex * 3 + 1);
	
	iptvTitle1 = getStringArrayAt(favLinkArray , chIndex * 3 );
	
	iptvTitle = getStringArrayAt(favLinkArray , chIndex * 3 );
	chIndex1 = 0 + chIndex + 1;
	iptvTitle = " " + chIndex1 + ". " + iptvTitle;

	executeScript("initVars");

	setRefreshTime(100);
</playIndex>

<switchToNext>
	executeScript("loadList");

	chIndex += 1;

	if ( chIndex &gt; ( favLinkCount - 1 ) ) chIndex = 0;

	executeScript("playIndex");

	chIndex2 = chIndex;
</switchToNext>

<switchToPrev>
    	executeScript("loadList");

	chIndex -= 1;

	if ( chIndex &lt; 0 ) chIndex = favLinkCount - 1;

	executeScript("playIndex");

	chIndex2 = chIndex;
</switchToPrev>

<selectNext>
	chIndex2 += 1;
	if ( chIndex2 &gt; ( favLinkCount - 1 ) ) chIndex2 = 0;
</selectNext>

<selectPrev>
	chIndex2 -= 1;
	if ( chIndex2 &lt; 0 ) chIndex2 = favLinkCount - 1;
</selectPrev>

<selectNextPage>
	chIndex2 += 10;
	while ( chIndex2 &gt;= favLinkCount ) chIndex2 -= favLinkCount;
</selectNextPage>

<selectPrevPage>
	chIndex2 -= 10;
	while ( chIndex2 &lt; 0 ) chIndex2 += favLinkCount;
</selectPrevPage>

<onEnter>
	SetScreenSaverStatus("no");
	screenSaverPush=getCurrentSetting("$[SCREEN_SAVER_TIMING]");
	setScreenSaverTiming("$[OFF]");

	EPGEnabled = "<?= $epgenabled ?>";

	actionPending = 0;
	pendingUnpause = 0;

	rec = 0;
	recSec = 0;
	recMin = 0;

	aspectId = 0;
	aspectName = "";

	chIndex2 = 0;

	/* load first url */
	tmp = readStringFromFile("/tmp/iptv_url.dat");

	iptvTitle1 = getStringArrayAt( tmp, 0 );
	iptvTitle = getStringArrayAt( tmp, 1 );
	content_original = getStringArrayAt( tmp, 2 );
	content = getStringArrayAt( tmp, 3 );
	listName = getStringArrayAt( tmp, 4 );
 
	executeScript("initVars");

	setRefreshTime(100);
</onEnter>

<onExit>
	setRefreshTime(-1);
	playItemURL(-1, 1);

	executeScript("stopDownload");

	SetScreenSaverStatus("yes");
	setScreenSaverTiming( screenSaverPush );
	tearDownPlaybackFlow();
</onExit>

<onRefresh>

print("Refresh entry");

	setRefreshTime(-1);
	
	if ( pendingUnpause &gt; 0 )
	{
		pendingUnpause -= period;
		if ( pendingUnpause &lt;= 0 )
		{
			pendingUnpause = -1;
			postMessage("video_play");
			pauseVideo = 0;
		}
	}
			
	/* GUI counts */
	if ( GUIShow &gt; 0 ) GUIShow -= period;
	else
	{
		GUIVisible = 0;
		seekPos = -1;
	}

	if ( GUI2Show &gt; 0 ) GUI2Show -= period;
	else
	{
		GUI2Visible = 0;
		chIndex2 = chIndex;
	}

	if ( GUI3Show &gt; 0 ) GUI3Show -= period;
	else GUI3Visible = 0;

	/* actions */
	if ( actionPending == 1 )
	{
		if ( listName == "Recordings" ) rss="<?= getMosUrl().'?page=' ?>rss_help3";
		else rss="<?= getMosUrl().'?page=' ?>rss_help2";

		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	}
	else if ( actionPending == 10 )
	{
		if ( listName != "Recordings" ) rss="<?= getMosUrl().'?page=' ?>rss_sidemenu2";
		else rss="<?= getMosUrl().'?page=' ?>rss_sidemenu3";

      		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	
		if ( mret != "" )
		{
	    		input = mret;
	    		print ("[My Favorites - input()] get input:", userInput);
	    		executeScript("handleUserInput");
		}
	}
	else if ( actionPending == 11 )
	{
	    	executeScript("saveUrl");

		rss="<?= getMosUrl().'?page=' ?>rss_epg";
      		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	}
	actionPending = 0;

	/* play counter */
	msCounter += period;
	timerString = secondToString( integer( msCounter / 1000 ));

	period = 1000;

	/* start video */
	if (startVideo == 1)
	{
		print("Start video: ", content);
		playItemURL(-1, 1);
			
		if (bufPoolCount == 0)
		{
			playItemURL(content, 3, bufPoolUnit, "mediaDisplay", "previewWindow");
		}
		else
		{
			newURL = content+" fileCache=/tmp/videoCachefile cacheSize="+bufPoolCount;
			print("newURL:", newURL);
			playItemURL(newURL, 3, bufPoolUnit, "mediaDisplay", "previewWindow");
		}
		pauseVideo = 0;
		startVideo = 2;
		sound_fix_skip = 0;

	}

	isSeekable = videoIsSeekable();

	videoProgress = getPlaybackStatus();
	playElapsed = getStringArrayAt(videoProgress,0);
	playTotal   = getStringArrayAt(videoProgress,1);
	bufferedSec = getStringArrayAt(videoProgress, 2);
	playStatus  = getStringArrayAt(videoProgress, 3);

	print("Video status: ", videoProgress);

	if( playTotal == 100 ) isSeekable = "";

	if( isSeekable == 1 )
	{
		sound_fix_skip = 1;
		timerString = secondToString( playElapsed ) + " / " + secondToString( playTotal );
	}

	if( playStatus == 0 ) postMessage("return");

	/* initial */
	if( progressbarStatus == 0 )
	{
		lastFullness = 0;
		lastFullnessCount = 0;
		progressbarStatus = 1;
	}

	if( progressbarStatus == 1 )
	{
		print("State buffering");
		GUIVisible = 1;
		GUIShow = 1000;

		period = 300;

		bufProgress = getCachedStreamDataSize(0, bufPoolSize);
		curFullness = getStringArrayAt(bufProgress, 0);
		param1      = getStringArrayAt(bufProgress, 1);
		param2      = getStringArrayAt(bufProgress, 2);
		EOFFlag     = getStringArrayAt(bufProgress, 3);

		print("Buffer state:", curFullness, EOFFlag);
	    
		if( EOFFlag == 1 &amp;&amp; curFullness &gt; 0 )
		{
			print("End of stream is reached");
			progressbarStatus = 2;
		}
		else if( EOFFlag == 2 )
		{
			print("Network is down!");
			postMessage("return");
		}
		else if( curFullness &gt; 95 )
		{
			print("Buffer is ready to be shown");
			progressbarStatus = 2;
		}
		else if( lastFullness != curFullness )
		{
			print("curFullNess=" + curFullness);
			lastFullness = curFullness;
			lastFullnessCount = 0;
		}
		else if( lastFullness != 0 )
		{
			print("lastFullNess     =" + lastFullness);
			lastFullnessCount = Add( lastFullnessCount, 1 );
			print("lastFullNessCount=" + lastFullnessCount);

			if( lastFullnessCount &gt; sameFullnessLimit )
			{
				print("Nothing comes, stop buffering!");

				lastFullness = 0;
				lastFullnessCount = 0;
				progressbarStatus = 2;
			}
		}
		print("Exit buffering");
	}

	else if( progressbarStatus == 2 )
	{
		print("State preparing");
	  	rebufferSec = bufferedSec / 4;

		print("rebufferSec adjusted:", rebufferSec, bufferedSec);

		setVoutDisplay("true");
		if( firstBuf == 1 )
		{
			playAtTime(0);
			videoPaused = 0;
			firstBuf = 0;
		}
		else
		{
			postMessage("video_play");
		}

		progressbarStatus = 3;
		print("Exit preparing");
	}
			
	else if( progressbarStatus == 3 )
	{
		print("State playing");
		bufOffset = playTotal - playElapsed;
		if( bufferedSec &lt; rebufferSec &amp;&amp; bufOffset &gt; rebufferSec )
		{
			print("Starting rebuffering");

			postMessage("video_pause");
			lastFullness = 0;
			lastFullnessCount = 0;
			progressbarStatus = 1;
		}

		if ( ENABLE_SOUND_FIX == 1
		 &amp;&amp; sound_fix_skip == 0
		 &amp;&amp; playElapsed    == 1
		 &amp;&amp; pendingUnpause == 0 )
		{
			pendingUnpause = 500;
			postMessage("video_pause");
			pauseVideo = 1;
		}
		print("Exit playing");
	}

	print("setRefreshTime=" + period);

	setRefreshTime( period );

	print("onRefresh end");

</onRefresh>

<handleUserInput>

	if( input == "return" )
	{
		setRefreshTime(-1);
		ret = "false";
	}
	else if( input == "setup" || input == "guide" )
	{
		pauseVideo = 1;
		ret = "false";
	}
	else if( input == "video_stop" )
	{
		postMessage("return");
		ret = "true";
	}
	else if( input == "video_completed" )
	{
		playItemURL(-1,1);

		if ( returnOnVideoComplete == "yes" )
		{
			postMessage("return");
		}

		ret = "true";
	}
	else if( input == "video_play" )
	{
		if( pauseVideo == 1 )
		{
			print("video Resume>>>>>>>>");
			pauseVideo = 0;

			ret = "false";
		}
		else
		{
			print("video Start to Pause>>>>>>>>");
			pauseVideo = 1;
			postMessage("video_pause");
			ret = "true";
		}
	}
	else if (input=="video_pause")
	{
		print("video Pause>>>>>>>");
		
		pauseVideo = 1;
		ret = "false";
	}

else if (input == "right" || input == "left")
{
	if ( isSeekable == 1 )
	{
		if ( seekPos == -1 )
		{
			seekPos = playElapsed * 1.0 / playTotal;
		}

		GUIShow = 10000;

		GUIVisible = 1;
		GUI2Visible = 0;

		if ( input == "right" )
		{	 
	    	if ( seekPos &lt; 1 ) 
			{
				seekPos = 0.01 + seekPos;		
			}
			else
			{
				seekPos = 1;
			}

		}
		else if ( input == "left" )
		{	 
	    	if ( seekPos &gt; 0 ) 
			{
				seekPos = -0.01 + seekPos;		
			}
			else
			{
				seekPos = 0;
			}
		}
		redrawDisplay();
	}
	else
	{
		if ( input == "right" )
		{	 
	    	executeScript("switchToNext");
		}
		else if ( input == "left" )
		{	 
	    	executeScript("switchToPrev");
		}
	}

	ret = "true";
}
else if (input == "video_volume_up" || input == "video_volume_down")
{
	ret = "false";
}
else if (input == "enter")
{
	if ( GUI2Visible == 0 )
	{
		if ( GUIVisible != 0 )
		{

			GUIVisible = 0;
			GUIShow = 0;

			if ( isSeekable == 1 )
			{
				if ( seekPos != -1 )
				{
					playAtTime( playTotal * seekPos ); 
					GUIVisible = 1;
					GUIShow = 1000;
				}
			}
		}
		else
		{
			GUIVisible = 1;
			GUIShow = 10000;
		}
	}
	else
	{
		chIndex = chIndex2;
		executeScript("playIndex");
	}

	redrawDisplay();
	ret = "true";
}

	else if (input == "display")
	{
		GUIShow = 0;
		GUI2Show = 0;

		GUIVisible = 0;
		GUI2Visible = 0;

		redrawDisplay();
		if (listName == "Recordings") 
		{
			actionPending = 1;
		}
		else
		{
			actionPending = 11;
		}

		setRefreshTime(100);

        	ret = "true";
	}
	else if (input == "help")
	{
		GUIShow = 0;
		GUI2Show = 0;

		GUIVisible = 0;
		GUI2Visible = 0;

		redrawDisplay();

		actionPending = 1;

		setRefreshTime(100);

        	ret = "true";
	}
	else if (input == "option_green")
	{
		playItemURL(-1, 1);
		setRefreshTime(-1);

		executeScript("loadList");

		content = getStringArrayAt(favLinkArray , chIndex * 3 + 2 );
		content_original = getStringArrayAt(favLinkArray , chIndex * 3 + 1 );

		startVideo = 0;

		playItemUrl( content, 0 );

		returnOnVideoComplete = "yes";
		ret = "true";
	}

else if (input == "zoom")
{
	
	if ( aspectId == 0 )
	{
   		aspectId = 1;
		aspectName = "PanScan 4:3";
		setAspectRatio("$[PAN_SCAN_4_BY_3]");
	}
	else if ( aspectId == 1 )
	{
   		aspectId = 2;
		aspectName = "LetterBox 4:3";
		setAspectRatio("$[LETTER_BOX_4_BY_3]");
	}
	else if ( aspectId == 2 )
	{
   		aspectId = 3;
		aspectName = "WideScreen 16:9";
		setAspectRatio("$[WIDE_16_BY_9]");
	}
	else if ( aspectId == 3 )
	{
   		aspectId = 0;
		aspectName = "WideScreen 16:10";
		setAspectRatio("$[WIDE_16_BY_10]");
	}		
	
	GUI3Visible = 1;
	GUI3Show = 3000;
			
	ret = "true";
}
else if ( ( input == "menu" ) || ( input == "option_yellow" ) )
{
   	executeScript("loadList");

	if (listName == "Recordings") 
	{
		rss="<?= getMosUrl().'?page=' ?>rss_norecording";
   		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	}
	else
	{

	if ( rec == 0 )
	{
		rec = 1;
		recSec = 0;
		recMin = 0;
		executeScript("GetEPGName");
		result = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/download.php?downloadlink=" + urlEncode( content_original ) + "&amp;title=" + urlEncode( iptvTitle1 ) + "&amp;EPGName=" + urlEncode( EPGName ) );
		recFileName = getStringArrayAt( result, 0 );
	}
	else
	{
		executeScript("stopDownload");
		setRefreshTime(100);
	}

	}
   	redrawDisplay();
	ret = "true";
}
else if (input == "up" || input == "down" || input == "pageup")
{
	bb = 0;
	if ( isSeekable == 1 )
	{
		if (input == "pagedown" )
		{	 
	    	executeScript("switchToNext");
			bb = 1;
		}
		else if ( input == "pageup" )
		{	 
	    	executeScript("switchToPrev");
			bb = 1;
		}
	}

	if ( bb == 0 )
	{
	    executeScript("loadList");

		GUIShow = 10000;
		GUI2Show = 10000;

		GUIVisible = 1;
		GUI2Visible = 1;
	
		if ( input == "up" )
		{	 
		    executeScript("selectPrev");
		}
		else if ( input == "down" )
		{	 
		    executeScript("selectNext");
		}
		else if ( input == "pageup" )
		{	 
		    executeScript("selectPrevPage");
		}
		redrawDisplay();
	}

	ret = "true";
}
else if(input == "pagedown")  
{
	GUIShow = 0;
	GUI2Show = 0;

	GUIVisible = 0;
	GUI2Visible = 0;

	redrawDisplay();
	actionPending = 10;

	setRefreshTime(100);

        ret = "true";
}
else if(input == "video_frwd")  
{
	if ( isSeekable == 1 )
	{
		t = playElapsed - 60;
		if ( t &lt; 0 )
		{
			t = 0;
		}
		playElapsed = t;
		playAtTime( t ); 
		GUIVisible = 0;
		GUIShow = 0;		
	}
    ret = "true";
}
else if(input == "video_ffwd")  
{
	if ( isSeekable == 1 )
	{
		t = 60 + playElapsed;
		if ( t &gt;= playTotal )
		{
			t = playTotal - 5;
		}
		playElapsed = t;
		playAtTime( t ); 
		GUIVisible = 0;
		GUIShow = 0;		
	}
    ret = "true";
}
</handleUserInput>

<mediaDisplay name="threePartsView"
   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   showHeader="no"
   showDefaultInfo="no"
>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_01.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_02.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_03.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_04.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_05.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_06.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_07.png </idleImage>
  <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_08.png </idleImage>

  <previewWindow windowColor="0:0:0" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
  </previewWindow>

  <image redraw="yes" offsetXPC="0" offsetYPC="82" heightPC="18">
    /usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_MOVIE_INFO_BG.png
    <widthPC>
      <script>
	if ( ( GUIVisible + GUI2Visible ) != 0 ) 100;
	else 0;
      </script>
    </widthPC>
  </image>

  <image redraw="yes" offsetXPC="8" offsetYPC="89.6" heightPC="2">
    /usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_PLAYBACK_SEARCH_PROGRESSBAR_PLAY_UNFOCUS.png
    <widthPC>
      <script>
	50 * GUIVisible;
      </script>
    </widthPC>
  </image>

  <image redraw="yes" offsetXPC="8" offsetYPC="89.99" heightPC="1.5">
    /usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_PLAYBACK_SEARCH_PROGRESSBAR_PLAY.png
    <widthPC>
      <script>
	if ( isSeekable == 1 ) playElapsed * 50.0 / playTotal * GUIVisible;
	else curFullness / 2 * GUIVisible;
      </script>
    </widthPC>
  </image>

  <image redraw="yes" offsetYPC="89.9" heightPC="2">
    /usr/local/etc/mos/www/modules/iptvlist/images/seek.png
    <widthPC>
      <script>
	if ( seekPos != -1 ) 0.3 * GUIVisible;
	else 0;
      </script>
    </widthPC>
    <offsetXPC>
      <script>
	8 + 49.7 * seekPos;
      </script>
    </offsetXPC>
  </image>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '10', '11' ) ?>"
   offsetYPC="<?= detectFirmware( '71', '71.5' ) ?>"
   heightPC="30" fontSize="17" backgroundColor="-1:-1:-1" align="left" rolling="no" tailDots="yes">
    <script>
	iptvTitle;
    </script>
    <widthPC>
      <script>
	51 * GUIVisible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="0" offsetYPC="4" heightPC="10" fontSize="18" backgroundColor="0:0:0" align="center">
    <script>
	aspectName;
    </script>
    <widthPC>
      <script>
	100 * GUI3Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '7.7', '8' ) ?>"
   offsetYPC="84" heightPC="20" fontSize="10" backgroundColor="-1:-1:-1" foregroundColor="200:200:200">
    <script>
	if ( rec != 0 )
	{
		"";
	}
	else
	{
	       	if (playStatus == 2)
		{
			if (playElapsed == 0) "<?= getMsg( 'iptvBuffering' ) ?>";
			else "<?= getMsg( 'iptvPlaying' ) ?>";
		}
		else "<?= getMsg( 'iptvConnecting' ) ?>";
	}
    </script>
    <widthPC>
      <script>
	20 * GUIVisible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '7.7', '8' ) ?>"
   offsetYPC="84" heightPC="20" fontSize="10" backgroundColor="-1:-1:-1" foregroundColor="250:000:000">
    <script>
	if ( rec == 1 )
	{
		if ( recSec &lt; 10 ) "<?= getMsg( 'iptvRecording' ) ?>" + recMin + ":0" + recSec;
		else "<?= getMsg( 'iptvRecording' ) ?>" + recMin + ":" + recSec;
	}
	else if ( rec == 2 )
	{
		if ( recSec &lt; 6 ) recFileName;
		else
		{
			rec = 0;
			"";
		}	
	}
	else "";
    </script>
    <widthPC>
      <script>
	if ( rec != 0 ) 50;
	else 20 * GUIVisible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '38.4', '38.1' ) ?>"
   offsetYPC="84" heightPC="20" fontSize="10" backgroundColor="-1:-1:-1" foregroundColor="200:200:200" align="right">
    <script>
	timerString;
    </script>
    <widthPC>
      <script>
	20 * GUIVisible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="21" offsetYPC="84" heightPC="20" fontSize="10" backgroundColor="-1:-1:-1" foregroundColor="200:200:200" align="center">
    <script>
	s = secondToString( playTotal * seekPos );
    </script>
    <widthPC>
      <script>
	if ( seekPos == -1 ) 0;
	else 20 * GUIVisible;
      </script>
    </widthPC>
  </text>

  <image redraw="yes" offsetXPC="64.1" offsetYPC="84.3" heightPC="6">
    /usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_BW_LIST_NETWORK.png
    <widthPC>
      <script>
	if ( ( GUIVisible - GUI2Visible ) &gt; 0 ) 5 * GUIVisible;
	else 0;
      </script>
    </widthPC>
  </image>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '66', '69' ) ?>"
   offsetYPC="72.3" heightPC="30" backgroundColor="-1:-1:-1" foregroundColor="200:200:200" align="left">
    <script>
	if ( listName == "Recordings" ) "<?= getMsg( 'iptvRecordings' ) ?>";
	else "<?= getMsg( 'iptvIPTV' ) ?>";
    </script>
    <widthPC>
      <script>
	if ( ( GUIVisible - GUI2Visible ) &gt; 0 ) 100 * GUIVisible;
	else 0;
      </script>
    </widthPC>
    <fontSize>
      <script>
	if ( listName == "Recordings" ) 12;
	else 15;
      </script>
    </fontSize>
  </text>

  <image redraw="yes" offsetXPC="80.6" offsetYPC="85" heightPC="4.8">
    /usr/local/etc/mos/www/modules/iptvlist/images/pd.png
    <widthPC>
      <script>
	if ( ( GUIVisible - GUI2Visible ) &gt; 0 ) 2.7 * GUIVisible;
	else 0;
      </script>
    </widthPC>
  </image>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '81.2', '84.2' ) ?>"
   offsetYPC="<?= detectFirmware( '84.8', '85' ) ?>"
   heightPC="4.72" fontSize="10" foregroundColor="255:255:255" backgroundColor="-1:-1:-1" align="left" tailDots="no">
    <script>
	"<?= getMsg( 'iptvShowActionsMenu' ) ?>";
    </script>
    <widthPC>
      <script>
	if ( ( GUIVisible - GUI2Visible ) &gt; 0 ) 100 * GUIVisible;
	else 0;
      </script>
    </widthPC>
  </text>

  <text redraw="yes"
   offsetXPC="<?= detectFirmware( '64.4', '65.2' ) ?>"
   offsetYPC="91.4" heightPC="4.72" fontSize="10" foregroundColor="155:155:155" backgroundColor="-1:-1:-1" align="left" tailDots="yes" rolling="no">
    <script>
	if ( rec == 0 ) content_original;
	else recFileName;
    </script>
    <widthPC>
      <script>
	if ( ( GUIVisible - GUI2Visible ) &gt; 0 ) 31 * GUIVisible;
	else 0;
      </script>
    </widthPC>
  </text>

  <image redraw="yes" offsetXPC="64.7" offsetYPC="88" heightPC="4" useBackgroundSurface="yes">
    /usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_FAVORITS_FOC.png
    <widthPC>
      <script>
	34 * GUI2Visible;
      </script>
    </widthPC>
  </image>

  <text redraw="yes" offsetXPC="66" offsetYPC="84" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" color="250:250:250">
    <script>
	if ( chIndex2 &gt; 0 ) chIndex2;
	else favLinkCount;
    </script>
    <widthPC>
      <script>
	5 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="69" offsetYPC="84" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" tailDots="yes" color="250:250:250"> 
    <script>
	if ( chIndex2 &gt; 0 ) getStringArrayAt( favLinkArray, ( chIndex2 - 1 ) * 3 );
	else getStringArrayAt( favLinkArray, ( favLinkCount - 1 ) * 3 );
    </script>
    <widthPC>
      <script>
	30 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="66" offsetYPC="88" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" color="250:250:250">
    <script>
	0 + chIndex2 + 1;
    </script>
    <widthPC>
      <script>
	5 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="69" offsetYPC="88" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" tailDots="yes" color="250:250:250"> 
    <script>
	getStringArrayAt( favLinkArray, chIndex2 * 3 );
    </script>
    <widthPC>
      <script>
	30 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="66" offsetYPC="92" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" color="250:250:250">
    <script>
	ch = 0 + chIndex2 + 2;
	if ( ch &lt;= favLinkCount ) ch;
	else 1;
    </script>
    <widthPC>
      <script>
	5 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <text redraw="yes" offsetXPC="69" offsetYPC="92" heightPC="4" fontSize="12" backgroundColor="-1:-1:-1" tailDots="yes" color="250:250:250"> 
    <script>
	ch = 0 + chIndex2 + 1;
	if ( ch &lt; favLinkCount ) getStringArrayAt( favLinkArray, ch * 3 );
	else itemT = getStringArrayAt( favLinkArray, 0 );
    </script>
    <widthPC>
      <script>
	30 * GUI2Visible;
      </script>
    </widthPC>
  </text>

  <onUserInput>
	input = currentUserInput();
	ret = "false";
	print("1 Got input: ", input);

	executeScript("handleUserInput");

	ret;
  </onUserInput>

</mediaDisplay>

<channel>
	<title>iptvplay</title>
	<link>iptvplay.rss</link>

</channel>

</rss>
<?php

}

?>