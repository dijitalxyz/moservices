<?php

class rssSkinListView extends rssSkinList
{
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

		$th = static::topHeight;
		$bh = static::bottomHeight;

		$iw = static::itemWidth;
		$ih = static::itemHeight;

		$ah = $vh - $th - $bh;

		$nr = floor( $ah / $ih );

		$ax = ($vw - $iw)/2;
		$ay = $th + ($ah - $ih * $nr)/2;

?>
  <mediaDisplay name="onePartView"
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
   forceFocusOnItem="yes"

   itemPerPage="<?= $nr ?>"
   itemXPC="<?= round($ax/$vw*100, 4) ?>"
   itemYPC="<?= round($ay/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"
   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   itemGapXPC="0"
   itemGapYPC="0"

   focusBorderColor = "<?= static::focusBorderColor ?>"
   unFocusBorderColor = "<?= static::unFocusBorderColor ?>"

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

		$this->showItemDisplay();
		$this->showMenuDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
}

?>
