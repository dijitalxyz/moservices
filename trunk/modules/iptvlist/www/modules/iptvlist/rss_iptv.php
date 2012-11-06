<?php

function rss_iptv_content()
{
	header( "Content-type: text/plain" );
	echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>

<!--
	iptvlist created by Roman Lut aka hax.
-->

<bookmark> my_favorites </bookmark>

<playLink_OSD>
	<link>
		<script>
			url = "<?= getMosUrl().'?page=' ?>rss_iptvplay";
		</script>
	</link>
</playLink_OSD>

<popupDialog>
</popupDialog>


<readIndex>
	path = getStoragePath("tmp");
	path = path + "iptv_index.dat";

	chIndex = 0 + readStringFromFile(path);
	
	if ( chIndex == null )
	{
		chIndex = 0;
	}

	if ( chIndex &lt; 0 )
	{
		chIndex = favLinkCount - 1;
	}

	if ( chIndex &gt; ( favLinkCount - 1 ) )
	{
		chIndex = 0;
	}
</readIndex>

<writeIndex>
	path = getStoragePath("tmp");
	path = path + "iptv_index.dat";
	writeStringToFile(path,chIndex);
</writeIndex>

<ReloadList>
	path = "/tmp/iptv_playlist.txt";
	favLinkArray = readStringFromFile( path );
	favLinkCount = 0 + getStringArrayAt( favLinkArray, 0 );
	favLinkArray = deleteStringArrayAt( favLinkArray, 0 );
	listName = getStringArrayAt( favLinkArray, 0 );
	isRecordings = listName == "Recordings";
	favLinkArray = deleteStringArrayAt( favLinkArray, 0 );
   	redrawDisplay();
</ReloadList>

<BuildFavList>
	getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/loadlist.php" );
    executeScript("ReloadList");
</BuildFavList>


<moveItemUp>

	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt; 0 ) &amp;&amp; ( focusIndex &lt; favLinkCount ) )
	{
		focusIndexMinusOne = focusIndex - 1;

    	setFocusItemIndex( focusIndexMinusOne );

		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?swap1=" + focusIndex + "&amp;swap2=" + focusIndexMinusOne );

    	executeScript("ReloadList");
	} 
</moveItemUp>

<moveItemUpPage>
	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt; 0 ) &amp;&amp; ( focusIndex &lt; favLinkCount ) )
	{
		if ( focusIndex &gt; pageSize )
		{
			focusIndex1 = focusIndex - pageSize;
		}
		else
		{
			focusIndex1 = 0;
		}

    	setFocusItemIndex( focusIndex1 );

		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?delete=" + focusIndex + "&amp;insert=" + focusIndex1 );

    	executeScript("ReloadList");
	} 
</moveItemUpPage>

<moveItemDown>
	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt;= 0 ) &amp;&amp; ( focusIndex &lt; ( favLinkCount - 1 ) ) )
	{
		focusIndex1 = focusIndex + 1;

    	setFocusItemIndex( focusIndex1 );

		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?swap1=" + focusIndex + "&amp;swap2=" + focusIndex1 );

    	executeScript("ReloadList");
	} 
</moveItemDown>

<moveItemDownPage>
	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt;= 0 ) &amp;&amp; ( focusIndex &lt; ( favLinkCount - 1 ) ) )
	{
		if ( ( focusIndex + pageSize ) &lt; favLinkCount )
		{
			focusIndex1 = focusIndex + pageSize;
		}
		else
		{
			focusIndex1 = favLinkCount - 1;
		}

    	setFocusItemIndex( focusIndex1 );

		getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?delete=" + focusIndex + "&amp;insert=" + focusIndex1 );

    	executeScript("ReloadList");
	} 
</moveItemDownPage>

<deleteItem>
	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt;= 0 ) &amp;&amp; ( focusIndex &lt; favLinkCount ) &amp;&amp; ( favLinkCount &gt; 0 ) )
	{
		if ( listName == "Recordings" )
		{
			focusIndex = 0 + getFocusItemIndex();
			recUrl = getStringArrayAt( favLinkArray, focusIndex * 3 + 1 );		
			result = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/deleterec.php?name=" + urlEncode( recUrl ) );
		    executeScript("BuildFavList");
		}
		else
		{
			if ( favLinkCount &gt; 1 )
			{
				getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?delete=" + focusIndex );
			   	executeScript("ReloadList");
			}
		}

		if ( focusIndex == favLinkCount )
		{
    			setFocusItemIndex( focusIndex - 1 );
		}

	} 
</deleteItem>

