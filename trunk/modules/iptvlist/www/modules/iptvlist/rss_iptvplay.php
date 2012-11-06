<?php

function rss_iptvplay_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<!--
	iptvlist created by Roman Lut aka hax.
-->

<initTimer>
	msCounter = 0;
	secCounter = 0;
	minCounter = 0;
	hourCounter = 0;
	timerString = "00:00:00";
	secString = "00";
	minString = "00";
	hourString = "00";
	seekPos = -1;
</initTimer>

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

<script>
/*
  	1 - enable sound fix
	0 -disable sound fix;
*/
	ENABLE_SOUND_FIX = 1;

	path = getStoragePath("tmp");
	path = path + "iptv_url.dat";
	tmp = readStringFromFile(path);

	iptvTitle1 = getStringArrayAt( tmp, 0 );
	iptvTitle = getStringArrayAt( tmp, 1 );
	content_original = getStringArrayAt( tmp, 2 );
	content = getStringArrayAt( tmp, 3 );
	listName = getStringArrayAt( tmp, 4 );

    progressbarStatus = 0;
	playStatus=0;
    startVideo = 0;
    bkcurFullness = 0;
    bkcurFullnessCount = 0;
    bufFullness = 0;
    bBufferFull = 0;

    bkcurFullnessCountStopInd = 10;
    bufStopIndicator = 6;
    bufPoolUnit = 1048576;
    bufPoolCount = 2;
    bufPoolSize = bufPoolUnit * bufPoolCount;

	GUIVisible = 1;
	GUI2Visible = 0;
	GUI3Visible = 0;
	GUIShow = 4000;
	GUI2Show = 0;
	GUI3Show = 0;

	period = 1000;

   	executeScript("initTimer");

	chIndex2 = 0;

	actionPending = 0;

	pendingUnpause = 0;

	rec = 0;
	recSec = 0;
	recMin = 0;

	aspectId = 0;
    aspectName = "";

</script>

<ReleaseFavList>
    favLinkArray = null;
    favLinkCount = 0;
</ReleaseFavList>

<BuildFavList>
    	executeScript("ReleaseFavList");
/*    
		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/loadlist.php" );
*/
		path = "/tmp/iptv_playlist.txt";

		favLinkArray = readStringFromFile( path );

		favLinkCount = 0 + getStringArrayAt( favLinkArray, 0 );
		favLinkArray = deleteStringArrayAt( favLinkArray, 0 );

		listName = getStringArrayAt( favLinkArray, 0 );
		favLinkArray = deleteStringArrayAt( favLinkArray, 0 );

    	redrawDisplay();
</BuildFavList>

<readIndex>
	path = getStoragePath("tmp");
	path = path + "iptv_index.dat";

	chIndex = 0 + readStringFromFile(path);

	if ( chIndex &lt; 0 )
	{
		chIndex = favLinkCount - 1;
	}

	if ( chIndex &gt; ( favLinkCount - 1 ) )
	{
		chIndex = 0;
	}

	chIndex2 = chIndex;
</readIndex>

<writeIndex>
	path = getStoragePath("tmp");
	path = path + "iptv_index.dat";
	writeStringToFile(path,chIndex);
</writeIndex>

<loadList>

	if ( favLinkArray == null )
	{
	    executeScript("BuildFavList");
	    executeScript("readIndex");
	}

</loadList>

