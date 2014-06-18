<?php

class rssSkinRadioTop50InformationView extends rssSkinList
{


	function showMoreDisplay()
	{

?>
    <text offsetXPC="0.3" offsetYPC="1" widthPC="99.7" heightPC="98.5"
        backgroundColor="40:40:40" cornerRounding="15" />

    <text offsetXPC="3" offsetYPC="5" widthPC="90" heightPC="15"
        backgroundColor="40:40:40"
        foregroundColor="130:130:130"
        align="center" fontSize="14">Сообщение</text>

    <image offsetXPC="3.5" offsetYPC="30" widthPC="14.5"
        heightPC="40"> <?= dirname( __FILE__ ) .'/information.png' ?></image>

    <text offsetXPC="18" offsetYPC="30" widthPC="79" heightPC="41"
        backgroundColor="40:40:40"
        foregroundColor="200:200:200"
        fontSize="14" lines="3">К сожалению нет возможности воспроизвести  поток на плеере! Но Вы можете послушать его на сайте.</text>

    <text offsetXPC="35" offsetYPC="73" widthPC="30" heightPC="15"
        backgroundColor="255:170:0"
        foregroundColor="255:255:255"
        align="center" fontSize="14" cornerRounding="5">OK</text>

    <itemDisplay>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            backgroundColor="255:170:0"
            foregroundColor="255:255:255"
            align="center" fontSize="14" cornerRounding="5">OK</text>
    </itemDisplay>
</mediaDisplay>

<?php
	}

	//
	// ------------------------------------
	function showDisplay()
	{
		$vw = static::viewAreaWidth;
		$vh = static::viewAreaHeight;

?>
<mediaDisplay name="onePartView"
    backgroundColor="130:130:130"
    showHeader="no"
    showDefaultInfo="no"
    imageFocus="null"
    imageParentFocus="null"
    mainPartColor="0:0:0"
    itemBackgroundColor="0:0:0"
    itemBorderColor="0:0:0"
    sideTopHeightPC="0"
    sideBottomHeightPC="0"
    sideLeftWidthPC="0"
    sideRightWidthPC="0"
    sideColorTop="0:0:0"
    sideColorBottom="0:0:0"
    sideColorLeft="0:0:0"
    sideColorRight="0:0:0"
    drawItemText="no"
    slidingItemText="no"
    sliding="no"
    viewAreaXPC="25"
    viewAreaYPC="35"
    viewAreaWidthPC="50"
    viewAreaHeightPC="30"

    itemImageXPC="35"
    itemImageYPC="73"
    itemImageWidthPC="0"
    itemImageHeightPC="0"

    itemXPC="35"
    itemYPC="73"
    itemWidthPC="30"
    itemHeightPC="15"


    autoSelectMenu="no"
    autoSelectItem="no"
    cornerRounding="15"
    itemPerPage="1"
    itemGap="0"


   idleImageXPC="<?= round( static::idleImageX/$vw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$vh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$vw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$vh*100, 4) ?>"
  >
<?php
		$this->showIdleBg();
//		$this->showTop();

		$this->showMoreDisplay();
?>

<?php
	$view = new rssSkinRadioTop50InformationView;
?>
 
<?php
	}
	public function showChannel()
	{
?>	
  <channel>
    <item>
        <title>Ok</title>
        <onClick>
            <script>
            postMessage("return");
            null;
            </script>
        </onClick>
    </item>
</channel>
<?php
	}
	
}
?>