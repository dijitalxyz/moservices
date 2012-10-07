<?php

class rssSkinLeftView extends rssSkinLeft
{
	//
	// ------------------------------------
	public $position = 0;

	public $itemImage = '';
	//
	// ------------------------------------
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('enter') ?>"  || userInput == "<?= getRssCommand('right') ?>")
	{
		idx = getFocusItemIndex();
		url = getItemInfo( idx, "link" );
		act = getItemInfo( idx, "action" );
		if( act == "rss" )
		{
			url = doModalRss(url);
		}
		else if ( act == "search" )
		{
			str = doModalRss("<?= getMosUrl().'?page=rss_keyboard' ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				url = url + urlEncode(str);
			}
			else url = "";
		}
		setReturnString( url );
		postMessage( "<?= getRssCommand('return') ?>" );
		ret = "true";
	}
	else
	if (userInput == "<?= getRssCommand('left') ?>")
	{
		postMessage( "<?= getRssCommand('return') ?>" );
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

		$vx = $vx + $vw * $this->position;

		$ix = static::itemX;
		$iy = static::itemY;
		$iw = static::itemWidth;
		$ih = static::itemHeight;

		$n = floor(( $vh - 2 * $iy ) / $ih );

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
   forceFocusOnItem="yes"
   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

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
		$this->showItemDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
}

?>
