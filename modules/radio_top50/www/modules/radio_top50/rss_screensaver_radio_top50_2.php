<?php

class rssSkinRadioTop50ScreensaverView extends rssSkinList
{
	//
	// ------------------------------------
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		setRefreshTime(-1);
		postMessage("return");		
		ret = "true";	
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		setRefreshTime(-1);
		postMessage("return");		
		ret = "true";	
	}
	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		setRefreshTime(-1);
		postMessage("return");		
		ret = "true";	
	}
	else if (userInput == "<?= getRssCommand('right') ?>")
	{
		setRefreshTime(-1);
		postMessage("return");		
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
	public function showScripts()
	{
?>
  <onEnter>
	pozic_x = null;
	pozic_y = null;
	url_screensaver = "<?= $this->url_screensaver ?>";
	refresh_screensaver_time = <?= $this->screensaver_time ?>;
	setRefreshTime(10);	
  </onEnter>

  <onExit>
	setRefreshTime(-1);
  </onExit>
	
  <onRefresh>
	setRefreshTime(-1);	
	pozic_x = getURL ("http://localhost//modules/radio_top50/gener.php");
	pozic_y = getURL ("http://localhost//modules/radio_top50/gener.php");
	pozic_x = Integer(pozic_x);
	pozic_y = Integer(pozic_y);
	
	if (refresh_screensaver_time == 2) {
	setRefreshTime(2000);	
	} else if (refresh_screensaver_time == 5) {
	setRefreshTime(5000);	
	} else if (refresh_screensaver_time == 10) {
	setRefreshTime(10000);	
	} else if (refresh_screensaver_time == 15) {
	setRefreshTime(15000);	
	} else if (refresh_screensaver_time == 20) {
	setRefreshTime(20000);	
	} else {
	setRefreshTime(-1);
	postMessage("return");
	}
	redrawDisplay();
  </onRefresh>
<?php
	}
	//
	// ------------------------------------
	function showDisplay()
	{
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

   idleImageXPC="<?= round( static::idleImageX/$vw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$vh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$vw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$vh*100, 4) ?>"
   >
<?php		
	$this->showIdleBg();
?>		
   <image widthPC="12" heightPC="8" redraw="yes">
	<offsetXPC><script>pozic_x;</script></offsetXPC>
	<offsetYPC><script>pozic_y;</script></offsetYPC>
	
	<script>url_screensaver;</script>
     </image>
<?php
		$this->showOnUserInput();

?>
  </mediaDisplay>
   
<?php
	}
	public function showChannel()
	{
?>	
  <channel>
    <script>
	0;
    </script>
</channel>
<?php
	}	
}
?>