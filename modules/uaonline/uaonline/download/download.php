#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<!-- ------------------------------------
//	Ukraine online services 	
//      Download module 0.3 (RSS)	
//	Created by Sashunya 2011	
//      wall9e@gmail.com           	
//  -------------------------------------
-->
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<onEnter>
	setRefreshTime(1);    
</onEnter>


<mediaDisplay name="threePartsView"
	forceRedrawItems=yes
	showHeader="yes"
	showDefaultInfo="no"
	itemPerPage="6"	
	itemBackgroundColor="0:0:0" 
	itemGap="0"
	backgroundColor="0:0:0" 
	sideLeftWidthPC="0" 
	headerImageXPC ="0"
	headerImageYPC ="0"
	headerImageWidthPC = "0"
	headerImageHightPC = "0"
	headerCapWidthPC = "0"
	headerWidthPC = "0"
	itemXPC="5" 
	itemYPC="25" 
	itemWidthPC="90" 
	itemHeightPC = "10"
	itemImageXPC="5"
	itemImageYPC = "10"
	itemImageWidthPC = "0"
	itemImageHeightPC = "0"

	unFocusFontColor="101:101:101" 
	focusFontColor="255:255:255" 
	
	popupXPC = "70"
	popupYPC = "20"
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
		<image  offsetXPC=8 offsetYPC=8 widthPC=8 heightPC=11>
		/tmp/www/cgi-bin/scripts/download/image/down_title.png
		</image>

		<text  offsetXPC=35 offsetYPC=8 widthPC=75 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		Менеджер загрузок

		</text>			


	        <text offsetXPC=8  offsetYPC=18 widthPC=10 heightPC=5 fontSize=16 backgroundColor=-1:-1:-1 foregroundColor=200:200:100>Статус</text>
		<text offsetXPC=45 offsetYPC=18 widthPC=15 heightPC=5 fontSize=16 backgroundColor=-1:-1:-1 foregroundColor=200:200:100>Имя файла</text>
		<text offsetXPC=85 offsetYPC=18 widthPC=15 heightPC=5 fontSize=16 backgroundColor=-1:-1:-1 foregroundColor=200:200:100>Выполнено</text>



		<text align="center" redraw="yes" lines="1" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=5 fontSize=15 backgroundColor=0:0:0 foregroundColor=120:120:120>Обновить - INFO ([&#60;&#60;]); Функции - [OK]</text>
		
<itemDisplay>
	<script>
	        idx = getQueryItemIndex();

	if ( getDrawingItemState() == "focus")
		{
			bg = "0:0:0";
			fg = "255:255:255";
		}
		else
		{
			bg="0:0:0";
			fg="101:101:101";
		}


			state = getStringArrayAt(pidstateArray,idx);
			if (state == "loading") 
				{
				state = "/tmp/www/cgi-bin/scripts/download/image/H006.png";
				}
			else if	(state == "done") 
				{
				state = "/tmp/www/cgi-bin/scripts/download/image/ok.png";
				}
			else if	(state == "stopped") 	
				{
				state = "/tmp/www/cgi-bin/scripts/download/image/stop-alt.png";
				}
			else if	(state == "none") 	
				{
				state = "/tmp/www/cgi-bin/scripts/download/image/nav-stop.png";
				}	
	</script> 

        <image  offsetXPC="4" offsetYPC="0" widthPC="8" heightPC="100">
        <script> state; </script>
	</image>

      	<text offsetXPC="16" offsetYPC="0" widthPC="75" heightPC="100" fontSize="15" lines="2">
       	<foregroundColor><script>fg;</script></foregroundColor>       	
	        	<script>getStringArrayAt(pidfileArray,idx);</script>
	</text>	

      	<text offsetXPC="92" offsetYPC="0" widthPC="10" heightPC="100" fontSize="18" lines="1">
	<foregroundColor><script>fg;</script></foregroundColor>       	
	<script>getStringArrayAt(pidpercentArray,idx)+"%";</script>
	</text>	


</itemDisplay>		

<onUserInput>
	ret = "false";
	input = currentUserInput();
	if ( input == "display" || input == "DISPLAY" || input == "video_play" )
	{
		setRefreshTime(1);
  	    ret = "true"; 


	}
	else if ("left" == input || "right" == input || "L" == input || "R" == input)
	{
  	    ret = "true"; 


	}

	ret;
</onUserInput>

</mediaDisplay>


<onRefresh>
	showIdle();
	setRefreshTime(-1);    
	pidfileArray = null;
	pidlinkArray = null;
	pidnumArray = null;
	pidpercentArray = null;
	pidstateArray = null;       
	dlok = loadXMLFile("http://127.0.0.1/cgi-bin/scripts/download/download_mod.php?display=1");
	
	if (dlok !=null) 
	{
		itemSize = getXMLElementCount("downloads","file");
		count = 0;
                while( count != itemSize )
		{

			pidfile = getXMLText("downloads","file",count,"name");
			pidlink = getXMLText("downloads","file",count,"filelink");
			pidnum = getXMLText("downloads","file",count,"pid");
			pidpercent = getXMLText("downloads","file",count,"percent");
			pidstate = getXMLText("downloads","file",count,"state");
			
			pidfileArray = pushBackStringArray(pidfileArray, pidfile);
			pidlinkArray = pushBackStringArray(pidlinkArray, pidlink);
			pidnumArray = pushBackStringArray(pidnumArray, pidnum);
			pidpercentArray = pushBackStringArray(pidpercentArray, pidpercent);
			pidstateArray = pushBackStringArray(pidstateArray, pidstate);
			count +=1;
			
		}

  		setFocusItemIndex(0);
		redrawDisplay();
		}
</onRefresh>

		
<item_template>
<onClick>
	 idx = getFocusItemIndex();
	 action = null;

	 pidfile = getStringArrayAt( pidfileArray , idx );
	 pidlink = getStringArrayAt( pidlinkArray , idx );
	 killpid = getStringArrayAt( pidnumArray , idx );
  	 act = doModalRss("rss_file:///usr/local/etc/mos/uaonline/download/dialog.rss");
  	 
	 if( act != null ) {
	 	if (act == 0 ){
		 action = "kill="+killpid; 
		 }
 		else if (act == 1 ){
		 action = "title=" + urlEncode(pidfile) + "&amp;downloadlink=" + urlEncode(pidlink);
		 }
        	else if (act == 2 ){
		 action = "delete=" + pidlink; 
		 }
 		else if (act == 3 ){
		 action = "clear=1"; 
		 }
      	 	dlok = loadXMLFile("http://127.0.0.1/cgi-bin/scripts/download/download_mod.php?"+action);
		 setRefreshTime(1);
	 }
	 null;
  </onClick>
</item_template>


		
<channel>
	<title>Загрузки</title>
	<link>http://127.0.0.1/cgi-bin/scripts/download/download.php</link>
	<itemSize>
		<script>
			itemSize;
		</script>
        </itemSize>
</channel>
</rss>