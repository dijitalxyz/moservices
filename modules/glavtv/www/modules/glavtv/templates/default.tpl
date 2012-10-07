<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

<? showRegionParams('itemImage', 2, 10) ?>
<? showRegionParams('item', 2, 10, 47, 4.8) ?>

    itemPerPage="16"
    itemGap="0"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

<? addCounter($keysAdded) ?>

    <text redraw="yes" offsetXPC="10.2" offsetYPC="87" widthPC="51.8" heightPC="13"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="14">
        <script>getItemInfo("<?=Item::TITLE?>");</script>
    </text>

    <text offsetXPC="62" offsetYPC="88" widthPC="38" heightPC="8" fontSize="13"
        foregroundColor="<?=$colors->get('fgDark')?>" lines="2">
        <script>"*<?=$lang->msg('Default template')?>";</script>
    </text>

    <!-- detailed info -->
<?
    $length = 14;
    $cNum = count($object->getParams());
    $iNum = count($object->getItemParams());
    $sum = $cNum + $iNum;
    $cNum = min($cNum, (int) (0 == $sum ? ($length / 2) : ($cNum * $length / $sum)));
    $iNum = $length - $cNum;

    $diffY = 5;
    $startOffY = 10.5;
?>
    <!-- params split <?=$cNum . ':' . $iNum . '/' . $length?> -->

    <text offsetXPC="50" offsetYPC="9.4" widthPC="50" heightPC="<?=2.9 + $cNum * $diffY?>"
        backgroundColor="<?=$colors->get('bgPanel1')?>" />
    <text offsetXPC="50" offsetYPC="<?=9.4 + 2.9 + $cNum * $diffY?>" widthPC="50" heightPC="<?=3.8 + $iNum * $diffY?>"
        backgroundColor="<?=$colors->get('bgPanel2')?>" />

    <text offsetXPC="50" offsetYPC="9.4" widthPC="0.3" heightPC="76.7"
        backgroundColor="<?=$colors->get('lines')?>" />
    <text offsetXPC="50" offsetYPC="<?=9.4 + 2.9 + $cNum * $diffY?>" widthPC="50" heightPC="0.3"
        backgroundColor="<?=$colors->get('lines')?>" />


    <!-- channel params: <?=$cNum?> -->
    <?
        $offY = $startOffY;
        $i = 0;
        foreach ($object->getParams() as $param => $value) {
            if ($i++ < $cNum && $value != "") {
    ?>
    <text offsetXPC="52" offsetYPC="<?=$offY?>" widthPC="10" heightPC="<?=$diffY?>"
        fontSize="15" foregroundColor="<?=$colors->get('lines')?>"
        backgroundColor="<?=$colors->get('bgPanel1')?>">
        <script>"<?=$param?>";</script>
    </text>

    <text offsetXPC="62" offsetYPC="<?=$offY?>" widthPC="36" heightPC="<?=$diffY?>"
        fontSize="15" foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgPanel1')?>">
        <script>"<?=str_replace('"', '\\"', $value)?>";</script>
    </text>
    <?
                $offY += $diffY;
            }
        }
    ?>


    <!-- item params: <?=$iNum?> -->
    <?
        $offY = $startOffY + 3.3 + $diffY * $cNum;
        $i = 0;
        foreach ($object->getItemParams() as $param) {
            if ($i++ < $iNum) {
    ?>

    <text redraw="yes" offsetXPC="52" offsetYPC="<?=$offY?>" widthPC="10" heightPC="<?=$diffY?>"
        fontSize="15" foregroundColor="<?=$colors->get('lines')?>"
        backgroundColor="<?=$colors->get('bgPanel2')?>">
        <script>"<?=$param?>";</script>
    </text>
    <text redraw="yes" offsetXPC="62" offsetYPC="<?=$offY?>" widthPC="36" heightPC="<?=$diffY?>"
        fontSize="15" foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgPanel2')?>">
        <script>getItemInfo("<?=$param?>");</script>
    </text>
    <?
                $offY += $diffY;
            }
        }
    ?>



    <itemDisplay>
        <script>
        <?=calculateItemColors()?>
        </script>

        <text offsetXPC="7" offsetYPC="0" widthPC="10" heightPC="100">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <text align="right" offsetXPC="0" offsetYPC="0" widthPC="12" heightPC="100" fontSize="15" cornerRounding="10">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>getPageInfo("focusItemIndex") + ". ";</script>
        </text>
        <text offsetXPC="12" offsetYPC="0" widthPC="88" heightPC="100" fontSize="15" cornerRounding="10">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
