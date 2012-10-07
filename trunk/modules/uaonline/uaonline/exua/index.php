#!/tmp/www/cgi-bin/php
<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://'.$_SERVER['HTTP_HOST'];
?>
<rss version="2.0">
<onEnter>
  startitem = "left";
  setRefreshTime(1);
  columnCount=3

  lang = 0;
  rssPath = getStoragePath("key")+"ex_lang.dat";
  rssconf = readStringFromFile(rssPath);
  print("rssconf:",rssconf);
  if(rssconf != null)
  {
    value=getStringArrayAt(rssconf, 0);
  }
  if (value != null &amp;&amp; value != "")
      lang = value;
    else lang = 0;
  
</onEnter>

<!--
<onExit>
  rssPath = getStoragePath("key")+"ex_lang.dat";
  rssconf = readStringFromFile(rssPath);
  if(rssconf != null)
  {
    rssconf=deleteStringArrayAt(rssconf, 0);
  }
    rssconf=pushBackStringArray(rssconf, lang);
    writeStringToFile(rssPath, rssconf);
  

</onExit>
-->
<searchLink>
<mediaDisplay name="threePartsView"/>
<link><script>
url;
</script></link>
</searchLink>

<onRefresh>
  setRefreshTime(-1);
  itemCount = getPageInfo("itemCount");
  middleItem = Integer(itemCount / 2);
  if(startitem == "middle")
    setFocusItemIndex(1);
  else
  if(startitem == "right")
    setFocusItemIndex(middleItem);
  redrawDisplay();
</onRefresh>

        <mediaDisplay name=photoView
          centerXPC=7
                centerYPC=30
                centerHeightPC=50
