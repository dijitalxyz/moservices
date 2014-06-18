<?php
//
// ------------------------------------
function im_screensaver_content()
{
global $mos;

	header( "Content-type: text/plain" );

	$f = $mos.'/iconmenu/iconmenu.conf';
	$xml = simplexml_load_file( $f );

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

    <text redraw="yes" align="center" fontSize="<?= $xml->screensaver->size ?>"
     widthPC="20" heightPC="10"
     backgroundColor="0:0:0" foregroundColor="<?= $xml->screensaver->color ?>">
      <offsetXPC><script>posX;</script></offsetXPC>
      <offsetYPC><script>posY;</script></offsetYPC>
      <script>
	stime;
      </script>
    </text>

    <onUserInput>
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('return') ?>")
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
	posX = "20";
	posY = "78";
	setRefreshTime(100);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);

	/* clear current values */
	stime = "";
	redrawDisplay();

	/* get time */
	s = getTimeDate();
	t = getStringArrayAt(s, 3); if( t &lt; 10 ) t = "0" + t; d = t + ":";
	t = getStringArrayAt(s, 4); if( t &lt; 10 ) t = "0" + t; d = d + t ;
<?php
	if( $xml->fw == 'mele' )
	{

?>
	MeleVFDShow(d);
<?php
	}
	elseif( $xml->fw == 'inext' )
	{

?>
	sekatorSWF_vfdShowMessage(d);
<?php
	}

?>
	/* new position */
	posX = getURL("<?= getMosUrl().'?page=im_random&amp;max=80' ?>");
	posY = getURL("<?= getMosUrl().'?page=im_random&amp;max=90' ?>");

	stime = d;
	redrawDisplay();

	setRefreshTime(5000);
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