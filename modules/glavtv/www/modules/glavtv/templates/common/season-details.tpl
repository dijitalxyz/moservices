<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $shots = $object->get(Channel::SCREENSHOTS);
    $hasScreenshots = is_array($shots) && ! empty($shots);

    $allParams = $object->getItemParams();
    $showQuality = in_array(Item::QUALITY, $allParams);

    $ratio = $object->get(Channel::THUMB_RATIO, 2);
    if (1 == $ratio) {
        $width = 20.5; $height = 25;
    } else if (2 == $ratio) {
        $width = 11.5; $height = 32;
    } else {
        $width = 20.5; $height = 28;
    }
    $offX =  2 + (22.5 - $width)  / 2;
    $offY = 13 + (32 - $height) / 2;

    $marX = $cfg->get('marginX', 0);
?>

<switchSeason>
    FMI = getFocusMenuIndex();
    if (FMI &lt; 0 || FMI &gt;= <?=count($object->getItems())?>) {
        titleArray = null;
        videoArray = null;
        linkArray  = null;
        itemSize   = 0;
    }
<?
$counter = 0;
foreach ($object->getItems() as $item) {
    $titles = array();
    $links  = array();
    $videos = array();
    foreach ($item->get(Item::SUBITEMS) as $subitem) {
        $titles[]   = $subitem->get(Item::TITLE);
        $links[]    = $subitem->get(Item::LINK);
        $enclosures = $subitem->getEnclosures();
        if (! empty($enclosures)) {
            list($url, $type) = each($enclosures);
        } else {
            $url = null;
        }
        $videos[] = $url;
    }
?>
    else if (FMI == <?=$counter++?>) {
        titleArray = "<?=implode('&#10;', $titles)?>";
        videoArray = "<?=implode('&#10;', str_replace('&', '&amp;', $videos))?>";
        linkArray  = "<?=implode('&#10;', str_replace('&', '&amp;', $links))?>";
        itemSize   = <?=count($titles)?>;
    }
<?}?>
</switchSeason>

<onEnter>
<?
$menus = array();
foreach ($object->getItems() as $item) {
    $menus[] = $item->get(Item::TITLE);
}
?>
    itemSize  = 0;
    menuArray = "<?=implode('&#10;', $menus)?>";
    menuSize  = <?=count($menus)?>;

    /* no set focus when playback ends */
    if (null == titleArray) {
        setFocusItemIndex(0);
    }
    executeScript("switchSeason");
</onEnter>


<mediaDisplay name="threePartsView"
<? showMediaDisplayParams(array('sideLeftWidthPC' => 26)) ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu', 1.6 + $marX, 49.2, 23.4 - $marX, 5.9) ?>
<? showRegionParams('header') ?>

<? showRegionParams('itemImage', 26.1, 11.1) ?>
<? showRegionParams('item', 26.1, 11.1, 27.7, 6.2) ?>

    menuPerPage="6"
    itemPerPage="12"
    itemGap="0"
    >
<? addIdleImages() ?>

    <!-- Manual user input handling -->
<? addDefaultButtons($object) ?>
    <onUserInput>
        res = "false";
        url = null;
        key = currentUserInput();
        if (3 == 5) {
            /* stub section, real logic comes from hot keys */
        }
<?
    addPlayButtonReaction();
    addHotkeyReaction($object);
    addVideoScriptReaction($object);
?>

        if (getPageInfo("majorContext") == "menu") {
            FMI = getFocusMenuIndex();
            if (key == "<?=$keys['down']?>") {
                FMI = Add(FMI, 1) % menuSize;
                setFocusMenuIndex(FMI);
                setFocusItemIndex(0);
                executeScript("switchSeason");
                res = "true";
            } else if (key == "<?=$keys['up']?>") {
                FMI = Add(FMI, menuSize - 1) % menuSize;
                setFocusMenuIndex(FMI);
                setFocusItemIndex(0);
                executeScript("switchSeason");
                res = "true";
            } else if (key == "<?=$keys['enter']?>") {
                postMessage("<?=$keys['right']?>");
                res = "true";
            } else if (key == "<?=$keys['right']?>") {
                /* Hack: send double Right to select item */
                postMessage("<?=$keys['right']?>");
            }
        } else {
            if (key == "<?=$keys['right']?>") {
                res = "true";
            } else if (key == "<?=$keys['left']?>") {
                redrawDisplay();
            } else if (key == "<?=$keys['down']?>") {
                cnt = getPageInfo("itemCount");
                FII = getFocusItemIndex();
                FII = Add(FII, 1) % cnt;
                setFocusItemIndex(FII);
                res = "true";
            } else if (key == "<?=$keys['up']?>") {
                cnt = getPageInfo("itemCount");
                FII = getFocusItemIndex();
                FII = Add(FII, cnt - 1) % cnt;
                setFocusItemIndex(FII);
                res = "true";
            } else if (key == "<?=$keys['enter']?>") {
                link = getItemInfo("link");
                if (null == link || "" == link) {
                    video = getItemInfo("video");
                    if (null == video || "" == video) {
                        url = "<?=$cfg->get('home_url')?>?req=message&amp;text=<?=urlencode($lang->msg('Video is not available, probably it was removed'))?>";
                    } else {
                        playItemURL(-1, 1);
                        playItemURL(video, 10);
                    }
                    res = "true";
                }
            }
        }

        if (null != url) {
            jumpToLink("rssLink");
            res = "true";
        }
        if ("true" == res) {
            redrawDisplay();
        }
        res;
    </onUserInput>
    <!-- End of manual user input handling -->