columnCount=3
          rowCount=1
                menuBorderColor="55:55:55"
                sideColorBottom="0:0:0"
                sideColorTop="0:0:0"
          backgroundColor="0:0:0"
                imageBorderColor="0:0:0"
                itemBackgroundColor="0:0:0"
                itemGapXPC=0
                itemGapYPC=1
                sideTopHeightPC=22
                bottomYPC=85
                sliding=yes
                showHeader=no
                showDefaultInfo=no
                >
                >

        <text align="center" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="20" fontSize="30" backgroundColor="10:105:150" foregroundColor="100:200:255">
                  <script>getPageInfo("pageTitle");</script>
                </text>

        <text redraw="yes" offsetXPC="85" offsetYPC="12" widthPC="10" heightPC="6" fontSize="20" backgroundColor="10:105:150" foregroundColor="60:160:205">
                  <script>sprintf("%s / ", focus-(-1))+itemCount;</script>
                </text>

        <text align="center" redraw="yes" lines="4" offsetXPC=10 offsetYPC=75 widthPC=75 heightPC=15 fontSize=18 backgroundColor=0:0:0 foregroundColor=120:120:120>
           <script>print(annotation); annotation;</script>
        </text>

        <text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=18 backgroundColor=0:0:0 foregroundColor=180:180:220 >
           переключение EX.UA РУС/УКР/ENG -- кнопка [>>]
        </text>


	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_01.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_02.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_03.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_04.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_05.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_06.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_07.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_08.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
                <itemDisplay>
                        <image>
                                <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                                        if(focus==idx)
                                        {
                                          annotation = getItemInfo(idx, "annotation");
                                        }
                                        getItemInfo(idx, "image");
                                </script>
                         <offsetXPC>
                           <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) 50 * (1 - columnCount/3); else 12 + 37 * (1 - columnCount/3);
                           </script>
                         </offsetXPC>
                         <offsetYPC>
                           <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) 0; else 6;
                           </script>
                         </offsetYPC>
                         <widthPC>
                           <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) 100 * columnCount/3; else 75 * columnCount/3;
                           </script>
                         </widthPC>
                         <heightPC>
                           <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) 50; else 37;
                           </script>
                         </heightPC>
                        </image>

                        <text align="center" lines="3" offsetXPC=0 offsetYPC=55 widthPC=100 heightPC=45 backgroundColor=-1:-1:-1>
                                <script>
                                        idx = getQueryItemIndex();
                                        getItemInfo(idx, "title");
                                </script>
                                <fontSize>
                                <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) "22"; else "18";
                                </script>
                                </fontSize>
                          <foregroundColor>
                                <script>
                                        idx = getQueryItemIndex();
                                        focus = getFocusItemIndex();
                            if(focus==idx) "255:255:255"; else "75:75:75";
                                </script>
                          </foregroundColor>
                        </text>

                </itemDisplay>

  <onUserInput>
    <script>
      ret = "false";
      userInput = currentUserInput();
      majorContext = getPageInfo("majorContext");

      if( userInput == "video_ffwd" || userInput == "video_quick_stop")
	    {
	      if ( lang == 2 ) lang = 0;
	      else if (lang == 0) lang = 1;
	      else if (lang == 1) lang = 2;
	      else lang = 0;
	      redrawDisplay ();
		  rssPath = getStoragePath("key")+"ex_lang.dat";
  			rssconf = readStringFromFile(rssPath);
  		if(rssconf != null)
  		{
    		rssconf=deleteStringArrayAt(rssconf, 0);
  		}
    		rssconf=pushBackStringArray(rssconf, lang);
    		writeStringToFile(rssPath, rssconf);


	    }

      print("*** majorContext=",majorContext);
      print("*** userInput=",userInput);

      ret;
    </script>
  </onUserInput>

        </mediaDisplay>

        <item_template>
                <mediaDisplay  name="threePartsView" idleImageXPC="40" idleImageYPC="40" idleImageWidthPC="20" idleImageHeightPC="26">
        <idleImage>image/busy1.png</idleImage>
        <idleImage>image/busy2.png</idleImage>
        <idleImage>image/busy3.png</idleImage>
        <idleImage>image/busy4.png</idleImage>
        <idleImage>image/busy5.png</idleImage>
        <idleImage>image/busy6.png</idleImage>
        <idleImage>image/busy7.png</idleImage>
        <idleImage>image/busy8.png</idleImage>
                </mediaDisplay>
                <link>
                  <script>getItemInfo(getQueryItemIndex(), "location");</script>
                </link>
        </item_template>

  <channel>
    <title>EX.UA</title>

   <item>
	   <onClick>

			if (lang == 0) url = "http://127.0.0.1/cgi-bin/scripts/exua/rus.php";
			else if (lang == 1) url = "http://127.0.0.1/cgi-bin/scripts/exua/ukr.php";
			else if (lang == 2) url = "http://127.0.0.1/cgi-bin/scripts/exua/eng.php";

			jumpToLink("searchLink");
	    </onClick>
        <title>
         <script>
           	if (lang == 0) url = "РУССКИЙ";
			else if (lang == 1) url = "УКРАИНСКИЙ";
			else if (lang == 2) url = "ENGLISH";

         </script>
        </title>
        <annotation>Фильмы, мультики, сериалы</annotation>
        <image>/tmp/www/cgi-bin/scripts/image/exua_2.png</image>
        <mediaDisplay name="threePartsView"/>

  </item>

  <item>
    <link>http://127.0.0.1/cgi-bin/scripts/exua/bookmarks.php</link>
    <title>EX.UA ЗАКЛАДКИ</title>
    <annotation>список закладок</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/bookmarks.png</image>
    <mediaDisplay name="threePartsView"/>
  </item>

 <!-- <item>
    <link>http://127.0.0.1/cgi-bin/scripts/exua/ukr.php</link>
    <title>Фильмы EX.UA(укр.)</title>
    <annotation>Фильмы, мультики, сериалы</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/exua.png</image>
    <mediaDisplay name="threePartsView"/>
  </item>  -->

     <item>
    <title>ПОИСК на EX.UA</title>
	<onClick>
				rss = "rss_file:///tmp/www/cgi-bin/scripts/exua/key/keyboard.rss";
				keyword = doModalRss(rss);
				if (keyword!=null)
				{
					writeStringToFile("/tmp/tmp_search.dat", keyword);
					url = "http://127.0.0.1/cgi-bin/scripts/exua/search.php?sch=";
					url += ""+urlEncode(keyword)+"&amp;";
					jumpToLink("searchLink");
				}
			</onClick>
    <annotation></annotation>
    <image>/tmp/www/cgi-bin/scripts/image/search.png</image>
	<mediaDisplay  name="photoView" />
  </item>


    <item>
    <title>НАСТРОЙКИ</title>
    <link>rss_file:///tmp/www/cgi-bin/scripts/exua/settings.rss</link>
    <annotation>НАСТРОЙКИ</annotation>
    <image>/tmp/www/cgi-bin/scripts/exua/image/settings.png</image>
    <mediaDisplay name="onePartView"/>
  </item>



<!--      <item>
    <link>http://127.0.0.1/cgi-bin/scripts/exua/level.php</link>
    <title>Загрузка</title>
    <annotation>Менеджер загрузок</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/exua.png</image>
    <mediaDisplay name="threePartsView"/>
  </item>   -->
</channel>

</rss>
