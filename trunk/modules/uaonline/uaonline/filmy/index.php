#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://'.$_SERVER['HTTP_HOST'];
?>
<rss version="2.0" xmlns:media="http://purl.org/dc/elements/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="threePartsView"
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
	filmy.net.ua
		</text>
</mediaDisplay>

<searchLink>
<mediaDisplay name="threePartsView"/>
<link><script>
url;
</script></link>
</searchLink>


  <channel>

    <title>filmy.net.ua</title>
    <item>
    <title>ПОИСК на filmy.net.ua</title>
	<onClick>
				rss = "rss_file:///tmp/www/cgi-bin/scripts/exua/key/keyboard.rss";
				keyword = doModalRss(rss);
				if (keyword!=null)
				{
					writeStringToFile("/tmp/tmp_search.dat", keyword);
					url = "http://127.0.0.1/cgi-bin/scripts/filmy/search.php?sch=";
					url += ""+urlEncode(keyword)+"&amp;";
					jumpToLink("searchLink");
				}
			</onClick>
    <annotation></annotation>
    <media:thumbnail url="/tmp/www/cgi-bin/scripts/image/search.png"/>
    <mediaDisplay  name="photoView" />
  </item>

      <item>
      <title>Последние поступления</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/top.php?mode=last</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
      </item>

  
      <item>
      <title>TOP 20</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/top.php?mode=rating</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
      </item>


     <item>
      <title>---------------------------------------------------------</title>
	      <link></link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
  		<mediaDisplay name="threePartsView"/>
      </item>



       <item>
      <title>Анимационный</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,1,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>

	    <item>
      <title>Биграфия</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,25,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>

	    <item>
      <title>Боевик</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,2</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Вестерн</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,28,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Военный</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,23,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Детектив</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,4,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Документальный</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,6,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Драма</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,7,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Исторический</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,8,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Комедия</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,9,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Криминал</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,22,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Мелодрама</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,11,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Мистика</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,12,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	<item>
      <title>Музыка</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,26,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Мультфильмы</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,21,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Мюзикл</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,32,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Приключения</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,14,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Семейный</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,15,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Спорт</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,27,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Триллер</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,16,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Ужасы</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,17,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Фантастика</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,18,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
	    <item>
      <title>Фэнтези</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,29,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
		    <item>
      <title>Юмор</title>
	      <link>http://127.0.0.1/cgi-bin/scripts/filmy/filmy.php?query=1,33,</link>
	      <media:thumbnail url="http://www.filmy.net.ua/images/logo.jpg"/>
				<mediaDisplay name="threePartsView"/>
	</item>
</channel>
</rss>