<renameItem>
	focusIndex = 0 + getFocusItemIndex();
        
	if ( ( focusIndex &gt;= 0 ) &amp;&amp; ( focusIndex &lt; favLinkCount ) &amp;&amp; ( favLinkCount &gt; 1 ) )
	{
		focusIndex = 0 + getFocusItemIndex();
		recUrl = getStringArrayAt( favLinkArray, focusIndex * 3 + 1 );		
		result = getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/renamerec.php?name=" + urlEncode( recUrl ) + "&amp;newname=" + urlEncode( newname ) );
	    executeScript("BuildFavList");
	} 
</renameItem>

<onEnter>
	actionPending = 0;
	pageSize = 13;
    showIdle();

    prepareAnimation();
/*
    setEnv("isInMyFav", "yes");    
*/
    bProcessing = "false";
    print("[MyFavorites - onEnter()] start init.");
    executeScript("BuildFavList");
    executeScript("readIndex");
    NetTryAgain = "false";
	setEnv("Net_userID", "");
	setEnv("Net_Password", "");	    
	setEnv("Net_SavePSW", "no");	
	setEnv("Net_Cancel", "0");	  
	setEnv("currrentIdx", 0);  
    setFocusItemIndex(chIndex);
    setRefreshTime(1000);
</onEnter>

<onExit>
    cancelIdle();
/*    executeScript("ReleaseFavList"); */
    if(bProcessing == "false")
    	setEnv("isIPTVReturn", "yes");
    print("[MyFavorites - onExit()] exit.");
    unsetEnv("Net_userID");
    unsetEnv("Net_Password");
    unsetEnv("Net_SavePSW");
    unsetEnv("Net_Cancel");
    unsetEnv("currrentIdx");
    redrawDisplay();
</onExit>

<onRefresh>
	if ( actionPending == 1 )
	{
		actionPending = 0;
   		executeScript("moveItemUp");
   		cancelIdle();
	}
	else if ( actionPending == 2 )
	{
		actionPending = 0;
   		executeScript("moveItemDown");
   		cancelIdle();
	}
	else if ( actionPending == 3 )
	{
		actionPending = 0;
   		executeScript("moveItemUpPage");
   		cancelIdle();
	}
	else if ( actionPending == 4 )
	{
		actionPending = 0;
   		executeScript("moveItemDownPage");
   		cancelIdle();
	}
	else if ( actionPending == 5 )
	{
		actionPending = 0;
   		executeScript("deleteItem");
   		cancelIdle();
	}
	else if ( actionPending == 6 )
	{
		actionPending = 0;
		if ( listName != "Recordings" )
		{
			rss="<?= getMosUrl().'?page=' ?>rss_help1";
		}
		else
		{
			rss="<?= getMosUrl().'?page=' ?>rss_help4";
		}
      	mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	}
	else if ( actionPending == 7 )
	{
		actionPending = 0;
		rss="<?= getMosUrl().'?page=' ?>rss_select_playlist";
   		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );

		if ( mret == "true" )
		{
   			showIdle();
		    	executeScript("BuildFavList");
			chIndex = 0;
	    		executeScript("writeIndex");
		    	setFocusItemIndex(0);
    			cancelIdle();
		}            
	}
	else if ( actionPending == 8 )
	{
		actionPending = 0;
		rss="<?= getMosUrl().'?page=' ?>rss_addtofavorites";
   		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );

		if ( mret == "true" )
		{
    			showIdle();
			focusIndex = 0 + getFocusItemIndex();
			getCSVFromURL( "<?= getMosUrl() ?>modules/iptvlist/savelist.php?favorite=" + focusIndex );
    			cancelIdle();
		}            
	}
	else if ( actionPending == 9 )
	{
		actionPending = 0;
		rss="<?= getMosUrl().'?page=' ?>rss_alreadyfavorites";
   		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
	}
	else if ( actionPending == 10 )
	{
		actionPending = 0;

		if ( listName != "Recordings" )
		{
			rss="<?= getMosUrl().'?page=' ?>rss_sidemenu1";
		}
		else
		{
			rss="<?= getMosUrl().'?page=' ?>rss_sidemenu4";
		}
      		mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );

		if ( mret != "" )
		{
	    		userInput = mret;
	    		print ("[My Favorites - input()] get input:", userInput);
			showIdle();
	    		executeScript("handleUserInput");
			cancelIdle();
		}

	}


    setRefreshTime(1000);
</onRefresh>

