<?
global $colors;

require_once 'rss-funcs.inc';
require_once 'tools/config.inc';
$colors = new ConfigFile('templates/colors.ini');


// Idle image size and position
function showIdleImageParams() {
    showRegionParams('idleImage', 4.5, 76, 10, 16);
}

// Idle images
function addIdleImages() {
    global $cfg;
    for ($i = 1; $i <= 6; $i++) {
        echo '    <idleImage>' . $cfg->get('resource_disk', '.');
        echo "img/loading/loading_$i.jpg</idleImage>" . PHP_EOL;
    }
}

// Calculate item colors for normal and focus items
function calculateItemColors() {
    global $cfg, $colors;
?>

            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
            } else {
                itemBg = "<?=$colors->get('bgNormal')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
            }
<?
}

function addBoundingFrame($w = 0.5, $h = 0.7, $color = null, $offX = 0, $offY = 0, $width = 100, $height = 100) {
    if (null == $color) {
        global $colors;
        $color = $colors->get('fgDark');
    }
?>
    <!-- Bounding frame -->
    <text offsetXPC="<?=$offX?>"     offsetYPC="<?=$offY?>"       widthPC="<?=$width?>" heightPC="<?=$h?>"      backgroundColor="<?=$color?>" />
    <text offsetXPC="<?=$offX?>"     offsetYPC="<?=$offY?>"       widthPC="<?=$w?>"     heightPC="<?=$height?>" backgroundColor="<?=$color?>" />
    <text offsetXPC="<?=$offX+$width-$w?>" offsetYPC="<?=$offY?>" widthPC="<?=$w?>"     heightPC="<?=$height?>" backgroundColor="<?=$color?>" />
    <text offsetXPC="<?=$offX?>"     offsetYPC="<?=$offY+$height-$h?>"  widthPC="<?=$width?>" heightPC="<?=$h?>"      backgroundColor="<?=$color?>" />
<?
}

function addHeader($channel, $showThumb = true) {
    global $version, $projectName, $colors, $lang;
    $thumb   = $channel->get(Channel::IMAGE);
    $caption = $channel->get(Channel::TITLE);
    $service = $channel->get(Channel::SERVICE);
    if (null != $service) {
        $service = $lang->msg($service);
        $srvOffX = 5;
    } else {
        $srvOffX = 0;
    }
?>
    <!-- header -->
<?  if ($showThumb && null != $thumb && '' != $thumb) { ?>
    <image offsetXPC="3.7" offsetYPC="2.6" widthPC="3.1" heightPC="4.8"><?=str_replace('&', '&amp;', $thumb)?></image>
<?
        $offX = 7;
    } else {
        $offX = 3;
    }
    $headerLine = 'offsetYPC="3" heightPC="4" fontSize="14"';
?>
    <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="9.1" backgroundColor="<?=$colors->get('bgHeader')?>" />
    <text offsetXPC="0" offsetYPC="9" widthPC="100" heightPC="0.5" backgroundColor="<?=$colors->get('lines')?>" />

    <text <?=$headerLine?> offsetXPC="<?=$offX?>"         widthPC="<?=48-$offX-$srvOffX?>" foregroundColor="<?=$colors->get('fgInfo')?>" ><?=$caption?></text>
    <text <?=$headerLine?> offsetXPC="<?=48-$srvOffX?>"   widthPC="15"                     foregroundColor="<?=$colors->get('fgInfo')?>" ><?=$projectName?></text>
    <text <?=$headerLine?> offsetXPC="<?=64-$srvOffX?>"   widthPC="<?=20+$srvOffX?>"       foregroundColor="<?=$colors->get('fgFocus')?>"><?=$service?></text>
    <text <?=$headerLine?> offsetXPC="<?=57-$srvOffX?>"   widthPC="15"                     foregroundColor="<?=$colors->get('version')?>"><?=$version?></text>
    <text <?=$headerLine?> offsetXPC="82.5"               widthPC="15"                     foregroundColor="<?=$colors->get('fgInfo')?>"
        backgroundColor="<?=$colors->get('bgHeader')?>" redraw="yes" align="right">
        <script>
            executeScript("getTimeStr");
            timeStr + "  ";
        </script>
    </text>
<?
}

function addFooter($channel) {
    global $colors;
?>
    <!-- footer -->
    <text offsetXPC="0" offsetYPC="86" widthPC="100" heightPC="14"  backgroundColor="<?=$colors->get('bgFooter')?>" />
    <text offsetXPC="0" offsetYPC="86" widthPC="100" heightPC="0.5" backgroundColor="<?=$colors->get('lines')?>" />
<?
}

