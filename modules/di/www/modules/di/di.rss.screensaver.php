<?php
//
// ------------------------------------
function di_rand_content()
{
	if( ! isset( $_REQUEST['max'] )) $m = 100.0;
	else $m = ( $_REQUEST['max'] );

	echo round( lcg_value() * $m, 4 );
}
//
// ------------------------------------
function rss_di_screensaver_content()
{
	header( "Content-type: text/plain" );

	echo '<?xml version="1.0" ?>'.PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

?>
  <mediaDisplay name="onePartView"
   viewAreaXPC="0"
   viewAreaYPC="0"
   viewAreaWidthPC="100"
   viewAreaHeightPC="100"

   backgroundColor="0:0:0"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="0:0:0"
   sideColorRight="0:0:0"

   showHeader="no"
   showDefaultInfo="no"

   idleImageXPC="45.5882"
   idleImageYPC="42.1875"
   idleImageWidthPC="8.8235"
   idleImageHeightPC="15.625"
  >
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle01.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle02.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle03.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle04.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle05.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle06.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle07.png</idleImage>
    <idleImage>/usr/local/etc/mos/www/modules/core/rss/images/idle08.png</idleImage>

    <text redraw="yes" align="center" lines="1" fontSize="15"
     widthPC="30" heightPC="3.5"
     backgroundColor="0:0:0" foregroundColor="160:160:160">
      <offsetXPC><script>posX;</script></offsetXPC>
      <offsetYPC><script>posY;</script></offsetYPC>
      <script>
	sTime;
      </script>
    </text>

    <text redraw="yes" align="center" lines="1" fontSize="11"
     widthPC="30" heightPC="3" rolling = "yes"
     backgroundColor="0:0:0" foregroundColor="255:255:255">
      <offsetXPC><script>posX;</script></offsetXPC>
      <offsetYPC><script>posY - -3.5;</script></offsetYPC>
      <script>
	channelPlay;
      </script>
    </text>

    <onUserInput>
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('return') ?>"
	 || userInput == "video_volume_up"
	 || userInput == "video_volume_down"
	 || userInput == "video_volume_mute")
	{
		ret = "false";
	}
	else
	{
		postMessage( "<?= getRssCommand('return') ?>" );
		ret = "true";
	}
	ret;
    </onUserInput>
  </mediaDisplay>

  <onEnter>
	s = readStringFromFile( "/tmp/di_channel.txt" );
	if( s != null &amp;&amp; s != "" )
	{
		stationId = getStringArrayAt(s, 0);
		channelId = getStringArrayAt(s, 1);
	}
	else
	{
		stationId = "";
		channelId = "";
	}

	posX = "20";
	posY = "78";

	countGet = 0;
	channelPlay = "";

	sTime = "";

	setRefreshTime(100);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);

	/* get time */
	s = getTimeDate();
	t = getStringArrayAt(s, 3); if( t &lt; 10 ) t = "0" + t; sTime = t + ":";
	t = getStringArrayAt(s, 4); if( t &lt; 10 ) t = "0" + t; sTime = sTime + t ;
	
	/* get play now */
	if( countGet == 0 )
	{
		countGet = 3;

		if( channelId != "" )
		{
			/* get now playing */
			s = getURL("<?= getMosUrl().'?page=di_track' ?>"
			+ "&amp;station=" + stationId
			+ "&amp;id="      + channelId
			);

			if ( s != null &amp;&amp; s != "" )
			{
				channelPlay = getStringArrayAt(s, 0);
			}
		}
	}
	else countGet -= 1;

	/* new position */
	posX = getURL("<?= getMosUrl().'?page=di_rand&amp;max=70' ?>");
	posY = getURL("<?= getMosUrl().'?page=di_rand&amp;max=64' ?>");
	redrawDisplay();

	setRefreshTime(10000);
	null;
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
	cancelIdle();
  </onExit>

  <channel>
    <itemSize>
      <script>
	0;
      </script>
    </itemSize>
  </channel>
</rss>
<?php

}

?>