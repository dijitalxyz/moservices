<?
global $cfg, $lang, $srv, $req, $keys;

// finds matching width for given channels thumbs ratio
function checkThumbsRatio($channel, $fullWidth = 100, $hWidth = 70, $sWidth = 50, $vWidth = 35) {
    $ratio = $channel->get(Channel::THUMB_RATIO, 1);
    if (1 == $ratio) {
        $imgWidth = $hWidth;
    } else if (2 == $ratio) {
        $imgWidth = $vWidth;
    } else {
        $imgWidth = $sWidth;
    }
    $imgOffset = ($fullWidth - $imgWidth) / 2;
    return array($imgWidth, $imgOffset);
}

// Show 4 region bounding params
function showRegionParams($regionName = 'header', $x = 0, $y = 0, $w = 0, $h = 0) {
    print '    ' . $regionName . 'XPC="'      . $x . '"' . PHP_EOL;
    print '    ' . $regionName . 'YPC="'      . $y . '"' . PHP_EOL;
    print '    ' . $regionName . 'WidthPC="'  . $w . '"' . PHP_EOL;
    print '    ' . $regionName . 'HeightPC="' . $h . '"' . PHP_EOL . PHP_EOL;
}

// View area attributes
function showViewAreaParams($x = 0, $y = 0, $w = 100, $h = 100) {
    global $cfg;
    $mx = $cfg->get('marginX', 0);
    $my = $cfg->get('marginY', 0);
    if (1 == $cfg->get('marginUseAlways', 0)) {
        showRegionParams('viewArea', ($x + $mx), ($y + $my), ($w - (2 * $mx)), ($h - (2 * $my)));
    } else {
        showRegionParams('viewArea', max($x, $mx), max($y, $my), min($w, 100 - (2 * $mx)), min($h, 100 - (2 * $my)));
    }
}

// Common mediaDisplay parameters
function showMediaDisplayParams($modArray = null, $defBgColor = null) {
    global $cfg, $colors;
    $defBgColor = empty($defBgColor) ? $colors->get('bgNormal') : $defBgColor;
    $defaults = array(
        'showHeader'          => 'no',
        'showDefaultInfo'     => 'no',
        'imageFocus'          => 'null',
        'imageParentFocus'    => 'null',
      # 'imageBorderPC'       => '0',
        'backgroundColor'     => $defBgColor,
        'mainPartColor'       => $defBgColor,
        'itemBackgroundColor' => $defBgColor,
        'itemBorderColor'     => $defBgColor,
        'sideTopHeightPC'     => '0',
        'sideBottomHeightPC'  => '0',
        'sideLeftWidthPC'     => '0',
        'sideRightWidthPC'    => '0',
        'sideColorTop'        => $defBgColor,
        'sideColorBottom'     => $defBgColor,
        'sideColorLeft'       => $defBgColor,
        'sideColorRight'      => $defBgColor,
        'drawItemText'        => 'no',
        'slidingItemText'     => 1 == $cfg->get('sliding', 0) ? 'yes' : 'no',
        'sliding'             => 1 == $cfg->get('sliding', 0) ? 'yes' : 'no',
    );
    $defaults = (isset($modArray) ? $modArray : array()) + $defaults;
    foreach ($defaults as $name => $value) {
        print "    $name=\"$value\"" . PHP_EOL;
    }
}

function storeVariable($var, $storeId) {
    global $cfg;
    if ($cfg->get('nativeEnv', 0) == 0) {
        print "writeStringToFile(\"/tmp/env_$storeId\", $var);\n";
    } else {
        print "setEnv(\"$storeId\", $var);\n";
    }
}

function restoreVariable($var, $storeId) {
    global $cfg;
    if ($cfg->get('nativeEnv', 0) == 0) {
        print "$var = readStringFromFile(\"/tmp/env_$storeId\");\n";
    } else {
        print "$var = getEnv(\"$storeId\");\n";
    }
}

// Link used by the jumpToLink command (must be outside of the mediaDisplay item)
function addRssLink() {
    print '<rssLink><link><script>url;</script></link></rssLink>' . PHP_EOL;
}


