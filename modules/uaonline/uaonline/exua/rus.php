#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://'.$_SERVER['HTTP_HOST'];
?>
<rss version="2.0" xmlns:media="http://purl.org/dc/elements/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
DoAnimation = "yes"
	AnimationType = 1
	AnimationStep = 26
	AnimationDelay = 1
	BackgroundDark = "no"
    itemBackgroundColor="0:0:0"
    backgroundColor="0:0:0"
    sideLeftWidthPC="0"
    itemImageXPC="5"
    itemXPC="20"
    itemYPC="20"
    itemWidthPC="65"
    unFocusFontColor="101:101:101"
    focusFontColor="255:255:255" >


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
		EX-UA РУССКИЙ
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
      <title>ФИЛЬМЫ ЗАРУБЕЖНЫЕ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=2,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

	    <item>
      <title>ФИЛЬМЫ НАШИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=70538,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

	    <item>
      <title>СЕРИАЛЫ НАШИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=422546,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>СЕРИАЛЫ ЗАРУБЕЖНЫЕ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=1988,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>МУЛЬТФИЛЬМЫ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=1989,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>ДОКУМЕНТАЛЬНОЕ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=1987,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>ПРИКОЛЫ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=23785,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

    <item>
      <title>КЛИПЫ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=1991,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>


		    <item>
      <title>КОНЦЕРТЫ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=70533,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>ШОУ И ПЕРЕДАЧИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=28713,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
	</item>

    <item>
      <title>ТРЕЙЛЕРЫ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=1990,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>



      <item>
      <title>СПОРТ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=69663,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>

    <item>
      <title>АНИМЕ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=23786,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>

    <item>
      <title>ТЕАТР</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=2,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>


    <item>
      <title>ПРОПОВЕДИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=371146,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>



    <item>
      <title>РЕКЛАМНЫЕ РОЛИКИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=371152,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>



    <item>
      <title>СОЦИАЛЬНАЯ РЕКЛАМА</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=4313886,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>


    <item>
      <title>УРОКИ И ТРЕНИНГИ</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=28714,</link>
	      <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuarus.png"/>
				<mediaDisplay name="threePartsView"/>
      </item>



</channel>
</rss>