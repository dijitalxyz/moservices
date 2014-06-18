<?php

include 'onlinerec.init.php';

//
// ------------------------------------
function rss_onlinerec_channels_content()
{
global $onlinerec_session;

	$scrWidth  = 1280;
	$scrHeight = 720;

	$areaWidth  = 348;
	$areaHeight = 720;

	$itemWidth  = 280;
	$itemHeight = 47;

	$backgroundColor            = '86:91:103';

	$focusFontColor             = '0:0:0';
	$unfocusFontColor           = '255:255:255';
	$focusBackgroundColor       = '255:255:255';

	header( "Content-type: text/plain" );

	echo '<?xml version="1.0" ?>' .PHP_EOL;
	echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' .PHP_EOL;

?>
  <mediaDisplay name="onePartView"
   viewAreaXPC="0"
   viewAreaYPC="0"
   viewAreaWidthPC="<?= round(( $areaWidth + 0.5 ) / $scrWidth * 100, 4) ?>"
   viewAreaHeightPC="<?= round( $areaHeight / $scrHeight * 100, 4) ?>"

   backgroundColor="<?= $backgroundColor ?>"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="<?= $backgroundColor ?>"
   sideColorRight="<?= $backgroundColor ?>"

   showHeader="no"
   showDefaultInfo="no"
   forceFocusOnItem="yes"

   itemGap="0"
   itemPerPage="12"

   itemXPC="<?= round( 60.5 / $areaWidth  * 100, 4) ?>"
   itemYPC="<?= round( 75.5 / $areaHeight * 100, 4) ?>"
   itemWidthPC="<?=   round( $itemWidth  / $areaWidth  * 100, 4) ?>"
   itemHeightPC="<?=  round( $itemHeight / $areaHeight * 100, 4) ?>"

   itemImageXPC="<?= round( 60.5 / $areaWidth  * 100, 4) ?>"
   itemImageYPC="<?= round( 75.5 / $areaHeight * 100, 4) ?>"
   itemImageHeightPC="0"
   itemImageWidthPC="0"

   itemBackgroundColor="<?= $backgroundColor ?>"

   idleImageXPC="45.5882"
   idleImageYPC="42.1875"
   idleImageWidthPC="8.8235"
   idleImageHeightPC="15.625"
  >
    <idleImage><?= getRssImages() ?>idle01.png</idleImage>
    <idleImage><?= getRssImages() ?>idle02.png</idleImage>
    <idleImage><?= getRssImages() ?>idle03.png</idleImage>
    <idleImage><?= getRssImages() ?>idle04.png</idleImage>
    <idleImage><?= getRssImages() ?>idle05.png</idleImage>
    <idleImage><?= getRssImages() ?>idle06.png</idleImage>
    <idleImage><?= getRssImages() ?>idle07.png</idleImage>
    <idleImage><?= getRssImages() ?>idle08.png</idleImage>

    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();
	if (drawState == "unfocus")
	{
		bgcolor = "<?= $backgroundColor ?>";
		color   = "<?= $unfocusFontColor ?>";
	}
	else
	{
		bgcolor = "<?= $focusBackgroundColor ?>";
		color   = "<?= $focusFontColor ?>";
	}
      </script>
<!-- bar -->
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<!-- icon -->
<?php
		$px = round( 10.5 / $itemWidth  * 100, 4);;
		$py = round( 10.5 / $itemHeight * 100, 4);
		$pw = round( 27.5 / $itemWidth  * 100, 4);;
		$ph = round( 27.5 / $itemHeight * 100, 4);

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
	<script>
	  getItemInfo( idx, "image" );
	</script>
      </image>
<!-- title -->
<?php
		$px = round(  47.5 / $itemWidth  * 100, 4);;
		$py = round(  11.5 / $itemHeight * 100, 4);
		$pw = round( 235.5 / $itemWidth  * 100, 4);;
		$ph = round(  27.5 / $itemHeight * 100, 4);

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="left" lines="1" fontSize="14" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
	<script>
	  getItemInfo( idx, "title" );
	</script> 
      </text>
    </itemDisplay>

    <onUserInput>
	input = currentUserInput();
	ret = "false";

	if( input == "<?= getRssCommand('enter') ?>" )
	{
		setReturnString( getFocusItemIndex() );
		postMessage( "return" );
		ret = "true";
	}	

	else if( input == "<?= getRssCommand('left') ?>" || input == "<?= getRssCommand('right') ?>" )
	{
		ret = "true";
	}		
	ret;
    </onUserInput>
  </mediaDisplay>

  <onEnter>
	idx = getEnv( "onlinerecChannelNumber" );
	cmd = getEnv( "onlinerecChannelCommand" );
	setFocusItemIndex( idx );
	postMessage( cmd );
  </onEnter>

  <channel>
<?php

	foreach( $onlinerec_session['channels'] as $id => $item )
	{
		echo "    <item>\n";
		echo '      <id>'   . $id .'</id>'.PHP_EOL;
		echo '      <title>'. $item['title'] .'</title>'.PHP_EOL;
		echo '      <image>'. $item['image'] .'</image>'.PHP_EOL;
		echo "    </item>\n";
	}

?>
  </channel>
</rss>
<?php

}

?>