function addCommonInit() {
    global $cfg, $lang, $keys;
    print '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
    print '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . PHP_EOL . PHP_EOL;

    $pfDir  = $cfg->get('resource_disk') . 'platform/';
    $plCfg  = new ConfigFile($pfDir . $cfg->get('platform') . '.ini');

    $layout = $cfg->getFromSection('platform', 'layout', $plCfg->get('layout'));
    $images = $cfg->getFromSection('platform', 'images', $plCfg->get('images', $layout));
    $native = $cfg->getFromSection('platform', 'native', $plCfg->get('native')) == 1;

    $keys = parse_ini_file($pfDir . "remotes/layout-$layout.ini");
    $cfg->set('remote_images', $pfDir . "remotes/$images/");
    require_once $pfDir . '/time/time-' . ($native ? 'native' : 'pseudo') . '.tpl';

    addRssLink();
    addDefaultOnEnter();
}

function addUrl($url = null, $itemParams = null) {
    $tabs = '            ';
    print $tabs . 'url = "' . $url . '";' . PHP_EOL;
    if (null != $itemParams && 0 != count($itemParams)) {
        print $tabs . 'FII = getFocusItemindex();' . PHP_EOL;
        foreach ($itemParams as $param => $name) {
            print $tabs . "url += \"&amp;$param=\" + urlEncode(getItemInfo(FII, \"$name\"));" . PHP_EOL;
        }
    }
}

function addKeyReaction($key, $url, $itemParams = null, $setReturnMark = false) {
?>
        else if (key == "<?=$key?>") {
<? if ($setReturnMark) { ?>
            SetBranch(0, 1);
<? } ?>
<? addUrl(str_replace('&', '&amp;', $url), $itemParams) ?>
        }
<?
}

function addInputReaction($key, $url, $itemParams = null) {
    global $cfg;
?>
        else if (key == "<?=$key?>") {
            showIdle();
            modalUrl = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=keyboard";
            ret = doModalRss(modalUrl);
            if (null != ret &amp;&amp; "" != ret) {
    <? addUrl(str_replace('&', '&amp;', $url), $itemParams) ?>
                url += "&amp;input=" + urlEncode(ret);
            }
        }
<?
}

function addPlayButtonReaction() {
    global $cfg, $keys;
?>
        else if (key == "<?=$keys['play']?>") {
            alternativeMode = <?=$cfg->get('playerMode', 1) == 0 ? 7 : 0?>;
            if (null != getItemInfo(getFocusItemindex(), "link")) {
                <? storeVariable('alternativeMode', 'viewerMode') ?>
            } else {
                SwitchViewer(alternativeMode);
            }
            postMessage("<?=$keys['enter']?>");
            res = "true";
        }
<?
}

function addDefaultButtons(&$channel) {
    global $cfg, $lang, $srv;
    if (null != $channel->getSearchUrl()) {
        $channel->addHotkey('user2', $lang->msg('Search'),
            'INPUT:' . $channel->getSearchUrl());
    }
    if ($srv != 'utils' && $srv != 'fav') {
        $channel->addHotkey('user3', $lang->msg('Possibilities'),
            $cfg->get('home_url') . '?' . http_build_query(array(
            'srv'   => 'utils', 'req' => 'showPossibilities',
            'title' => $channel->get(Channel::TITLE),
            'descr' => $channel->get(Channel::DESCRIPTION),
            'url'   => parse_url($cfg->get('self_url'), PHP_URL_QUERY))));
    }
    if (null != $channel->getPager()) {
        $channel->addHotkey('user4', $lang->msg('Pager'),
            $cfg->get('home_url') . '?' . http_build_query(array(
            'srv'   => 'utils', 'req' => 'pager',
            'pages' => $channel->getPager()->getPages(),
            'src'   => http_build_query($_GET))));
    }
    if (count($channel->getSupportedTemplates()) > 1) {
        $params = array('nexttpl' => 1) + $_GET;
        $channel->addHotkey('display', $lang->msg('View'),
            $cfg->get('home_url') . '?' . http_build_query($params));
    }
    # Home button cannot be handled yet, since not accepted by firmware
    # if ($srv != 'utils') {
    #     $channel->addHotkey('home', null, $cfg->get('home_url'));
    # }
}

function addHotkeyReaction($channel) {
    global $keys;
    foreach ($channel->getHotkeys() as $keyId => $params) {
        $url = $params['url'];
        $itemParams = $params['itemParams'];
        if (0 === strpos($url, 'INPUT:')) {
            addInputReaction($keys[$keyId], substr($url, strlen('INPUT:')), $itemParams);
        } else {
            $setReturnMark = false;
            if (0 === strpos($url, 'RETMARK:')) {
                $setReturnMark = true;
                $url = substr($url, strlen('RETMARK:'));
            }
            addKeyReaction($keys[$keyId], $url, $itemParams, $setReturnMark);
        }
    }
}

