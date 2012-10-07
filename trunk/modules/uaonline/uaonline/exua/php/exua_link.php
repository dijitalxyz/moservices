#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
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
	itemHeightPC = 20
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
 	popupBackgroundColor="28:35:51" >
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_01.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_02.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_03.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_04.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_05.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_06.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_07.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_08.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
<!--  -->
<onUserInput>
    <script>
	userInput = currentUserInput();

    if( userInput == "video_ffwd" || userInput == "video_quick_stop")
	{
       bookmarksPath = getStoragePath("key")+"ex_bookmarks.dat";
       bookmarksdat = null;
       if (view != null &amp;&amp; view != "1" ) {
         bookmarksdat = readStringFromFile(bookmarksPath);
         bookmarksdat = pushBackStringArray(bookmarksdat, view);
         writeStringToFile(bookmarksPath, bookmarksdat);
         view = null;
         redrawDisplay();
         }
	}


	if ( userInput == "video_frwd"|| userInput == "video_play" )      
	   {

 	   itemLink = getItemInfo(getFocusItemIndex(), "download");
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






<?php

  function UrlTranslit($string, $allow_slashes = false, $allow_dots = false)
  {
   $slash = "";
   $dots = "\.";
   $reverse = "";
   if ($allow_slashes) $slash = "\/";
   if ($allow_dots){ $dots = ""; $reverse = "\."; }

  $convert_to = array(
    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
    "v", "w", "x", "y", "z", "à", "á", "â", "ã", "ä", "å", "æ", "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï",
    "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "ø", "ù", "ú", "û", "ü", "ý", "а", "б", "в", "г", "д", "е", "ё", "ж",
    "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы",
    "ь", "э", "ю", "я"
  );
  $convert_from = array(
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U",
    "V", "W", "X", "Y", "Z", "À", "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï",
    "Ð", "Ñ", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж",
    "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ъ",
    "Ь", "Э", "Ю", "Я"
  );
  $cyr = array(
    "Щ",  "Ш", "Ч", "Ц","Ю", "Я", "Ж", "А","Б","В","Г","Д","Е","Ё","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х", "Ь","Ы","І","Э","Є","Ї",
    "щ",  "ш", "ч", "ц","ю", "я", "ж", "а","б","в","г","д","е","ё","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х", "ь","ы","і","э","є","ї");
  $lat = array(
    "Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","'","Y","I","E","Je","Ji",
    "shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","'","y","i","e","je","ji"
    );
   $string = preg_replace("/[_\s".$dots.",?!\[\](){}]+/", "_", $string);
   $string = preg_replace("/-{2,}/", "--", $string);
   $string = preg_replace("/_-+_/", "--", $string);
   $string = preg_replace("/[_\-]+$/", "", $string);
   $string = str_replace($convert_from, $convert_to, $string);
   $string = preg_replace("/(ь|ъ)/", "", $string);
   $string = str_replace($cyr, $lat, $string);
   $string = preg_replace("/[^".$slash.$reverse."0-9a-z_\-]+/", "", $string);
   return $string;
  }

$link1 = $_GET["file"];
    $link = file($link1);
    $quality = file("/usr/local/etc/dvdplayer/ex_settings.dat");
	$t1 = explode("http://www.ex.ua/playlist/", $link1);
	$t2 = explode(".m3u", $t1[1]);
	$t3[0] = $t2[0];

	$ds = "http://www.ex.ua/view/".$t2[0];
	$ds = file_get_contents($ds);
	$cont = $ds;
	$t1 = explode("<table width=100% cellpadding=0 cellspacing=0 border=0><tr><td valign=top>", $ds);
	$t2 = explode("</td>", $t1[1]);
	$t2 = explode("<p>", $t2[0]);
	$ds =  strip_tags($t2[1].' --- '.$t2[2].' --- '.$t2[3].' --- '.$t2[4]);

	$hd = explode("alt='", $t2[0]);
	$hd = explode("'", $hd[1]);
	if ($quality[0][0]=='1') {$qual="Высокое";} else {$qual="Низкое";}



    echo '
		<text  offsetXPC=20 offsetYPC=8 widthPC=75 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		'.$hd[0].'
		</text>
		<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=85 widthPC=75 heightPC=5 fontSize=14 backgroundColor=0:0:0 foregroundColor=120:120:120> Информация-[&#62;]. Загрузить-[&#60;&#60;]. Качество видео: '.$qual.'</text>


<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=18 backgroundColor=0:0:0 >

 <foregroundColor>
  <script>
    if (view == null)   "120:120:255";
    else    "120:120:120";
  </script>
 </foregroundColor>

  <script>
   if (view == null) "УЖЕ В ЗАКЛАДКАХ";
   else if (view == "1" ) "";
        else "ДОБАВИТЬ В ЗАКЛАДКИ - [>>]";
  </script>

</text>

</mediaDisplay>


<onEnter>';
   if (count($link)==0) echo 'view ="1";';
   else echo 'view='.$t3[0].';';
   echo '
   bookmarksPath = getStoragePath("key")+"ex_bookmarks.dat";
   bookmarksdat = null;
   bookmarksdat = readStringFromFile(bookmarksPath);
   counter = 0;
     while (1)
      {
       bkmrk = null;
   	   bkmrk=getStringArrayAt(bookmarksdat, counter);
   	   if ( bkmrk == null ||  bkmrk == "")   break;
   	   if ( bkmrk == view )   { view=null; break;}
   	   counter+=1;
      }
</onEnter>


';

	if (count($link)==0)  {
		echo '
		 <script>
 		   JumpToLink("page2");
 	     </script>';
       }


   echo '



<destination>
	<link>
	  	<script>
	  	  "http://127.0.0.1/cgi-bin/scripts/download/download.php?link=" + getItemInfo(getFocusItemIndex(),"download") + "=" + getItemInfo(getFocusItemIndex(),"name");
	  	</script>
	</link>
</destination>

<page2>
 <link>
	<script>
	  "http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev2.php?query='.$t3[0].'";
	 </script>
 </link>
</page2>


<manage>
	<link>http://127.0.0.1/cgi-bin/scripts/download/download.php</link>
</manage>	

<channel>

	<title>'.$hd[0].'</title>
	<menu>main menu</menu>';

	$name= substr($hd[0],0,40);
	$name= UrlTranslit($name);
	$col_lines = count($link);
	for ($i=0; $i<$col_lines; $i++ ) {
	$ln = str_replace("\n","",$link[$i]);
    $ln = str_replace("\r","",$ln);
	$dn = explode("http://www.ex.ua/get/", $ln);
	$flv_ln= explode("http://www.ex.ua/show/".$dn[1], $cont);
	$flvlink = explode(".flv", $flv_ln[1]);
    $ln2 = "http://www.ex.ua/show/".$dn[1].$flvlink[0].".flv";


    echo '<item>';
	$title = $i+1;


	$findme = "<a href='/get/".$dn[1]."' title='";
	$cont2 = substr($cont,strpos($cont, $findme)+strlen($findme));

	if ($quality[0][0]=='1'){
	$play = $ln;
	}else{ 	$play = $ln2;}

    echo '<title>'.$title.'. '.strtoupper(substr($cont2,0,strpos($cont2, "' rel="))).'</title>';
//	echo '<title>'.$link[$i].'_'.$play.'</title>';
//    echo '<download>'.$dn[1].'</download>';
    echo '<download>'.$ln.'</download>';
    echo '<downame>'.substr($cont2,0,strpos($cont2, "' rel=")).'</downame>';
	if ($title==1) $title='';
	echo '<name>'.$name.''.$title.'</name>';
	echo '<description>'.$ds.'</description>';
//    echo  '<enclosure type="video/x-flv"  url="'.$play.'" />';
	echo  '<media:content url="'.$play.'" />';
    echo '</item>';

}
?>
</channel>
</rss>