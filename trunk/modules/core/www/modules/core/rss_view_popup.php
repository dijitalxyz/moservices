<?php

class rssSkinPopupView extends rssSkinPopup
{
	//
	// ------------------------------------
	public $itemImage = '';
	//
	// ------------------------------------
	function showScripts()
	{
?>
  <onEnter>
	cancelIdle();
<?php
	if( getMosOption('sdk_version') > 3 )
	{
?>
	setParentFade(96);
<?php
	}
?>
	itemCount = getPageInfo( "itemCount" );
  </onEnter>
<?php
	}
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

	// calculate viewArea
		$ot = static::offsetTop;
		$ob = static::offsetBottom;
		$vw = static::viewAreaWidth;

		$tx = static::topX;
		$ty = static::topY;
		$tw = static::topWidth;
		$th = static::topHeight;

		$bh = static::bottomHeight;

		$iw = static::itemWidth;
		$ih = static::itemHeight;

		$ah = $sh - $ot - $ob;
		$nn = floor(( $ah - 2*$th - $bh - $ty ) / $ih );
		$n = count( $this->items );

		if( $n > $nn ) $n = $nn;

		$vh = $th + $bh + $ty + $ih * $n;

		$vx = ( $kw - $vw )/2;
		$vy = ( $kh - $vh )/2 - $th;

		$ix = ( $vw - $iw )/2;
		$iy = $th + $ty + $bh/2;

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

   idleImageXPC="<?= round( static::idleImageX/$sw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$sh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$sw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$sh*100, 4) ?>"
  >
<?php
		$this->showIdleBg();
		$this->showTop( $vw, $vh );
		$this->showItemDisplay();
		$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
	}
}

?>
