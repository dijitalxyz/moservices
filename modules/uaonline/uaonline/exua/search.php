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
	capWidthPC="70"
	unFocusFontColor="101:101:101"
	focusFontColor="255:255:255"
	popupXPC = "40"
  popupYPC = "55"
  popupWidthPC = "22.3"
  popupHeightPC = "5.5"
  popupFontSize = "13"
	popupBorderColor="28:35:51"
	popupForegroundColor="255:255:255"
 	popupBackgroundColor="28:35:51"
>
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
function latrus ($string) # Теперь задаём функцию перекодировки транслита в кириллицу.
{
$string = ereg_replace("zh","ж",$string);
$string = ereg_replace("yo","ё",$string);
$string = ereg_replace("jo","ё",$string);
$string = ereg_replace("yi","ї",$string);
$string = ereg_replace("jі","ї",$string);
$string = ereg_replace("ju","ю",$string);
$string = ereg_replace("yu","ю",$string);
$string = ereg_replace("sh","ш",$string);
$string = ereg_replace("yе","є",$string);
$string = ereg_replace("jа","я",$string);
$string = ereg_replace("yа","я",$string);
$string = ereg_replace("ch","ч",$string);
$string = ereg_replace("i","і",$string);
$string = ereg_replace("'","ь",$string);
$string = ereg_replace("c","ц",$string);
$string = ereg_replace("u","у",$string);
$string = ereg_replace("k","к",$string);
$string = ereg_replace("e","е",$string);
$string = ereg_replace("n","н",$string);
$string = ereg_replace("g","г",$string);
$string = ereg_replace("z","з",$string);
$string = ereg_replace("h","х",$string);
$string = ereg_replace("f","ф",$string);
$string = ereg_replace("y","и",$string);
$string = ereg_replace("j","й",$string);
$string = ereg_replace("v","в",$string);
$string = ereg_replace("w","ы",$string);
$string = ereg_replace("a","а",$string);
$string = ereg_replace("p","п",$string);
$string = ereg_replace("r","р",$string);
$string = ereg_replace("o","о",$string);
$string = ereg_replace("l","л",$string);
$string = ereg_replace("d","д",$string);
$string = ereg_replace("s","с",$string);
$string = ereg_replace("m","м",$string);
$string = ereg_replace("t","т",$string);
$string = ereg_replace("b","б",$string);

return $string;
}

$query = $_GET["query"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
}

$host = 'http://'.$_SERVER['HTTP_HOST'];

$search = file_get_contents("/tmp/tmp_search.dat");
if($search[0]=="`") {
$search=substr($search, 1);
$search=str_replace("\'", "'", $search);
 $search=latrus($search);

    }

if(!$page) $page=1;
	echo '
		<text  offsetXPC=40 offsetYPC=8 widthPC=35 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		ПОИСК "'.$search.'"
		</text>
		<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=15 backgroundColor=0:0:0 foregroundColor=120:120:120>Страница: '.$page.'</text>
</mediaDisplay>
<channel>
	<title>ПОИСК "'.$search.'"</title>
	<menu>main menu</menu>
';
	$search=urlencode($search);


	if($page) {
 $nt= $page-1;
        $html = file_get_contents("http://www.ex.ua/search?s=".$search."&p=".$nt);
    }
else {
    $page = 1;
        $html = file_get_contents("http://www.ex.ua/search?s=".$search);
    }

if($page > 1) { ?>

<item>
<?php
$sThisFile = 'http://127.0.0.1'.$_SERVER['SCRIPT_NAME'];
$url = $sThisFile."?query=".($page-1).",";
?>
<title>Предыдущая страница</title>
<link><?php echo $url;?></link>
<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/left.jpg" />
</item>
<?php } ?>

<?php

$videos = explode("</td></tr><tr><td><a href='/view", $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1 = explode("/", $video);
    $t2 = explode("'", $t1[1]);
    $link = 'http://www.ex.ua/playlist/'.$t2[0];
    $t3 = $t2[0];
    $link1 = file($link);

    $t1 = explode("' alt='", $video);
    $t2 = explode("'", $t1[1]);
    $title = $t2[0];

    $t1 = explode(" src='", $video);
    $t2 = explode("'", $t1[1]);
    $image = $t2[0];

if (($image!="")&($title!="")&($link!=""))   {
   if (count($link1)==0) $link ="http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev2.php?query=".$t3;
   else $link = "http://127.0.0.1/cgi-bin/scripts/exua/php/exua_link.php?file=".$link;
   echo '
    <item>
    <title>'.$title.'</title>
    <link>'.$link.'</link>
    <media:thumbnail url="'.$image.'" />
    </item>
    ';
	}
	}
?>

<item>
<?php
$sThisFile = 'http://127.0.0.1'.$_SERVER['SCRIPT_NAME'];
$url = $sThisFile."?query=".($page+1).",";
?>
<title>Следующая страница</title>
<link><?php echo $url;?></link>
<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/right.jpg" />
</item>

</channel>
</rss>