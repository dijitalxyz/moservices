<?php
	class rssMyPlayerView extends rssSkinPlayer
{
	function showOnUserInput()
	{
?>
<onUserInput>
	input = currentUserInput();
	writeStringToFile("/tmp/lastkey.dat", input);
	ret = "true";
	str = null;
	if (input=="return") {
		if (playStatus == 2 &amp;&amp; player_state == "video_play") {
			player_action = input;
			setRefreshTime(1);
		} else {
			if (playStatus == 2) {
				playItemURL(-1,1);
			}
			ret = "false";
		}
	} else if (input == "guide" || input == "setup") {
		setVoutDisplay("false", 0, 0);
		pauseVideo = 1;
		ret = "false";
	} else if(input=="video_play"&amp;&amp;pauseVideo==1) {
		setVoutDisplay("true");
		pauseVideo = 0;
		ret = "false";
	} else if (input=="video_play"&amp;&amp;pauseVideo==0) {
		pauseVideo = 1;
		postMessage("video_pause");
	} else if (input=="video_pause") {
		pauseVideo = 1;
		ret = "false";
	} else if (input == "display" || input == "video_stop") {
		player_action = input;
		setRefreshTime(1);	
	} else if (input == "enter" &amp;&amp; track_time != null) {
		player_action = "enter";
		setRefreshTime(1);
	} else if(input == "video_completed") {
		playItemURL(-1,1);
	} else if (input == "video_volume_up" || input == "video_volume_down") {
		ret = "false";
	} else if (input == "zoom") {
		currentAspectRatio = getCurrentSetting("$[ASPECT_RATIO]");
		if (null == originalAspectRatio) { originalAspectRatio = currentAspectRatio;}
		if (currentAspectRatio=="$[PAN_SCAN_4_BY_3]") {	setAspectRatio("$[LETTER_BOX_4_BY_3]");
		} else if (currentAspectRatio=="$[LETTER_BOX_4_BY_3]") { setAspectRatio("$[WIDE_16_BY_9]");
		} else if (currentAspectRatio=="$[WIDE_16_BY_9]") { setAspectRatio("$[WIDE_16_BY_10]");
		} else if (currentAspectRatio=="$[WIDE_16_BY_10]") {setAspectRatio("$[PAN_SCAN_4_BY_3]");}
	} else if (input == "pagedown" || input == "pageup") {
		player_action = input;
		setRefreshTime(1);
	}
	ret;
</onUserInput>
<?php
	}
	
	function showScripts()
	
