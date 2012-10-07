<?php
/*	------------------------------
	Ukraine online services 	
	RSS part keyboard module v1.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include ("ua_paths.inc.php");

class ua_rss_keyb_const 
{
	const mainBackground		=   'ua_background.png';
	const focusFontColor		=	'0:0:0';
	const unFocusFontColor		=	'255:255:255';
	const focusFontBackColor	=	'255:255:255';
	const unFocusFontBackColor	=	'80:80:80';
	
	
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
	
	// верхний заголовок 
	const text_header_align		=	'left'; // далее идут константы для текста заголовка
	const text_header_redraw	=	'no';
	const text_header_lines		=	'1';
	const text_header_offsetXPC	=	'27';
	const text_header_offsetYPC	=	'2';
	const text_header_widthPC	=	'90';
	const text_header_heightPC	=	'10';
	const text_header_fontSize	=	'20'; // размер шрифта заголовка
	const text_header_backgroundColor	=	'-1:-1:-1';// фон 
	const text_header_foregroundColor	=	'0:0:0'; //цвет шрыфта
	
	
	// нижний заголовок
	const text_footer_align		=	'left';
	const text_footer_redraw	=	'no';
	const text_footer_lines		=	'1';
	const text_footer_offsetXPC	=	'6';
	const text_footer_offsetYPC	=	'88';
	const text_footer_widthPC	=	'95';
	const text_footer_heightPC	=	'10';
	const text_footer_fontSize	=	'19'; 
	const text_footer_backgroundColor	=	'-1:-1:-1';
	const text_footer_foregroundColor	=	'0:0:0'; 
	
	// это бордюр и текстовое поле для вводимого текста
	const descr_offsetXPC		=	'12';
	const descr_offsetYPC		=	'15';
	const descr_widthPC			=	'78';
	const descr_heightPC		=	'10';
	const descr_image			=	'ua_border_descr.png';

	const text_descr_align		=	'left';
	const text_descr_redraw		=	'yes';
	const text_descr_offsetXPC	=	'14';
	const text_descr_offsetYPC	=	'16';
	const text_descr_widthPC	=	'78';
	const text_descr_heightPC	=	'8';
	const text_descr_fontSize	=	'22'; 
	const text_descr_backgroundColor	=	'-1:-1:-1';
	const text_descr_foregroundColor	=	'255:255:255'; 
	
	const items_border_offsetXPC		= '0';
	const items_border_offsetYPC		= '0'; 
	const items_border_widthPC			= '100';
	const items_border_heightPC			= '100';

	const items_text_align				= 'center'; 
	const items_text_lines				= '1'; 
	const items_text_offsetXPC			= '0'; 
	const items_text_offsetYPC			= '0'; 
	const items_text_widthPC			= '100'; 
	const items_text_heightPC			= '100'; 
	const items_text_fontSize			= '22'; 
	
	
	
}

class ua_rss_keyb extends ua_rss_keyb_const
{
	// функция анимации ожидания и вывода текстового поля, где будет отображаться вводимый текст
	public function showIdle()
	{
		include("ua_rss_idle.inc.php");
		?>	
		
		<text redraw="<?= static::text_descr_redraw ?>" align="<?= static:: text_descr_align ?>" offsetXPC="<?= static::text_descr_offsetXPC ?>" offsetYPC="<?= static::text_descr_offsetYPC ?>" widthPC="<?= static::text_descr_widthPC ?>" heightPC="<?= static::text_descr_heightPC ?>" fontSize="<?= static::text_descr_fontSize ?>" backgroundColor="<?= static::text_descr_backgroundColor ?>" foregroundColor="<?= static::text_descr_foregroundColor ?>">
		<script>inputText + "_";</script>
		<foregroundColor>
				<script> if(firstUse == 1) "80:80:80"; else "200:200:200";</script>
		</foregroundColor>
		</text>
		
		<image offsetXPC="<?= static::descr_offsetXPC ?>" offsetYPC="<?= static::descr_offsetYPC ?>" widthPC="<?= static::descr_widthPC ?>" heightPC="<?= static::descr_heightPC ?>" >
			<?= $ua_images_path . static::descr_image ?>
		</image>
	
		<?php
		
		
	}

	public function itemDisplay_script()
	{
	global $ua_images_path;
		
	?>

	<script>
			idx = getQueryItemIndex();
			itemTitle = getStringArrayAt(itemTitleArray, idx-(-1));
			drawState = getDrawingItemState();
			if (drawState == "unfocus")
				{
					
					backcolor = "<?= static::unFocusFontBackColor ?>";
					if (itemTitle == "OK" || itemTitle == "spac" || itemTitle == "del" || itemTitle == "lang") color="255:239:69";				
					else
					color = "<?= static::unFocusFontColor ?>";
				}
			else
				{
					
					backcolor = "<?= static::focusFontBackColor ?>";
		
					color = "<?= static::focusFontColor ?>";
				}
      </script>
		
						
		<text align="<?= static::items_text_align ?>" lines="<?= static::items_text_lines ?>" offsetXPC="<?= static::items_text_offsetXPC ?>" offsetYPC="<?= static::items_text_offsetYPC ?>" widthPC="<?= static::items_text_widthPC ?>" heightPC="<?= static::items_text_heightPC ?>" fontSize="<?= static::items_text_fontSize ?>" >
		<foregroundColor>
				<script>
					color;
				</script>
			</foregroundColor>
		<backgroundColor>
				<script>
					backcolor;
				</script>
		</backgroundColor>
		

		<script>
			 	getStringArrayAt(itemTitleArray, idx-(-1));
				
		
		
		</script>
		</text>

	<?php
	}
	
	public function onUserInput_script()	
	{
	global $ua_path_link;	
	global $key_return;
	global $key_up;
	global $key_down;
	global $key_left;
	global $key_right;
	?>
		<onUserInput>
		<script>
			ret = "false";
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
	
	public function onEnters()
	{
	global $ua_path_link;
	?>
	<onEnter>
    itemLocale = "rus";
	curLang="rus";
	dlok = getURL("<?= $ua_path_link."ua_keyboard_layout.php?lang="?>"+curLang);
	itemTitleArray = readStringFromFile(dlok);
	itemSize = getStringArrayAt(itemTitleArray, 0);
	inputTextCount = 0;
	inputTextArray = null;
	inputText = getURL("<?=$ua_path_link.'ua_keyboard_layout.php?search_history=req'?>");
	print("!!!!!!---",inputText);
	firstUse = 0;
	if(inputText != "")
	firstUse = 1;
	redrawDisplay();
	setFocusItemIndex(20);
	
	</onEnter>
	
	<?php
	}
	public function mediaDisplay_content()
	{
	?>
		
		
		<text align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		<script>
			itemLang = "Клавиатура. Язык - ";			
			if (curLang=="rus") itemLang+="Русский";
			if (curLang=="ukr") itemLang+="Украинский";
			if (curLang=="eng") itemLang+="Английский";
		
		</script>
		</text>
			
		<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		OK-конец ввода; spac-пробел; del-удалить символ; lang-язык; RET-выход
		</text>
	
	<?php	
		
	}
	
	public function itemTemplate_content()
	{
	global $ua_path_link;
	global $key_return;
	?>
	<item_template>
	<onClick>
		idx = getFocusItemIndex();
		itemTitle = getStringArrayAt(itemTitleArray, idx-(-1));
		if (itemTitle == "lang" )
		{
				if (curLang == "rus") curLang = "eng"; else
				if (curLang == "eng") curLang = "ukr"; else
				if (curLang == "ukr") curLang = "rus"; 
				
				dlok = getURL("<?= $ua_path_link."ua_keyboard_layout.php?lang="?>"+curLang);
				itemTitleArray = readStringFromFile(dlok);
				itemSize = getStringArrayAt(itemTitleArray, 0);
				setFocusItemIndex(20);
				ret = "true";
		}
		else
		if (itemTitle == "OK" )
		{
    		    
				data=getURL("<?=$ua_path_link.'ua_keyboard_layout.php?search_history=send&amp;sh_val='?>"+urlEncode(inputText));
				setReturnString(inputText);
				postMessage("<?=$key_return?>");
		}
		else	
		if (itemTitle == "del"){
		 	
			if (firstUse == 0)
			{
				if(inputTextCount &gt; 0)
				{
					inputTextCount -= 1;
					print("inputTextCount2=",inputTextCount);
					inputTextArray = deleteStringArrayAt(inputTextArray, inputTextCount);
					inputText = "";
					counter = 0;
					while(1)
					{
						if(counter &gt;= inputTextCount)
							break;
						inputText += getStringArrayAt(inputTextArray, counter);
						counter += 1;
					}
					
				}	
			}
			
		}
		else
		{
		if (itemTitle == "spac") itemTitle=" ";
			
		if(firstUse == 1)
		  {
	      inputText = "";
	      inputTextArray = null;
	      inputTextCount = 0;
  		  firstUse = 0;
		  }
		
		inputText += itemTitle;
		inputTextArray  = pushBackStringArray(inputTextArray, itemTitle);
		inputTextCount -= -1;
		}
		null;
	</onClick>
	</item_template>
	<?php
	}
	
	public function channel()
	{
	?>
		<channel>
			<title>Keyboard</title>
			<itemSize>
				<script>
					itemSize;
				</script>
			</itemSize>
		</channel>
	
	<?php
	}
	

	
	public function showDisplay()
	{
	global $ua_images_path;
	?>
	<mediaDisplay name="photoView"
		rowCount			= "4" 
		columnCount			= "12"
		drawItemText		= "no" 
		menuBorderColor		= "0:0:0" 
		sideColorBottom		= "0:0:0" 
		sideColorTop		= "0:0:0" 
		sideBottomHeightPC	= "0"
		fontSize			= "22"
		sideTopHeightPC		= "0"
		
		itemOffsetXPC		= "9" 
		itemOffsetYPC		= "30" 
		itemWidthPC			= "6.5" 
		itemHeightPC		= "11.5" 
		itemBackgroundColor = "0:0:0"
		itemBorderColor		= "0:0:0" 
		backgroundColor		= "0:0:0" 
		sliding				= "no" 
		showHeader			= "no" 
		showDefaultInfo		= "no"
		rollItems			= "no" 
		forceFocusOnItem	= "yes"
		itemGapXPC			= "0.4"
		itemGapYPC			= "0.4"
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
		$this->onEnters();
		$this->itemTemplate_content();
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
	
$view = new ua_rss_keyb;
$view->showRss();
exit;	
?>