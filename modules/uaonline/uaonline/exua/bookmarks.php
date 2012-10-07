#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
	showHeader="no"
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

 <onUserInput>
	userInput = currentUserInput();

	if( userInput == "video_ffwd" || userInput == "video_quick_stop")
	{
	  bookmarksPath = getStoragePath("key")+"ex_bookmarks.dat";
      bookmarksdat = null;
      bookmarksdat = readStringFromFile(bookmarksPath);
      if (bookmarksdat != null &amp;&amp; bookmarksdat != "")
       {
         bookmarksdat_new = deleteStringArrayAt ( bookmarksdat, getFocusItemIndex());
	     deleteItem(getFocusItemIndex());
	     writeStringToFile (bookmarksPath,bookmarksdat_new);
         redrawDisplay ();
       }
	}
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
		ЗАКЛАДКИ
		</text>

		<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=18 backgroundColor=0:0:0 foregroundColor=120:120:255 >
        УДАЛИТЬ ЗАКЛАДКУ - КНОПКА [>>]
        </text>

</mediaDisplay>
<channel>
	<title>ЗАКЛАДКИ</title>
	<menu>main menu</menu>


<?php
$host = 'http://'.$_SERVER['HTTP_HOST'];

$bookmarks = NULL;
$bookmarks = file("/usr/local/etc/dvdplayer/ex_bookmarks.dat");
if (!sizeof($bookmarks) || $bookmarks == FALSE) {
	 echo '
    <item>
    <title> СПИСОК ПУСТОЙ </title>
    <link>http://127.0.0.1/cgi-bin/scripts/exua/null.php</link>
    </item>';

}
else {

for($i = 0; $i < sizeof($bookmarks); $i++)
     $bookmarks[$i] = trim($bookmarks[$i]);
    $index=0;

foreach($bookmarks as $bookmark) {
    $html = file_get_contents("http://www.ex.ua/view/".$bookmark);
    $hd = explode("<title>", $html);
    $hd = explode("</title>", $hd[1]);
    $title=$hd[0];

    $t1 = explode("<table width=100% cellpadding=0 cellspacing=0 border=0><tr><td valign=top>", $html);
    $t2 = explode("<img src='", $t1[1]);
    $t3 =   explode("'", $t2[1]);
    $image = $t3[0];

    $link = "http://www.ex.ua/playlist/".$bookmark;
    $link1 = file($link);
    if (count($link1)==0) {$link ="http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev2.php?query=".$bookmark; $dset='';}
    else {
      $link = "http://127.0.0.1/cgi-bin/scripts/exua/php/exua_link.php?file=".$link;
      $dset =' "   <mediaDisplay name=threePartsView
	sideColorLeft="0:0:0"
    sideLeftWidthPC="18"
	sideRightWidthPC="10"
	sideColorRight="0:0:0"
	headerXPC="14"
	headerYPC="3"
	headerWidthPC="95"
	itemImageXPC="8"
	itemImageYPC="18"
	itemXPC="20"
	itemYPC="18"
	itemWidthPC="75"
	menuXPC="5"
	menuWidthPC="15"
	headerCapXPC="90"
	headerCapYPC="10"
	headerCapWidthPC="0"
        showDefaultInfo=yes
	backgroundColor="0:0:0"
	itemBackgroundColor="0:0:0"
	infoYPC="85"
	popupXPC="7"
	popupWidthPC="15"
	popupBorderColor="0:0:0"
	idleImageXPC=45
	idleImageYPC=42
	idleImageWidthPC=10
	idleImageHeightPC=16
  >
	<idleImage> image/POPUP_LOADING_01.jpg </idleImage>
        <idleImage> image/POPUP_LOADING_02.jpg </idleImage>
        <idleImage> image/POPUP_LOADING_03.jpg </idleImage>
        <idleImage> image/POPUP_LOADING_04.jpg </idleImage>
        <idleImage> image/POPUP_LOADING_05.jpg </idleImage>
        <idleImage> image/POPUP_LOADING_06.jpg </idleImage>


  <!-- itemDisplay will draw widget inside the item area, item area is decided by mediaDisplay attributes -->
  <itemDisplay>

     <text offsetXPC=1 widthPC=100 heightPC=35 fontSize=14 backgroundColor=-1:-1:-1 >
      <script_replace>
        getItemInfo("title");
      </script_replace>
	  <offsetYPC>
	   <script_replace>
	     21;
	   </script_replace>
	  </offsetYPC>
      <foregroundColor>
      	<script_replace>
        if (getDrawingItemState()=="focus") "255:255:255";
        else  "101:101:101";
        </script_replace>
      </foregroundColor>
    </text>

</itemDisplay>

<infoDisplay>
  <onEnter>
    startVideo = 1;
    setRefreshTime(150);
  </onEnter>

  <onRefresh>
    if (startVideo == 1) {
       playItemURL(-1, 0, "mediaDisplay", "infoDisplay", "previewWindow");
       setRefreshTime(1000);
       startVideo = 0;
    }
    progress = getPlaybackStatus();
    updatePlaybackProgress(progress, "mediaDisplay", "infoDisplay", "progressBar");
  </onRefresh>

  <onExit>
    playItemURL(-1, 1);
    setRefreshTime(-1);
  </onExit>

  <text align="center" offsetXPC=10 offsetYPC=8 widthPC=75 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 lines=1>
    <script_replace>
      getItemInfo("title");
    </script_replace>
  </text>

  <text offsetXPC=20 offsetYPC=60 widthPC=70 heightPC=35 fontSize=15 backgroundColor=-1:-1:-1 foregroundColor=255:255:255 lines=10>
    <script_replace>
      getItemInfo("description");
    </script_replace>
  </text>

  <previewWindow windowColor=20:20:20 offsetXPC=30 offsetYPC=20 widthPC=42 heightPC=35>
  </previewWindow>

  <progressBar backgroundColor=32:32:32, offsetXPC=30, offsetYPC=55, widthPC=42, heightPC=3>

    <bar offsetXPC=3, offsetYPC=35, widthPC=73, heightPC=30, barColor=0:0:0, progressColor=255:0:0 bufferColor=050:000:000 />

    <playbackStatus offsetXPC=-10 offsetYPC=-5 widthPC=6.9 heightPC=135/>
    <run></run>
    <pause></pause>
    <stop></stop>
    <currentTime offsetXPC=78 offsetYPC=3 widthPC=12.5 heightPC=100 fontSize=10 backgroundColor=32:32:32 foregroundColor=255:255:255 />

  </progressBar>

<fullScreenBar>
	<backgroundImage></backgroundImage>
	<playbackImage></playbackImage>
	<bufferImage></bufferImage>
	<text backgroundColor=255:255:255 foregroundColor=200:200:200 fontSize=10></text>
</fullScreenBar>

</infoDisplay>
</mediaDisplay>"';
 }



   echo '
    <item>
    <title>'.$title.'</title>
    <link>'.$link.'</link>
    <media:thumbnail url="'.$image.'" />
    '.$dset.'
    <index>'.$index.'</index>
    </item>';
    $index=$index+1;
}
}
?>

</channel>
</rss>