function addHotkeysLine($channel) {
    global $cfg, $colors, $keys;
    $hotkeys = array();
    foreach ($channel->getHotkeys() as $keyId => $params) {
        if (isset($params['title'])) {
            $hotkeys[$keyId] = $params;
        }
    }

    $count = count($hotkeys);
    if (0 == $count) {
        return false;
    } else if ($count <= 4) {
        $width = 20;
    } else if ($count <= 6) {
        $width = 16;
    } else {
        $count = min($count, 8);
        $width = 96 / $count;
    }

    $xPos  = 100 - $count * $width;
    $yPos  = 96;
?>
    <!-- Hot keys line -->
    <text offsetXPC="<?=$xPos-3?>" offsetYPC="<?=$yPos?>" widthPC="<?=103 - $xPos + 5?>" heightPC="12"
        cornerRounding="52" backgroundColor="<?=$colors->get('bgKeys')?>" />

<?
    $platform = $cfg->get('platform');
    $ipath = $cfg->get('remote_images');
    foreach ($hotkeys as $keyId => $params) {
        if ($count-- == 0) {
            break;
        }
        if (0 === strpos($keyId, 'user')) {
            $aliases = array_keys($keys, $keys[$keyId]);
            $aliases = array_diff($aliases, array($keyId));
            $keyId = empty($aliases) ? $keyId : $aliases[0];
        }
        addSingleHotkey($xPos, $yPos, $width,
            $keyId, $ipath . $keyId . '.png', $params['title']);
        $xPos += $width;
    }
    return true;
}

function addSingleHotkey($xPos, $yPos, $width, $keyId, $icon, $title) {
    global $colors, $keys;
    $iconWidth = 3.5;
    $textWidth = $width - $iconWidth;
?>
    <!-- Hot key <?=$keyId . ' = ' . $keys[$keyId]?> hint -->
    <image offsetXPC="<?=$xPos+0.5?>" offsetYPC="<?=$yPos+0.4?>"
        widthPC="<?=$iconWidth?>" heightPC="3.2"><?=$icon?></image>
    <text  offsetXPC="<?=$xPos+$iconWidth+0.5?>" offsetYPC="<?=$yPos+0.3?>" widthPC="<?=$textWidth?>" heightPC="3.4"
        foregroundColor="<?=$colors->get('fgKeys')?>" fontSize="13"
        backgroundColor="<?=$colors->get('bgKeys')?>"><?=$title?></text>

<?
}

function addCounter($keysAdded = false) {
    global $colors;
?>
    <text redraw="yes" align="center" offsetXPC="2.2"  offsetYPC="87" widthPC="8"
        heightPC="<?=$keysAdded ? 8.6 : 13?>"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>" fontSize="14">
        <script>getPageInfo("focusItemIndex") + "/" + getPageInfo("itemCount");</script>
    </text>
<?
}

function addInfo($withRating = true) {
    global $colors;
?>
    <text redraw="yes" fontSize="15"
        offsetXPC="10.1" offsetYPC="87" widthPC="<?=$withRating ? 72.9 : 89.9?>" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            value = getItemInfo("<?=Item::INFO?>");
            if (null == value || "" == value) {
                value = getItemInfo("<?=Item::TITLE?>");
            }
            value;
        </script>
    </text>
<?  if (! $withRating) return; ?>
    <text redraw="yes" align="right" fontSize="15"
        offsetXPC="83" offsetYPC="87" widthPC="16" heightPC="4.3"
        foregroundColor="<?=$colors->get('fgInfo')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            value = getItemInfo("<?=Item::RATING?>");
            if (null == value || "" == value) {
                value = getItemInfo("<?=Item::YEAR?>");
            }
            value + "  ";
        </script>
    </text>
<?
}

function addDescription($channel, $keysAdded = false) {
    global $colors;
?>
    <text redraw="yes" fontSize="15" lines="<?=$keysAdded ? 1 : 2?>"
        offsetXPC="10.1" offsetYPC="91.3" widthPC="89.9" heightPC="<?=$keysAdded ? 4.3 : 8.6?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            value = getItemInfo("<?=Item::DESCRIPTION?>");
            if (null == value || "" == value) {
                value = "<?=str_replace('"', '″', $channel->get(Channel::DESCRIPTION))?>";
            }
            value;
        </script>
    </text>
<?
}
?>