<playIndex>
    executeScript("stopDownload");

      playItemURL(-1, 1);
      setRefreshTime(-1);

	content = getStringArrayAt(favLinkArray , chIndex * 3 + 2);
	content_original = getStringArrayAt(favLinkArray , chIndex * 3 + 1);
	
	iptvTitle1 = getStringArrayAt(favLinkArray , chIndex * 3 );
	
	iptvTitle = getStringArrayAt(favLinkArray , chIndex * 3 );
	chIndex1 = 0 + chIndex + 1;
    iptvTitle = " " + chIndex1 + ". " + iptvTitle;

    progressbarStatus = 0;
	playStatus=0;
    startVideo = 0;
    bkcurFullness = 0;
    bkcurFullnessCount = 0;
    bufFullness = 0;
    bBufferFull = 0;

    bkcurFullnessCountStopInd = 10;
    bufStopIndicator = 6;
    bufPoolUnit = 1048576;
    bufPoolCount = 2;
    bufPoolSize = bufPoolUnit * bufPoolCount;

     startVideo = 1;
     pauseVideo = -1;
	 bufProgress = "";
     curFullness = 0;
     EOFFlag = 0;
     progress = "";

     setRefreshTime(100);
	period=100;

	GUIVisible = 1;
	GUIShow = 4000;

	GUI2Visible = 0;
	GUI2Show = 0;

   	executeScript("initTimer");
	
	pendingUnpause = 0;

</playIndex>


<switchToNext>

    	executeScript("loadList");
  
	chIndex += 1;

	if ( chIndex &gt; ( favLinkCount - 1 ) )
	{
		chIndex = 0;
	}

    	executeScript("writeIndex");

    	executeScript("playIndex");

	chIndex2 = chIndex;
</switchToNext>

<switchToPrev>

    	executeScript("loadList");

	chIndex -= 1;

	if ( chIndex &lt; 0 )
	{
		chIndex = favLinkCount - 1;
	}

    	executeScript("writeIndex");

    	executeScript("playIndex");

	chIndex2 = chIndex;
</switchToPrev>

<selectNext>
	chIndex2 += 1;

	if ( chIndex2 &gt; ( favLinkCount - 1 ) )
	{
		chIndex2 = 0;
	}
</selectNext>

<selectPrev>
	chIndex2 -= 1;

	if ( chIndex2 &lt; 0 )
	{
		chIndex2 = favLinkCount - 1;
	}
</selectPrev>

<selectNextPage>
	chIndex2 += 10;

	while ( chIndex2 &gt;= favLinkCount )
	{
		chIndex2 -= favLinkCount;
	}
</selectNextPage>

<selectPrevPage>
	chIndex2 -= 10;

	while ( chIndex2 &lt; 0 )
	{
		chIndex2 += favLinkCount;
	}
</selectPrevPage>

<onEnter>
     startVideo = 1;
     pauseVideo = -1;
	 bufProgress = "";
     curFullness = 0;
     EOFFlag = 0;
     progress = "";
     setRefreshTime(100);
	period=100;

	SetScreenSaverStatus("no");
	screenSaverPush=getCurrentSetting("$[SCREEN_SAVER_TIMING]");
	setScreenSaverTiming("$[OFF]");

</onEnter>

<onExit>
      playItemURL(-1, 1);
      setRefreshTime(-1);
	    executeScript("stopDownload");
	
	SetScreenSaverStatus("yes");
	setScreenSaverTiming( screenSaverPush );
	tearDownPlaybackFlow();
</onExit>