<? addHeader($object, false) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <text offsetXPC="<?=2.2 + $marX?>"  offsetYPC="91.3" widthPC="8" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgNormal')?>" align="center"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="14" redraw="yes">
        <script>getPageInfo("focusItemIndex") + "/" + getPageInfo("itemCount");</script>
    </text>

    <text offsetXPC="<?=10.1 + $marX?>" offsetYPC="91.3" widthPC="<?=89.9 - $marX?>" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="15" redraw="yes">
        <script>getItemInfo("<?=Item::TITLE?>")<?if($showQuality){?> + " - " + getItemInfo("<?=Item::QUALITY?>")<?}?>;</script>
    </text>

    <!-- detailed info -->
    <text offsetXPC="<?=1.6 + $marX?>"  offsetYPC="10.9" widthPC="<?=23.3 - $marX?>" heightPC="36.5" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="<?=1.8 + $marX?>"  offsetYPC="11.1" widthPC="<?=22.9 - $marX?>" heightPC="36.1" backgroundColor="<?=$colors->get('bgPanel2')?>" />

    <text offsetXPC="54.9" offsetYPC="10.9" widthPC="43.5" heightPC="73.5" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="55.1" offsetYPC="11.1" widthPC="43.1" heightPC="73.1" backgroundColor="<?=$colors->get('bgPanel1')?>" />

    <!-- poster image -->
    <text  offsetXPC="<?=$offX-0.1 + $marX?>" offsetYPC="<?=$offY-0.1?>"
        widthPC="<?=$width+0.2 - $marX?>" heightPC="<?=$height+0.2?>"
        backgroundColor="<?=$colors->get('fgDark')?>" />
    <image offsetXPC="<?=$offX+0.1 + $marX?>" offsetYPC="<?=$offY+0.1?>"
        widthPC="<?=$width-0.2 - $marX?>" heightPC="<?=$height-0.2?>" redraw="yes">
        <script>
            value = getItemInfo("<?=Item::THUMBNAIL?>");
            if (null == value || "" == value) {
                value = "<?=str_replace('&', '&amp;', $object->get(Channel::IMAGE))?>";
            }
            value;
        </script>
    </image>


<?
function displayParam($offY, $diffY, $twoLinesFields, $param, $value) {
    global $colors, $lang;
    $param  = is_numeric($param) ? '' : $lang->msg(ucfirst($param));
    $height = in_array($param, $twoLinesFields, true) ? $diffY * 2 - 1 : $diffY;
?>
    <text offsetXPC="56" offsetYPC="<?=$offY?>" widthPC="11" heightPC="<?=$diffY?>"
        foregroundColor="<?=$colors->get('fgNormal')?>" fontSize="15"><?=$param?></text>
    <text offsetXPC="66" offsetYPC="<?=$offY?>" widthPC="32"
        heightPC="<?=$height?>" lines="<?=$height==$diffY ? 1 : 2?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgPanel1')?>" fontSize="15"
        <?=0===strpos($value, '<script>') ? 'redraw="yes"' : ''?>><?=$value?></text>

<?
    return $height;
}

