<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $colW   = 6;
    $rowH   = 6.1;

    $count  = count($object->getItems());
    $rows   = min(10, (int) sqrt($count));
    $cols   = min((int) (96 / $colW), (int) (($count - 1) / $rows + 1));

    $width  = $cols * $colW + 2;
    $height = $rows * $rowH + 2;

    $frameW = 20 / $width;
    $frameH = 30 / $height;

    $offX   = (96 - $width)  / 2 + $frameW;
    $offY   = (89 - $height) / 2 + $frameH;

    $colW   = (90 - $frameW) / $cols;
    $rowH   = (90 - $frameH) / $rows;
?>


<mediaDisplay name="photoView"
<? showMediaDisplayParams(null, $colors->get('bgPanel1')) ?>
<? showViewAreaParams($offX, $offY, $width, $height) ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    rowCount="<?=$rows?>"
    columnCount="<?=$cols?>"

    itemOffsetXPC="6"
    itemOffsetYPC="5"
    itemWidthPC="<?=$colW?>"
    itemHeightPC="<?=$rowH?>"

    itemGapXPC="0.0"
    itemGapYPC="0.0"
    >
<? addIdleImages() ?>
<? addBoundingFrame($frameW, $frameH) ?>

    <itemDisplay>
        <script>
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
            } else {
                itemBg = "<?=$colors->get('bgPanel1')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            align="center" fontSize="15" cornerRounding="10">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>
</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