<onRefresh>
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
			
		if ( GUIShow &gt; 0 )
		{
			GUIShow -= period;
		}
		else
		{
			GUIVisible = 0;
			seekPos = -1;
		}

		if ( GUI2Show &gt; 0 )
		{
			GUI2Show -= period;
		}
		else
		{
			GUI2Visible = 0;
			chIndex2 = chIndex;
		}

		if ( GUI3Show &gt; 0 )
		{
			GUI3Show -= period;
		}
		else
		{
			GUI3Visible = 0;
		}

		if ( actionPending == 1 )
		{
			actionPending = 0;	
			setRefreshTime(1000);
			period=1000;

			if ( listName == "Recordings" )
			{
				rss="<?= getMosUrl().'?page=' ?>rss_help3";
			}
			else
			{
				rss="<?= getMosUrl().'?page=' ?>rss_help2";
			}
    	    		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
		}
		else if ( actionPending == 10 )
		{
			actionPending = 0;
			setRefreshTime(1000);
			period=1000;

			if ( listName != "Recordings" )
			{
				rss="<?= getMosUrl().'?page=' ?>rss_sidemenu2";
			}
			else
			{
				rss="<?= getMosUrl().'?page=' ?>rss_sidemenu3";
			}
	      		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	
			if ( mret != "" )
			{
		    		input = mret;
		    		print ("[My Favorites - input()] get input:", userInput);
		    		executeScript("handleUserInput");
			}
		}

		msCounter += period;

		while ( msCounter &gt; 1000 )
		{
			msCounter -= 1000;

			secCounter += 1;
			if ( secCounter == 60 )
			{
				setRefreshTime(1000);
				period=1000;

				secCounter = 0;

				minCounter += 1;
				if ( minCounter == 60 )
				{

					minCounter = 0;
					hourCounter += 1;

					if ( hourCounter &lt; 10 )
					{
						hourString = "0" + hourCounter;
					}
					else
					{
						hourString = hourCounter;
					}
				}

				if ( minCounter &lt; 10 )
				{
					minString = "0" + minCounter;
				}
				else
				{
					minString = minCounter;
				}
			}

			if ( secCounter &lt; 10 )
			{
				secString = "0" + secCounter;
			}
			else
			{
				secString= secCounter;
			}

			recSec += 1;
			if ( recSec == 60 )
			{
				recSec = 0;

				recMin += 1;
			}

		}

		timerString = hourString + ":" + minString + ":" + secString;

		videoProgress = getPlaybackStatus();

		isSeekable = videoIsSeekable();
		playElapsed = getStringArrayAt(videoProgress,0);
		videoPlayLength = getStringArrayAt(videoProgress,1);

		if ( listName != "Recordings" )
		{
			isSeekable = "";
		}

		if ( videoPlayLength == 100 )
		{
			isSeekable = "";
		}


		if ( isSeekable == 1 )
		{
			sound_fix_skip = 1;

			timerString = secondToString( playElapsed ) + " / " + secondToString( videoPlayLength );
		}

		if (startVideo == 1)
		{
			print("I am moviePlayback onEnter !! startVideo: ", startVideo);
			playItemURL(-1, 1);
			
			if (bufPoolCount &lt; 3)
			{
				playItemURL(content, 3, bufPoolUnit, "mediaDisplay", "previewWindow");
			}
			else
			{
				newURL = content+" fileCache=/tmp/videoCachefile cacheSize="+bufPoolCount;
				print("newURL:", newURL);
				playItemURL(newURL, 3, bufPoolUnit, "mediaDisplay", "previewWindow");
			}
			
			setRefreshTime(300);
			period = 300;
			pauseVideo = 0;
			startVideo = 2;
			sound_fix_skip = 0;

		}
		else if (startVideo &gt; 1)
		{
			bufMax = getStringArrayAt(videoProgress, 1);
			bufFullness = getStringArrayAt(videoProgress, 2);
			playStatus = getStringArrayAt(videoProgress, 3);
			print("Video status !!!!", videoProgress);

			if (playStatus == 0)
			{
				postMessage("return");
			}
			else if (playStatus &gt; 0)
			{
				if (progressbarStatus == 0)
				{
					bkcurFullness = 0;
					bkcurFullnessCount = 0;
					bBufferFull = 0;
					progressbarStatus = 1;
				}
				
				if (progressbarStatus == 3)
				{
					bufOffset = bufMax - playElapsed;
					if (bufOffset &gt; 0)
					{
						if (bufFullness &lt; bufStopIndicator &amp;&amp; bufOffset &gt; bufStopIndicator)
						{
							postMessage("video_pause");
							bkcurFullness = 0;
							bkcurFullnessCount = 0;
							progressbarStatus = 1;
							bBufferFull = 0;
						}
					}


					if ( ENABLE_SOUND_FIX == 1 )
					{
						if ( sound_fix_skip == 0 )
						{
							if ( playElapsed == 1 )
							{  	
								if ( pendingUnpause == 0 )
								{
									pendingUnpause = 500;
									postMessage("video_pause");
									pauseVideo = 1;
								}
							}
						}
					}

				}
			 }
		}

		
		if (progressbarStatus == 1)
		{
			GUIVisible = 1;
			GUIShow = 1000;

			bufProgress = getCachedStreamDataSize(0, bufPoolSize);
			curFullness = getStringArrayAt(bufProgress, 0);
			EOFFlag = getStringArrayAt(bufProgress,3);
	    
		    if (bkcurFullness == curFullness &amp;&amp; bkcurFullness!=0)
		    	bkcurFullnessCount = bkcurFullnessCount+1;
		    if (bkcurFullness != curFullness)
		    {
		        bkcurFullness = curFullness;
		        bkcurFullnessCount = 0;
		    }
		        
		    if (curFullness &gt; 90)
			{
		      progressbarStatus = 2;
			}
			else
			{
				if ( secCounter &gt; 2)
				{
		   	 		if (curFullness &gt; 5)
					{
		      			progressbarStatus = 2;
						sound_fix_skip = 1;
					}
				}
			}

		    if (bkcurFullnessCount &gt; bkcurFullnessCountStopInd)
		    {
		      bBufferFull=1;
		      progressbarStatus = 2;
		      bkcurFullnessCount = 0;
		      bkcurFullness = 0;
		      print("stop buffer !!");
		    }

		    if (EOFFlag == 1) 
			{
		      if (curFullness &gt; 0)
		          progressbarStatus = 2;
		 	}
		 	else if (EOFFlag == 2) 
			{
		    	print("Network is down!!!!!");
		    	setRefreshTime(-1);
		    	postMessage("return");
		  	}

			progress = curFullness + "&#xA;100&#xA;0";
		}
		else if (progressbarStatus == 2)
		{
			setRefreshTime(1000);
			period=1000;
		  	bufStopIndicator = bufFullness / 4;

			setVoutDisplay("true");
			postMessage("video_play");
			progressbarStatus = 3;
			bBufferFull = 0;
		}