function parseDescription(&$channel, $channelParams) {
    $lines = explode("\n", $channel->get(Channel::DESCRIPTION));
    $channel->set(Channel::DESCRIPTION, null);
    foreach ($lines as $line) {
        $pos = strpos($line, ': ');
        if (false !== $pos && $pos < 45) {
            list($param, $value) = explode(': ', $line);
            $channelParams[$param] = $value;
        } else if (null != $line && ! preg_match('|^\s*$|', $line)) {
            $oldValue = $channel->get(Channel::DESCRIPTION);
            if (null != $oldValue && '' != $oldValue) {
                $channelParams[] = $oldValue;
            }
            $channel->set(Channel::DESCRIPTION, $line);
        }
    }
    return $channelParams;
}

    $twoLinesFields = array('actors', 'info');
    foreach ($twoLinesFields as $key => $value) {
        $twoLinesFields[$key] = $lang->msg(ucfirst($value));
    }
    $channelParams = array_diff_key($object->getParams(), array_flip(array(
        Channel::TITLE, Channel::DESCRIPTION, Channel::LINK, Channel::IMAGE,
        Channel::THUMB_RATIO, Channel::SCREENSHOTS, Channel::SERVICE)));
    $channelParams = parseDescription($object, $channelParams);
    $itemParams = array_diff($allParams, array(
        Item::TITLE, Item::DESCRIPTION, Item::THUMBNAIL, Item::LINK, Item::ID,
        Item::VIDEOSCRIPT, Item::REDIRECT, Item::QUALITY, Item::SUBITEMS));

    $length = $hasScreenshots ? 6 : 10;
    $cNum = count($channelParams);
    $iNum = count($itemParams);
    $sum  = $cNum + $iNum;
    $cNum = min($cNum, (int) (0 == $sum ? ($length / 2) : ($cNum * $length / $sum)));
    $iNum = $length - $cNum;

    $topY  = $hasScreenshots ? 63 : 83;
    $diffY = 5;
    $offY  = 12;
    $shown = 0;

    print "    <!-- params split $cNum:$iNum/$length -->" . PHP_EOL;
    print "    <!-- channel params: $cNum -->" . PHP_EOL;
    foreach ($channelParams as $param => $value) {
        if ($shown++ >= $cNum) break;
        $offY += displayParam($offY, $diffY, $twoLinesFields, $param,
            str_replace('"', '\\"', $value));
    }

    print "    <!-- item params: $iNum -->" . PHP_EOL;
    foreach ($itemParams as $param) {
        if ($shown++ >= $length) break;
        $offY += displayParam($offY, $diffY, $twoLinesFields, $param,
            '<script>getItemInfo("' . $param . '");</script>');
    }

    $offY  += 0 == $shown ? 0 : 2.5;
    $height = $topY - $offY;
    $lines  = $height / $diffY + 1;
?>
    <text offsetXPC="55.1" offsetYPC="<?=$offY?>" widthPC="43.1" heightPC="<?=$height?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        align="justify" fontSize="15" lines="<?=$lines?>">
        <script>
            value = getItemInfo("<?=Item::DESCRIPTION?>");
            if (null == value || "" == value) {
                value = "<?=str_replace('"', '″', $object->get(Channel::DESCRIPTION))?>";
            }
            value;
        </script>
    </text>

<?
    if ($hasScreenshots) {
        $offY += $height + 2;
        $height = 83 - $offY;
        $offX = 76;
        $width = 11.4;
        $count = min(3,count($shots));
        $shots = array_slice($shots, -$count);
        foreach ($shots as $shot) {
            if ($count-- == 0) break;
?>
    <image offsetXPC="<?=$offX?>" offsetYPC="<?=$offY?>"
        widthPC="<?=$width?>" heightPC="<?=$height?>"><?=$shot?></image>
<?
            $offX += $width + 1;
        }
    }
?>


    <menuDisplay>
        <script>
            selected = getFocusMenuIndex() == getQueryMenuIndex();
            active   = getPageInfo("majorContext") == "menu";
            if (selected) {
                if (active) {
                    itemBg = "<?=$colors->get('bgFocus')?>";
                } else {
                    itemBg = "<?=$colors->get('bgFocusInactive')?>";
                }
                itemFg = "<?=$colors->get('fgFocus')?>";
            } else {
                itemBg = "<?=$colors->get('bgNormal')?>";
                itemFg = "<?=$colors->get('fgDark')?>";
            }
        </script>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <text offsetXPC="3" offsetYPC="0" widthPC="97" heightPC="100" fontSize="16">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getStringArrayAt(menuArray, getQueryMenuIndex());</script>
        </text>
    </menuDisplay>

    <itemDisplay>
        <script>
            selected = getFocusItemIndex() == getQueryItemIndex();
            active   = getPageInfo("majorContext") == "items";
            if (selected) {
                if (active) {
                    itemBg = "<?=$colors->get('bgFocus')?>";
                } else {
                    itemBg = "<?=$colors->get('bgFocusInactive')?>";
                }
                itemFg = "<?=$colors->get('fgFocus')?>";
            } else {
                itemBg = "<?=$colors->get('bgNormal')?>";
                itemFg = "<?=$colors->get('fgDark')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="0">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <text offsetXPC="2" offsetYPC="0" widthPC="<?=$showQuality ? 55 : 98?>" heightPC="100"
            fontSize="16">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
<? if ($showQuality) {?>
        <text offsetXPC="55" offsetYPC="0" widthPC="42" heightPC="100"
            fontSize="16" align="right">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::QUALITY?>");</script>
        </text>
<? }?>
    </itemDisplay>

</mediaDisplay>

<? foreach ($object->getItems() as $item) { ?>
<submenu><title><?=$item->get(Item::TITLE)?></title></submenu>
<? } ?>

<item_template>
    <title><script>getStringArrayAt(titleArray, -1);</script></title>
    <video><script>getStringArrayAt(videoArray, -1);</script></video>
    <link><script>getStringArrayAt(linkArray, -1);</script></link>
</item_template>

<channel>
    <itemSize><script>itemSize;</script></itemSize>
</channel>
</rss>
