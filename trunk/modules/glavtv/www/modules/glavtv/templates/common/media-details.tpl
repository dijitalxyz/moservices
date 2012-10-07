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
        $width = 29; $height = 33;
    } else if (2 == $ratio) {
        $width = 20; $height = 50;
    } else {
        $width = 29; $height = 46;
    }
    $offX =  2 + (31 - $width)  / 2;
    $offY = 13 + (50 - $height) / 2;
?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

<? showRegionParams('itemImage', 1.6, 67) ?>
<? showRegionParams('item', 1.6, 67, 31.8, 6.1) ?>

    itemPerPage="3"
    itemGap="0"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object, false) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <text offsetXPC="2.2"  offsetYPC="91.3" widthPC="8" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgNormal')?>" align="center"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="14" redraw="yes">
        <script>getPageInfo("focusItemIndex") + "/" + getPageInfo("itemCount");</script>
    </text>

    <text offsetXPC="10.1" offsetYPC="91.3" widthPC="89.9" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="15" redraw="yes">
        <script>getItemInfo("<?=Item::TITLE?>")<?if($showQuality){?> + " - " + getItemInfo("<?=Item::QUALITY?>")<?}?>;</script>
    </text>

    <!-- detailed info -->
    <text offsetXPC="1.6"  offsetYPC="10.9" widthPC="31.8" heightPC="54.5" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="1.8"  offsetYPC="11.1" widthPC="31.4" heightPC="54.1" backgroundColor="<?=$colors->get('bgPanel2')?>" />

    <text offsetXPC="34.9" offsetYPC="10.9" widthPC="63.5" heightPC="73.5" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="35.1" offsetYPC="11.1" widthPC="63.1" heightPC="73.1" backgroundColor="<?=$colors->get('bgPanel1')?>" />

    <!-- poster image -->
    <text  offsetXPC="<?=$offX-0.1?>" offsetYPC="<?=$offY-0.1?>"
        widthPC="<?=$width+0.2?>" heightPC="<?=$height+0.2?>"
        backgroundColor="<?=$colors->get('fgDark')?>" />
    <image offsetXPC="<?=$offX+0.1?>" offsetYPC="<?=$offY+0.1?>"
        widthPC="<?=$width-0.2?>" heightPC="<?=$height-0.2?>" redraw="yes">
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
    <text offsetXPC="37" offsetYPC="<?=$offY?>" widthPC="14" heightPC="<?=$diffY?>"
        foregroundColor="<?=$colors->get('fgNormal')?>" fontSize="15"><?=$param?></text>
    <text offsetXPC="51" offsetYPC="<?=$offY?>" widthPC="47"
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
        Item::VIDEOSCRIPT, Item::REDIRECT, Item::QUALITY));

    $length = $hasScreenshots ? 6 : 10;
    $cNum = count($channelParams);
    $iNum = count($itemParams);
    $sum = $cNum + $iNum;
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
    <text offsetXPC="35.1" offsetYPC="<?=$offY?>" widthPC="63.1" heightPC="<?=$height?>"
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
        $offX = 36;

        $count = count($shots);
        if ($count <= 3) {
            $width = 19.5;
        } else {
            $width = 11.4;
            $count = 5;
            $shots = array_slice($shots, -5);
        }

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

    <itemDisplay>
        <script>
        <?=calculateItemColors()?>
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <text offsetXPC="2" offsetYPC="0" widthPC="<?=$showQuality ? 55 : 98?>" heightPC="100"
            fontSize="16">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
<? if($showQuality) {?>
        <text offsetXPC="55" offsetYPC="0" widthPC="42" heightPC="100"
            fontSize="16" align="right">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::QUALITY?>");</script>
        </text>
<?}?>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
