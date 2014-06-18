<?php
/*	------------------------------
	Ukraine online services 	
	RSS photoview style module v1.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");

// этот класс подготавливает скелет выхода готового rss понимаемого плеером
class ua_rss_view_photo 
{
	// функция анимации ожидания
	public function showIdle()
	{
		include("ua_rss_idle.inc.php");
	}
	
	
	// эта функция выводит секцию script (она для всех одинаковая) в itemDisplay 
	// функция выводит список категорий
	public function itemDisplay_script()
	{
	global $ua_images_path;
		
	?>
		 <script>
			idx = getQueryItemIndex();
			drawState = getDrawingItemState();
			if (drawState == "focus")
				{
					border = "<?= $ua_images_path.static::imageFocusBorder ?>";
					color = "<?= static::focusFontColor ?>";
				}
			else
				{
					border = "<?= $ua_images_path.static::imageUnFocusBorder ?>";
					color = "<?= static::unFocusFontColor ?>";
				}
      </script>
	
		<image redraw="no" offsetXPC="<?= static::items_border_offsetXPC ?>" offsetYPC="<?= static::items_border_offsetYPC ?>" widthPC="<?= static::items_border_widthPC ?>" heightPC="<?= static::items_border_heightPC ?> " useBackgroundSurface=no >
			<script>
				border;
			</script>
		</image>
		
		
		<image redraw="no" offsetXPC="<?= static::items_offsetXPC ?>" offsetYPC="<?= static::items_offsetYPC ?>" widthPC="<?= static::items_widthPC ?>" heightPC="<?= static::items_heightPC ?>" useBackgroundSurface=no >
			<script>
				getItemInfo( idx, "image" );
			</script>
		</image>
		
		<text redraw="no" align="<?= static::items_text_align ?>" lines="<?= static::items_text_lines ?>" offsetXPC="<?= static::items_text_offsetXPC ?>" offsetYPC="<?= static::items_text_offsetYPC ?>" widthPC="<?= static::items_text_widthPC ?>" heightPC="<?= static::items_text_heightPC ?>" fontSize="<?= static::items_text_fontSize ?>" backgroundColor="<?= static::items_text_backgroundColor ?>" useBackgroundSurface=no>
			<foregroundColor>
				<script>
					color;
				</script>
			</foregroundColor>
		<script>
			getItemInfo( idx, "title" );
		</script>
		</text>
    
	
	
	<?php
	}
	
	public function onUserInput_script()	
	{
	global $key_left;	
	?>
		<onUserInput>
			<script>
				ret = "false";
				i = getFocusItemIndex();
				userInput = currentUserInput();
				majorContext = getPageInfo("majorContext");
				if ( majorContext == "menu" )
					{
						if (userInput == "<?= $key_left ?>" )
							{
								ret="true";
							}
				}
				
				ret;
				
			</script>
		</onUserInput>
	<?php
	}
	
	//-------------------------------
	public function onEnters()
	{
	?>
	<onEnter>
		itemCount = getPageInfo( "itemCount" );
		writeStringToFile("/tmp/env_returnFromList_message", "");
	</onEnter>
	<onExit>
		writeStringToFile("/tmp/env_returnFromList_message", "1");
	</onExit>
	<?php
	}
	
	// функция подготовки параметров выходного RSS
	public function showDisplay()
	{
	global $ua_images_path;
	?>
	<mediaDisplay name="photoView"
		viewAreaXPC			= "0"
		viewAreaYPC			= "0"
		viewAreaWidthPC		= "100"
		viewAreaHeightPC	= "100"
		backgroundColor		= "<?= static::backgroundColor ?>"
		sideTopHeightPC		= "0"
		sideBottomHeightPC	= "0"
		sideColorBottom		= "0:0:0"
		sideColorTop		= "0:0:0"
		rowCount			= "<?= static::rowCount ?>"
		columnCount			= "<?= static::columnCount ?>"
		
				
		itemOffsetXPC		= "<?= static::itemOffsetXPC ?>"
		itemOffsetYPC		= "<?= static::itemOffsetYPC ?>"
		itemWidthPC			= "<?= static::itemWidthPC ?>"
		itemHeightPC		= "<?= static::itemHeightPC ?>"
		itemGapXPC			= "1"
		itemGapYPC			= "1"
		itemBackgroundColor = "<?= static::itemBackgroundColor ?>"
		sliding				= "no"
		rollItems			= "no"
		drawItemText		= "no"
		forceFocusOnItem	= "yes"
		enableStretchBlt=no
		circlingItems=no
		BackgroundDark=no
		forceRedrawItems=yes
		slideItems=no
    	stretchInFocus=no
		DoAnimation=no
		drawItemBorder=no
		
		showHeader			= "no"
		showDefaultInfo		= "no"
		idleImageXPC		="88"
		idleImageYPC		="80"
		idleImageWidthPC	="5"
		idleImageHeightPC	="8"
	>
	<?php
		$this->showIdle();
	?>
	<backgroundDisplay name=MainMenuBackground>
			<image  redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
					<?=$ua_images_path?>ua_background_main.png
			</image>
	</backgroundDisplay>
	
		
	<?php	
	
		$this->mediaDisplay_content();
	
	?>	
		<itemDisplay>
	<?php	
			$this->itemDisplay_script();
			//$this->itemDisplay_content();
	?>	
		</itemDisplay>
	<?php
		$this->onUserInput_script();
	?>
	</mediaDisplay>
<?php
		$this->onEnters();
		$this->channel();
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


if (file_exists('ua_constants_photo.inc.php' ))
	include( 'ua_constants_photo.inc.php' );

?>