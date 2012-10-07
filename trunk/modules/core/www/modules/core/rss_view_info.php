<?php

class rssSkinInfoView extends rssSkinList
{
	public $infos = array();
	//
	// ------------------------------------
	function showMoreDisplay()
	{
		foreach( $this->infos as $info )
		{
			$px = round( $info['posX'] / static::viewAreaWidth * 100, 4);
			$py = round( $info['posY'] / static::viewAreaHeight * 100, 4);
			$pw = round( $info['width']  / static::viewAreaWidth  * 100, 4);
			$ph = round( $info['height'] / static::viewAreaHeight * 100, 4);

			if( $info['type'] == 'image' )
			{

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" >
      <?= $info['image'] ?> 
    </image>
<?php
			}
			else
			{
				$pa = isset( $info['align'] ) ? $info['align'] : static::itemTextAlign;
				$pl = isset( $info['lines'] ) ? $info['lines'] : static::itemTextLines;
				$ps = isset( $info['fontSize'] ) ? $info['fontSize'] : static::itemTextFontSize;
				$pb = isset( $info['bgColor'] ) ? $info['bgColor'] : static::itemBackgroundColor;
				$pf = isset( $info['fgColor'] ) ? $info['fgColor'] : static::unFocusFontColor;

?>
    <text align="<?= $pa ?>"<?php

				if( $pl > 0 ) echo " lines=\"$pl\"";

?> offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     fontSize="<?= $ps ?>" backgroundColor="<?= $pb ?>" foregroundColor="<?= $pf ?>">
      <?= $info['text'] ?> 
    </text>
<?php
			}
		}
	}
	//
	// ------------------------------------
	public $_link;
	public $_action;

	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('enter') ?>")
	{
		url = "<?= $this->_link ?>";
		act = "<?= $this->_action ?>";

		if( act == "get" )
		{
			showIdle();
			url = getUrl(url);
			if(( url != null )&amp;&amp;( url != "" ))
			{
				playItemURL( url, 0 );
			}
			cancelIdle();
		}
		else if( act == "rss" )
		{
			url = doModalRss(url);
		}
		else
		{
			showIdle();
			playItemURL(url, 0);
			cancelIdle();
		}
		ret = "true";
	}
	else
	if (userInput == "<?= getRssCommand('left') ?>" || userInput == "<?= getRssCommand('right') ?>")
	{
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
	}
	//
	// ------------------------------------
	function showDisplay()
	{

		$sw = static::screenWidth;
		$sh = static::screenHeight;

		$kw = static::skinWidth;
		$kh = static::skinHeight;

		$sx = ($sw - $kw)/2;
		$sy = ($sh - $kh)/2;

		$vx = static::viewAreaX;
		$vy = static::viewAreaY;
		$vw = static::viewAreaWidth;
		$vh = static::viewAreaHeight;

?>
  <mediaDisplay name="threePartsView"
   viewAreaXPC="<?= round(( $sx + $vx )/$sw*100, 4) ?>"
   viewAreaYPC="<?= round(( $sy + $vy )/$sh*100, 4) ?>"
   viewAreaWidthPC="<?= round(( $vw )/$sw*100, 4) ?>"
   viewAreaHeightPC="<?= round(( $vh )/$sh*100, 4) ?>"

   backgroundColor="<?= static::backgroundColor ?>"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="<?= static::sideColorLeft ?>"
   sideColorRight="<?= static::sideColorRight ?>"

   showHeader="no"
   showDefaultInfo="no"

   itemPerPage="1"
   itemXPC="0"
   itemYPC="0"
   itemWidthPC="0"
   itemHeightPC="0"
   forceFocusOnItem="no"
   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   itemGapXPC="0"
   itemGapYPC="0"

   focusBorderColor = "<?= static::focusBorderColor ?>"
   unFocusBorderColor = "<?= static::unFocusBorderColor ?>"

   imageFocus=""
   imageParentFocus=""
   imageUnFocus=""

   idleImageXPC="<?= round( static::idleImageX/$vw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$vh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$vw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$vh*100, 4) ?>"
  >
<?php
		$this->showIdleBg();
		$this->showTop();
		$this->showBottom();

		$this->showMoreDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
	//
	// ------------------------------------
	public function showChannel()
	{
?>
  <channel>
  </channel>
<?php
	}
}

?>
