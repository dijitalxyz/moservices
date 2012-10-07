<?php

class rssSkinPlayerView extends rssSkinPlayer
{
	//
	// ------------------------------------
	function showOnUserInput()
	{
?>
    <onUserInput>
	input = currentUserInput();
	ret = "false";

	if( input == "<?= getRssCommand('enter') ?>" )
	{
		if(showInfo == 0) showInfo = 1;
		else showInfo = 0;
		ret = "true";
	}	

	else if( input == "<?= getRssCommand('play') ?>" )
	{
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
		if( showInfo == 0 ) showInfo = 1;
		ret = "true";
	}

	else if( input == "<?= getRssCommand('left') ?>" )
	{
		if( itemCount != 1 )
		{
			if( cItem == 0 ) cItem = itemCount - 1;
			else cItem -= 1;
			startVideo = 1;
		}
		ret = "true";
	}		
	else if( input == "<?= getRssCommand('right') ?>" )
	{
		if( itemCount != 1 )
		{
			cItem -= -1;
			if( cItem == itemCount ) cItem = 0;
			startVideo = 1;
		}
		ret = "true";
	}
	else if( input == "<?= getRssCommand('up') ?>" || input == "<?= getRssCommand('down') ?>" )
	{
		ret = "true";
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
	itemCount = getPageInfo( "itemCount" );
	cItem = <?= $this->cItem ?>;

	startVideo = 1;
	cPlayPause = 1;

	setRefreshTime(100);
  </onEnter>

  <onExit>
	playItemURL(-1, 1);
	setRefreshTime(-1);
  </onExit>
	
  <onRefresh>
	if (startVideo == 1)
	{
		playItemURL(-1, 1);

		cTitle = getItemInfo( cItem, "title" );
		cImg   = getItemInfo( cItem, "image" );
		cUrl   = getItemInfo( cItem, "link" );
		cAct   = getItemInfo( cItem, "action" );
		if( cAct == "get" )
		{
			showIdle;
			cUrl = getUrl( cUrl );
			cancelIdle;
		}
		setRefreshTime(1000);
		startVideo = 0;
		showInfo = 1;
		timeLine = "";
		playItemURL(cUrl, 0, "mediaDisplay", "previewWindow");
	}

	vidProgress = getPlaybackStatus();
	bufProgress = getCachedStreamDataSize(0, 262144);
	playElapsed = getStringArrayAt(vidProgress, 0);
	playTotal = getStringArrayAt(vidProgress, 1);
	playStatus = getStringArrayAt(vidProgress, 3);

	if (playElapsed != 0)
	{
		x = Integer(playElapsed / 60);
		h = Integer(playElapsed / 3600);
		s = playElapsed - (x * 60);
		m = x - (h * 60);
		if(h &lt; 10) elapsedTime = "0" + sprintf("%s:", h);
		else elapsedTime = sprintf("%s:", h);
		if(m &lt; 10) elapsedTime += "0";
		elapsedTime += sprintf("%s:", m);
		if(s &lt; 10) elapsedTime += "0";
		elapsedTime += sprintf("%s", s);

		x = Integer(playTotal / 60);
		h = Integer(playTotal / 3600);
		s = playTotal - (x * 60);
		m = x - (h * 60);
		if(h &lt; 10) totalTime = "0" + sprintf("%s:", h);
		else totalTime = sprintf("%s:", h);
		if(m &lt; 10)  totalTime += "0";
		totalTime += sprintf("%s:", m);
		if(s &lt; 10) totalTime += "0";
		totalTime += sprintf("%s", s);

		timeLine = elapsedTime+"/"+totalTime;

		if (startVideo == 0)
		{
			if( showInfo == 1 )
			{
				startVideo = 2;
				statusTimeout = 5;
			}
		}
		else
		{
			if( showInfo == 0 ) statusTimeout = 0;
			else statusTimeout -= 1;

			if ( statusTimeout == 0 )
			{
				updatePlaybackProgress("delete", "mediaDisplay", "progressBar");
				showInfo = 0;
				startVideo = 0;
			}
		}
	}
	else if (playStatus == 0)
	{
		postMessage("<?= getRssCommand('return') ?>");
	}

	if(showInfo == 1)
	{
		updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
	}
  </onRefresh>
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
   viewAreaXPC="<?= round(( $sx + $vx )/$sw*100, 4) ?>"
   viewAreaYPC="<?= round(( $sy + $vy )/$sh*100, 4) ?>"
   viewAreaWidthPC="<?= round(( $vw )/$sw*100, 4) ?>"
   viewAreaHeightPC="<?= round(( $vh )/$sh*100, 4) ?>"

   backgroundColor="<?= static::backgroundColor ?>"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="<?= static::sideColorLeft ?>"
   sideColorRight="<?= static::sideColorRight ?>"

   showHeader="no"
   showDefaultInfo="no"

   idleImageXPC="<?= round( static::idleImageX/$sw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$sh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$sw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$sh*100, 4) ?>"
  >
    <previewWindow 
     windowColor="<?= static::backgroundColor ?>" 
     offsetXPC="0"
     offsetYPC="0"
     widthPC="100"
     heightPC="100"
    />
    <progressBar
     backgroundColor="<?= static::progressBackgroundColor ?>"
     offsetXPC="<?= round( static::progressX/$vw*100, 4) ?>"
     offsetYPC="<?= round( static::progressY/$vh*100, 4) ?>"
     widthPC="<?=   round( static::progressWidth/$vw*100, 4) ?>"
     heightPC="<?=  round( static::progressHeight/$vh*100, 4) ?>"
    >
<?php
		if( static::progressBackground != '' )
		{

?>
      <image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
        <?= getSkinPath().static::progressBackground ?> 
      </image>
<?php
		}

		// icon
		$px = round( static::iconX / static::progressWidth  * 100, 4);
		$py = round( static::iconY / static::progressHeight * 100, 4);
		$pw = round( static::iconWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::iconHeight / static::progressHeight * 100, 4);

?>
      <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
        <script>cImg;</script>
      </image>
<?php
		// title
		$px = round( static::titleX / static::progressWidth  * 100, 4);
		$py = round( static::titleY / static::progressHeight * 100, 4);
		$pw = round( static::titleWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::titleHeight / static::progressHeight * 100, 4);

?>
      <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       align="<?= static::titleAlign ?>" lines="<?= static::titleLines ?>" fontSize="<?= static::titleFontSize ?>" backgroundColor="<?= static::titleBackgroundColor ?>" foregroundColor="<?= static::titleForegroundColor ?>">
	<script>cTitle;</script>
      </text>
<?php
		$px = round( static::prevX / static::progressWidth  * 100, 4);
		$py = round( static::prevY / static::progressHeight * 100, 4);
		$pw = round( static::prevWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::prevHeight / static::progressHeight * 100, 4);

?>
      <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
        <script>
	if ( itemCount &gt; 1 ) "<?= getSkinPath().static::prevIcon ?>";
	else null;
        </script>
      </image>
<?php
		$px = round( static::nextX / static::progressWidth  * 100, 4);
		$py = round( static::nextY / static::progressHeight * 100, 4);
		$pw = round( static::nextWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::nextHeight / static::progressHeight * 100, 4);

?>
      <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
        <script>
	if ( itemCount &gt; 1 ) "<?= getSkinPath().static::nextIcon ?>";
	else null;
        </script>
      </image>
<?php
		$px = round( static::statusX / static::progressWidth  * 100, 4);
		$py = round( static::statusY / static::progressHeight * 100, 4);
		$pw = round( static::statusWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::statusHeight / static::progressHeight * 100, 4);

?>
      <image redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
        <script>
	if (playStatus == 2)
	{
		if (cPlayPause == 1) "<?= getSkinPath().static::statusIconPlay ?>";
		else "<?= getSkinPath().static::statusIconPause ?>";
	}
	else "<?= getSkinPath().static::statusIconStop ?>";
        </script>
      </image>
<?php
		$px = round( static::barX / static::progressWidth  * 100, 4);
		$py = round( static::barY / static::progressHeight * 100, 4);
		$pw = round( static::barWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::barHeight / static::progressHeight * 100, 4);

?>
      <bar offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" barColor="<?= static::barBarColor ?>" progressColor="<?= static::barProgressColor ?>" bufferColor="<?= static::barBufferColor ?>" />
<?php
		$px = round( static::timeX / static::progressWidth  * 100, 4);
		$py = round( static::timeY / static::progressHeight * 100, 4);
		$pw = round( static::timeWidth  / static::progressWidth  * 100, 4);
		$ph = round( static::timeHeight / static::progressHeight * 100, 4);

?>
      <text redraw="yes" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       align="left" lines="1" fontSize="<?= static::timeFontSize ?>" backgroundColor="<?= static::timeBackgroundColor ?>" foregroundColor="<?= static::timeForegroundColor ?>">
	<script>timeLine;</script>
      </text>
      <destructor offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" color="-1:-1:-1">
      </destructor>
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
