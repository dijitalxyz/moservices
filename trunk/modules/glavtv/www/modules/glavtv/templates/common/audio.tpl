<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<onEnter>
    SwitchViewer(0);
    executeScript("playbackStop");
    executeScript("loadBackgrounds");
    fullScreen = 0;
    setRefreshTime(5000);
</onEnter>

<onRefresh>
    bgIndex = Add(bgIndex, 1) % bgMax;
</onRefresh>

<onExit>
    setRefreshTime(-1);
/*  executeScript("playbackStop"); */
</onExit>

<playbackStart>
    executeScript("playbackStop");
    FII = getFocusItemindex();
    streamUrl = getItemInfo(FII, "link");
    streamUrl = getURL(streamUrl);
    if (null != streamUrl &amp;&amp; "" != streamUrl) {
        playingIndex = FII;
        playingTitle = getItemInfo(FII, "title");
        playingThumb = getItemInfo(FII, "thumbnail");
        playItemURL(streamUrl, 5);
    }
</playbackStart>

<playbackStop>
    playingIndex = -1;
    playingTitle = "";
    playingThumb = "";
    playItemURL(-1, 1);
</playbackStop>

<loadBackgrounds>
    url = "<?=$cfg->get('home_url') . '?req=backgrounds'?>";
    loadOk  = loadXMLFile(url);
    bgIndex = 0;
    bgMax   = 1;
    bgArray = null;
    if (null != loadOk) {
        index = 0;
        bgMax = getXMLElementCount("rss", "channel", "item");
        if (bgMax == 0) {
            bgMax = 1;
        } else {
            while (index &lt; bgMax) {
                img = getXMLText("rss", "channel", "item", index, "title");
                bgArray = pushBackStringArray(bgArray, img);
                index = Add(index, 1);
            }
        }
    }
    redrawDisplay();
</loadBackgrounds>



<mediaDisplay name="photoView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    rowCount="3"
    columnCount="11"

    itemOffsetXPC="3.5"
    itemOffsetYPC="57"
    itemWidthPC="8.5"
    itemHeightPC="12.5"

    itemGapXPC="0"
    itemGapYPC="0"
    >
<? addIdleImages() ?>
<? $object->addHotkey('user1', $lang->msg('Full screen'), null) ?>
<? addDefaultButtons($object) ?>

    <onUserInput>
        res = "true";
        url = null;
        key = currentUserInput();

        if (fullScreen != 0) {
            fullScreen = 0;
        }
        else if (key == "<?=$keys['user1']?>") {
            fullScreen = 1;
        }
        else if (key == "<?=$keys['enter']?>") {
            executeScript("playbackStart");
        }
        else if (key == "<?=$keys['stop']?>") {
            executeScript("playbackStop");
        }