	{
?>
	<onEnter>
		nnn = 0;
		previewPos = 0;
		$nextprev = 2;
		itemSize = 0;
		countkol = 0; timeStamp = 0; startVideo = 0; currentProtocol = null;
		itemTitleArray = null;
		itemURLArray = null;
		itemACTArray = null;
		itemSize = 0;
		dlok = getPageInfo( "itemCount");
		if (dlok != null || dlok != 0) {
			iSize = dlok; print("itemSize = ", iSize);
			if (iSize &gt; 0) {
				itemCount = 0;
				itemTitleArray = getItemInfoarray("title");
				itemURLArray  = getItemInfoarray("link");
				itemACTArray = getItemInfoarray("act");
				itemimageArray = getItemInfoarray("image");
				act = getStringArrayAt(itemACTArray, 0);
				url = getStringArrayAt(itemURLArray, 0);
				title = getStringArrayAt(itemTitleArray, 0); 
				preview = getStringArrayAt(itemimageArray, 0); 
				itemSize = itemSize + 1;
			}
		}	
		if(itemSize == 0)
			postMessage("return");
		else {
			player_action = null;
			counterInfo = -1;
			time_elapsed = null;
			time_all = null;
			track_time = null;
			pauseVideo = -1;
			setRefreshTime(100);
		}
		player_action = null;
	</onEnter>

	<onExit>
		playItemURL(-1, 1);
		setRefreshTime(-1);
	</onExit>
<onRefresh>
	vidProgress = getPlaybackStatus();
	bufProgress = getCachedStreamDataSize(0, 262144);
	playElapsed = getStringArrayAt(vidProgress, 0);
	playTime = getStringArrayAt(vidProgress, 1);
	playStatus = getStringArrayAt(vidProgress, 3);
	print("Media status =", vidProgress);
	
	if (player_action != null) {
		setRefreshTime(1000);
		if (player_action == "display") {
			if (counterInfo &gt;= 0) {
				updatePlaybackProgress("delete", "mediaDisplay", "progressBar");
				counterInfo = -1;
				track_time = null;
			} else {
				counterInfo = 5;
			}
		} else if (player_action == "left" &amp;&amp; playStatus != 0) {
			if (counterInfo == -1) {
				track_time = playElapsed;
			} else {
				track_time -= 30;
				if(track_time &lt; 0) {
					track_time = 0;
				}
			}
			counterInfo = 5;
		} else if (player_action == "right" &amp;&amp; playStatus != 0) {
			if (counterInfo == -1) {
				track_time = playElapsed;
			} else {
				track_time -= -30;
				if(track_time &gt; playTime) {
					track_time = playTime;
				}
			}
			counterInfo = 5;
		} else if (player_action == "enter" &amp;&amp; track_time != null &amp;&amp; playStatus != 0) {
			fromstart = track_time;
			playAtTime(track_time);
			counterInfo = 5;
		} else if (player_action == "goto" &amp;&amp; player_state == "video_play") {
			if (player_action_percent &gt;= 0 &amp;&amp; player_action_percent &lt; 100) {
				at_time = Integer(player_action_percent * playTime / 100);
				fromstart = at_time;
				playAtTime(at_time);
			}
		} else if (player_action == "video_stop" || player_action == "return") {
				playItemURL(-1, 1);
				act = "stop";
				postMessage("return");
		} else if (player_action == "pagedown") {
			urln = "<?= getMosUrl().'?page=serialutv_next' ?>" + "&amp;next=1"+"&amp;id=" + url;
			urln = getUrl(urln);
			title1 = getStringArrayAt(urln, 0); 
			preview = getStringArrayAt(urln, 1);
			urln = getStringArrayAt(urln, 2);
			if (urln!="none") {
				title = title1; url = urln; nextprev=-1; time_all = null; playItemURL(-1, 1);
			}
		} else if (player_action == "pageup") {
			urln = "<?= getMosUrl().'?page=serialutv_next' ?>" + "&amp;next=-1"+"&amp;id=" + url;
			urln = getUrl(urln);
			title1 = getStringArrayAt(urln, 0); 
			preview = getStringArrayAt(urln, 1);
			urln = getStringArrayAt(urln, 2);
			if (urln!="none") {
				title = title1; url = urln; nextprev=-1; time_all = null; playItemURL(-1, 1);
			}
		}
		player_action = null;
	}
	
	if(playStatus == 0) {
		setRefreshTime(1000);
		currentPlay = 0;
		
		if (nextprev==0) { 
			urln = "<?= getMosUrl().'?page=serialutv_next' ?>" + "&amp;next=1"+"&amp;id=" + url;
			urln = getUrl(urln);
			title = getStringArrayAt(urln, 0); 
			preview = getStringArrayAt(urln, 1);
			urln = getStringArrayAt(urln, 2);
			url = urln; 
		}
		
		if (currentPlay &gt;= itemSize || url == "none") {
			postMessage("return");
		} else {
			counterInfo = 5;			
			
			if (act != "play") {
				url = "<?= getMosUrl().'?page=' ?>" + act + "&amp;id=" + url;
				url = getUrl(url);
			}
			
			loading = "true";
			if (loading == "true") {
					writeStringToFile("/tmp/alt_start_url.dat", url);
					url = "<?= getMosUrl().'?page=serialutv_iddetect'?>" + "&amp;id=" + url;
					url = getURL(url);
					playItemURL(url, 10, "mediaDisplay", "previewWindow");
					nextprev=0; previewPos=0;
				}
			}
			pauseVideo = 0;
		}
	
	if (counterInfo &gt; 0) {
		
		if (playStatus == 2) {
			x = Integer(playElapsed / 60);
			h = Integer(playElapsed / 3600);
			s = playElapsed - (x * 60);m = x - (h * 60);
			if(h &lt; 10) time_elapsed = "0" + sprintf("%s:", h); else time_elapsed = sprintf("%s:", h);
			if(m &lt; 10)  time_elapsed += "0";time_elapsed += sprintf("%s:", m);
			if(s &lt; 10)  time_elapsed += "0";time_elapsed += sprintf("%s", s);
			print("time_elapsed =", time_elapsed);
			if(time_all == null) {
				x = Integer(playTime / 60);
				h = Integer(playTime / 3600);
				s = playTime - (x * 60);m = x - (h * 60);
				if(h &lt; 10) time_all = "0" + sprintf("%s:", h); else time_all = sprintf("%s:", h);
				if(m &lt; 10)  time_all += "0";time_all += sprintf("%s:", m);
				if(s &lt; 10)  time_all += "0";time_all += sprintf("%s", s);
				print("time_all =", time_all);
			}
		}
		if (track_time != null) {
			x = Integer(track_time / 60);
			h = Integer(track_time / 3600);
			s = track_time - (x * 60);m = x - (h * 60);
			if(h &lt; 10) time_user = "0" + sprintf("%s:", h); else time_user = sprintf("%s:", h);
			if(m &lt; 10)  time_user += "0";time_user += sprintf("%s:", m);
			if(s &lt; 10)  time_user += "0";time_user += sprintf("%s", s);
		}
		counterInfo -= 1;
		updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
		time_all = null;
	} else if (counterInfo == 0) {
		updatePlaybackProgress("delete", "mediaDisplay", "progressBar");
		counterInfo = -1;
		time_all = null;
		track_time = null;
	}
</onRefresh>

<?php
	}

