#!/tmp/www/cgi-bin/php
<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://'.$_SERVER['HTTP_HOST'];
?>
<rss version="2.0">
<onEnter>
  startitem = "left";
  setRefreshTime(1);
columnCount=3
</onEnter>

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


	DoAnimation = "yes"
	AnimationType = 1
	AnimationStep = 26
	AnimationDelay = 1
	BackgroundDark = "no"
                >
                >

        <text align="center" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="20" fontSize="30" backgroundColor="10:105:150" foregroundColor="100:200:255">
                  <script>getPageInfo("pageTitle");</script>
                </text>

        <text redraw="yes" offsetXPC="85" offsetYPC="12" widthPC="10" heightPC="6" fontSize="20" backgroundColor="10:105:150" foregroundColor="60:160:205">
                  <script>sprintf("%s / ", focus-(-1))+itemCount;</script>
                </text>

                <text align="center" redraw="yes" lines="4" offsetXPC=10 offsetYPC=80 widthPC=75 heightPC=15 fontSize=15 backgroundColor=0:0:0 foregroundColor=120:120:120>
                        <script>print(annotation); annotation;</script>
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

      print("*** majorContext=",majorContext);
      print("*** userInput=",userInput);

      ret;
    </script>
  </onUserInput>

        </mediaDisplay>

        <item_template>
                <mediaDisplay  name="threePartsView">
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_01.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_02.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_03.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_04.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_05.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_06.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_07.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_08.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
                </mediaDisplay>
                <link>
                  <script>getItemInfo(getQueryItemIndex(), "location");</script>
                </link>
        </item_template>

<searchLink>
<mediaDisplay name="threePartsView"/>
<link><script>
url;
</script></link>
</searchLink>

<adultLink>
<mediaDisplay name="photoView"/>
<link><script>
url;
</script></link>
</adultLink>

  <channel>
    <title>UKRAINE ONLINE</title>
     <item>
    <link>http://127.0.0.1/cgi-bin/scripts/exua/index.php</link>
    <title>ФИЛЬМЫ EX.UA</title>
    <annotation>ФИЛЬМЫ, МУЛЬТИКИ, СЕРИАЛЫ</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/exua.png</image>
    <mediaDisplay name="photoView" />
    </item>

    <item>
    <link>http://127.0.0.1/cgi-bin/scripts/filmy/index.php</link>
    <title>ФИЛЬМЫ www.filmy.net.ua</title>
    <annotation>КАТАЛОГ ФИЛЬМОВ</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/filmynet.png</image>
    <mediaDisplay name="threePartsView"/>
  </item>

  <item>
  <link>http://127.0.0.1/cgi-bin/scripts/i_u/igru.php</link>
  <title>igru.net.ua</title>
  <image>/tmp/www/cgi-bin/scripts/i_u/image/videothek1.png</image>  
  <annotation>ФИЛЬМЫ СЕРИАЛЫ ОНЛАЙН</annotation> 
  <mediaDisplay name="photoView"/>
 </item>


<item>
      <title>uletno.info</title>
      <link>/tmp/www/cgi-bin/scripts/i_u/uletno.rss</link>
      <image>/tmp/www/cgi-bin/scripts/i_u/image/uletno.png</image>  
      <annotation>ФИЛЬМЫ СЕРИАЛЫ ОНЛАЙН</annotation> 
</item>


 <item>
  <link>http://127.0.0.1/cgi-bin/scripts/download/download.php</link>
  <title>МЕНЕДЖЕР ЗАГРУЗОК</title>
  <image>/tmp/www/cgi-bin/scripts/image/downmanage.png</image>  
  <annotation>МЕНЕДЖЕР ЗАГРУЗОК</annotation> 
  </item>




    <item>
        <onClick>
				rss = "rss_file:///tmp/www/cgi-bin/scripts/exua/key/pass.rss";
				keyword = doModalRss(rss);
				if (keyword!=null)
				{
					url = "http://127.0.0.1/cgi-bin/scripts/adult/adult";
					url += keyword+".php";
					jumpToLink("adultLink");
				}
	  </onClick>
    <title>ADULT CONTENT</title>
    <annotation>ВИДЕО ДЛЯ ВЗРОСЛЫХ</annotation>
    <image>/tmp/www/cgi-bin/scripts/image/xxx.png</image>
    <mediaDisplay name="photoView"/>
  </item>



</channel>

</rss>
