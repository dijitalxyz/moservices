<?php
/*	------------------------------
	Ukraine online services 	
	RSS player standard module v1.1
	------------------------------
	Created by Sashunya 2013
	wall9e@gmail.com			
	----------------------------- */
include ("ua_paths.inc.php");

class rss_player_std 
{
	public function showDisplay()
	{
	global $ua_images_path;
	global $xtreamer;
	global $key_enter;
	global $key_return;
	global $screensaver;
	?>
	
<onEnter>
	<?
			if ($screensaver == "1")
			{
		?>
			SetScreenSaverStatus("no");
		<?	
			}
		?>
	idx_play="<?=$_GET["idx"]?>";
	<?php
				if ($xtreamer)
					{
					?>
						SwitchViewer(7);
					<?php
					}
				?>
	postMessage("<?= $key_enter ?>");
    setRefreshTime(3000);

</onEnter>

<onRefresh>
	postMessage("<?= $key_return ?>");
		
</onRefresh>

<onExit>
		<?
			if ($screensaver == "1")
			{
		?>
			SetScreenSaverStatus("yes");
		<?	
			}
		?>
	playItemURL(-1, 1);
	setRefreshTime(-1);
	writeStringToFile("/tmp/env_idx_play_message", idx_play);
	writeStringToFile("/tmp/env_use_alt_player_message", "1");
</onExit>


<mediaDisplay name="photoView"
    backgroundColor="0:0:0"
    itemBackgroundColor="0:0:0"
    showHeader="no"
    showDefaultInfo="no"
    imageFocus="null"
    imageParentFocus="null"
    mainPartColor="0:0:0"
    itemBorderColor="0:0:0"
    sideTopHeightPC="0"
    sideBottomHeightPC="0"
    sideLeftWidthPC="0"
    sideRightWidthPC="0"
    sideColorTop="0:0:0"
    sideColorBottom="0:0:0"
    sideColorLeft="0:0:0"
    sideColorRight="0:0:0"
    drawItemText="no"
    slidingItemText="no"
    sliding="no"
    viewAreaXPC="0"
    viewAreaYPC="0"
    viewAreaWidthPC="0"
    viewAreaHeightPC="0"

    idleImageXPC		="88"
	idleImageYPC		="80"
	idleImageWidthPC	="0"
	idleImageHeightPC	="0"


    rowCount="1"
    columnCount="1"

    itemOffsetXPC="13"
    itemOffsetYPC="2"
    itemWidthPC="85"
    itemHeightPC="96"

    itemGapXPC="0"
    itemGapYPC="0"
    >
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_01.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_02.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_03.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_04.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_05.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_06.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_07.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_08.png</idleImage>
    
</mediaDisplay>
<item_template>
		<onClick>
			<script>
				playItemURL("<?=$_GET["link"]?>", 0);
			</script>	
		</onClick>
	</item_template>
<channel>
    <title></title>
    <link></link>
	<itemSize>
		<script>
			1;
		</script>
    </itemSize>
</channel>
</rss>

<?
	}
	public function showRss()
	{
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay();
		
		echo '</rss>'.PHP_EOL;
	}
}

$view = new rss_player_std();
$view->showRss();

?>