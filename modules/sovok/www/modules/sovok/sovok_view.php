<?php

class rssSkinSovokView extends rssSkinPopup
{
	const titleBackgroundColor = '200:200:200';
	const itemBackgroundColor  = '20:20:20';

//	const titleBackgroundColor = '245:173:29';
//	const itemBackgroundColor  = '0:9:39';

	const topBackground         = '';

// view
	const viewAreaWidth	= 500;
	const viewAreaHeight	= 640;

	const borderX		= 2;
	const borderY		= 2;

// top
	const topX		= 4;
	const topY		= 4;
	const topWidth		= 592;
	const topHeight		= 36;

// top title
	const topOffsetX	= 2;
	const topOffsetY	= 2;

// items
	const itemOffsetX	= 15;
	const itemOffsetY	= 15;

	const itemX		= 200;

	const itemHeight	= 40;
	const itemWidth		= 280;

	const itemTextX		= 20;
	const itemTextY		= 0;
	const itemTextWidth	= 260;
	const itemTextHeight	= 40;
	//
	// ------------------------------------

	private $areaWidth;
	private $areaHeight;

	//
	// ------------------------------------
	public function showIdleBg()
	{

?>
    <idleImage><?= getRssImages() ?>idle01.png</idleImage>
    <idleImage><?= getRssImages() ?>idle02.png</idleImage>
    <idleImage><?= getRssImages() ?>idle03.png</idleImage>
    <idleImage><?= getRssImages() ?>idle04.png</idleImage>
    <idleImage><?= getRssImages() ?>idle05.png</idleImage>
    <idleImage><?= getRssImages() ?>idle06.png</idleImage>
    <idleImage><?= getRssImages() ?>idle07.png</idleImage>
    <idleImage><?= getRssImages() ?>idle08.png</idleImage>
<?php
		// Background
		$px = round( ( static::borderX ) / $this->areaWidth * 100, 4);
		$py = round( ( static::borderY + static::topHeight ) / $this->areaHeight * 100, 4);
		$pw = round( ( $this->areaWidth - 2 * static::borderX )  / $this->areaWidth  * 100, 4);
		$ph = round( ( $this->areaHeight - static::topHeight - 2 * static::borderY ) / $this->areaHeight * 100, 4);

?>
    <backgroundDisplay>
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
       cornerRounding="15" backgroundColor="<?= static::titleBackgroundColor ?>" >
      </text>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       cornerRounding="15" backgroundColor="<?= static::itemBackgroundColor ?>" >
      </text>
    </backgroundDisplay>
<?php
	}
	//
	// ------------------------------------
	public $infos = array();
	//
	// ------------------------------------
	function showMoreDisplay()
	{
		foreach( $this->infos as $info )
		{
			$px = round( $info['posX'] / $this->areaWidth * 100, 4);
			$py = round( $info['posY'] / $this->areaHeight * 100, 4);
			$pw = round( $info['width']  / $this->areaWidth  * 100, 4);
			$ph = round( $info['height'] / $this->areaHeight * 100, 4);

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
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
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
	public function showItemDisplay()
	{

?>
    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();
	if (drawState == "unfocus")
	{
		bcolor = "<?= static::itemBackgroundColor ?>";
		fcolor = "<?= static::unFocusFontColor ?>";
	}
	else
	{
		bcolor = "<?= static::itemFocusBgColor ?>";
		fcolor = "<?= static::focusFontColor ?>";
	}
      </script>
<?php
		// item title
		if( $this->itemTitle != '' )
		{
			$px = round( static::itemTextX / static::itemWidth  * 100, 4);
			$py = round( static::itemTextY / static::itemHeight * 100, 4);
			$pw = round( static::itemTextWidth  / static::itemWidth  * 100, 4);
			$ph = round( static::itemTextHeight / static::itemHeight * 100, 4);

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       align="<?= static::itemTextAlign ?>" lines="<?= static::itemTextLines ?>" fontSize="<?= static::itemTextFontSize ?>" >
	<backgroundColor><script>bcolor;</script></backgroundColor>
	<foregroundColor><script>fcolor;</script></foregroundColor>
        <?= $this->itemTitle ?>
      </text>
<?php
		}

		// more item's fields
		$this->showMoreItemDisplay();

?>
    </itemDisplay>
<?
	}
	//
	// ------------------------------------
	public function showScripts()
	{}
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

	// calculate viewArea
		$vw = static::viewAreaWidth;
		$vh = static::viewAreaHeight;

		$tx = static::borderX;
		$ty = static::borderY;
		$th = static::topHeight;

		$iw = static::itemWidth;
		$ih = static::itemHeight;
		$ix = static::itemOffsetX;
		$iy = static::itemOffsetY;

		$nn = floor(( $vh - $th - $ty - 2*iy ) / $ih );
		$n = count( $this->items );

		if( $n > $nn ) $n = $nn;

		$vh = $th + $ty + 2*$iy + $ih * $n;

		$vx = ( $kw - $vw )/2;
		$vy = ( $kh - $vh )/2 - $th;

		$ix = static::itemX;
		$iy = $th + $iy;

		$this->areaWidth  = $vw;
		$this->areaHeight = $vh;

?>
  <mediaDisplay name=onePartView
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

   itemPerPage="<?= $n ?>"
   itemXPC="<?= round($ix/$vw*100, 4) ?>"
   itemYPC="<?= round($iy/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"
   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   drawItemText="no"
   forceFocusOnItem="yes"

   itemGapXPC="0"
   itemGapYPC="0"

   focusBorderColor = "<?= static::focusBorderColor ?>"
   unFocusBorderColor = "<?= static::unFocusBorderColor ?>"

   imageFocus=""
   imageParentFocus=""
   imageUnFocus=""

   idleImageXPC="<?= round( static::idleImageX/$sw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$sh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$sw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$sh*100, 4) ?>"
  >
<?php
		$this->showIdleBg();

		$this->showTop( $vw, $vh );
		$this->showMoreDisplay();
		$this->showItemDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
}

?>
