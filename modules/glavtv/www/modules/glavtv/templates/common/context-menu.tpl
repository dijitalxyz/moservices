<?
/****** Tester ********************
    $items = $object->getItems();
    $item = $items[0];
    $items = array();
    for ($i = 0; $i < 10; $i++) {
        $items[] = $item;
    }
    $object->setItems($items);
***********************************/

    $allParams  = $object->getItemParams();
    $showDescr  = in_array(Item::DESCRIPTION, $allParams);
    $titleDiff  = null == $object->get(Channel::TITLE) ? 0 : 1;

    $rows = min(10, count($object->getItems()) + $titleDiff);

    $height      = $rows * 6 + 2;
    $top         = 48 - $height / 2;

    $offY        = 1.0 * 100 / $height;
    $frameHeight = 0.3 * 100 / $height;
    $rowHeight   = (100 - $offY * 2) / $rows - $frameHeight;

    $sepsHeight  = 50 * $frameHeight / $rowHeight;

?>
<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<onEnter>
    if (null == alreadyEntered) {
        alreadyEntered = 1;
    } else {
        setRefreshTime(200);
    }
</onEnter>

<onRefresh>
    postMessage("<?=$keys['return']?>");
</onRefresh>

<onExit>
    setRefreshTime(-1);
</onExit>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams(26, $top, 48, $height) ?>
<? showIdleImageParams() ?>

<? showRegionParams('itemImage', 1.5, $offY + $titleDiff * $rowHeight) ?>
<? showRegionParams('item', 1.5, $offY + $titleDiff * $rowHeight, 97, $rowHeight + $frameHeight) ?>

    itemPerPage="<?=$rows - $titleDiff?>"
    itemGap="0"
    >
<? addIdleImages() ?>

    <onUserInput>
        key = currentUserInput();
        res = "false";
        if (key == "<?=$keys['enter']?>") {
            FII    = getFocusItemIndex();
            link   = getItemInfo(FII, "link");
            encUrl = getItemInfo(FII, "playURL");
            if (null == link &amp;&amp; null != encUrl &amp;&amp; "" != encUrl) {
                url  = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=open";
                url += "&amp;url="   + urlEncode(encUrl);
                url += "&amp;title=" + urlEncode(getItemInfo(FII, "title"));
                jumpToLink("rssLink");
                redrawDisplay();
                res = "true";
            } else {
                setRefreshTime(3000);
            }
        }
        <? addPlayButtonReaction(); ?>

        res;
    </onUserInput>

<? if (0 != $titleDiff) { ?>
    <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="<?=$rowHeight?>"
        foregroundColor="<?=$colors->get('fgNormal')?>" align="left"
        backgroundColor="<?=$colors->get('bgContextHeader')?>" fontSize="16"
        ><?=$object->get(Channel::TITLE)?></text>
<? } ?>

<? addBoundingFrame(0.3, $frameHeight, $colors->get('contextLines')) ?>

    <itemDisplay>
        <script>
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                infoFg = itemFg;
            } else {
                itemBg = "<?=$colors->get('bgContextMenu')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
                infoFg = "<?=$colors->get('fgDark')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="100" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <text offsetXPC="0" offsetYPC="0" widthPC="<?=$showDescr ? 65 : 100?>" heightPC="100"
            fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
<? if ($showDescr) { ?>
        <text offsetXPC="64" offsetYPC="0" widthPC="33" heightPC="100"
            fontSize="15" align="right">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::DESCRIPTION?>");</script>
        </text>
<? } ?>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
