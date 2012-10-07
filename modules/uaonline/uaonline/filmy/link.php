#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threeView" 
	showHeader=yes
			itemPerPage=3
			forceRedrawItems=yes
	itemBackgroundColor="0:0:0" 
	backgroundColor="0:0:0" 
	sideLeftWidthPC="0" 
			headerImageXPC = 0
		headerImageYPC = 0
		headerImageWidthPC = 0
		headerImageHightPC = 0
		headerCapWidthPC = 0
		headerWidthPC = 0
	itemXPC="20" 
	itemYPC="20" 
	itemWidthPC="76" 
	itemHeightPC = 15
		itemImageXPC="5"
		itemImageYPC = 20
		itemImageWidthPC = 10
		itemImageHeightPC = 20
	unFocusFontColor="101:101:101" 
	focusFontColor="255:255:255" 
	popupXPC = "70"
  popupYPC = "20"
  popupWidthPC = "22.3"
  popupHeightPC = "5.5"
  popupFontSize = "13"
	popupBorderColor="28:35:51" 
	popupForegroundColor="255:255:255"
 	popupBackgroundColor="28:35:51">
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_01.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_02.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_03.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_04.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_05.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_06.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_07.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_08.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>


<onUserInput>
    <script>
	userInput = currentUserInput();


	if ( userInput == "video_frwd"|| userInput == "video_play" )      
	   {

 	   itemLink = getItemInfo(getFocusItemIndex(), "downurl");
       itemTitle = getItemInfo(getFocusItemIndex(), "downame"); 
	   dlok = loadXMLFile("http://127.0.0.1/cgi-bin/scripts/download/download_mod.php?title=" + urlEncode(itemTitle) + "&amp;downloadlink=" + urlEncode(itemLink));
       jumpToLink("manage");
	   redrawDisplay();
	   }
<!--     if ( userInput == "video_frwd" )
	   {

	    jumpToLink("manage");
		redrawDisplay();
	}     

-->


    </script>
</onUserInput>




		<backgroundDisplay>
			<image  offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/tmp/www/cgi-bin/scripts/exua/image/backgd.jpg
			</image>  
		</backgroundDisplay> 
		<image  offsetXPC=0 offsetYPC=2.8 widthPC=100 heightPC=15.6>
		/tmp/www/cgi-bin/scripts/exua/image/rss_title.jpg
		</image>
		<text  offsetXPC=40 offsetYPC=8 widthPC=35 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		ФИЛЬМЫ filmy.net.ua
		</text>			

		<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=14 backgroundColor=0:0:0 foregroundColor=120:120:120> Информация-[&#62;]. Загрузить-[&#60;&#60;]</text>

</mediaDisplay>

<manage>
	<link>http://127.0.0.1/cgi-bin/scripts/download/download.php</link>
</manage>	

<channel>
	<title>ФІльми filmy.net.ua</title>
	<menu>main menu</menu>

<?php 
function utf8_encode_b( $string, $source = "windows-1251" )
{
   if ( function_exists( "iconv" ) )
   {
      return iconv( $source, 'utf-8', $string ); 
   } else
   {
      $out = '';
      for ( $i = 0; $i < strlen( $string ); ++$i )
      {
         $ch = ord( $string{$i} );
         if ( $ch < 0x80 )
         {
            $out .= chr( $ch );
         } else
         if ( $ch >= 0xC0 )
         {
            if ( $ch < 0xF0 )
            {
               $out .= "\xD0".chr(0x90 + $ch - 0xC0); // А-Я, а-п (A-YA, a-p)
            } else
            {
               $out .= "\xD1".chr(0x80 + $ch - 0xF0); // р-я (r-ya)
            }; //if
         } else
         switch( $ch )
         {
            case 0xA8: $out .= "\xD0\x81"; break; // YO
            case 0xB8: $out .= "\xD1\x91"; break; // yo
            // ukrainian
            case 0xA1: $out .= "\xD0\x8E"; break; // Ў (U)
            case 0xA2: $out .= "\xD1\x9E"; break; // ў (u)
            case 0xAA: $out .= "\xD0\x84"; break; // Є (e)
            case 0xAF: $out .= "\xD0\x87"; break; // Ї (I..)
            case 0xB2: $out .= "\xD0\x86"; break; // I (I)
            case 0xB3: $out .= "\xD1\x96"; break; // i (i)
            case 0xBA: $out .= "\xD1\x94"; break; // є (e)
            case 0xBF: $out .= "\xD1\x97"; break; // ї (i..)
            // chuvashian
            case 0x8C: $out .= "\xD3\x90"; break; // ? (A)
            case 0x8D: $out .= "\xD3\x96"; break; // ? (E)
            case 0x8E: $out .= "\xD2\xAA"; break; // ? (SCH)
            case 0x8F: $out .= "\xD3\xB2"; break; // ? (U)
            case 0x9C: $out .= "\xD3\x91"; break; // ? (a)
            case 0x9D: $out .= "\xD3\x97"; break; // ? (e)
            case 0x9E: $out .= "\xD2\xAB"; break; // ? (sch)
            case 0x9F: $out .= "\xD3\xB3"; break; // ? (u)
         }; //switch
      }; //for
      return $out;
   }; //if
}; //func

$link = $_GET["file"];
  $link = file_get_contents($link);
  $link = convert_cyr_string($link, k, w);
   $link = utf8_encode_b($link);
  $t1 = explode('href="http://www.filmy.net.ua:8080', $link);
   $t2 = explode('"', $t1[1]);
   $ln = 'http://www.filmy.net.ua:8080'.$t2[0];
   $tmp = $t2[0];
   $t4 = explode("/", $tmp);
   $down = $t4[sizeof($t4)-1];
   
	$t1 = explode('<div style="padding: 4px 0px 0px 4px;color: #e2e2e2; font-size: 11px;" >', $link);
	$t1 = explode('</div>', $t1[1]);
	$ds =  strip_tags($t1[0]);
	
	$t1 = explode('<td id="menusel"><h1>', $link);
	$t1 = explode('</h1></td>', $t1[1]);
	$title =  strip_tags($t1[0]);
	
	$t1 = explode('<img src="/images/resize/', $link);
	$t1 = explode('"', $t1[1]);
	$image = 'http://www.filmy.net.ua/images/resize/'.$t1[0];
	
    echo '<item>';
    echo '<title>'.$down.'</title>';
    echo '<downame>'.$down.'</downame>';
    echo '<downurl>'.$ln.'</downurl>';
    //echo '<title>'.$title.'</title>';
	echo '<description>'.$ds.'</description>';
//    echo  '<enclosure type="video"  url="'.$ln.'" />';
	echo  '<media:content url="'.$ln.'" />';
	echo  '<media:thumbnail url="'.$image.'" />';
	echo '</item>
	';

?>
</channel>
</rss>