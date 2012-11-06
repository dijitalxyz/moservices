<?php

/*	------------------------------
	Ukraine online services 2	
	RSS EX.UA & uakino setup module v 2.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */

include ("ua_paths.inc.php");
	
class ua_rss_setup_const 
{
	const imageFocus 			= 	'ua_focus_category.bmp';
	const imageParentFocus 		= 	'ua_parent_focus_category.bmp';
	const imageUnFocus 			= 	'ua_unfocus_category.bmp';
	const background_image		=   'ua_setup_border.png';
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
	// текст заголовка
	const text_header_align		=	'left'; // далее идут константы для текста заголовка
	const text_header_redraw	=	'yes';
	const text_header_lines		=	'1';
	const text_header_offsetXPC	=	'27';
	const text_header_offsetYPC	=	'2';
	const text_header_widthPC	=	'70';
	const text_header_heightPC	=	'10';
	const text_header_fontSize	=	'20'; // размер шрифта заголовка
	const text_header_backgroundColor	=	'-1:-1:-1';// фон 
	const text_header_foregroundColor	=	'0:0:0'; //цвет шрыфта
	
	// текст подписи 
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
	const image_site_footer_display_offsetXPC 	= '85';
	const image_site_footer_display_offsetYPC 	= '90.3';
	const image_site_footer_display_widthPC 	= '4';
	const image_site_footer_display_heightPC	= '5.5';
	const exua_logo								= 'ua_exua_ukr.png';
}
class ua_rss_setup extends ua_rss_setup_const 
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
	global $ua_path;
	global $ua_rss_setup_filename
?>	
<onEnter>
	regionArray = null;
	regionCount = 2;
	regionArray = pushBackStringArray(regionArray, "EX.UA");
	regionArray = pushBackStringArray(regionArray, "FEX.NET");
	regionIndex = 0;
	languageArray = null;
	languageCount = 3;
	languageArray = pushBackStringArray(languageArray, "РУССКИЙ");
	languageArray = pushBackStringArray(languageArray, "УКРАИНСКИЙ");
	languageArray = pushBackStringArray(languageArray, "АНГЛИЙСКИЙ");
	languageIndex = 0;
	itemTitleArray = null;
	itemTitleArray  = pushBackStringArray(itemTitleArray, "САЙТ:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ЯЗЫК:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ДЕКОДИРОВАНИЕ СТРОК:");
	itemSize = 3;
	uakinoDecodeArray = null;
	uakinoDecodeCount = 2;
	uakinoDecodeArray = pushBackStringArray(uakinoDecodeArray, "ВКЛ.");
	uakinoDecodeArray = pushBackStringArray(uakinoDecodeArray, "ВЫКЛ.");
	uakinoDecodeIndex = 0;
	
	dlok = getURL("<?=$ua_path_link.$ua_setup_parser_filename."?load=1"?>");
		if (dlok != null)
			{
				regIdx = getStringArrayAt(dlok, 0);
				langIdx = getStringArrayAt(dlok, 1);
				uakinoIdx = getStringArrayAt(dlok, 2);
			}
	regionIndex -=-regIdx;
	languageIndex -= -langIdx;
	uakinoDecodeIndex -= -uakinoIdx;
	setFocusItemIndex(0);
	setItemFocus(0);
	redrawDisplay();
</onEnter>

<onExit>
dlok = getURL("<?=$ua_path_link.$ua_setup_parser_filename."?save_region="?>"+regionIndex+"&amp;save_language="+languageIndex+"&amp;decode_strings="+uakinoDecodeIndex);
</onExit>