<? addHotkeyReaction($object); ?>
        else {
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

<? $keysAdded = addHotkeysLine($object) ?>

    <!-- Custom header -->
<?
    global $version, $projectName;
    $caption = $object->get(Channel::TITLE);
    $service = $object->get(Channel::SERVICE);
?>
    <text offsetXPC="0" offsetYPC="9" widthPC="100" heightPC="0.5" backgroundColor="<?=$colors->get('lines')?>" />

    <text offsetXPC="3"    offsetYPC="3" widthPC="40" heightPC="4" fontSize="14" foregroundColor="<?=$colors->get('fgInfo')?>"><?=$caption?></text>
    <text offsetXPC="43"   offsetYPC="3" widthPC="10" heightPC="4" fontSize="14" foregroundColor="<?=$colors->get('fgInfo')?>"><?=$projectName?></text>
    <text offsetXPC="56.5" offsetYPC="3" widthPC="28" heightPC="4" fontSize="14" foregroundColor="<?=$colors->get('fgFocus')?>"><?=$service?></text>
    <text offsetXPC="52"   offsetYPC="3" widthPC="7"  heightPC="4" fontSize="14" foregroundColor="<?=$colors->get('version')?>"><?=$version?></text>
    <text offsetXPC="84.5" offsetYPC="3" widthPC="13" heightPC="4" fontSize="14" foregroundColor="<?=$colors->get('fgInfo')?>"  backgroundColor="<?=$colors->get('bgHeader')?>" redraw="yes">
        <script>
            executeScript("getTimeStr");
            timeStr;
        </script>
        <offsetXPC><script>if (fullScreen == 0) 84.5; else -90;</script></offsetXPC>
    </text>

    <!-- detailed info -->
    <text offsetXPC="1.6"  offsetYPC="10.9" widthPC="96.8" heightPC="44.5" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="1.8"  offsetYPC="11.1" widthPC="96.4" heightPC="44.1" backgroundColor="<?=$colors->get('bgPanel1')?>" />

    <text  offsetXPC="3.3" offsetYPC="12.8" widthPC="25.4" heightPC="40.4" backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <image offsetXPC="3.5" offsetYPC="13"   widthPC="25"   heightPC="40"   redraw="yes" >
        <script>
            if (playingIndex == -1) {
                getItemInfo(-1, "<?=Item::THUMBNAIL?>");
            } else {
                playingThumb;
            }
        </script>
        <offsetXPC><script>if (fullScreen == 0) 3.5; else -90;</script></offsetXPC>
    </image>

    <text offsetXPC="45" offsetYPC="21" widthPC="10" heightPC="16"
        redraw="yes" backgroundColor="<?=$colors->get('bgPanel1')?>">
        <script>"";</script>
        <offsetXPC><script>if (fullScreen == 0) 45; else -90;</script></offsetXPC>
    </text>
    <image offsetXPC="45" offsetYPC="21" widthPC="10" heightPC="16" redraw="yes">
        <script>
            if (playingIndex == -1) {
                "<?=$cfg->get('resource_disk') . 'img/audio-play.png'?>";
            } else {
                "<?=$cfg->get('resource_disk') . 'img/audio-stop.png'?>";
            }
        </script>
        <offsetXPC><script>if (fullScreen == 0) 45; else -90;</script></offsetXPC>
    </image>
    <text  offsetXPC="30" offsetYPC="37.1" widthPC="40" heightPC="10"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        backgroundColor="<?=$colors->get('bgPanel1')?>"
        redraw="yes" align="center" fontSize="18">
        <script>
            if (playingIndex == -1) {
                getItemInfo(-1, "<?=Item::TITLE?>");
            } else {
                playingTitle;
            }
        </script>
        <offsetXPC><script>if (fullScreen == 0) 30; else -90;</script></offsetXPC>
    </text>

    <text  offsetXPC="71.3" offsetYPC="12.8" widthPC="25.4" heightPC="40.4" backgroundColor="<?=$colors->get('bgPanel2')?>" />
    <image offsetXPC="71.5" offsetYPC="13"   widthPC="25"   heightPC="40"   redraw="yes">
        <script>
            if (fullScreen == 0) {
                x = 71.5; y = 13; w = 25; h = 40;
            } else {
                x = 0; y = 0; w = 100; h = 100;
            }
            getStringArrayAt(bgArray, bgIndex);
        </script>
        <offsetXPC><script>x;</script></offsetXPC>
        <offsetYPC><script>y;</script></offsetYPC>
        <widthPC><script>w;</script></widthPC>
        <heightPC><script>h;</script></heightPC>
    </image>

    <itemDisplay>
        <script>
            FII = getFocusItemIndex();
            QII = getQueryItemIndex();
            itemBg  = "<?=$colors->get('bgNormal')?>";
            itemFg  = "<?=$colors->get('fgDark')?>";
            if (QII == playingIndex) {
                itemBg  = "<?=$colors->get('bgFocusInactive')?>";
                itemFg  = "<?=$colors->get('fgFocus')?>";
            }
            if (QII == FII) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
            }
        </script>

        <text  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="10">
            <backgroundColor><script>itemBg;</script></backgroundColor>
        </text>
        <text  offsetXPC="5" offsetYPC="5" widthPC="90"  heightPC="90"  backgroundColor="<?=$colors->get('bgPanel2')?>" />
        <image offsetXPC="6" offsetYPC="6" widthPC="88"  heightPC="88">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
