<doInit>
    executeScript("platformInit");

    /* always use timerInterval to set refresh time!*/
    timerInterval = 10;
    setRefreshTime(timerInterval * 1000);

    refreshInterval = {$config->get(refreshInterval)} * 60;
    if (refreshInterval &lt; 60) {
        /* reset to default if invalid */
        refreshInterval = 5 * 60;
    }

    minRefreshTime  = 60;
    forceRefresh    = "false";
    refreshed       = 0;

    /* set last update only if it was not set before */
    if (null == lastUpdate) {
        executeScript("getTime");
        lastUpdate = now;
    }

    osdNotifications = 1;
    osdTimeout       = 0;
    videoOn          = 0;
    playingChannel   = -1;
    executeScript("osdHide");
</doInit>

<doRefresh>
    executeScript("platformRefresh");

    executeScript("getTime");
    timeGone = now - lastUpdate;

{if {$config->get(rss_debug)} == "1"}
    /* some traces useful for debug */
    print("================= entering Refresh");
    print("================= videoOn : ", videoOn);
    print("================= osdPos  : ", osdPos);
    print("================= percent : ", currPercent);
    print("================= sec gone: ", timeGone);
    print("================= playing : ", playingChannel);
    print("================= focus   : ", getFocusItemIndex());
    print("================= date    : ", now);

    vidProgress = getPlaybackStatus();
    bufProgress = getCachedStreamDataSize(0, 262144);
    playElapsed = getStringArrayAt(vidProgress, 0);
    playStatus  = getStringArrayAt(vidProgress, 3);
    print("================= video   : ", vidProgress);
    print("================= buffer  : ", bufProgress);
    print("================= elapsed : ", playElapsed);
    print("================= status  : ", playStatus);
{/if}

    /* check whether video was stopped */
    if (0 != videoOn) {
        vidProgress = getPlaybackStatus();
        playStatus  = getStringArrayAt(vidProgress, 3);
        if (0 == playStatus) {
            executeScript("videoStop");
        }
    }

    /* if refresh interval is reached or forced refresh wished */
    videoOffRefreshCond = 0 == videoOn &amp;&amp;
        (timeGone &gt;= refreshInterval || "true" == forceRefresh);

    /* if current program ends and OSD is shown or notification allowed */
    videoOnRefreshCond = 0 != videoOn &amp;&amp;
        (100 == currPercent &amp;&amp; (0 == osdPos || 1 == osdNotifications));

    /* no matter what's happening, min refresh time should be respected */
    if ((1 == videoOnRefreshCond || 1 == videoOffRefreshCond) &amp;&amp; timeGone &gt;= minRefreshTime) {
    {if {$config->get(rss_debug)} == "1"}
        print("================= Reloading data");
        print("    videoOnRefreshCond : ", videoOnRefreshCond);
        print("    videoOffRefreshCond: ", videoOffRefreshCond);
        print("    forceRefresh       : ", forceRefresh);
        print("    currPercent        : ", currPercent);
        print("    timeGone           : ", timeGone);
        print("    now                : ", now);
        print("    lastUpdate         : ", lastUpdate);
        print("    osdPos             : ", osdPos);
        print("    osdNotifications   : ", osdNotifications);
    {/if}

        forceRefresh = "false";
{if {$config->get(enableOSD)} == "1"}
        if (1 == videoOnRefreshCond) {
            executeScript("osdShow");
        }
{/if}
        refreshed = 1;
        lastUpdate = now;
        dataUrl = "{$selfUrl}&amp;mode=data&amp;nostat";
        {if {$config->get(rss_debug)} == "1"}
            print("================= Data reloading from: ", dataUrl);
        {/if}

        csvData = "";
        {$portion = 41.0}
        csvData = "";
        fullLength = {$mediaObj->getChildCount()};
		readed = 0;
		while (readed &lt; fullLength) {
			csvData += getCSVFromURL(dataUrl + "&amp;offset=" + readed + "&amp;length={$portion}");
			readed = Add(readed,{$portion}); 
	    }
		if (csvData != null) {
            {if {$config->get(rss_debug)} == "1"}
                print("================= Data reloading: OK");
            {/if}
            redrawDisplay();
        } else {
            {if {$config->get(rss_debug)} == "1"}
                print("================= Data reloading: FAILED");
            {/if}
        }
    }
{if {$config->get(enableOSD)} == "1"}
    /* automatically hide OSD when timeout is reached */
    if (osdTimeout &gt; 0) {
        osdTimeout = osdTimeout - 1;
        if (0 == osdTimeout) {
            executeScript("osdHide");
        }
    }
{/if}
</doRefresh>

