<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $allParams  = $object->getItemParams();
    $showCats   = in_array(Item::CATEGORY, $allParams);
    $sepsHeight = 0.4;
?>

<onEnter>
    if (null == firstStart) {
        firstStart = 1;
    } else {
        executeScript("dataReload");
    }
    setRefreshTime(<?=$cfg->get('channelsRefreshInterval', 5 * 60) * 1000?>);
</onEnter>
<onRefresh>
    executeScript("dataReload");
</onRefresh>
<onExit>
    setRefreshTime(-1);
</onExit>
<dataReload>
    dataArray = null;
    url = "<?=str_replace('&', '&amp;', $cfg->get('self_url') . '&tpl=tv-channels-data')?>";
    if (null != loadXMLFile(url)) {
        index = 0;
        lastIndex = <?=count($object->getItems())?>;
        while (index &lt; lastIndex) {
            dataArray = pushBackStringArray(dataArray, getXMLText("data", "c", index, "i"));
            dataArray = pushBackStringArray(dataArray, getXMLText("data", "c", index, "t"));
            dataArray = pushBackStringArray(dataArray, getXMLText("data", "c", index, "p"));
            dataArray = pushBackStringArray(dataArray, getXMLText("data", "c", index, "d"));
            index = Add(index, 1);
        }
    }
    redrawDisplay();
</dataReload>

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

    <text redraw="yes" align="center" fontSize="15"
        offsetXPC="1" offsetYPC="<?=$showCats ? 87 : 91.3?>" widthPC="24" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            if (null != currPercent &amp;&amp; "" != currPercent) {
                currTime + "   (" + currPercent + "%)";
            } else {
                currTime;
            }
        </script>
    </text>

<?  if ($showCats) {?>
    <text redraw="yes" align="center" fontSize="15"
        offsetXPC="1" offsetYPC="91.3" widthPC="24" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>getItemInfo(-1, "<?=Item::CATEGORY?>");</script>
    </text>
<?  } ?>

    <text redraw="yes" lines="<?=$keysAdded ? 2 : 3?>" fontSize="15"
        offsetXPC="25" offsetYPC="86.8" widthPC="75" heightPC="<?=$keysAdded ? 9.2 : 12.9?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>currDescr;</script>
    </text>

    <itemDisplay>
        <script>
            FII = getFocusItemIndex();
            QII = getQueryItemIndex();
            if (FII == QII) {
                progBg = "<?=$colors->get('bgProgFocus')?>";
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                infoFg = itemFg;
            } else {
                progBg = "<?=$colors->get('bgPanel1')?>";
                itemBg = "<?=$colors->get('bgNormal')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
                infoFg = "<?=$colors->get('fgDark')?>";
            }

            if (null == dataArray) {
                info    = getItemInfo("<?=Item::INFO?>");
                time    = getItemInfo("<?=Item::TIME?>");
                percent = getItemInfo("percent");
                if (FII == QII) {
                    currTime    = time;
                    currPercent = percent;
                    currDescr   = getItemInfo("<?=Item::DESCRIPTION?>");
                }
            } else {
                offset  = QII * 4;
                info    = getStringArrayAt(dataArray, offset);
                time    = getStringArrayAt(dataArray, Add(offset, 1));
                percent = getStringArrayAt(dataArray, Add(offset, 2));
                if (FII == QII) {
                    currTime    = time;
                    currPercent = percent;
                    currDescr   = getStringArrayAt(dataArray, Add(offset, 3));
                }
            }
        </script>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="100" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <image offsetXPC="3" offsetYPC="5" widthPC="3.5" heightPC="90">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
        <text offsetXPC="7" offsetYPC="0" widthPC="19" heightPC="100" fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
        <text offsetXPC="26.5" offsetYPC="<?=$sepsHeight?>" widthPC="57" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>progBg;</script></backgroundColor>
            <widthPC><script>percent * 57 / 100;</script></widthPC>
        </text>
        <text offsetXPC="26.5" offsetYPC="0" widthPC="57" heightPC="100" fontSize="15">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>info;</script>
        </text>
        <text offsetXPC="84" offsetYPC="0" widthPC="13" heightPC="100" fontSize="15" align="right">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>time;</script>
        </text>
    </itemDisplay>
</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