function addVideoScriptReaction($channel) {
    global $cfg, $keys;
    if (in_array(Item::VIDEOSCRIPT, $channel->getItemParams())) {
?>
        else if (key == "<?=$keys['enter']?>") {
            scriptUrl = getItemInfo(getFocusItemindex(), "videoScript");
            if (null != scriptUrl &amp;&amp; "" != streamUrl) {
                streamUrl = getURL(scriptUrl);
                print("streamUrl: ", streamUrl);
                if (null != streamUrl &amp;&amp; "" != streamUrl &amp;&amp; "protected" != streamUrl) {
                    playItemURL(-1, 1);
                    playItemURL(streamUrl, 10);
                    res = "true";
                }
            }
        }
<?
    }
}

function addEnclosureReaction() {
    global $cfg, $keys;
?>
        else if (key == "<?=$keys['enter']?>") {
            FII    = getFocusItemIndex();
            link   = getItemInfo(FII, "link");
            encUrl = getItemInfo(FII, "playURL");
            if (null == link &amp;&amp; null != encUrl &amp;&amp; "" != encUrl) {
                url  = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=open";
                url += "&amp;url="   + urlEncode(encUrl);
                url += "&amp;title=" + urlEncode(getItemInfo(FII, "title"));
            }
            res = "true";
        }
<?
}

function addPgupReaction($menuJump) {
    global $keys;
?>
        else if (key == "<?=$keys['pgup']?>") {
            focus = getFocusItemIndex() - <?=$menuJump?>;
            if (focus &lt; 0) focus = 0;
            setFocusItemIndex(focus);
            res = "true";
        }
<?
}

function addPgdnReaction($menuJump) {
    global $keys;
?>
        else if (key == "<?=$keys['pgdn']?>") {
            max = getPageInfo("itemCount") - 1;
            focus = Add(getFocusItemIndex(), <?=$menuJump?>);
            if (focus &gt; max) focus = max;
            setFocusItemIndex(focus);
            res = "true";
        }
<?
}

function addUserInputHandling(&$channel, $wrapEnclosures = true) {
    global $cfg, $keys;

    addDefaultButtons($channel);
?>
    <onUserInput>
        res = "false";
        url = null;
        key = currentUserInput();
        if (3 == 5) {
            /* stub section, real logic comes from hot keys */
        }
<?
    addPlayButtonReaction();
    addHotkeyReaction($channel);
    addVideoScriptReaction($channel);

    if ($wrapEnclosures && $channel->hasEnclosures()) {
        addEnclosureReaction();
    }

    $menuJump = $cfg->get('menuJump');
    if (! empty($menuJump)) {
        addPgupReaction($menuJump);
        addPgdnReaction($menuJump);
    }
?>

        if (null != url &amp;&amp; "" != url) {
            jumpToLink("rssLink");
            res = "true";
        }
        if ("true" == res) {
            redrawDisplay();
        }
        res;
    </onUserInput>
<?
}

function addDefaultOnEnter() {
    global $cfg;
?>
<onEnter>
    <? restoreVariable('viewerMode', 'viewerMode') ?>
    defaultMode = <?=$cfg->get('playerMode', 1) == 0 ? 0 : 7?>;
    if (null == viewerMode || "" == viewerMode) {
        viewerMode = defaultMode;
    }
    SwitchViewer(viewerMode);
    <? storeVariable('defaultMode', 'viewerMode') ?>
</onEnter>
<?
}

function addCursorRestoration($restoreFunc = 'onEnter', $storeFunc = 'onExit', $cursorVar = null) {
    $cursorVar = null != $cursorVar ? $cursorVar : 'CURS_' . implode('_', $_GET);
?>
<<?=$restoreFunc?>>
    <? restoreVariable('startIndex', $cursorVar) ?>
    if (null != startIndex) {
        setFocusItemIndex(startIndex);
    }
</<?=$restoreFunc?>>
<<?=$storeFunc?>>
    startIndex = getFocusItemIndex();
    <? storeVariable('startIndex', $cursorVar) ?>
</<?=$storeFunc?>>
<?
}

function addSelectedIndexSupport($channel) {
?>
<onEnter>
    if (null == selectedIndex) {
        selectedIndex = <?=$channel->get('selectedIndex', count($channel->getItems()) / 2)?>;
        setFocusItemIndex(selectedIndex);
    }
</onEnter>
<?
}

function addRefreshOnEnter() {
?>
<onEnter>
    setRefreshTime(300);
</onEnter>
<onExit>
    setRefreshTime(-1);
</onExit>
<onRefresh>
    setRefreshTime(-1);
    redrawDisplay();
</onRefresh>
<?
}
?>