print("end");

</onRefresh>

<handleUserInput>
if (input=="return")
{
	if (startVideo==1)
	{
		print("Video Stop>>>>>>>>>>>>>");
		playItemURL(-1,1);
		
	}
	ret = "false";
}
else if (input == "setup" || input == "guide")
{
	pauseVideo = 1;
	ret = "false";
}
else if (input == "video_stop")
{
	GUIVisible = 0;
	GUI2Visible = 0;
	GUI3Visible = 0;
	print("Video Stop>>>>>>>>>>>>>");
	playItemURL(-1,1);
	startVideo = 0;
	postMessage("return");
	ret = "true";
}
else if(input == "video_completed")
{
	print("Video Completed>>>>>>>>>>");
	playItemURL(-1,1);

	if ( returnOnVideoComplete == "yes" )
	{
		postMessage("return");
	}

	ret = "true";
}
else if(input=="video_play"&amp;&amp;pauseVideo==1)
	{
		print("video Resume>>>>>>>>");
		pauseVideo = 0;
		        
		ret = "false";
	}
else if (input=="video_play"&amp;&amp;pauseVideo==0)
	{
		print("video Start to Pause>>>>>>>>");
		pauseVideo = 1;
		postMessage("video_pause");
		ret = "true";
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
			seekPos = playElapsed * 1.0 / videoPlayLength;
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
					playAtTime( videoPlayLength * seekPos ); 
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
    	executeScript("writeIndex");
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
		actionPending = 1;

		setRefreshTime(100);
		period=100;

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
		period=100;

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
		result = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/download.php?downloadlink=" + urlEncode( content_original ) + "&amp;title=" + urlEncode( iptvTitle1 ) );
		recFileName = getStringArrayAt( result, 0 );
/*
		setRefreshTime(5000);
		period=5000;
*/

/*
      playItemURL(-1, 1);
      setRefreshTime(-1);
	  content_original = recFileName;
    progressbarStatus = 0;
	playStatus=0;
    startVideo = 0;
    bkcurFullness = 0;
    bkcurFullnessCount = 0;
    bufFullness = 0;
    bBufferFull = 0;

    bkcurFullnessCountStopInd = 10;
    bufStopIndicator = 6;
    bufPoolUnit = 1048576;
    bufPoolCount = 2;
    bufPoolSize = bufPoolUnit * bufPoolCount;

     startVideo = 1;
     pauseVideo = -1;
	 bufProgress = "";
     curFullness = 0;
     EOFFlag = 0;
     progress = "";

     setRefreshTime(1000);
	period=1000;

	GUIVisible = 1;
	GUIShow = 4000;

	GUI2Visible = 0;
	GUI2Show = 0;

   	executeScript("initTimer");
	
	pendingUnpause = 0;

*/


	}
	else
	{
	    executeScript("stopDownload");
		setRefreshTime(1000);
		period=1000;

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
	period=100;

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
		if ( t &gt;= videoPlayLength )
		{
			t = videoPlayLength - 5;
		}
		playElapsed = t;
		playAtTime( t ); 
		GUIVisible = 0;
		GUIShow = 0;		
	}
    ret = "true";
}
</handleUserInput>

