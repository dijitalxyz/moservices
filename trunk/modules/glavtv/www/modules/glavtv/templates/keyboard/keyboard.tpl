<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<? define('SW_LAYOUT',  'я-z') ?>
<? define('SW_KEYS',    '+=')  ?>
<? define('SW_HISTORY', '...') ?>
<? define('SW_OK',      'OK')  ?>
<? define('SW_SPACE',   '__')  ?>
<? define('SW_BACK',    ' X ') ?>

<onEnter>
    rows = 7;
    cols = 8;

    layoutCols  = 5;
    specialRows = 5;

    readHstUrl  = "<?=$cfg->get('home_url')?>?req=history";
    writeHstUrl = "<?=$cfg->get('home_url')?>?req=addhistory&amp;str=";
    showHstUrl  = "<?=$cfg->get('home_url')?>?req=showhistory";

    layoutFiles = null;
<?
    $layoutIdx = 0;
    $keyLang   = $cfg->get('keyLang', 'de');
    $path      = $cfg->get('resource_url') . 'keyboard/';
    $scanPath  = $cfg->get('resource_disk') . 'keyboard/';
    $i = 0;
    foreach (scandir($scanPath) as $entry) {
        if (0 === strpos($entry, 'layout_')) {
            print '    layoutFiles  = pushBackStringArray(layoutFiles, "';
            print $entry . '");' . PHP_EOL;
            $locale = substr($entry, strlen('layout_'));
            $locale = substr($locale, 0, strpos($locale, '.'));
            if (strtolower($locale) === $keyLang) {
                $layoutIdx = $i;
            }
            $i++;
        }
    }


    $platform = $cfg->get('platform');
    # $platform = 'realtek';
    $ipath = $cfg->get('remote_images');

    $help = array(
        'display' => 'Layout',
        'pgup'    => 'Backspace',
        'stop'    => 'Clear',
        'pgdn'    => 'History',
        'play'    => 'Done',
        'return'  => 'Cancel');
?>

    <? restoreVariable('layoutIdx', 'keyLayoutIdx') ?>
    if (null == layoutIdx) {
        layoutIdx = <?=$layoutIdx?>;
    }

    layoutArray  = null;
    specialArray = null;
    specialIdx   = -1;

    inputArray   = null;
    inputText    = "";

    layoutIdx   -= 1;
    executeScript("switchLayout");
    executeScript("switchSpecialKeys");
    executeScript("loadHistory");
</onEnter>

<getArrayLength>
    length = 0;
    while (null != array &amp;&amp; null != getStringArrayAt(array, length)) {
        length = Add(length, 1);
    }
</getArrayLength>

<switchLayout>
    layoutIdx = Add(layoutIdx, 1);
    file = getStringArrayAt(layoutFiles, layoutIdx);
    if (null == file) {
        layoutIdx = 0;
        file = getStringArrayAt(layoutFiles, layoutIdx);
    }
    layoutArray = getURL("<?=$path?>" + file);
    <? storeVariable('layoutIdx', 'keyLayoutIdx') ?>
</switchLayout>

<switchSpecialKeys>
    fullArray = getURL("<?=$path?>special_keys.txt");

    pageSize = specialRows * (cols - layoutCols);
    specialIdx = Add(specialIdx, 1);
    if (null == getStringArrayAt(fullArray, pageSize * specialIdx)) {
        specialIdx = 0;
    }

    specialArray = null;
    idx = 0;
    while (idx &lt; pageSize) {
        value = getStringArrayAt(fullArray, Add(idx, pageSize * specialIdx));
        if (null == value) {
            value = "";
        }
        specialArray = pushBackStringArray(specialArray, value);
        idx = Add(idx, 1);
    }

    specialArray = pushBackStringArray(specialArray, "<?=SW_LAYOUT?>");
    specialArray = pushBackStringArray(specialArray, "<?=SW_KEYS?>");
    specialArray = pushBackStringArray(specialArray, "<?=SW_HISTORY?>");
    specialArray = pushBackStringArray(specialArray, "<?=SW_OK?>");
    specialArray = pushBackStringArray(specialArray, "<?=SW_SPACE?>");
    specialArray = pushBackStringArray(specialArray, "<?=SW_BACK?>");
</switchSpecialKeys>

<backspace>
    array = inputArray;
    executeScript("getArrayLength");
    if (length &gt; 0 &amp;&amp; inputArray != null) {
        inputArray = deleteStringArrayAt(inputArray, length - 1);
        inputText = "";
        while (length &gt; 0) {
            length -= 1;
            inputText = getStringArrayAt(inputArray, length) + inputText;
        }
    }
</backspace>

<clearAll>
    inputText = "";
    inputArray = null;
</clearAll>

<onComplete>
    executeScript("saveHistory");
    setReturnString(inputText);
    postMessage("<?=$keys['return']?>");
</onComplete>

<loadHistory>
<? if (! isset($_GET['nohist'])) { ?>
    inputArray = getURL(readHstUrl + "&amp;length=1");

    /* refresh inputText via backspace */
    inputArray = pushBackStringArray(inputArray, "X");
    executeScript("backspace");
<? } ?>
</loadHistory>

