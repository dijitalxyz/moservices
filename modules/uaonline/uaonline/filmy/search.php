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
function Utf8ToWin($fcontents) {
     $out = $c1 = '';
     $byte2 = false;
     for ($c = 0;$c < strlen($fcontents);$c++) {
         $i = ord($fcontents[$c]);
         if ($i <= 127) {
             $out .= $fcontents[$c];
         }
         if ($byte2) {
             $new_c2 = ($c1 & 3) * 64 + ($i & 63);
             $new_c1 = ($c1 >> 2) & 5;
             $new_i = $new_c1 * 256 + $new_c2;
             if ($new_i == 1025) {
                 $out_i = 168;
             } else {
                 if ($new_i == 1105) {
                     $out_i = 184;
                 } else {
                     $out_i = $new_i - 848;
                 }
             }
             // UKRAINIAN fix
             switch ($out_i){
                 case 262: $out_i=179;break;
                 case 182: $out_i=178;break;
                 case 260: $out_i=186;break;
                 case 180: $out_i=170;break;
                 case 263: $out_i=191;break;
                 case 183: $out_i=175;break;
                 case 321: $out_i=180;break;
                 case 320: $out_i=165;break;
             }
             $out .= chr($out_i);
             
             $byte2 = false;
         }
         if ( ( $i >> 5) == 6) {
             $c1 = $i;
             $byte2 = true;
         }
     }
     return $out;
 }


$query = $_GET["sch"];
$search = file_get_contents("/tmp/tmp_search.dat");
$tmpsrch= $search;
$search = Utf8ToWin($search);
$search = convert_cyr_string($search, w, k);
//file_put_contents("/usr/local/etc/mos/uaonline/log", $search."_2\n", FILE_APPEND | LOCK_EX);	
$image = 'http://www.filmy.net.ua/images/logo.jpg';

$host = 'http://'.$_SERVER['HTTP_HOST'];
$html = file_get_contents("http://www.filmy.net.ua/?com=search&text=".urlencode($search));
echo '
		<text  offsetXPC=30 offsetYPC=8 widthPC=75 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		ПОИСК - '.$tmpsrch.'
		</text>
</mediaDisplay>
<channel>
	<title>ПОИСК - '.$tmpsrch.'</title>
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




$videos = explode('/film/', $html);
unset($videos[0]);
unset($videos[1]);
foreach ($videos as $value){
	$v1 = explode('/" title="', $value);
	$v1[0] = substr($v1[0],0,stripos($v1[0],'/'));	
	$v1[0] = convert_cyr_string($v1[0], k, w);
	$v1[0] = utf8_encode_b($v1[0]);

	$link = 'http://www.filmy.net.ua/film/'.$v1[0];
	$link = "http://127.0.0.1/cgi-bin/scripts/filmy/link.php?file=".$link;

        $tmp = ';">
									';
	$v2 = explode($tmp, $v1[1]);
	$out=substr($v2[1],0,stripos($v2[1],'	'));

	$out = convert_cyr_string($out, k, w);
	$out = utf8_encode_b($out);

        
    	echo '
	    <item>
		    <title>'.$out.'</title>
		    <link>'.$link.'</link>
		    <media:thumbnail url="'.$image.'" />
	    </item>
    ';


}


?>

</channel>
</rss>