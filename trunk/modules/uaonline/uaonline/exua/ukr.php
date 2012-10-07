#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://'.$_SERVER['HTTP_HOST'];
?>
<rss version="2.0" xmlns:media="http://purl.org/dc/elements/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
 itemBackgroundColor="0:0:0" backgroundColor="0:0:0" sideLeftWidthPC="0" itemImageXPC="5" itemXPC="20" itemYPC="20" itemWidthPC="65" unFocusFontColor="101:101:101" focusFontColor="255:255:255" >
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
		<text  offsetXPC=40 offsetYPC=8 widthPC=35 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		EX-UA УКРАЇНСЬКИЙ
		</text>

<onUserInput>
    <script>
	userInput = currentUserInput();

    if( userInput == "video_frwd" || userInput == "video_play")
	{
       jumpToLink("indexLink");
	}
    </script>
</onUserInput>

</mediaDisplay>


<indexLink>
 <link>
	<script>
	  "http://127.0.0.1/cgi-bin/scripts/exua/index.php";
	 </script>
 </link>
</indexLink>


  <channel>

    <title>EX-UA</title>

    <item>
      <title>ЗАРУБІЖНЕ КІНО</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82470,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

	    <item>
      <title>МУЛЬТФІЛЬМИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82484,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>СЕРІАЛИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82480,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>ДОКУМЕНТАЛЬНІ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82476,</link>
		  <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>НАШЕ КІНО</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82473,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>КЛІПИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82489,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>КОНЦЕРТИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82490,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>ШОУ ТА ПЕРЕДАЧІ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82493,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>СПОРТ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82496,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

    <item>
      <title>ТРЕЙЛЕРИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82483,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

    <item>
      <title>АНІМЕ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82488,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
    <item>
      <title>УРОКИ ТА ТРЕНІНГИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82495,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
    <item>
      <title>EXTUBE</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82499,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
    <item>
      <title>ТЕАТР</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82508,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
    <item>
      <title>ПРОПОВІДІ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=371167,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
    <item>
      <title>РЕКЛАМНІ РОЛИКИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=371168,</link>
	        <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exua.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
</channel>
</rss>