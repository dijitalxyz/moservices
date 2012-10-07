
<platformInit>
    executeScript("initConstants");
    currentTimestamp = {$smarty.now};
    currentDate = "{$smarty.now|date_format:'%d.%m'}";
    /*for OSD only viewer 0 is valid*/
{if {$config->get(enableOSD)} == "1"}
    SwitchViewer(0);
{else}
    SwitchViewer({$config->get("viewer{$mediaObj->type}")});
{/if}
</platformInit>

<!-- increase timestamp -->
<platformRefresh>
    currentTimestamp = currentTimestamp + timerInterval;
    redrawDisplay();
</platformRefresh>

<!-- Common realtek key constants -->
<initConstants>
keyVPlay    = "video_play";
keyVStop    = "video_stop";
keyFrWd     = "video_frwd";
keyFfWd     = "video_ffwd";
keyPgUp     = "pageup";
keyPgDn     = "pagedown";
keyUp       = "up";
keyDown     = "down";
keyLeft     = "left";
keyRight    = "right";
keyEnter    = "enter";
keyDisplay  = "display";
keyZoom     = "zoom";
keyEdit     = "edit";
keyReturn   = "return";
keyGuide    = "guide";

/*redefine used keys*/
keyUser1 = keyPgDn;
keyUser2 = keyZoom;
keyUser3 = keyPgUp;
keyUser4 = keyDisplay;
</initConstants>

<getTime>
    now = currentTimestamp;
</getTime>
<getTimeStr>
    now = currentTimestamp + {$util->getTimezoneOffset()};
    timeOnly = now - Integer1(now/86400)*86400;
    Hours = Integer1(timeOnly/3600);
    Minutes  = Integer1((timeOnly - (Hours*3600))/60);
    Seconds  = timeOnly - Hours*3600 - Minutes*60;
    if(Hours &lt; 10) {
        Hours = "0" + Hours;
    }
    if(Minutes &lt; 10) {
        Minutes = "0" + Minutes;
    }
    if(Seconds &lt; 10) {
        Seconds = "0" + Seconds;
    }
    timeStr = currentDate + " " + Hours + ":" + Minutes;
</getTimeStr>
{if {$config->get(rss_debug)} == "1"}

<writeLog>
	log = log + msg + "&#10;";
	writeStringToFile("/usr/local/etc/mos/uaonline/TVonTop/rss_log.txt",log);
</writeLog>
{/if}