<doDestroy>
    setRefreshTime(-1);
{if {$config->get(enableOSD)} == "1"}
    playItemURL(-1, 1);
{/if}
</doDestroy>

<osdShow>
{if {$config->get(rss_debug)} == "1"}
    print("================= osdShow");
{/if}
    osdTimeout = 7;
    osdPos     = 0;
</osdShow>

<osdHide>
{if {$config->get(rss_debug)} == "1"}
    print("================= osdHide");
{/if}
    osdTimeout = 0;
    osdPos     = 101;
</osdHide>

<videoStart>
    executeScript("getStreamUrl");
    if (null != streamUrl &amp;&amp; "" != streamUrl &amp;&amp; "protected" != streamUrl) {
{if {$config->get(enableOSD)} == "1"}
        playItemURL(-1, 1);
        executeScript("osdShow");
        playingChannel = index;
        videoOn = 100;
        playItemURL(streamUrl, 10, "mediaDisplay",  "previewWindow");
{else}
        playItemURL(streamUrl,0);
        redrawDisplay();
{/if}
    } else {
        executeScript("videoStop");
    }
</videoStart>

<videoStop>
{if {$config->get(rss_debug)} == "1"}
    print("================= videoStop");
{/if}
{if {$config->get(enableOSD)} == "1"}
    playingChannel = -1;
    videoOn = 0;
    playItemURL(-1, 1);
    executeScript("osdHide");
    redrawDisplay();
{/if}
</videoStop>

<!--
Returns value of item either from XML or directly.
Params:
    index : index of item which value is needed.
    name  : name of item tag which value is needed.
Return:
    value : requested item value.
-->
<getValue>
    if (0 == refreshed) {
        value = getItemInfo(index, name);
    } else {
    	value = getStringArrayAt(csvData, Add(index * 4, position));
        if (value == "") {
            value = null;
        } 
    {if {$config->get(rss_debug)} == "1"}
        print("================= getValue:", name, "[", index, ",", position, "] :", value);
    {/if}
    }
</getValue>

<!--
Returns URL of currently selected video.
Shows authorization window for protected channels.
Return:
    streamUrl : URL of currently selected video.
-->
<getStreamUrl>
    executeScript("getTime");
    index = getFocusItemIndex();
    url   = getItemInfo(index, "link");
{if {$config->get(rss_debug)} == "1"}
    print("================= Link to load: ", url);
{/if}
    streamUrl = getURL(url);

{if {$config->get(rss_debug)} == "1"}
    print("================= streamUrl: ", streamUrl);
    print("================= status: ", playStatus);

{/if}
    if ("protected" == streamUrl) {
    	/*inputPassAge = getEnv("pkey_age");*/
		envKey = "pkey_age";
        executeScript("getEnvValue");
        inputPassAge = envValue;

        /*inputPass = getEnv("pkey");*/
		envKey = "pkey";
        executeScript("getEnvValue");
        inputPass = envValue;
    {if {$config->get(rss_debug)} == "1"}
        print("================= inputPassAge : ", inputPassAge);
    {/if}

        if (null == inputPass || now - inputPassAge &gt; {$config->get(pkey_lifetime)}) {
            inputPass = getInput("passWord", "doModal");
        }

        authUrl = url + "&amp;pkey=" + inputPass;
        streamUrl = getURL(authUrl);
        if ("protected" == streamUrl) {
            setEnv("caption", "{$locale->msg('Error')}");
            setEnv("msg", "{$locale->msg('Wrong password')}!");
            url = "{$config->get(cfg_home,Runtime)}{$config->get(activeTemplate,Runtime)|replace:'\\':'/'}popup.xml";
            jumpToLink("rssLink");
            redrawDisplay();
        } else {
            /* save password */
            /*setEnv("pkey", inputPass);*/
			envKey 		= "pkey";
			envValue	= inputPass;
        	executeScript("setEnvValue");
            /*setEnv("pkey_age", now);*/
			envKey 		= "pkey_age";
			envValue	= now;
        	executeScript("setEnvValue");
        }
    }
</getStreamUrl>
