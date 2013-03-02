<?php
/*	------------------------------
	Ukraine online services 	
	RSS threePartsView style module v1.4
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");

// этот класс подготавливает скелет выхода готового rss понимаемого плеером
class ua_rss_view 
{
	// функция анимации ожидания
	public function showIdle()
	{
	   include("ua_rss_idle.inc.php");
	}
	// эта функция выводит секцию script (она для всех одинаковая) в itemDisplay 
	public function itemDisplay_script()
	{
	?>
		<script>
			idx = getQueryItemIndex();
			drawState = getDrawingItemState();
			if (drawState == "unfocus")
				{
						color = "<?= static::unfocus_color ?>";
				}
			else
				{
		
						color = "<?= static::focus_color ?>";
				}
		</script> 
	<?php
	}
	public function onExits()	
	{
	global $xtreamer;
	?>
	
	<?php
	}
	public function onUserInput_script()	
	{
		global $key_up;
		global $key_down;
		global $key_left;
		global $key_right;
		global $key_return;
		global $xtreamer;
		global $key_pageup;
		global $key_pagedown;
	?>
		<onUserInput>
				<script>
					userInput = currentUserInput();
					itm_index = getFocusItemIndex();
					majorContext = getPageInfo("majorContext");	
					ret = "false";
					
					if ( majorContext == "items" )
					{
					if (userInput == "<?= $key_right ?>" )
					{	
						ret="true";
					}
					<?php
					if (!$xtreamer)
					{
					?>
					tmp_count =itemCount-1;
					if (userInput == "<?= $key_up ?>" )
					{
						if (itm_index == 0)
						{
							setFocusItemIndex(itemCount);
						}			
					}
					if (userInput == "<?= $key_down ?>" )
					{
						if (itm_index == tmp_count)
						{
							setFocusItemIndex(-1);
						}			
					}					
					<?php
					}
					?>
					if (userInput == "<?= $key_pageup ?>" || userInput == "<?= $key_pagedown ?>")
					{
						if (userInput == "<?= $key_pagedown ?>") { page-=-1; itm_index=0;}
						if (userInput == "<?= $key_pageup ?>" )  {if (page &gt;1) page-=1; itm_index=0;}
						ret="true";
						setRefreshTime(1);
					}
					}
					if ( majorContext == "menu" )
					{
					menu = getFocusMenuIndex();
										
					if (userInput == "<?= $key_left ?>" )
					{
						ret="true";
					}
					tmp_count =menuCount-1;
					if (userInput == "<?= $key_up ?>" )
					{
						if (menu == 0)
						{
							setFocusMenuIndex(menuCount);
						}			
					}
					if (userInput == "<?= $key_down ?>" )
					{
						if (menu == tmp_count)
						{
							setFocusMenuIndex(-1);
						}			
					}					
						
					}	
					
					ret;
			</script>
			</onUserInput>
	<?php
	}
	
	// функция подготовки параметров выходного RSS
	public function showDisplay()
	{
	global $ua_images_path;
	?>
	<mediaDisplay name = "threePartsView"
	
		sideColorLeft		= "0:0:0"
        sideRightWidthPC 	= "0"
		
		parentFocusFontColor	= "<?= static::parentFocusFontColor ?>"
		
		forceRedrawItems	= yes
		backgroundColor		= "<?= static::backgroundColor ?>"
		focusFontColor		= "<?= static::focusFontColor ?>"
		unFocusFontColor	= "<?= static::unFocusFontColor ?>"
		selectMenuOnRight	= "no"
		forceFocusOnItem	= "no"
		forceFocusOnMenu	= "no"
		showHeader			= "no"
		showDefaultInfo		= "no"
	
		headerCapXPC		= "100"
		headerCapYPC		= "0"
		headerCapWidthPC	= "0"
		headerImageWidthPC	= "0"
		headerXPC			= "0"
		headerYPC			= "0"
		headerWidthPC		= "0"

		itemBackgroundColor	= "<?= static::itemBackgroundColor ?>"
		itemXPC				= "<?= static::itemXPC ?>" 
		itemYPC				= "<?= static::itemYPC ?>" 
		itemImageXPC		= "<?= static::itemImageXPC ?>"
		itemImageYPC 		= "<?= static::itemImageYPC ?>"
		itemImageWidthPC	= "<?= static::itemImageWidthPC ?>" 
		itemImageHeightPC	= "<?= static::itemImageHeightPC ?>" 
		itemPerPage			= "<?= static::itemPerPage ?>"	
		itemWidthPC			= "<?= static::itemWidthPC ?>" 
		itemHeightPC 		= "<?= static::itemHeightPC ?>"
		itemGap				= "<?= static::itemGap ?>"
	
		menuXPC				= "<?= static::menuXPC ?>"
		menuYPC				= "<?= static::menuYPC ?>"
		menuWidthPC			= "<?= static::menuWidthPC ?>"
		menuHeightPC		= "<?= static::menuHeightPC ?>"

		imageFocus = "<?= $ua_images_path.static::imageFocus ?>"
		imageParentFocus = "<?= $ua_images_path.static::imageParentFocus ?>"
		imageUnFocus = "<?= $ua_images_path.static::imageUnFocus ?>"
		idleImageXPC		="88"
		idleImageYPC		="80"
		idleImageWidthPC	="5"
		idleImageHeightPC	="8"
	>
	
		
	<?php
		$this->showIdle();
	?>
	
	<backgroundDisplay>
			<image  redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
					<?=$ua_images_path?>ua_background_items.png
			</image>
	</backgroundDisplay>
	
	
	
	<?php	
		// сюда вставить бордюры 
		// также сюда вставить все картинки и текст
		$this->mediaDisplay_content();
	
	?>	
		<itemDisplay>
	<?php	
			$this->itemDisplay_script();
			$this->itemDisplay_content();
	?>	
		</itemDisplay>
	<?php
		$this->onUserInput_script();
	?>
	</mediaDisplay>
	<?php
		$this->onRefresh();
		$this->menu();
		$this->onEnters();
		$this->onExits();
		$this->items();
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


if (file_exists('ua_constants.inc.php' ))
	include( 'ua_constants.inc.php' );

?>