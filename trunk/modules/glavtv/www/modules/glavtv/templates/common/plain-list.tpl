<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $count = count($object->getItems());
    $cols = (int) (($count - 1) / 10 + 1);
    if ($cols >= 4) {
        $offX = 3;    $colW = 23; $cols = 4;
    } else if ($cols == 3) {
        $offX = 12;   $colW = 25;
    } else if ($cols == 2) {
        $offX = 14.5; $colW = 35;
    } else {
        $offX = 25;   $colW = 50; $cols = 1;
    }
    $rows = min(10, (int) (($count - 1) / $cols + 1));
?>


<mediaDisplay name="photoView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    rowCount="<?=$rows?>"
    columnCount="<?=$cols?>"

    itemOffsetXPC="<?=$offX?>"
    itemOffsetYPC="<?=44.5 - 6.1 * $rows / 2?>"
    itemWidthPC="<?=$colW?>"
    itemHeightPC="6.1"

    itemGapXPC="0.5"
    itemGapYPC="0.5"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <text redraw="yes" align="center" tailDots="no" lines="<?=$keysAdded ? 2 : 3?>" fontSize="15"
        offsetXPC="0" offsetYPC="87.2" widthPC="100" heightPC="<?=$keysAdded ? 8.6 : 12.9?>"
        foregroundColor="<?=$colors->get('fgInfo')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            value = getItemInfo("<?=Item::DESCRIPTION?>");
            if (null == value || "" == value) {
                value = "<?=str_replace('"', '″', $object->get(Channel::DESCRIPTION))?>";
            }
            value;
        </script>
    </text>

    <itemDisplay>
        <script>
            <?=calculateItemColors()?>
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="10">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <? if (in_array(Item::THUMBNAIL, $object->getItemParams())) {?>
        <image offsetXPC="3" offsetYPC="8" widthPC="14" heightPC="84">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
        <?} else {?>
        <text align="right" offsetXPC="0" offsetYPC="0" widthPC="20" heightPC="100" fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getPageInfo("focusItemIndex") + ". ";</script>
        </text>
        <?}?>
        <text offsetXPC="20" offsetYPC="0" widthPC="80" heightPC="100" fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