<mediaDisplay name="onePartView"
	sideColorLeft		="0:0:0"
	sideLeftWidthPC		="0"
	sideColorRight		="0:0:0"
	headerImageWidthPC	="0"
	headerXPC			="16"
	headerYPC			="3"
	headerWidthPC		="0"
	itemXPC				="25"
	itemYPC				="20"
	itemWidthPC			="30"
	itemHeightPC		="10"
	capXPC				="51"
	capYPC				="19"
	capHeightPC			="10"
	headerCapXPC		="90"
	headerCapYPC		="10"
	headerCapWidthPC	="0"
	backgroundColor		="0:0:0"
	itemBackgroundColor	="0:0:0"
	showHeader			=no
	selectMenuOnRight	=no
	forceFocusOnItem	=yes
	forceFocusOnMenu	=no
	showDefaultInfo		=no
	imageFocus 			= "<?= $ua_images_path.static::imageFocus ?>"
	imageParentFocus 	= "<?= $ua_images_path.static::imageParentFocus ?>"
	imageUnFocus 		= "<?= $ua_images_path.static::imageUnFocus ?>"
	idleImageXPC		="88"
	idleImageYPC		="80"
	idleImageWidthPC	="5"
	idleImageHeightPC	="8"
	>
	<?php
		include("ua_rss_idle.inc.php");
	?>
	<image  redraw="no" offsetXPC="<?= static::header_offsetXPC ?>" offsetYPC="<?= static::header_offsetYPC ?>" widthPC="<?= static::header_widthPC ?>" heightPC="<?= static::header_heightPC ?>">
			<?= $ua_images_path.static::header ?>
	</image>
	
	<image  redraw="no" offsetXPC="<?= static::footer_offsetXPC ?>" offsetYPC="<?= static::footer_offsetYPC ?>" widthPC="<?= static::footer_widthPC ?>" heightPC="<?= static::footer_heightPC ?>">
			<?= $ua_images_path.static::footer ?>
	</image>
		
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		НАСТРОЙКИ
	</text>
	
	<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 ВЫХОД - RETURN
	</text>
	<text  align="left" redraw="yes" lines="1" offsetXPC="10" offsetYPC="25" widthPC="50" heightPC="10" fontSize="16" backgroundColor="-1:-1:-1">
		<foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 0 || idx == 1 ) color = "255:227:37";
				else color = "255:255:255";
				color;
			</script>
		</foregroundColor>
		<script>"EX.UA";</script>
	</text>
	
	<text  align="left" redraw="yes" lines="1" offsetXPC="10" offsetYPC="40" widthPC="50" heightPC="10" fontSize="16" backgroundColor="-1:-1:-1" >
	<foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 2) color = "255:227:37";
				else color = "255:255:255";
				color;
			</script>
		</foregroundColor>
		<script>"UAKINO.NET";</script>
	</text>
	
	
	<text redraw="yes" offsetXPC=57 offsetYPC=20 widthPC=30 heightPC=8 fontSize=17 backgroundColor=8:8:8 foregroundColor=150:150:150>
	  <script>
	  region = getStringArrayAt(regionArray, regionIndex);
	  print("region=",region);
	  region;
	  </script>
	</text>
 	<text redraw="yes" offsetXPC=57 offsetYPC=30 widthPC=30 heightPC=8 fontSize=17 backgroundColor=8:8:8 foregroundColor=150:150:150>
	  <script>
	  language = getStringArrayAt(languageArray, languageIndex);
	  print("language=",language);
	  language;
	  </script>
	</text>
 	
	<text redraw="yes" offsetXPC=57 offsetYPC=40 widthPC=30 heightPC=8 fontSize=17 backgroundColor=8:8:8 foregroundColor=150:150:150>
	  <script>
	  uakinoDecode = getStringArrayAt(uakinoDecodeArray, uakinoDecodeIndex);
	  print("uakinoDecode=",uakinoDecode);
	  uakinoDecode;
	  </script>
	</text>
<itemDisplay>
		<text offsetXPC=0 offsetYPC=25 widthPC=100 heightPC=50 fontSize=17 backgroundColor=-1:-1:-1 foregroundColor=200:200:200>
			<script>
				getStringArrayAt(itemTitleArray , -1);
			</script>
		</text>
</itemDisplay>

	
<onUserInput>
	<script>
      ret = "false";
      
      userInput = currentUserInput();
      majorContext = getPageInfo("majorContext");
      
      print("*** majorContext=",majorContext);
      print("*** userInput=",userInput);
      
      idx = Integer(getFocusItemIndex());

      if (majorContext == "items" &amp;&amp; idx &lt; 3)
      {
        if( userInput == "<?= $key_left ?>" )
        {
          if(idx == 0)
          {
            if(regionIndex == 0)
              regionIndex = regionCount - 1;
            else
              regionIndex = regionIndex - 1;
          }
          else if(idx == 1)
          {
            if(languageIndex == 0)
              languageIndex = languageCount - 1;
            else
              languageIndex = languageIndex - 1;
          }
		  else if(idx == 2)
          {
            if(uakinoDecodeIndex == 0)
              uakinoDecodeIndex = uakinoDecodeCount - 1;
            else
              uakinoDecodeIndex = uakinoDecodeIndex - 1;
          }
          ret = "true";
          redrawDisplay();
        }
        else if( userInput == "<?= $key_right ?>" || userInput == "<?= $key_enter ?>" )
        {
          if(idx == 0)
          {
            regionIndex = regionIndex + 1;
            if(regionIndex == regionCount)
            {
              regionIndex = 0;
            }
          }
          else if(idx == 1)
          {
            languageIndex = languageIndex + 1;
            if(languageIndex == languageCount)
            {
              languageIndex = 0;
            }
          }
		   else if(idx == 2)
          {
            uakinoDecodeIndex = uakinoDecodeIndex + 1;
            if(uakinoDecodeIndex == uakinoDecodeCount)
            {
              uakinoDecodeIndex = 0;
            }
          }
			ret = "true";
			redrawDisplay();
        }
      }
       print("*** regionIndex=",regionIndex);
      print("*** languageIndex=",userInput);
	  ret;
    </script>
</onUserInput>

</mediaDisplay>


<channel>
<title>Advanced Menu Dialog</title>
<link><?=$ua_path.$ua_rss_setup_filename?></link>
<itemSize><script>itemSize;</script></itemSize>

</channel>
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


//-------------------------------
$view = new ua_rss_setup;
$view->showRss();
exit;