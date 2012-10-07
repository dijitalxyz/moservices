<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $frameHeight = 0.4;
    $rowHeight   = 10.2;
    $sepsHeight  = 50 * $frameHeight / $rowHeight;

    $platform = $cfg->get('platform');
    $ipath = $cfg->get('remote_images');

    $help = array(
        'enter'   => 'Toggle on/off',
        'frwd'    => 'Move up',
        'ffwd'    => 'Move down',
        'play'    => 'Save',
        'pgup'    => 'Make first',
        'pgdn'    => 'Make last');
?>

<onEnter>
    if (null == idArray) {
        executeScript("initArrays");
    }
</onEnter>

<initArrays>
<?
$ids    = array();
$titles = array();
$thumbs = array();
$states = array();
foreach ($object->getItems() as $item) {
    $ids[]    = $item->get(Item::ID);
    $titles[] = $item->get(Item::TITLE);
    $thumbs[] = $item->get(Item::THUMBNAIL);
    $states[] = $item->get(Item::CATEGORY);
}
?>
    itemSize   = <?=count($titles)?>;
    idArray    = "<?=implode('&#10;', $ids)?>";
    titleArray = "<?=implode('&#10;', $titles)?>";
    thumbArray = "<?=implode('&#10;', $thumbs)?>";
    stateArray = "<?=implode('&#10;', $states)?>";
    setFocusItemIndex(0);
</initArrays>

<toggleState>
    FII = getFocusItemIndex();
    idx = 0;
    newStateArray = null;
    while (idx &lt; itemSize) {
        value = getStringArrayAt(stateArray, idx);
        if (idx == FII) {
            if ("on" == value) {
                value = "off";
            } else if ("off" == value) {
                value = "on";
            }
        }
        newStateArray = pushBackStringArray(newStateArray, value);
        idx = Add(idx, 1);
    }
    stateArray = newStateArray;
</toggleState>

<moveItem>
    FII = getFocusItemIndex();
    srcId    = getStringArrayAt(idArray,    FII);
    srcTitle = getStringArrayAt(titleArray, FII);
    srcThumb = getStringArrayAt(thumbArray, FII);
    srcState = getStringArrayAt(stateArray, FII);
    if (dst &lt; 0) {
        dst = 0;
    }
    if (dst &gt; FII) {
        dst = Add(dst, 1);
    }

    idx = 0;
    newIdArray    = null;
    newTitleArray = null;
    newThumbArray = null;
    newStateArray = null;
    while (idx &lt; itemSize) {
        id    = getStringArrayAt(idArray,    idx);
        title = getStringArrayAt(titleArray, idx);
        thumb = getStringArrayAt(thumbArray, idx);
        state = getStringArrayAt(stateArray, idx);

        if (idx == dst) {
            newIdArray    = pushBackStringArray(newIdArray,    srcId);
            newTitleArray = pushBackStringArray(newTitleArray, srcTitle);
            newThumbArray = pushBackStringArray(newThumbArray, srcThumb);
            newStateArray = pushBackStringArray(newStateArray, srcState);
        }
        if (idx != FII) {
            newIdArray    = pushBackStringArray(newIdArray,    id);
            newTitleArray = pushBackStringArray(newTitleArray, title);
            newThumbArray = pushBackStringArray(newThumbArray, thumb);
            newStateArray = pushBackStringArray(newStateArray, state);
        }
        idx = Add(idx, 1);
    }

    if (dst &gt;= itemSize) {
        dst = itemSize;
        newIdArray    = pushBackStringArray(newIdArray,    srcId);
        newTitleArray = pushBackStringArray(newTitleArray, srcTitle);
        newThumbArray = pushBackStringArray(newThumbArray, srcThumb);
        newStateArray = pushBackStringArray(newStateArray, srcState);
    }

    if (dst &gt; FII) {
        dst = dst - 1;
    }

    idArray    = newIdArray;
    titleArray = newTitleArray;
    thumbArray = newThumbArray;
    stateArray = newStateArray;
    setFocusItemIndex(dst);
</moveItem>

<saveIds>
    order = "";
    disabled = "";
    idx = 0;
    while (idx &lt; itemSize) {
        id = getStringArrayAt(idArray, idx);
        if ("off" == getStringArrayAt(stateArray, idx)) {
            if ("" != disabled) {
                disabled += ",";
            }
            disabled += id;
        }
        if ("" != order) {
           order += ",";
        }
        order += id;
        idx = Add(idx, 1);
    }

    url  = "<?=$cfg->get('service_url')?>&amp;req=saveIniParams&amp;msg=1";
    url += "&amp;fwd=<?=urlencode($this->cfg->get('home_url') . '?req=services')?>";
    url += "&amp;" + urlEncode("params[serviceOrder]") + "=" + order;
    url += "&amp;" + urlEncode("params[serviceDisabled]") + "=" + disabled;
</saveIds>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams(26, 7, 48, 86) ?>
<? showIdleImageParams() ?>

