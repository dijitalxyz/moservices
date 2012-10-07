
<platformInit>
    executeScript("initConstants");
/*for OSD only viewer 0 is valid*/
{if {$config->get(enableOSD)} == "1"}
    SwitchViewer(0);
{else}
    SwitchViewer({$config->get("viewer{$mediaObj->type}")});
{/if}
</platformInit>

<platformRefresh>
</platformRefresh>

<!-- xtreamer changed key constants -->
<initConstants>
key0        = "0";
key1        = "1";
key2        = "2";
key3        = "3";
key4        = "4";
key5        = "video_search";
key6        = "6";
key7        = "7";
key8        = "8";
key9        = "9";
keyVPlay    = "video_play";
keyVStop    = "video_stop";
keyFrWd     = "video_frwd";
keyFfWd     = "video_ffwd";
keyPgUp     = "PG";
keyPgDn     = "PD";
keyUp       = "U";
keyDown     = "D";
keyLeft     = "L";
keyRight    = "R";
keyEnter    = "ENTR";
keyDisplay  = "DISPLAY";
keyReturn   = "RET";

/*redefine used keys*/
keyUser1 = key1;
keyUser2 = key2;
keyUser3 = key3;
keyUser4 = key5;
</initConstants>

<getTime>
    now = date("%s");
</getTime>
<getTimeStr>
    timeStr = date("%d.%m %H:%M");
</getTimeStr>
{if {$config->get(rss_debug)} == "1"}
<writeLog>
	log = log + msg + "&#10;";
    writeStringToFile("/tmp/usbmounts/sda1/scripts/TVonTop/log/rss_log.txt",log);
</writeLog>
{/if}