<handleUserInput>
	    ret = "false";
        if ( userInput == "left" )
        {
	       	focusIndex = 0 + getFocusItemIndex();			
			if ( focusIndex &gt; pageSize )
			{
				setFocusItemIndex( focusIndex - pageSize );
			}
			else
			{
				setFocusItemIndex( 0 );
			}
			redrawDisplay();
            ret = "true";
        }
        else if ( userInput == "right" )
        {
	       	focusIndex = 0 + getFocusItemIndex();			
			if ( focusIndex + pageSize &lt; favLinkCount )
			{
				setFocusItemIndex( focusIndex + pageSize );
			}
			else
			{
				setFocusItemIndex( favLinkCount - 1 );
			}
			redrawDisplay();
            ret = "true";
        }
        else if ((userInput == "up") || (userInput == "down"))
        {
            if (favLinkCount &lt; 1)
            {
                ret = "true";
            }
        }
        else if(userInput == "video_play")  
        {
	       	focusIndex = 0 + getFocusItemIndex();
           	if (favLinkCount &gt; 0)
            	{
					chIndex = focusIndex;
	        		executeScript("writeIndex");

					podUrl = getStringArrayAt(favLinkArray, focusIndex * 3 + 2 );

					playItemUrl( podUrl, 0 );
                	ret = "false";
		
             	}
        }
        else if(userInput == "video_ffwd")  
        {
		if ( listName != "Recordings" )
		{
			actionPending = 2;
			showIdle();
    			setRefreshTime(1);
          		ret = "true";
		}
	}
        else if(userInput == "video_frwd")  
        {
		if ( listName != "Recordings" )
		{
			actionPending = 1;
			showIdle();
    			setRefreshTime(1);
          		ret = "true";
		}
        }
	else if (userInput == "pagedown")
        {
		showIdle();
		actionPending = 10;
		setRefreshTime(1);
          	ret = "true";
	}	
	else if (userInput == "movepagedown")
        {
		if ( listName != "Recordings" )
		{
			actionPending = 4;
			showIdle();
    			setRefreshTime(1);
          		ret = "true";
		}
        }
	else if (userInput == "pageup")
        {
		if ( listName != "Recordings" )
		{
			actionPending = 3;
			showIdle();
    			setRefreshTime(1);
          		ret = "true";
		}
        }
	else if (userInput == "option_green")
        {
			if ( listName == "Recordings" )
			{
				rss="<?= getMosUrl().'?page=' ?>rss_deleteconfirm1";
			}
			else
			{
				rss="<?= getMosUrl().'?page=' ?>rss_deleteconfirm";
			}
           	mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );

			if ( mret == "true" )
			{
				actionPending = 5;
				showIdle();
    			setRefreshTime(1);
			}            

          	ret = "true";
        }
        else if(userInput == "return")  
        {
		ret = "false";
        }
        else if(userInput == "display")  
        {
		/*
		todo
		*/	
          	ret = "true";
	}
        else if(userInput == "help")  
        {
			showIdle();
			actionPending = 6;
          	ret = "true";
        }
		else if ( (userInput == "menu") || (userInput =="option_yellow" ) )
		{
			showIdle();
			actionPending = 7;
          	ret = "true";
		}
		else if (userInput == "video_stop")
		{
			if ( listName != "Favorites" )
			{
				showIdle();
				actionPending = 8;
			}
			else 
			{
				showIdle();
				actionPending = 9;
			}
          	ret = "true";
		}

		else if (userInput == "zoom")
		{
			if ( listName == "Recordings" )
			{       
				ret = getInput("Rename video", "doModal");
				if( ret != NULL ) 
				{
					if( ret != "" ) 
					{
						newname = ret;
					    executeScript("renameItem");
					}
				}
			}
			else
			{
				rss="<?= getMosUrl().'?page=' ?>rss_norename";
   				mret = doModalRss(rss,"mediaDisplay", "popupDialog", 0 );
			}
          	ret = "true";
		}

/*

		favLinkArray = userInput;
		redrawDisplay();
*/
</handleUserInput>


<item_template>
    <displayTitle>
        <script>
            linkName = getStringArrayAt( favLinkArray, ( 0 + getQueryItemIndex() * 3 ) );
            linkName;
        </script>
    </displayTitle>
    <chNumber>
        <script>
            linkName = 0 + getQueryItemIndex() + 1; 
		if ( linkName &gt; favLinkCount )
		{
			linkName = "";
		}
            linkName;
        </script>
    </chNumber>
    <onClick>
	       	focusIndex = 0 + getFocusItemIndex();
            if (favLinkCount &gt; 0)
            {

        	path = getStoragePath("tmp");
			path = path + "iptv_url.dat";

			tmp = null;

			podUrl = getStringArrayAt(favLinkArray, focusIndex * 3 );		
			tmp = pushBackStringArray( tmp, podUrl );	

			chIndex1 = 0 + focusIndex + 1;
           	podUrl = " " + chIndex1 + ". " + podUrl;

			tmp = pushBackStringArray( tmp, podUrl );	

			podUrl = getStringArrayAt( favLinkArray, focusIndex * 3 + 1 );		
			tmp = pushBackStringArray( tmp, podUrl );	
	
			podUrl = getStringArrayAt( favLinkArray, focusIndex * 3 + 2 );		
			tmp = pushBackStringArray( tmp, podUrl );	

			tmp = pushBackStringArray( tmp, listName );	

			writeStringToFile(path,tmp);

			chIndex = focusIndex;
	    	executeScript("writeIndex");

			jumpToLink( "playLink_OSD" );
			null;
                }
    </onClick>
