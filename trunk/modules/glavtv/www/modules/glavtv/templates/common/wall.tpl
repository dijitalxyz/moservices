<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $ratio = $object->get(Channel::THUMB_RATIO, 2);
    if (1 == $ratio) {
        $rows = 4; $cols = 5;
    } else if (2 == $ratio) {
        $rows = 3; $cols = 10;
    } else {
        $rows = 3; $cols = 6;
    }
    $itemWidth  = 99.0 / $cols;
    $itemHeight = 69.0 / $rows;
?>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(array('itemBorderColor' => $colors->get('bgFocus'))) ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    rowCount="<?=$rows?>"
    columnCount="<?=$cols?>"

    itemOffsetXPC="1"
    itemOffsetYPC="9.5"
    itemWidthPC="<?=$itemWidth?>"
    itemHeightPC="<?=$itemHeight?>"

    itemGapXPC="0"
    itemGapYPC="0"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <text offsetXPC="0" offsetYPC="80" widthPC="100" heightPC="5"
        foregroundColor="<?=$colors->get('fgFocus')?>"
        backgroundColor="<?=$colors->get('bgPanel2')?>"
        redraw="yes" align="center" fontSize="16">
        <script>
            year = getItemInfo("<?=Item::YEAR?>");
            if (null != year) {
                getItemInfo("<?=Item::TITLE?>") + " - " + year;
            } else {
                getItemInfo("<?=Item::TITLE?>");
            }
        </script>
    </text>

<? addCounter($keysAdded) ?>
<?
   $allParams = $object->getItemParams();
   addInfo(in_array(Item::RATING, $allParams) || in_array(Item::YEAR, $allParams))
?>
<? addDescription($object, $keysAdded) ?>

    <itemDisplay>
        <script>
            <?=calculateItemColors()?>
        </script>

        <text  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="10">
            <backgroundColor><script>itemBg;</script></backgroundColor>
        </text>
        <text  offsetXPC="5" offsetYPC="5" widthPC="90"  heightPC="90"  backgroundColor="<?=$colors->get('bgNormal')?>" />
        <image offsetXPC="6" offsetYPC="6" widthPC="88"  heightPC="88">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
