#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
	itemBackgroundColor="0:0:0"
	backgroundColor="0:0:0"
	sideLeftWidthPC="0"
	itemImageXPC="5"
	itemXPC="20"
	itemYPC="20"
	itemWidthPC="65"
	unFocusFontColor="101:101:101"
	focusFontColor="255:255:255"
	popupXPC = "40"
  popupYPC = "55"
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
		<backgroundDisplay>
			<image  offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/tmp/www/cgi-bin/scripts/exua/image/backgd.jpg
			</image>
		</backgroundDisplay>
		<image  offsetXPC=0 offsetYPC=2.8 widthPC=100 heightPC=15.6>
		/tmp/www/cgi-bin/scripts/exua/image/rss_title.jpg
		</image>



<?php
$query = $_GET["mode"];
if ($query){
	if ($query == 'rating'){$html = file_get_contents("http://www.filmy.net.ua/rating/"); $disp = 'TOP 20';}
	if ($query == 'last'){$html = file_get_contents("http://www.filmy.net.ua/last/");$disp = 'Последние поступления';}
}

//$html = file_get_contents("http://www.filmy.net.ua/rating/");

echo '
		<text  offsetXPC=30 offsetYPC=8 widthPC=65 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		filmy.net.ua - '.$disp.'
		</text>
</mediaDisplay>
<channel>
	<title>filmy.net.ua - '.$disp.'</title>
	<menu>main menu</menu>
';

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

if ($query == 'rating'){
$videos = explode('<div id="mainfilm" style="padding-left: 15px">
	      <table cellpadding="0" cellspacing="0" border="0">', $html);
}
if ($query == 'last'){
$videos = explode('<div id="mainfilm" style="padding-left: 25px">
	      <table cellpadding="0" cellspacing="0" border="0">', $html);
}



unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
	$video = convert_cyr_string($video, k, w);
	$video = utf8_encode_b($video);

    $t1 = explode('<h2><a href="/film/', $video);
    $t2 = explode('/', $t1[1]);
    $link = 'http://www.filmy.net.ua/film/'.$t2[0];

    $t1 = explode('alt="', $video);
    $t2 = explode('"', $t1[1]);
    $title = $t2[0];

	$t1 = explode('"><img src="', $video);
    $t2 = explode('"', $t1[1]);
    $image = 'http://www.filmy.net.ua'.$t2[0];

   $link = "http://127.0.0.1/cgi-bin/scripts/filmy/link.php?file=".$link;
    echo '
    <item>
    <title>'.$title.'</title>
    <link>'.$link.'</link>
    <media:thumbnail url="'.$image.'" />
    </item>
    ';
}
?>

</channel>
</rss>