</item_template>

<mediaDisplay name=onePartView
    sideColorLeft=-1:-1:-1 
    sideColorRight=-1:-1:-1 
    sideColorTop=-1:-1:-1
    sideColorBottom=-1:-1:-1
    
    backgroundColor=-1:-1:-1
    itemBackgroundColor=-1:-1:-1
    focusBorderColor=-1:-1:-1
    unFocusBorderColor=-1:-1:-1
    
    sideLeftWidthPC=0
    sideRightWidthPC=0
    sideBottomHeightPC = 0
    sideTopHeightPC=0
    
    itemXPC = 12.03
    itemYPC = 16.25
    itemWidthPC = 35
    itemHeightPC = 4.17
    itemGap = 0
    
    itemPerPage=13
    
    forceFocusOnItem=yes

    showHeader=no
    showDefaultInfo=no
	rollItems="no"
	forceRedrawItems="yes"	
	    
	idleImageXPC=83.12
	idleImageYPC=89.58
	idleImageWidthPC=3.1
	idleImageHeightPC=5.5	
>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_01.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_02.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_03.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_04.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_05.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_06.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_07.png </idleImage>
        <idleImage> /usr/local/etc/mos/www/modules/iptvlist/images/POPUP_LOADING_08.png </idleImage>
    
    <image redraw=yes offsetXPC=82.11 offsetYPC=12.91 widthPC=1.88 heightPC=1.95>
        <script>
            idx = getFocusItemIndex();

            if (idx == 0 || favLinkCount == 0 || favLinkCount &lt; 0)
            	"/usr/local/etc/mos/www/modules/iptvlist/images/arrow_up_unfocus.png";
            else
            	"/usr/local/etc/mos/www/modules/iptvlist/images/arrow_up.png";
            idx;
        </script>
    </image>
    
    <image redraw=yes offsetXPC=82.11 offsetYPC=67.36 widthPC=1.88 heightPC=1.95>
        <script>
            idx = getFocusItemIndex();
            
            if (idx == (favLinkCount-1) || favLinkCount == 0 || favLinkCount &lt; 0)
            	"/usr/local/etc/mos/www/modules/iptvlist/images/arrow_down_unfocus.png";
            else
            	"/usr/local/etc/mos/www/modules/iptvlist/images/arrow_down.png";
            idx;
        </script>
    </image>
    
    <text offsetXPC=16.49 offsetYPC=8.82 widthPC=59.61 heightPC=5.05 fontSize=17 useBackgroundSurface=yes redraw=no backgroundColor=-1:-1:-1>
        <script>
            titleStr = hint = "<?= getMsg( 'iptvTitle' ) ?> - " + listName;
        </script>
    </text>
    
    <itemDisplay>
        <!--text offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100 useBackgroundSurface=yes/-->
        <image offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100 useBackgroundSurface=yes>
            <script>
                status = getDrawingItemState();
                if (status == "focus")
                {
                	"/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_FAVORITS_FOC.png";
                }
                else
                	"";
            </script>
        </image>
        <text offsetXPC=4.0 offsetYPC=4 widthPC=20 heightPC=92 fontSize=12 backgroundColor=-1:-1:-1>
        	<foregroundColor>
	            <script>
	            	color = "250:250:250";
	                color;
	            </script>
        	</foregroundColor>
            <script>
                itemT = getItemInfo("chNumber");
                itemT;
            </script>
            <rolling>
    	        <script>
        	   "no";
        	</script>
        	</rolling>
        </text>
        <text offsetXPC=13.0 offsetYPC=4 widthPC=88 heightPC=92 fontSize=12 backgroundColor=-1:-1:-1 tailDots=yes> 
        	<foregroundColor>
	            <script>
	            	color = "250:250:250";
	                color;
	            </script>
        	</foregroundColor>
            <script>
                itemT = getItemInfo("displayTitle");
                itemT;
            </script>
            <rolling>
    	        <script>
        	    if (getDrawingItemState() == "focus")
        	    {
        	  	    "yes";
        	    }
        	    else
        	    {
        	  	    "no";
        	    }
        	    </script>
        	</rolling>
        </text>
    </itemDisplay>
    
    <!-- background image -->
    <backgroundDisplay name="MyFavoritsBackground">
		<image offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/usr/local/etc/mos/www/modules/iptvlist/images/IMAGE_TV_BG.png
		</image>
    </backgroundDisplay>
    
    <!-- hint indicator -->
    <image offsetXPC=28.45 offsetYPC=85.42 widthPC=2.4 heightPC=4.1 useBackgroundSurface=yes redraw=no>
		<script>
			icon = null;
			if(favLinkCount &gt; 0) 
			{
				icon = "/usr/local/etc/mos/www/modules/iptvlist/images/ok.png";
			}
			icon;
		</script>
	</image>

	<text offsetXPC=30.94 offsetYPC=85.10 widthPC=51.87 heightPC=4.72 fontSize=10 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align=left tailDots=no useBackgroundSurface=yes redraw=no>
		<script>
			hint = "";
			if(favLinkCount &gt; 0) 
			{
			    hint = "<?= getMsg( 'iptvPlay' ) ?>";
			}
			hint;
		</script>
	</text>
	
	<!-- Rename-->
    <image offsetXPC=28.4 offsetYPC=90.97 widthPC=2.7 heightPC=4.6 useBackgroundSurface=yes redraw=no>
		<script>
			icon = "/usr/local/etc/mos/www/modules/iptvlist/images/pd.png";
			icon;
		</script>
	</image>

	<text offsetXPC=30.94 offsetYPC=90.97 widthPC=58 heightPC=4.72 fontSize=10 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align=left tailDots=no useBackgroundSurface=yes redraw=no>
		<script>
		    hint = "<?= getMsg( 'iptvShowActionsMenu' ) ?>";
			hint;
		</script>
	</text>

    <image offsetXPC=48.4 offsetYPC=90.97 widthPC=2.5 heightPC=4.3 useBackgroundSurface=yes redraw=no>
		<script>
			icon = "/usr/local/etc/mos/www/modules/iptvlist/images/notes.png";
			icon;
		</script>
	</image>

	<text offsetXPC=50.94 offsetYPC=90.97 widthPC=58 heightPC=4.72 fontSize=10 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align=left tailDots=no useBackgroundSurface=yes redraw=no>
		<script>
		    hint = "<?= getMsg( 'iptvSelectPlaylist' ) ?>";
			hint;
		</script>
	</text>

    <image offsetXPC=66.4 offsetYPC=90.97 heightPC=4.3 useBackgroundSurface=yes redraw=no>
		<widthPC>
			<script>
				if ( isRecordings ) {s= 2.5;} else {s = 0;};
				s;
			</script>
		</widthPC>


		<script>
			icon = "/usr/local/etc/mos/www/modules/iptvlist/images/zoom.png";
			icon;
		</script>
	</image>

	<text offsetXPC=68.94 offsetYPC=90.97 heightPC=4.72 fontSize=10 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align=left tailDots=no useBackgroundSurface=yes redraw=no>
		<widthPC>
			<script>
				if ( isRecordings ) {s= 58;} else {s = 0;};
				s;
			</script>
		</widthPC>
		<script>
		    hint = "<?= getMsg( 'iptvRenVideo' ) ?>";
			hint;
		</script>
	</text>

	<text offsetXPC=84.2 offsetYPC=87.5 widthPC=11 heightPC=4.72 fontSize=7 foregroundColor=255:255:255 backgroundColor=-1:-1:-1 align=right tailDots=no useBackgroundSurface=yes redraw=no>
		<script>
			hint = "";
			if(favLinkCount &gt; 0 ) 
			{
			    hint = "iptvlist v1.2.3 by hax";
			}
			hint;
		</script>
	</text>
		
	<!-- input -->
	<onUserInput>
	    userInput = currentUserInput();
	    print ("[My Favorites - input()] get input:", userInput);
	    executeScript("handleUserInput");
	    ret;
	</onUserInput>
</mediaDisplay>

<MyNeighborsPasswordPopup>
	<link><?= getMosUrl().'?page=' ?>rss_deleteconfirm</link>
</MyNeighborsPasswordPopup>

<!--
<LoginControlPopup>
	<link><?= getMosUrl().'?page=' ?>rss_myfavorites_LoginControlPopup</link>
</LoginControlPopup>
-->

<itemSize>
<script>
    favLinkCount;
</script>
</itemSize>



</rss>
<?php

}

?>
