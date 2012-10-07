<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams(array('backgroundColor' => $colors->get('bgKeySpecial'),
    'itemBackgroundColor' => $colors->get('bgKeySpecial'))) ?>
<? showViewAreaParams(27.15, 10.4, 45.78, 68.3) ?>
<? showIdleImageParams() ?>
<? showRegionParams('itemImage', 1, 14.5) ?>
<? showRegionParams('item', 1, 14.5, 98, 8.5) ?>

    itemPerPage="10"

    >
<? addIdleImages() ?>

    <onUserInput>
        res = "false";
        key = currentUserInput();
        if (key == "<?=$keys['enter']?>") {
            rHstUrl = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=history";
            rHstUrl += "&amp;offset=" + getFocusItemIndex() + "&amp;length=1";
            inputArray = getURL(rHstUrl);

            setReturnString(inputArray);
            postMessage("<?=$keys['return']?>");
            res = "true";
        }
        res;
    </onUserInput>

    <text offsetXPC="0"    offsetYPC="0"    widthPC="100" heightPC="10"  backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <text offsetXPC="0.5"  offsetYPC="0.5" widthPC="99" heightPC="9"
        redraw="yes" fontSize="18" backgroundColor="<?=$colors->get('bgKeySpecial')?>">
        <script>getItemInfo("<?=Item::TITLE?>");</script>
    </text>

    <text offsetXPC="0"    offsetYPC="10"   widthPC="100" heightPC="4"   backgroundColor="<?=$colors->get('bgNormal')?>" />

    <text offsetXPC="0"    offsetYPC="14"   widthPC="100" heightPC="0.5" backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <text offsetXPC="0"    offsetYPC="14"   widthPC="0.5" heightPC="86"  backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <text offsetXPC="99.5" offsetYPC="14"   widthPC="0.5" heightPC="86"  backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <text offsetXPC="0"    offsetYPC="99.5" widthPC="100" heightPC="0.5" backgroundColor="<?=$colors->get('bgPanel2')?>" />

    <itemDisplay>
        <script>
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                infoFg = itemFg;
            } else {
                itemBg = "<?=$colors->get('bgKeySpecial')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
                infoFg = "<?=$colors->get('fgInfo')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
            <backgroundColor><script>itemBg;</script></backgroundColor>
        </text>

        <text offsetXPC="0" offsetYPC="0" widthPC="70" heightPC="100" fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
        <text align="right" offsetXPC="70" offsetYPC="0" widthPC="28" heightPC="100" fontSize="13">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::DESCRIPTION?>") + "  ";</script>
        </text>
    </itemDisplay>
</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