<mediaDisplay name=threePartsView >

        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_01.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_02.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_03.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_04.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_05.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_06.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_07.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_08.png </idleImage>


<previewWindow windowColor=0:0:0 offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>

</previewWindow>

<image redraw="yes" offsetXPC=0 offsetYPC=82 heightPC=18>
		/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_MOVIE_INFO_BG.png
	<widthPC>
		<script>
			if ( ( GUIVisible + GUI2Visible ) != 0 )
			{
				s= 100;
			}
			else
			{
				s = 0;
			}
			s;
		</script>
	</widthPC>
</image>

<image redraw="yes" offsetXPC=8 offsetYPC=89.6 heightPC=2>
	/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_PLAYBACK_SEARCH_PROGRESSBAR_PLAY_UNFOCUS.png
	<widthPC>
		<script>
			s = 50 * GUIVisible;
			s;
		</script>
	</widthPC>
</image>

<image redraw="yes" offsetXPC=8 offsetYPC=89.99 heightPC=1.5>
	/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_PLAYBACK_SEARCH_PROGRESSBAR_PLAY.png
	<widthPC>
		<script>
			if ( isSeekable == 1 )
			{
				s = playElapsed * 50.0 / videoPlayLength * GUIVisible;
			}
			else
			{
				s = curFullness / 2 * GUIVisible;
			}
			s;
		</script>
	</widthPC>
</image>

<image redraw="yes" offsetYPC=89.9 heightPC=2>
	/usr/local/etc/mos/www/modules/iptvlist/images/seek.png
	<widthPC>
		<script>
			if ( seekPos != -1 )
			{
				s = 0.3 * GUIVisible;
			}
			else
			{
				s = 0;
			}
			s;
		</script>
	</widthPC>
	<offsetXPC>
		<script>
			s = 8 + 49.7 * seekPos;
			s;
		</script>
	</offsetXPC>
</image>

<text redraw="yes" offsetXPC=10 offsetYPC=71 heightPC=30 fontSize=17 backgroundColor=-1:-1:-1 align="left" rolling=no tailDots=yes>
  <script>
	iptvTitle;
  </script>
	<widthPC>
		<script>
			s = 53 * GUIVisible;
			s;
		</script>
	</widthPC>
</text>

<text redraw="yes" offsetXPC=0 offsetYPC=4 heightPC=10 fontSize=18 backgroundColor=0:0:0 align="center">
  <script>
	aspectName;
  </script>
	<widthPC>
		<script>
			s = 100 * GUI3Visible;
			s;
		</script>
	</widthPC>
</text>


