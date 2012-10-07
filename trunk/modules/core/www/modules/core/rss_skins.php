<?php
//
// ------------------------------------
function getSkinPath()
{
global $rss_skin_path;

	return $rss_skin_path;
}
//
// ------------------------------------
function getRssImages()
{
global $rss_images;

	return $rss_images;
}
//
// ------------------------------------
class rssView
{
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

		if( static::background != '' )
		{

?>
    <backgroundDisplay>
      <image offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >
        <?= getSkinPath().static::background ?> 
      </image>
    </backgroundDisplay>
<?php
		}
	}
	//
	// ------------------------------------
	public $topTitle = '';
	public $bottomTitle = '';

	//
	// ------------------------------------
	public function showTop( $vw = 0, $vh = 0 )
	{
		if( $vw == 0 ) $vw = static::viewAreaWidth;
		if( $vh == 0 ) $vh = static::viewAreaHeight;

		if( static::topBackground != '' )
		{
			$px = round( static::topX / $vw * 100, 4);
			$py = round( static::topY / $vh * 100, 4);
			$pw = round( static::topWidth  / $vw * 100, 4);
			$ph = round( static::topHeight / $vh * 100, 4);

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" >
      <?= getSkinPath().static::topBackground ?> 
    </image>
<?php
		}

		if( $this->topTitle != '' )
		{
			$px = round(( static::topX + static::topOffsetX ) / $vw * 100, 4);
			$py = round(( static::topY + static::topOffsetY ) / $vh * 100, 4);
			$pw = round(( static::topWidth  - 2 * static::topOffsetX ) / $vw * 100, 4);
			$ph = round(( static::topHeight - 2 * static::topOffsetY ) / $vh * 100, 4);

?>
    <text align="<?= static::topAlign ?>" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     fontSize="<?= static::topFontSize ?>" backgroundColor="<?= static::topBackgroundColor ?>" foregroundColor="<?= static::topFontColor ?>"><?= $this->topTitle ?> 
    </text>
<?php
		}
	}
	//
	// ------------------------------------
	public function showBottom( $vw = 0, $vh = 0 )
	{
		if( $vw == 0 ) $vw = static::viewAreaWidth;
		if( $vh == 0 ) $vh = static::viewAreaHeight;

		if( static::bottomBackground != '' )
		{
			$px = round( static::bottomX / static::viewAreaWidth  * 100, 4);
			$py = round( static::bottomY / static::viewAreaHeight * 100, 4);
			$pw = round( static::bottomWidth  / static::viewAreaWidth  * 100, 4);
			$ph = round( static::bottomHeight / static::viewAreaHeight * 100, 4);

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" >
      <?= getSkinPath().static::bottomBackground ?> 
    </image>
<?php
		}

		if( $this->bottomTitle != '' )
		{
			$px = round(( static::bottomX + static::bottomOffsetX ) / static::viewAreaWidth  * 100, 4);
			$py = round(( static::bottomY + static::bottomOffsetY ) / static::viewAreaHeight * 100, 4);
			$pw = round(( static::bottomWidth  - 2 * static::bottomOffsetX ) / static::viewAreaWidth  * 100, 4);
			$ph = round(( static::bottomHeight - 2 * static::bottomOffsetY ) / static::viewAreaHeight * 100, 4);

?>
    <text align="<?= static::bottomAlign ?>" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     fontSize="<?= static::bottomFontSize ?>" backgroundColor="<?= static::bottomBackgroundColor ?>" foregroundColor="<?= static::bottomFontColor ?>"><?= $this->bottomTitle ?> 
    </text>
<?php
		}
	}
	//
	// ------------------------------------
	public function showMoreDisplay()
	{ }
	//
	// ------------------------------------
	public $itemImage =
'
	<script>
	  getItemInfo( idx, "image" );
	</script>
';
	//
	// ------------------------------------
	public $itemTitle =
'
	<script>
	  getItemInfo( idx, "title" );
	</script>