	function showDisplay()
	{
?>
	<mediaDisplay name=onePartView
		viewAreaXPC=0
		viewAreaYPC=0
		viewAreaWidthPC=100
		viewAreaHeightPC=100

		sideColorLeft=-1:-1:-1
		sideColorRight=-1:-1:-1

        itemPerPage=5

		itemImageXPC = 4.1
		itemImageYPC = 7
		itemImageWidthPC = 91.79
		itemImageHeightPC = 42.75

		itemXPC = 4
		itemYPC = 15
		itemWidthPC = 92
		itemHeightPC = 12
		itemGapYPC=7
		itemBackgroundColor=-1:-1:-1

	showHeader = "no"
	showDefaultInfo=no

	DoAnimation = "no"
	AnimationType = 1
	AnimationStep = 26
	AnimationDelay = 1
	BackgroundDark = "no"
	rollItems=no
	slidingItemText=yes
	backgroundColor=0:0:0

	idleImageXPC=83.12
	idleImageYPC=89.58
	idleImageWidthPC=3.1
	idleImageHeightPC=5.5
  >
  	<previewWindow windowColor=0:0:0 offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100 refill=yes></previewWindow>
	<image redraw="yes" align="center" offsetXPC="8" offsetYPC="5" widthPC="90" heightPC="74">
			<widthPC>
			<script>
				if ( bufProgress &gt; 70 || playElapsed &gt; 0) previewPos=100; else previewPos=0;
				85.5 - previewPos;
			</script>
			</widthPC>
			<script>preview;</script>
		</image>
	<progressBar backgroundColor=-1:-1:-1 offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
		<-- preview --->
		<image redraw="yes" align="center" offsetXPC="8" offsetYPC="5" widthPC="90" heightPC="74">
			<widthPC>
			<script>
				if ( bufProgress &gt; 70 || playElapsed &gt; 0) previewPos=100; else previewPos=0;
				85.5 - previewPos;
			</script>
			</widthPC>
			<script>preview;</script>
		</image>
		<text redraw=yes offsetXPC=0 offsetYPC=80 widthPC=100 heightPC=30 fontSize=12 backgroundColor=0:0:0></text>
		<text redraw=yes offsetXPC=0 offsetYPC=80 widthPC=100 heightPC=0.5 fontSize=1 backgroundColor=100:100:100></text>
		<text redraw=yes align=center offsetXPC=10 offsetYPC=64 widthPC=80 heightPC=40 fontSize=15 backgroundColor=-1:-1:-1 foregroundColor=200:200:0>
			<script>
				title;
			</script>
		</text>
		<text redraw=yes offsetXPC=20 offsetYPC=88.1 heightPC=2.5 backgroundColor=60:60:60>
			<widthPC>
				<script>if (playStatus == 2) "60"; else "0";</script>
			</widthPC>
		</text>
		<text redraw=yes offsetXPC=20 offsetYPC=88.1 heightPC=2.5 backgroundColor=150:150:150>
			<widthPC>
				<script>if (playStatus == 2) Integer(playElapsed / playTime * 60); else "0";</script>
			</widthPC>
		</text>
		<text redraw=yes offsetYPC=88.1 heightPC=2.5 backgroundColor=200:0:0>
			<offsetXPC>
				<script>if (playStatus == 2 &amp;&amp; track_time != null) (19.85+(track_time / playTime * 60)); else "0";</script>
			</offsetXPC>
			<widthPC>
				<script>if (playStatus == 2 &amp;&amp; track_time != null) "0.3"; else "0";</script>
			</widthPC>
		</text>
		
		<text redraw=yes align=right offsetXPC=4.5 offsetYPC=82 widthPC=15 heightPC=15 fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
			<script>
				ret = null;
				if (playStatus == 2) {
					ret = time_elapsed;
					if (track_time != null) {
						ret += "/" + time_user;
					}
				}
				ret;
			</script>
		</text>
		
		<text redraw=yes offsetXPC=80.5 offsetYPC=82 widthPC=12 heightPC=15 fontSize=12 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
			<script>if (playStatus == 2 &amp;&amp; time_all != "00:00:00") time_all; else null;</script>
		</text>
		<destructor offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" color="-1:-1:-1"></destructor>
	</progressBar>
<?php
		$this->showIdleBg();
		$this->showOnUserInput();

?>
</mediaDisplay>
<?php
	}
}
?>