<? showRegionParams('itemImage', 1.5, 1.35) ?>
<? showRegionParams('item', 1.5, 1.35, 97, $rowHeight + $frameHeight) ?>

    itemPerPage="8"
    itemGap="0"
    >
<? addIdleImages() ?>

    <onUserInput>
        res = "true";
        url = null;
        key = currentUserInput();

        if (key == "<?=$keys['enter']?>") {
            executeScript("toggleState");
        } else if (key == "<?=$keys['play']?>") {
            executeScript("saveIds");
        } else if (key == "<?=$keys['pgup']?>") {
            dst = 0;
            executeScript("moveItem");
        } else if (key == "<?=$keys['pgdn']?>") {
            dst = itemSize;
            executeScript("moveItem");
        } else if (key == "<?=$keys['frwd']?>") {
            dst = getFocusItemIndex() - 1;
            executeScript("moveItem");
        } else if (key == "<?=$keys['ffwd']?>") {
            dst = Add(getFocusItemIndex(), 1);
            executeScript("moveItem");
        } else if (key == "<?=$keys['left']?>") {
            setFocusItemIndex(0);
        } else if (key == "<?=$keys['right']?>") {
            setFocusItemIndex(itemSize - 1);
        } else {
            res = "false";
        }

        if (null != url &amp;&amp; "" != url) {
            jumpToLink("rssLink");
            res = "true";
        }
        if ("true" == res) {
            redrawDisplay();
        }
        res;
    </onUserInput>


    <!-- Help area definition -->
    <text offsetXPC="0"    offsetYPC="86" widthPC="100" heightPC="14" backgroundColor="<?=$colors->get('bgContextHeader')?>" />
<?
    $cols = 3;
    $x = 4;
    $y = 86.7;
    $w = (100.0 - $x) / $cols;
    $h = 5;
    $extraH = 2;
    $imgW = 7;
    $keyW = $w - 6;

    $col = 0;
    foreach ($help as $key => $title) {
        $title = $lang->msg($title);
        $key = $ipath . $key . '.png';
?>
    <image offsetXPC="<?=$x + $col * $w?>" offsetYPC="<?=$y?>" widthPC="<?=$imgW?>" heightPC="<?=$h?>">
        <script>"<?=$key?>";</script>
    </image>
    <text offsetXPC="<?=$x + $col * $w + $imgW + 1?>" offsetYPC="<?=$y?>" widthPC="<?=$keyW?>" heightPC="<?=$h?>"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        fontSize="13">
        <script>"<?=$title?>";</script>
    </text>
<?
        $col++;
        if ($col == $cols) {
            $col = 0;
            $y += $h + $extraH;
        }
    }
?>

<? addBoundingFrame(0.5, 0.5, $colors->get('contextLines')) ?>

    <itemDisplay>
        <script>
            title = getItemInfo("<?=Item::TITLE?>");
            thumb = getItemInfo("<?=Item::THUMBNAIL?>");
            state = getItemInfo("<?=Item::CATEGORY?>");

            if ("off" == state) {
                overlay = "<?=$cfg->get('resource_disk')?>img/off.png";
                state   = "<?=$lang->msg('Switched off')?>";
                itemFg  = "<?=$colors->get('fgDark')?>";
            } else {
                overlay = "";
                state   = "";
                itemFg  = "<?=$colors->get('fgNormal')?>";
            }

            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg  = "<?=$colors->get('bgFocus')?>";
                itemFg  = "<?=$colors->get('fgFocus')?>";
                infoFg  = itemFg;
                numBg   = itemBg;
            } else {
                itemBg  = "<?=$colors->get('bgContextMenu')?>";
                infoFg  = "<?=$colors->get('fgDark')?>";
                numBg   = "<?=$colors->get('bgPanel2')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="100" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="8" heightPC="<?=100 - $sepsHeight?>"
            fontSize="16" align="center">
            <backgroundColor><script>numBg;</script></backgroundColor>
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>" " + getPageInfo("focusItemIndex") + ".";</script>
        </text>
        <image offsetXPC="10" offsetYPC="10" widthPC="11" heightPC="80">
            <script>thumb;</script>
        </image>
        <image offsetXPC="10" offsetYPC="10" widthPC="11" heightPC="80">
            <script>overlay;</script>
        </image>
        <text offsetXPC="22" offsetYPC="0" widthPC="45" heightPC="100"
            fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>title;</script>
        </text>
        <text offsetXPC="85" offsetYPC="0" widthPC="12" heightPC="100"
            fontSize="15" align="right">
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>state;</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<item_template>
    <title><script>getStringArrayAt(titleArray, -1);</script></title>
    <media:thumbnail><script>getStringArrayAt(thumbArray, -1);</script></media:thumbnail>
    <category><script>getStringArrayAt(stateArray, -1);</script></category>
</item_template>

<channel>
    <itemSize><script>itemSize;</script></itemSize>
</channel>

</rss>