';
	//
	// ------------------------------------
	public function showMoreItemDisplay()
	{ }
	//
	// ------------------------------------
	public function showMenuDisplay()
	{ }
	//
	// ------------------------------------
	public $_columnCount;
	public $_rowCount;

	public function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	i = getFocusItemIndex();
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		if( ( i % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		if( ( ( i - -1 ) % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
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
		border = "<?= getSkinPath().static::imageUnFocus ?>";
		color = "<?= static::unFocusFontColor ?>";
	}
	else
	{
		border = "<?= getSkinPath().static::imageFocus ?>";
		color = "<?= static::focusFontColor ?>";
	}
      </script>
      <image redraw="no" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
        <script>
            border;
        </script>
      </image>
<?php
		// item image
		if( $this->itemImage != '' )
		{
			$px = round( static::itemImageX / static::itemWidth  * 100, 4);
			$py = round( static::itemImageY / static::itemHeight * 100, 4);
			$pw = round( static::itemImageWidth  / static::itemWidth  * 100, 4);
			$ph = round( static::itemImageHeight / static::itemHeight * 100, 4);

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $this->itemImage ?>
      </image>
<?php
		}

		// item title
		if( $this->itemTitle != '' )
		{
			$px = round( static::itemTextX / static::itemWidth  * 100, 4);
			$py = round( static::itemTextY / static::itemHeight * 100, 4);
			$pw = round( static::itemTextWidth  / static::itemWidth  * 100, 4);
			$ph = round( static::itemTextHeight / static::itemHeight * 100, 4);

?>
      <text align="<?= static::itemTextAlign ?>" lines="<?= static::itemTextLines ?>" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       fontSize="<?= static::itemTextFontSize ?>" backgroundColor="<?= static::itemTextBackgroundColor ?>">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor><?= $this->itemTitle ?>
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

		$nc = floor( $vw / $iw );
		$nr = floor( $ah / $ih );

		$this->_columnCount = $nc;
		$this->_rowCount = $nr;


?>
  <mediaDisplay name="photoView"

   viewAreaXPC="<?= round(( $sx + $vx )/$sw*100, 4) ?>"
   viewAreaYPC="<?= round(( $sy + $vy )/$sh*100, 4) ?>"
   viewAreaWidthPC="<?= round(( $vw )/$sw*100, 4) ?>"
   viewAreaHeightPC="<?= round(( $vh )/$sh*100, 4) ?>"

   backgroundColor="<?= static::backgroundColor ?>"

   sideTopHeightPC="0"
   sideBottomHeightPC="0"

   sideColorBottom="<?= static::sideColorBottom ?>"
   sideColorTop="<?= static::sideColorTop ?>"

   rowCount="<?= $nr ?>"
   columnCount="<?= $nc ?>"

   itemOffsetXPC="<?= round(($vw - $iw * $nc)/2/$vw*100, 4) ?>"
   itemOffsetYPC="<?= round((($ah - $ih * $nr)/2 + $th )/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"

   itemGapXPC="0"
   itemGapYPC="0"

   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   sliding="no"
   rollItems="no"
   drawItemText="no"
   forceFocusOnItem="yes"

   showHeader="no"
   showDefaultInfo="no"

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
	//
	// ------------------------------------
	function showScripts()
	{
?>
  <onEnter>
	cancelIdle();
	itemCount = getPageInfo( "itemCount" );
  </onEnter>
<?php
	}
	//
	// ------------------------------------
	public $items = array();
	//
	// ------------------------------------
	function showChannel()
	{
		echo "  <channel>\n";
		if( isset( $this->items ))
		foreach( $this->items as $item )
		{
			echo "    <item>\n";

			foreach( $item as $tag => $val )
			 echo "      <$tag>$val</$tag>\n";

			echo "    </item>\n";
		}

		echo "  </channel>\n";
	}
	//
	// ------------------------------------
	public function showRss()
	{
		header( "Content-type: text/plain" );
		echo '<?xml version="1.0" encoding="UTF8" ?>'.PHP_EOL;
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay();
		$this->showScripts();
		$this->showChannel();

		echo '</rss>'.PHP_EOL;
	}
}

if( file_exists( getSkinPath().'def_skin.php' ))
	include( getSkinPath().'def_skin.php' );

?>