<?php
/*	------------------------------
	Ukraine online services 	
	RSS part for full description V1.0
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */
?>
<?php


include ("ua_paths.inc.php");
	
class ua_rss_description_const 
{
	const imageFocus 			= 	'ua_focus_main.png';
	const imageUnFocus 			= 	'ua_unfocus_main.png';
	const background_image		=   'ua_update_bkgd.png';
}
class ua_rss_description extends ua_rss_description_const 
{

public function showDisplay()
{
	global $key_enter;
	global $key_left;
	global $key_right;
	global $key_return;
	global $ua_images_path;
	global $ua_setup_parser_filename;
	global $ua_path_link;
	global $ua_rss_update_filename;
	global $tmpdescr;

	
?>	
<bookmark>dialog</bookmark>

<onEnter>
showIdle();
subMenuString = null;
subMenuValue = null;
subMenuString = pushBackStringArray(subMenuString, "Закрыть");
subMenuValue = pushBackStringArray(subMenuValue, "0");
subMenuSize = 1;
setFocusItemIndex(0);
redrawDisplay();
</onEnter>

<onExit>
cancelIdle();
</onExit>

<mediaDisplay name="onePartView"
 	
 viewAreaXPC=10
 viewAreaYPC=12
 viewAreaWidthPC=80
 viewAreaHeightPC=79
 sideColorRight=0:0:0
 sideColorLeft=0:0:0
 sideColorTop=0:0:0
 sideColorBottom=0:0:0 
 backgroundColor=0:0:0
 focusBorderColor=0:0:0
 unFocusBorderColor=0:0:0
 itemBackgroundColor=0:0:0
 showHeader="no"
 showDefaultInfo="no"
 itemPerPage=2
 itemWidthPC=15
 itemXPC=41
 itemHeightPC=7
 itemImageWidthPC=0
 itemImageHeightPC=0
 itemYPC=89
 itemImageXPC=0
 itemImageYPC=30
 idleImageXPC		="88"
 idleImageYPC		="80"
 idleImageWidthPC	="5"
 idleImageHeightPC	="8"
 >
<?php
	include("ua_rss_idle.inc.php");
?>
<backgroundDisplay>
	<image redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			<?=$ua_images_path.static::background_image?>
	</image>
</backgroundDisplay>
<?
	$desc=file($tmpdescr);
	$off_y=6;
	foreach ($desc as $val)
	{
		?>
			<text align="justify" lines="1" offsetXPC="4" offsetYPC="<?=$off_y?>" widthPC="92" heightPC="5" fontSize="16" backgroundColor="0:0:0">
			<?=$val?></text>
		<?
		$off_y +=5.5; 
	}
?>

<itemDisplay>

	
	<script>
			idx = getQueryItemIndex();
			drawState = getDrawingItemState();
			if (drawState == "unfocus")
				{
					border = "<?= $ua_images_path.static::imageUnFocus?>";
					color = "255:255:255";
				}
			else
				{
					border = "<?= $ua_images_path.static::imageFocus?>";
					color = "255:255:255";
				}
      </script>
	  		<image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
			<script>
				border;
			</script>
		</image>
		
			<text offsetXPC=0 offsetYPC=0 widthPC=95 heightPC=90 align=center fontSize=16 backgroundColor=-1:-1:-1 >
		<foregroundColor><script> color; </script></foregroundColor>
		<script> getStringArrayAt(subMenuString, getQueryItemIndex()); </script>
	</text>
</itemDisplay>

	
<onUserInput>
	handled = "false";
	userInput = currentUserInput();
	focusIndex = getFocusItemIndex();
	
	if ("<?= $key_enter ?>" == userInput) {
		data = getStringArrayAt(subMenuValue, focusIndex);
		postMessage("<?= $key_return ?>");
		handled = "true";
	}
	else if ("<?= $key_left ?>" == userInput || "<?= $key_right ?>" == userInput) {
		handled = "true";
	}
	handled;
</onUserInput>

</mediaDisplay>


<channel>
<title>Simple Menu Dialog</title>
<link>/tmp/usbmounts/sda1/scripts/uaonline/ua_rss_full_description.php</link>
<itemSize><script>subMenuSize;</script></itemSize>

</channel>
<?	
}

public function showRss($header1,$header2,$items)
	{
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay();
		
		echo '</rss>'.PHP_EOL;
	}
}


$view = new ua_rss_description;
$view->showRss();
exit;