<showHistory>
    ret = doModalRss(showHstUrl);
    if (null != ret &amp;&amp; "" != ret) {
        inputArray = ret;

        /* refresh inputText via backspace */
        inputArray = pushBackStringArray(inputArray, "X");
        executeScript("backspace");
    }
    redrawDisplay();
</showHistory>

<saveHistory>
<? if (! isset($_GET['nohist'])) { ?>
    getURL(writeHstUrl + urlEncode(inputText));
<? } ?>
</saveHistory>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(array('slidingItemText' => 'no', 'sliding' => 'no')) ?>
<? showViewAreaParams(25, 7, 50, 86) ?>
<? showIdleImageParams() ?>

    rowCount="7"
    columnCount="8"

    itemOffsetXPC="3.8"
    itemOffsetYPC="13"
    itemWidthPC="9.3"
    itemHeightPC="7.8"

    itemGapXPC="2.2"
    itemGapYPC="2.3"
    >
<? addIdleImages() ?>

    <onUserInput>
        key = currentUserInput();
        res = "true";

        idx = getFocusItemIndex();
        row = idx % rows;
        col = idx / rows;

        if (key == "<?=$keys['display']?>") {
            executeScript("switchLayout");
        } else if (key == "<?=$keys['pgdn']?>") {
            executeScript("showHistory");
        } else if (key == "<?=$keys['pgup']?>") {
            executeScript("backspace");
        } else if (key == "<?=$keys['stop']?>") {
            executeScript("clearAll");
        } else if (key == "<?=$keys['play']?>") {
            executeScript("onComplete");
        } else if (key == "<?=$keys['up']?>" &amp;&amp; Add(row, 0) == 0) {
            setFocusItemIndex(Add(idx, rows) - 1);
        } else if (key == "<?=$keys['down']?>" &amp;&amp; Add(row, 1) == rows) {
            setFocusItemIndex(Add(idx, 1) - rows);
        } else if (key == "<?=$keys['left']?>" &amp;&amp; Add(col, 0) == 0) {
            setFocusItemIndex(Add(idx, (cols - 1) * rows));
        } else if (key == "<?=$keys['right']?>" &amp;&amp; Add(col, 1) == cols) {
            setFocusItemIndex(idx - (cols - 1) * rows);
        } else {
            res = "false";
        }

        if (res == "true") {
            redrawDisplay();
        }
        res;
    </onUserInput>

    <!-- Help area definition -->
    <text offsetXPC="0"    offsetYPC="86" widthPC="100" heightPC="14" backgroundColor="<?=$colors->get('bgKeyHelp')?>" />
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

<? addBoundingFrame(0.5, 0.5, $colors->get('keyLines')) ?>

    <text offsetXPC="4.3" offsetYPC="3.8" widthPC="91.4" heightPC="8.4" backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <text redraw="yes" offsetXPC="4.5" offsetYPC="4" widthPC="91" heightPC="8" fontSize="18" backgroundColor="<?=$colors->get('bgPanel1')?>" foregroundColor="<?=$colors->get('fgNormal')?>">
        <script>inputText + "_";</script>
    </text>

    <itemDisplay>
        <text align="center" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" fontSize="20" backgroundColor="<?=$colors->get('bgPanel1')?>" foregroundColor="<?=$colors->get('fgNormal')?>" cornerRounding="5">
            <script>
                if (getFocusItemIndex() == getQueryItemIndex()) {
                    itemBg = "<?=$colors->get('bgFocus')?>";
                    itemFg = "<?=$colors->get('fgFocus')?>";
                } else {
                    itemBg = getItemInfo("bg");
                    itemFg = "<?=$colors->get('fgNormal')?>";
                }
                getItemInfo("title");
            </script>
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
        </text>
    </itemDisplay>
</mediaDisplay>

<item_template>
    <title>
        <script>
            idx = getQueryItemIndex();
            row = idx % rows;
            col = idx / rows;

            if (col &gt;= layoutCols) {
                array = specialArray;
                localCols = cols - layoutCols;
                col -= layoutCols;
                if (row &gt;= specialRows) {
                    bg = "<?=$colors->get('bgKeyControl')?>";
                } else {
                    bg = "<?=$colors->get('bgKeySpecial')?>";
                }
            } else {
                array = layoutArray;
                localCols = layoutCols;
                bg = "<?=$colors->get('bgKeyNormal')?>";
            }
            idx = Add(row * localCols, col);
            getStringArrayAt(array, idx);
        </script>
    </title>
    <bg><script>bg;</script></bg>

    <onClick>
        value = getItemInfo("title");
        if (value == "<?=SW_SPACE?>" || value == null) {
            value = " ";
        }

        if (value == "<?=SW_LAYOUT?>") {
            executeScript("switchLayout");
        } else if (value == "<?=SW_KEYS?>") {
            executeScript("switchSpecialKeys");
        } else if (value == "<?=SW_HISTORY?>") {
            executeScript("showHistory");
        } else if (value == "<?=SW_OK?>") {
            executeScript("onComplete");
        } else if (value == "<?=SW_BACK?>") {
            executeScript("backspace");
        } else if (null != value &amp;&amp; "" != value) {
            inputText += value;
            inputArray = pushBackStringArray(inputArray, value);
        }
        null;
    </onClick>
</item_template>

<channel>
    <itemSize><script>7*8;</script></itemSize>
</channel>
</rss>
