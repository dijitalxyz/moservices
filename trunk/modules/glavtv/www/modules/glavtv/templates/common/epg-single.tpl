<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>
<? addSelectedIndexSupport($object) ?>

<? $hasArc = $object->get('arc') ?>
<? $sepsHeight = 0.4 ?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams(array('slidingItemText' => 'no', 'sliding' => 'no')) ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

<? showRegionParams('itemImage', 0, 9.8, 0, 0) ?>
<? showRegionParams('item', 0, 9.8, 100, 5.9) ?>

    itemPerPage="13"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <image offsetXPC="1.5" offsetYPC="88.5" widthPC="4" heightPC="6" redraw="<?=$hasArc ? 'yes' : 'no'?>">
        <script>
<? if (! $hasArc) { ?>
            "<?=$this->cfg->get('resource_disk')?>img/indicator-gray.png";
<? } else { ?>
            if (1 == getItemInfo(-1, "novideo")) {
                "<?=$this->cfg->get('resource_disk')?>img/indicator-red.png";
            } else {
                "<?=$this->cfg->get('resource_disk')?>img/indicator-green.png";
            }
<? }?>
        </script>
    </image>

    <text redraw="yes" align="left" fontSize="20"
        offsetXPC="<?=$hasArc ? 6 : 6?>" offsetYPC="88" widthPC="8.5" heightPC="6.9"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>getItemInfo(-1, "<?=Item::TIME?>");</script>
    </text>

    <text redraw="yes" lines="<?=$keysAdded ? 2 : 3?>" fontSize="15"
        offsetXPC="15" offsetYPC="87" widthPC="85" heightPC="<?=$keysAdded ? 8.6 : 12.5?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>getItemInfo(-1, "<?=Item::TITLE?>") + " " + getItemInfo(-1, "<?=Item::DESCRIPTION?>");</script>
    </text>

    <itemDisplay>
        <script>
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                infoFg = itemFg;
            } else {
                itemFg = "<?=$colors->get('fgNormal')?>";
                infoFg = "<?=$colors->get('fgInfo')?>";

                timeClass = getItemInfo("<?=Item::CATEGORY?>");
                if (timeClass == "current") {
                    infoFg = "<?=$colors->get('fgNormal')?>";
                    itemBg = "<?=$colors->get('bgEpgCurrent')?>";
                } else if (timeClass == "future") {
                    itemBg = "<?=$colors->get('bgEpgFuture')?>";
                } else {
                    itemBg = "<?=$colors->get('bgEpgPast')?>";
                }
            }
        </script>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="100" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>

        <text offsetXPC="0" offsetYPC="5" widthPC="9" heightPC="95" fontSize="16" align="right">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TIME?>");</script>
        </text>
        <image offsetXPC="12.5" offsetYPC="12" widthPC="2.5" heightPC="70">
            <script>
                if (null != getItemInfo("<?=Item::DESCRIPTION?>")) {
                    "<?=$this->cfg->get('resource_disk') . 'img/epginfo.png'?>";
                } else {
                    "";
                }
            </script>
        </image>
        <text offsetXPC="15" offsetYPC="5" widthPC="85" heightPC="95" fontSize="15">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>") + " " + getItemInfo("<?=Item::DESCRIPTION?>");</script>
        </text>
    </itemDisplay>
</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