<text redraw="yes" offsetXPC=7.7 offsetYPC=84 heightPC=20 fontSize=10 backgroundColor=-1:-1:-1 foregroundColor=200:200:200>
  <script>
	if ( rec != 0 )
	{
		showstr = "";
	}
	else
	{
	       	if (playStatus == 2)
		{
			if (playElapsed == 0)
			{
				showstr = "<?= getMsg( 'iptvBuffering' ) ?>";
			}
			else
			{
				showstr = "<?= getMsg( 'iptvPlaying' ) ?>";
			}
		}
		else
		{
			showstr = "<?= getMsg( 'iptvConnecting' ) ?>";
		}
	}
	showstr;
  </script>
	<widthPC>
		<script>
			s = 20 * GUIVisible;
			s;
		</script>
	</widthPC>
</text>

<text redraw="yes" offsetXPC=7.7 offsetYPC=84 heightPC=20 fontSize=10 backgroundColor=-1:-1:-1 foregroundColor=250:000:000>
  <script>
	if ( rec == 1 )
	{
		if ( recSec &lt; 10 )
		{
			showstr = "<?= getMsg( 'iptvRecording' ) ?>" + recMin + ":0" + recSec;
		}
		else
		{
			showstr = "<?= getMsg( 'iptvRecording' ) ?>" + recMin + ":" + recSec;
		}
	}
	else if ( rec == 2 )
	{
		if ( recSec &lt; 6 )
		{
			showstr = recFileName;
		}
		else
		{
			rec = 0;
			showstr = "";
		}	
	}
	else
	{
		showstr = "";
	}
	showstr;
  </script>
	<widthPC>
		<script>
			s = 20 * GUIVisible;
			if ( rec != 0 )
			{
				s = 50;
			}
			s;
		</script>
	</widthPC>
</text>


<text redraw="yes" offsetXPC=38.4 offsetYPC=84 heightPC=20 fontSize=10 backgroundColor=-1:-1:-1 foregroundColor=200:200:200 align="right">
  <script>
	timerString;
  </script>
	<widthPC>
		<script>
			s = 20 * GUIVisible;
			s;
		</script>
	</widthPC>
</text>

<text redraw="yes" offsetXPC=21 offsetYPC=84 heightPC=20 fontSize=10 backgroundColor=-1:-1:-1 foregroundColor=200:200:200 align="center">
  <script>
	s = secondToString( videoPlayLength * seekPos );
  </script>
	<widthPC>
		<script>
			s = 20 * GUIVisible;
			if ( seekPos == -1 )
			{
				s = 0;
			}
			s;
		</script>
	</widthPC>
</text>


<image redraw="yes" offsetXPC=64.1 offsetYPC=84.3 heightPC=6>
	/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_BW_LIST_NETWORK.png
	<widthPC>
		<script>
			if ( ( GUIVisible - GUI2Visible ) &gt; 0 )
			{
				s = 5 * GUIVisible;
			}
			else
			{
				s = "";
			}
			s;
		</script>
	</widthPC>
</image>

<text redraw="yes" offsetXPC=68.5 offsetYPC=72.3 heightPC=30 fontSize=15 backgroundColor=-1:-1:-1 foregroundColor=200:200:200 align="left">
  <script>
	if ( listName == "Recordings" )
	{
		s ="<?= getMsg( 'iptvRecordings' ) ?>";
	}
	else
	{
    	s="<?= getMsg( 'iptvIPTV' ) ?>";
	}
	s;
  </script>
	<widthPC>
		<script>
			if ( ( GUIVisible - GUI2Visible ) &gt; 0 )
			{
				s= 100 * GUIVisible;
			}
			else
			{
				s = "";
			}
			s;
		</script>
	</widthPC>
</text>

<image redraw="yes" offsetXPC=80.6 offsetYPC=85 heightPC=4.8>
	/usr/local/etc/mos/www/modules/iptvlist/images/pd.png
	<widthPC>
		<script>
			if ( ( GUIVisible - GUI2Visible ) &gt; 0 )
			{
				s = 2.7 * GUIVisible;
			}
			else
			{
				s = "";
			}
			s;
		</script>
	</widthPC>
</image>

