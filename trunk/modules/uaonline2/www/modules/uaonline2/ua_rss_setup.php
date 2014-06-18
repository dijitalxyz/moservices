<?php

/*	------------------------------
	Ukraine online services 2	
	RSS EX.UA & uakino setup module v 2.5
	------------------------------
	Created by Sashunya 2014	
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
	
	// текст заголовка
	const text_header_align		=	'left'; // далее идут константы для текста заголовка
	const text_header_redraw	=	'yes';
	const text_header_lines		=	'1';
	const text_header_offsetXPC	=	'28';
	const text_header_offsetYPC	=	'2';
	const text_header_widthPC	=	'70';
	const text_header_heightPC	=	'10';
	const text_header_fontSize	=	'20'; // размер шрифта заголовка
	const text_header_backgroundColor	=	'-1:-1:-1';// фон 
	const text_header_foregroundColor	=	'255:255:255'; //цвет шрыфта
	
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
	const text_footer_foregroundColor	=	'255:255:255'; 
    
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
	itemTitleArray  = pushBackStringArray(itemTitleArray, "КАЧЕСТВО:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ПОСТЕРЫ:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ДЕКОДИРОВАНИЕ СТРОК:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "АЛЬТЕРНАТИВН. ПЛЕЕР:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ВСТРОЕН. КЛАВИАТУРА:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "СОХРАНЯТЬ ПОЗИЦИЮ:");
	itemTitleArray  = pushBackStringArray(itemTitleArray, "ФОНОВАЯ ЗАСТАВКА:");
	itemSize = 9;
	
	qualArray = null;
	qualCount = 2;
	qualArray = pushBackStringArray(qualArray, "НИЗКОЕ");
	qualArray = pushBackStringArray(qualArray, "ВЫСОКОЕ");
	qualIndex = 0;
	
	exPosterArray = null;
	exPosterCount = 2;
	exPosterArray = pushBackStringArray(exPosterArray, "ВЫКЛ.");
	exPosterArray = pushBackStringArray(exPosterArray, "ВКЛ.");
	exPosterIndex = 0;
	
	altPlayerArray = null;
	altPlayerCount = 2;
	altPlayerArray = pushBackStringArray(altPlayerArray, "ВЫКЛ.");
	altPlayerArray = pushBackStringArray(altPlayerArray, "ВКЛ.");
	altPlayerIndex = 0;
	
	keybArray = null;
	keybCount = 2;
	keybArray = pushBackStringArray(keybArray, "ВЫКЛ.");
	keybArray = pushBackStringArray(keybArray, "ВКЛ.");
	keybIndex = 0;
	
	positionArray = null;
	positionCount = 2;
	positionArray = pushBackStringArray(positionArray, "ВЫКЛ.");
	positionArray = pushBackStringArray(positionArray, "ВКЛ.");
	positionIndex = 0;
	
	screensaverArray = null;
	screensaverCount = 2;
	screensaverArray = pushBackStringArray(screensaverArray, "ВЫКЛ.");
	screensaverArray = pushBackStringArray(screensaverArray, "ВКЛ.");
	screensaverIndex = 0;
	
	uakinoDecodeArray = null;
	uakinoDecodeCount = 2;
	uakinoDecodeArray = pushBackStringArray(uakinoDecodeArray, "ВКЛ.");
	uakinoDecodeArray = pushBackStringArray(uakinoDecodeArray, "ВЫКЛ.");
	uakinoDecodeIndex = 0;
	
	dlok = getURL("<?=$ua_path_link.$ua_setup_parser_filename."?oper=load"?>");
		if (dlok != null)
			{
				regIdx = getStringArrayAt(dlok, 0);
				langIdx = getStringArrayAt(dlok, 1);
				uakinoIdx = getStringArrayAt(dlok, 2);
				altplayerIdx = getStringArrayAt(dlok, 3);
				keybIdx = getStringArrayAt(dlok, 4);
				qualIdx = getStringArrayAt(dlok, 5);
				positionIdx = getStringArrayAt(dlok, 6);
				screensaverIdx = getStringArrayAt(dlok, 7);
				exPosterIdx = getStringArrayAt(dlok, 8);
			}
	regionIndex -=-regIdx;
	languageIndex -= -langIdx;
	uakinoDecodeIndex -= -uakinoIdx;
	altPlayerIndex -= -altplayerIdx;
	keybIndex -= -keybIdx;
	positionIndex -= -positionIdx;
	qualIndex -= -qualIdx;
	screensaverIndex -= -screensaverIdx;
	exPosterIndex -= -exPosterIdx;
	setFocusItemIndex(0);
	setItemFocus(0);
	redrawDisplay();
</onEnter>

<onExit>
	showIdle();
	saveSettingsArray = null;
	saveSettingsArray = pushBackStringArray(saveSettingsArray, regionIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, languageIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, uakinoDecodeIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, altPlayerIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, keybIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, qualIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, positionIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, screensaverIndex);
	saveSettingsArray = pushBackStringArray(saveSettingsArray, exPosterIndex);
	writeStringToFile ("/tmp/ua_set",saveSettingsArray);
	dlok = getURL("<?=$ua_path_link.$ua_setup_parser_filename."?oper=save"?>");
	writeStringToFile("/tmp/env_returnFromList_message", "1");
	cancelIdle();
</onExit>

<mediaDisplay name="onePartView"
	sideColorLeft		="0:0:0"
	sideLeftWidthPC		="0"
	sideColorRight		="0:0:0"
	itemXPC				="25"
	itemYPC				="15"
	itemWidthPC			="30"
	itemHeightPC		="7"
	itemPerPage			="9"
	backgroundColor		="-1:-1:-1"
	itemBackgroundColor	="-1:-1:-1"
	showHeader			=no
	selectMenuOnRight	=no
	forceFocusOnItem	=yes
	forceFocusOnMenu	=no
	showDefaultInfo		=no
	rollItems			= "no"
	forceRedrawItems	= yes
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
	<backgroundDisplay name=SetupMenuBackground>
			<image  redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
					<?=$ua_images_path?>ua_background_main.png
			</image>
	</backgroundDisplay>
		
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		НАСТРОЙКИ
	</text>
	
	<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 Выход
	</text>
	<image redraw="no" offsetXPC="17" offsetYPC="90" widthPC="3" heightPC="6">
			<?=$ua_images_path?>ua_back.png
	</image>
	<text  align="left" redraw="yes" lines="1" offsetXPC="10" offsetYPC="25.5" widthPC="50" heightPC="10" fontSize="14" backgroundColor="-1:-1:-1">
		<foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx &gt;= 0 &amp;&amp; idx &lt;=3 ) color = "255:227:37";
				else color = "255:255:255";
				color;
			</script>
		</foregroundColor>
		<script>"EX.UA";</script>
	</text>
	
	<text  align="left" redraw="yes" lines="1" offsetXPC="10" offsetYPC="41.5" widthPC="50" heightPC="10" fontSize="14" backgroundColor="-1:-1:-1" >
	<foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 4) color = "255:227:37";
				else color = "255:255:255";
				color;
			</script>
		</foregroundColor>
		<script>"UAKINO.NET";</script>
	</text>
	
	<text  align="left" redraw="yes" lines="1" offsetXPC="10" offsetYPC="59.5" widthPC="50" heightPC="10" fontSize="14" backgroundColor="-1:-1:-1" >
	<foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx &gt;= 5 &amp;&amp; idx &lt;=8 ) color = "255:227:37";
				else color = "255:255:255";
				color;
			</script>
		</foregroundColor>
		<script>"РАЗНОЕ";</script>
	</text>
	
	
	
	<text redraw="yes" offsetXPC=57 offsetYPC=14.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1 foregroundColor=150:150:150>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 0)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  region = getStringArrayAt(regionArray, regionIndex);
	  region;
	  </script>
	</text>
 	<text redraw="yes" offsetXPC=57 offsetYPC=21.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 1)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  language = getStringArrayAt(languageArray, languageIndex);
	  language;
	  </script>
	</text>
 	 <text redraw="yes" offsetXPC=57 offsetYPC=28.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 2)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  qual = getStringArrayAt(qualArray, qualIndex);
	  qual;
	  </script>
	</text>
	
	<text redraw="yes" offsetXPC=57 offsetYPC=35.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 3)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  exPoster = getStringArrayAt(exPosterArray, exPosterIndex);
	  exPoster;
	  </script>
	</text>
	
	
	<text redraw="yes" offsetXPC=57 offsetYPC=42.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 4)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  uakinoDecode = getStringArrayAt(uakinoDecodeArray, uakinoDecodeIndex);
	  uakinoDecode;
	  </script>
	</text>
	<text redraw="yes" offsetXPC=57 offsetYPC=49.5 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 5)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  altPlayer = getStringArrayAt(altPlayerArray, altPlayerIndex);
	  altPlayer;
	  </script>
	</text>
	<text redraw="yes" offsetXPC=57 offsetYPC=56 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 6)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  keyb = getStringArrayAt(keybArray, keybIndex);
	  keyb;
	  </script>
	</text>
	<text redraw="yes" offsetXPC=57 offsetYPC=63 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 7)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  pos = getStringArrayAt(positionArray, positionIndex);
	  pos;
	  </script>
	</text>	
	<text redraw="yes" offsetXPC=57 offsetYPC=70 widthPC=30 heightPC=8 fontSize=14 backgroundColor=-1:-1:-1>
	  <foregroundColor>
			<script>
				idx = Integer(getFocusItemIndex());
				if (idx == 8)
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
	  <script>
	  scr = getStringArrayAt(screensaverArray, screensaverIndex);
	  scr;
	  </script>
	</text>	
<itemDisplay>
		<text offsetXPC=5 offsetYPC=25 widthPC=100 heightPC=50 fontSize=14 backgroundColor=-1:-1:-1 >
		<foregroundColor>
			<script>
				drawState = getDrawingItemState();
				if (drawState == "focus")
				{
					color = "255:227:37";
				}
				else  color = "255:255:255";
				color;
			</script>
		</foregroundColor>
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

      if (majorContext == "items" &amp;&amp; idx &lt; itemSize)
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
            if(qualIndex == 0)
              qualIndex = qualCount - 1;
            else
              qualIndex = qualIndex - 1;
          }
		   else if(idx == 3)
          {
            if(exPosterIndex == 0)
              exPosterIndex = exPosterCount - 1;
            else
              exPosterIndex = exPosterIndex - 1;
          }else if(idx == 4)
          {
            if(uakinoDecodeIndex == 0)
              uakinoDecodeIndex = uakinoDecodeCount - 1;
            else
              uakinoDecodeIndex = uakinoDecodeIndex - 1;
          }
		   else if(idx == 5)
          {
            if(altPlayerIndex == 0)
              altPlayerIndex = altPlayerCount - 1;
            else
              altPlayerIndex = altPlayerIndex - 1;
          }
		   else if(idx == 6)
          {
            if(keybIndex == 0)
              keybIndex = keybCount - 1;
            else
              keybIndex = keybIndex - 1;
          } else if(idx == 7)
          {
            if(positionIndex == 0)
              positionIndex = positionCount - 1;
            else
              positionIndex = positionIndex - 1;
          } else if(idx == 8)
          {
            if(screensaverIndex == 0)
              screensaverIndex = screensaverCount - 1;
            else
              screensaverIndex = screensaverIndex - 1;
          }
		  
          ret = "true";
          
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
            qualIndex = qualIndex + 1;
            if(qualIndex == qualCount)
            {
              qualIndex = 0;
            }
          }
			else if(idx == 3)
          {
             exPosterIndex = exPosterIndex + 1;
            if(exPosterIndex == exPosterCount)
            {
              exPosterIndex = 0;
            }
		  }
		  else if(idx == 4)
          {
            uakinoDecodeIndex = uakinoDecodeIndex + 1;
            if(uakinoDecodeIndex == uakinoDecodeCount)
            {
              uakinoDecodeIndex = 0;
            }
          }
		  else if(idx == 5)
          {
            altPlayerIndex = altPlayerIndex + 1;
            if(altPlayerIndex == altPlayerCount)
            {
              altPlayerIndex = 0;
            }
          }
		  else if(idx == 6)
          {
           keybIndex = keybIndex + 1;
            if(keybIndex == keybCount)
            {
              keybIndex = 0;
            }
          }
		  else if(idx == 7)
          {
           positionIndex = positionIndex + 1;
            if(positionIndex == positionCount)
            {
              positionIndex = 0;
            }
          }
		  else if(idx == 8)
          {
           screensaverIndex = screensaverIndex + 1;
            if(screensaverIndex == screensaverCount)
            {
              screensaverIndex = 0;
            }
          }
			ret = "true";
			
        }
      }
	redrawDisplay();      
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