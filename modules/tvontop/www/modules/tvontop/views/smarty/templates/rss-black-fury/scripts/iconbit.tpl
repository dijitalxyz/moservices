<platformInit>
    executeScript("initConstants");
    currentTimestamp = {$smarty.now};
    currentDate = "{$smarty.now|date_format:'%d.%m'}";
</platformInit>

<!-- increase timestamp -->
<platformRefresh>
    currentTimestamp = currentTimestamp + timerInterval;
    redrawDisplay();
</platformRefresh>

<!-- Common realtek key constants -->
<initConstants>
keyVPlay = "video_play";
keyVStop = "video_stop";
keyFrWd  = "video_frwd";
keyFfWd  = "video_ffwd";
keyPgUp  = "pageup";
keyPgDn  = "pagedown";
keyUp  = "up";
keyDown  = "down";
keyLeft  = "left";
keyRight = "right";
keyEnter = "enter";
keyDisplay = "display";
keyZoom  = "zoom";
keyReturn = "return";
keyEdit  = "guide";
keyGuide = "display";

key1    = "one";
key2    = "two";
key3    = "three";
key4    = "four";
key5    = "five";
key6    = "six";
key7    = "seven";
key8    = "eight";
key9    = "nine";
key0    = "zero";

/*
keyEdit     = "edit";
keyGuide    = "guide";
*/
/*redefine used keys*/
keyUser1 = key1;
keyUser2 = key2;
keyUser3 = key3;
keyUser4 = key4;
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
    log = log + msg + "--//--";
    writeStringToFile("/usr/local/etc/mos/uaonline/TVonTop/log_realtek.txt",log);
</writeLog>
{/if}
