<?php
/*	------------------------------
	Ukraine online services 	
	RSS main menu module v2.5
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include ("ua_paths.inc.php");

class ua_rss_cat_const
{
	// main items
	const focusFontColor		=	'0:0:0';
	const unFocusFontColor		=	'255:255:255';
	const imageFocusBorder 		= 	'ua_focus_main.png';
	const imageUnFocusBorder	= 	'ua_unfocus_main.png';
	const backgroundColor 		=	'0:0:0';
	const rowCount 				=	'5';
	const columnCount 			=	'4';
	const itemOffsetXPC			= 	'6';
	const itemOffsetYPC			= 	'11';
	const itemWidthPC			= 	'22';
	const itemHeightPC			= 	'15';
	const itemBackgroundColor 	= 	'0:0:0';

	const header				=   'ua_header.png';
	const footer				=   'ua_footer.png';
	const header_offsetXPC		=	'0';
	const header_offsetYPC		=	'4.3';
	const header_widthPC		=	'100';
	const header_heightPC		=	'5.6';
	const footer_offsetXPC		=	'0';
	const footer_offsetYPC		=	'90.4';
	const footer_widthPC		=	'100';
	const footer_heightPC		=	'5.6';

	
	const text_header_align		=	'left'; // далее идут константы для текста заголовка
	const text_header_redraw	=	'yes';
	const text_header_lines		=	'1';
	const text_header_offsetXPC	=	'27';
	const text_header_offsetYPC	=	'2';
	const text_header_widthPC	=	'90';
	const text_header_heightPC	=	'10';
	const text_header_fontSize	=	'20'; // размер шрифта заголовка
	const text_header_backgroundColor	=	'-1:-1:-1';// фон 
	const text_header_foregroundColor	=	'0:0:0'; //цвет шрыфта
	
	
		
	const text_footer_align		=	'left';
	const text_footer_redraw	=	'yes';
	const text_footer_lines		=	'1';
	const text_footer_offsetXPC	=	'8';
	const text_footer_offsetYPC	=	'88';
	const text_footer_widthPC	=	'95';
	const text_footer_heightPC	=	'10';
	const text_footer_fontSize	=	'20'; 
	const text_footer_backgroundColor	=	'-1:-1:-1';
	const text_footer_foregroundColor	=	'0:0:0'; 
    
	// название сайта (которое справа внизу)	
	const text_site_footer_align		=	'left';
	const text_site_footer_redraw		=	'yes';
	const text_site_footer_lines		=	'1';
	const text_site_footer_offsetXPC	=	'78';
	const text_site_footer_offsetYPC	=	'88';
	const text_site_footer_widthPC		=	'17';
	const text_site_footer_heightPC		=	'10';
	const text_site_footer_fontSize		=	'20'; 
	const text_site_footer_backgroundColor	=	'-1:-1:-1';
	const text_site_footer_foregroundColor	=	'0:0:0'; 
	
	// константы отображения итемов
	
	const items_border_offsetXPC		= '0';
	const items_border_offsetYPC		= '0'; 
	const items_border_widthPC			= '100';
	const items_border_heightPC			= '100';
	
	const items_offsetXPC				= '3.3333'; 
	const items_offsetYPC				= '8.3333'; 
	const items_widthPC					= '33.3333'; 
	const items_heightPC				= '83.3333';

	const items_text_align				= 'left'; 
	const items_text_lines				= '2'; 
	const items_text_offsetXPC			= '40'; 
	const items_text_offsetYPC			= '20'; 
	const items_text_widthPC			= '62'; 
	const items_text_heightPC			= '42'; 
	const items_text_fontSize			= '12'; 
	const items_text_backgroundColor	= '-1:-1:-1';
	
}	
	
	
class ua_rss_main extends ua_rss_cat_const
{
	public $language;
	
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
			if (drawState == "unfocus")
				{
					border = "<?= $ua_images_path.static::imageUnFocusBorder ?>";
					color = "<?= static::unFocusFontColor ?>";
				}
			else
				{
					border = "<?= $ua_images_path.static::imageFocusBorder ?>";
					color = "<?= static::focusFontColor ?>";
				}
      </script>
	
		<image redraw="no" offsetXPC="<?= static::items_border_offsetXPC ?>" offsetYPC="<?= static::items_border_offsetYPC ?>" widthPC="<?= static::items_border_widthPC ?>" heightPC="<?= static::items_border_heightPC ?>">
			<script>
				border;
			</script>
		</image>
		<image offsetXPC="<?= static::items_offsetXPC ?>" offsetYPC="<?= static::items_offsetYPC ?>" widthPC="<?= static::items_widthPC ?>" heightPC="<?= static::items_heightPC ?>" >
			<script>
				getStringArrayAt(itemImageArray,idx);
			</script>
		</image>
		<text align="<?= static::items_text_align ?>" lines="<?= static::items_text_lines ?>" offsetXPC="<?= static::items_text_offsetXPC ?>" offsetYPC="<?= static::items_text_offsetYPC ?>" widthPC="<?= static::items_text_widthPC ?>" heightPC="<?= static::items_text_heightPC ?>" fontSize="<?= static::items_text_fontSize ?>" backgroundColor="<?= static::items_text_backgroundColor ?>">
			<foregroundColor>
				<script>
					color;
				</script>
			</foregroundColor>
		<script>
			getStringArrayAt(itemTitleArray,idx);
		</script>
		</text>
    
	
	
	<?php
	}
	
	public function onUserInput_script()	
	{
		
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
			</script>		</onUserInput>
	<?php
	}	
	
	
	
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	global $ua_path;
	?>

		<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		ВЫБОР САЙТА
		</text>
	
		<text  align="<?= static::text_site_footer_align ?>" redraw="<?= static::text_site_footer_redraw ?>" lines="<?= static::text_site_footer_lines ?>" offsetXPC="<?= static::text_site_footer_offsetXPC ?>" offsetYPC="<?= static::text_site_footer_offsetYPC ?>" widthPC="<?= static::text_site_footer_widthPC ?>" heightPC="<?= static::text_site_footer_heightPC ?>" fontSize="<?= static::text_site_footer_fontSize ?>" backgroundColor="<?= static::text_site_footer_backgroundColor ?>" foregroundColor="<?= static::text_site_footer_foregroundColor ?>">
			<script>
				footer = "Rev. <?=file_get_contents($ua_path."ua_version")?>"; 
				footer; 
			</script>
		</text>
	
			<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 ВЫХОД - RETURN
	</text>
	
	<?php	
	}
	//-------------------------------
	public function onEnters()
	{
		global $exua_rss_list_filename;
		global $ua_path_link;
		global $exua_rss_cat_filename;
		global $uakino_rss_cat_filename;
		global $ua_images_path;
		global $ua_rss_favorites_filename;
		global $ua_rss_download_filename;
		global $ua_rss_setup_filename;
		global $ua_setup_parser_filename;
		global $uaix_rss_cat_filename;
		global $fsua_rss_cat_filename;
		global $key_return;
	?>
	<onEnter>
		data=readStringFromFile("/tmp/env_return_message");
		if (data=="return") 
		{
			postMessage("<?= $key_return ?>");
			writeStringToFile("/tmp/env_return_message", "");
		}
		setRefreshTime(1); 
		index=0;
	</onEnter>
	<?php
	}

	public function onRefresh()
	{
		global $exua_rss_list_filename;
		global $ua_path_link;
		global $exua_rss_cat_filename;
		global $uakino_rss_cat_filename;
		global $ua_images_path;
		global $ua_rss_favorites_filename;
		global $ua_rss_download_filename;
		global $ua_rss_setup_filename;
		global $ua_setup_parser_filename;
		global $fsua_rss_cat_filename;
		global $filmix_rss_cat_filename;
		global $ua_rss_update_filename;
		global $ua_update_standalone;
	?>
	<onRefresh>
		setRefreshTime(-1);    
		showIdle();
		dlok = getURL("<?=$ua_path_link.$ua_setup_parser_filename."?load=1"?>");
		
		if (dlok != null)
			{
				regionIndex = getStringArrayAt(dlok, 0);
				languageIndex = getStringArrayAt(dlok, 1);
			}
		
		itemCount = 6;
		itemTitleArray = null;
		itemImageArray = null;
		itemlinkArray = null;
		
		
		itemTitleArray  = pushBackStringArray(itemTitleArray, "ИЗБРАННОЕ");
		itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_favorites.png");
		itemlinkArray  = pushBackStringArray(itemlinkArray, "<?=$ua_path_link.$ua_rss_favorites_filename?>");
		if (languageIndex == "0") 
			{
				if (regionIndex == "0") 
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "EX.UA РУССКИЙ"); 
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_exua_rus.png");		
					}
					else
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "FEX.NET РУССКИЙ");				
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_fexnet_rus.png");								
					}
				itemlinkArray  = pushBackStringArray(itemlinkArray, "<?= $ua_path_link.$exua_rss_cat_filename."?lang=r"?>");
			}
		else if (languageIndex == "1") 
			{
				if (regionIndex == "0") 
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "EX.UA УКРАИНСКИЙ"); 
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_exua_ukr.png");		
					}
					else
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "FEX.NET УКРАИНСКИЙ");				
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_fexnet_ukr.png");								
					}
				itemlinkArray  = pushBackStringArray(itemlinkArray, "<?= $ua_path_link.$exua_rss_cat_filename."?lang=u"?>");	
			}
		else if (languageIndex == "2") 
			{
				if (regionIndex == "0") 
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "EX.UA АНГЛИЙСКИЙ"); 
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_exua_eng.png");		
					}
					else
					{
						itemTitleArray  = pushBackStringArray(itemTitleArray, "FEX.NET АНГЛИЙСКИЙ");				
						itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_fexnet_eng.png");								
					}
				itemlinkArray  = pushBackStringArray(itemlinkArray, "<?= $ua_path_link.$exua_rss_cat_filename."?lang=e"?>");	
			}
		
		
		
		
		itemTitleArray  = pushBackStringArray(itemTitleArray, "UAKINO.NET");
		itemTitleArray  = pushBackStringArray(itemTitleArray, "FS.UA");
		itemTitleArray  = pushBackStringArray(itemTitleArray, "МЕНЕДЖЕР ЗАГРУЗОК");
		itemTitleArray  = pushBackStringArray(itemTitleArray, "НАСТРОЙКИ EX.UA");
		

		
		
		
		itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_uakinonet.png");
		itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_fsua.png");
		itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_download_manager.png");
		itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_setup.png");
		

		
		
		itemlinkArray  = pushBackStringArray(itemlinkArray, "<?= $ua_path_link.$uakino_rss_cat_filename?>");
		itemlinkArray  = pushBackStringArray(itemlinkArray, "<?= $ua_path_link.$fsua_rss_cat_filename?>");
		itemlinkArray  = pushBackStringArray(itemlinkArray, "<?=$ua_path_link.$ua_rss_download_filename."?display=1"?>");
		itemlinkArray  = pushBackStringArray(itemlinkArray, "<?=$ua_path_link.$ua_rss_setup_filename?>");
		
		<?php
		if ($ua_update_standalone)
			{
			?>
				itemTitleArray  = pushBackStringArray(itemTitleArray, "ПРОВЕРКА ОБНОВЛЕНИЙ");	
				itemImageArray  = pushBackStringArray(itemImageArray, "<?=$ua_images_path ?>ua_update.png");		
				itemlinkArray  = pushBackStringArray(itemlinkArray, "<?=$ua_path_link."ua_rss_update.php"?>");		
				itemCount +=1;
			<?php			
			}
		?>
	
		setFocusItemIndex(index);
		redrawDisplay();
	</onRefresh>
	<?php
	}

	//-------------------------------
	public function channel()
	{
	global $ua_path;
	
	?>
	<item_template>
		<link>
			<script>
				idx = getFocusItemIndex();
				url = getStringArrayAt(itemlinkArray,idx);
			</script>
		</link>
	</item_template>	

	<channel>
	<title>MAIN</title>
	<link><?=$ua_path."ua_main2.php"?></link>
	<itemSize>
		<script>
			itemCount;
		</script>
    </itemSize>
		
	</channel>
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
		itemGapXPC			= "0"
		itemGapYPC			= "0"
		itemBackgroundColor = "<?= static::itemBackgroundColor ?>"
		sliding				= "no"
		rollItems			= "no"
		drawItemText		= "no"
		forceFocusOnItem	= "yes"
		
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
	
	<image  redraw="no" offsetXPC="<?= static::header_offsetXPC ?>" offsetYPC="<?= static::header_offsetYPC ?>" widthPC="<?= static::header_widthPC ?>" heightPC="<?= static::header_heightPC ?>">
			<?= $ua_images_path.static::header ?>
	</image>
	
	<image  redraw="no" offsetXPC="<?= static::footer_offsetXPC ?>" offsetYPC="<?= static::footer_offsetYPC ?>" widthPC="<?= static::footer_widthPC ?>" heightPC="<?= static::footer_heightPC ?>">
			<?= $ua_images_path.static::footer ?>
	</image>
	
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
		$this->onRefresh();
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
//-------------------------------
$view = new ua_rss_main();
$view->showRss();
exit;
?>