<text redraw="yes" offsetXPC=83.5 offsetYPC=84.8 heightPC=4.72 fontSize=10 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align="left" tailDots=no>
	<script>
		    hint = "<?= getMsg( 'iptvShowActionsMenu' ) ?>";
			hint;
  	</script>
	<widthPC>
		<script>
			if ( ( GUIVisible - GUI2Visible ) &gt; 0 )
			{
				s= 100 * GUIVisible;
			}
			else
			{
				s = "";
			}
			s;
		</script>
	</widthPC>
</text>

<text redraw="yes" offsetXPC=64.4 offsetYPC=91.4 heightPC=4.72 fontSize=10 foregroundColor=155:155:155 backgroundColor=-1:-1:-1 align="left" tailDots=yes rolling=no>
	<script>
		if ( rec == 0 )
		{
			itemT = content_original;
		}
		else
		{
			itemT = recFileName;
		}
		itemT;
  	</script>
	<widthPC>
		<script>
			if ( ( GUIVisible - GUI2Visible ) &gt; 0 )
			{
				s= 36 * GUIVisible;
			}
			else
			{
				s = "";
			}
			s;
		</script>
	</widthPC>
</text>


<image redraw=yes offsetXPC=64.7 offsetYPC=88 heightPC=4 useBackgroundSurface=yes>
	<script>
                	"/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_FAVORITS_FOC.png";
    </script>
	<widthPC>
		<script>
			s= 34 * GUI2Visible;
			s;
		</script>
	</widthPC>
</image>

<text redraw=yes offsetXPC=66 offsetYPC=84 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 color=250:250:250>
            <script>
				if ( chIndex2 &gt; 0 )
				{
	                itemT = chIndex2;
				}
				else
				{
					itemT = favLinkCount;
				}
                itemT;
            </script>
	<widthPC>
		<script>
			s= 5 * GUI2Visible;
			s;
		</script>
	</widthPC>
</text>

<text redraw=yes offsetXPC=69 offsetYPC=84 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 tailDots=yes color=250:250:250> 
            <script>
				if ( chIndex2 &gt; 0 )
				{
	                itemT = getStringArrayAt( favLinkArray, ( chIndex2 - 1 ) * 3 );
				}
				else
				{
					itemT = getStringArrayAt( favLinkArray, ( favLinkCount - 1 ) * 3 );
				}
                itemT;
            </script>
	<widthPC>
		<script>
			s= 30 * GUI2Visible;
			s;
		</script>
	</widthPC>
</text>

<text redraw=yes offsetXPC=66 offsetYPC=88 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 color=250:250:250>
            <script>
                itemT = 0+ chIndex2 + 1;
                itemT;
            </script>
	<widthPC>
		<script>
			s= 5 * GUI2Visible;
			s;
		</script>
	</widthPC>
</text>

<text redraw=yes offsetXPC=69 offsetYPC=88 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 tailDots=yes color=250:250:250> 
            <script>
                itemT = getStringArrayAt( favLinkArray, chIndex2 * 3 );
                itemT;
            </script>
	<widthPC>
		<script>
			s= 30 * GUI2Visible;
			s;
		</script>
	</widthPC>
</text>

<text redraw=yes offsetXPC=66 offsetYPC=92 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 color=250:250:250>
            <script>
				ch = 0 + chIndex2 + 2;
				if ( ch &lt;= favLinkCount )
				{
	                itemT = ch;
				}
				else
				{
					itemT = 1;
				}

                itemT;
            </script>
	<widthPC>
		<script>
			s= 5 * GUI2Visible;
			s;
		</script>
	</widthPC>
</text>

<text redraw=yes offsetXPC=69 offsetYPC=92 heightPC=4 fontSize=12 backgroundColor=-1:-1:-1 tailDots=yes color=250:250:250> 
            <script>
				ch = 0 + chIndex2 + 1;
				if ( ch &lt; favLinkCount )
				{
	                itemT = getStringArrayAt( favLinkArray, ch * 3 );
				}
				else
				{
					itemT = getStringArrayAt( favLinkArray, 0 );
				}
                itemT;
            </script>
	<widthPC>
		<script>
			s= 30 * GUI2Visible;
			s;
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
