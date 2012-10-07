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
		EX-UA ENGLISH
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
		<title>MOVIES</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82316,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>DOCUMENTARIES</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82318,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>SERIES</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82325,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>TRAILERS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82326,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>CARTOONS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82329,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>ANIME</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82331,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>CLIPS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82333,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>LIVE CONCERTS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82335,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>SHOWS & LIVE</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82339,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>LESSONS & TRAININGS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82343,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>SPORT</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82348,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>THEATRE & MUSICALS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=82354,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>
	<item>
		<title>COMMERCIALS</title>
			<link>http://127.0.0.1/cgi-bin/scripts/exua/php/exua_lev1.php?query=371172,</link>
			<media:thumbnail url="/tmp/www/cgi-bin/scripts/image/exuaeng.png"/>
			<mediaDisplay name="threePartsView"/>
	</item>

</channel>
</rss>
