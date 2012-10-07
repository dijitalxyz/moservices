
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
keyRed      = "option_red";
keyGreen    = "option_green";
keyYellow   = "option_yellow";
keyBlue     = "option_blue";
key1    	= "one";
key2    	= "two";
key3    	= "three";
key4    	= "four";
key5    	= "five";
key6    	= "six";
key7 	  	= "seven";
key8   		= "eight";
key9   	 	= "TVOUT";
key0    	= "zero";
keyVolUp	= "video_volume_up";
keyVolDown  = "video_volume_down";
keyMute		= "video_volume_mute";
keyRepeat	= "video_repeat";
keyAbRepeat	= "video_abrepeat";
keyEject	= "eject";
keySearch	= "video_search";


/*redefine used keys*/
keyUser1 = keyRed;
keyUser2 = keyGreen;
keyUser3 = keyYellow;
keyUser4 = keyBlue;
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
    writeStringToFile("/tmp/usbmounts/sda1/scripts/TVonTop/log/rss_log.txt",log);
</writeLog>
{/if}
