<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $w     = 0.2;
    $h     = 0.5;
    $delta = 1;
    $max   = 15;
?>

<onEnter>
    x = <?=$cfg->get('marginX', 2)?>;
    y = <?=$cfg->get('marginY', 2)?>;
</onEnter>

<saveMargins>
    url  = "<?=$cfg->get('service_url')?>&amp;req=saveIniParams";
    url += "&amp;" + urlEncode("params[marginX]") + "=" + x;
    url += "&amp;" + urlEncode("params[marginY]") + "=" + y;
    jumpToLink("rssLink");
</saveMargins>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showRegionParams('viewArea', 0, 0, 100, 100) ?>
<? showIdleImageParams() ?>
<? showRegionParams('itemImage', 35, 55) ?>
<? showRegionParams('item', 35, 55, 30, 10) ?>

    autoSelectMenu="no"
    autoSelectItem="no"
    itemPerPage="1"
    >
<? addIdleImages() ?>

    <onUserInput>
        key = currentUserInput();
        res = "true";
        if (key == "<?=$keys['enter']?>") {
            executeScript("saveMargins");
        } else if (key == "<?=$keys['right']?>") {
            if (x &lt;= <?=$max - $delta?>) {
                x = x - -<?=$delta?>;
            }
        } else if (key == "<?=$keys['left']?>") {
            if (x &gt;= <?=$delta?>) {
                x = x - <?=$delta?>;
            }
        } else if (key == "<?=$keys['down']?>") {
            if (y &lt;= <?=$max - $delta?>) {
                y = y - -<?=$delta?>;
            }
        } else if (key == "<?=$keys['up']?>") {
            if (y &gt;= <?=$delta?>) {
                y = y - <?=$delta?>;
            }
        } else {
            res = "false";
        }
        if (res == "true") {
            redrawDisplay();
        }
        res;
    </onUserInput>

    <backgroundDisplay>

        <image redraw="yes" offsetXPC="0" offsetYPC="0"  widthPC="100" heightPC="100">
            <script>"<?=$cfg->get('resource_disk')?>img/screenshot.jpg";</script>
            <offsetXPC><script>x;</script></offsetXPC>
            <offsetYPC><script>y;</script></offsetYPC>
            <widthPC><script>100 - 2*x;</script></widthPC>
            <heightPC><script>100 - 2*y;</script></heightPC>
        </image>

        <text redraw="yes" backgroundColor="<?=$colors->get('fgError')?>" offsetXPC="0" offsetYPC="0"  widthPC="100" heightPC="<?=$h?>">
            <script>"";</script>
            <offsetXPC><script>x;</script></offsetXPC>
            <offsetYPC><script>y;</script></offsetYPC>
            <widthPC><script>100 - 2*x;</script></widthPC>
        </text>
        <text redraw="yes" backgroundColor="<?=$colors->get('fgError')?>" offsetXPC="0" offsetYPC="0"  widthPC="<?=$w?>" heightPC="100">
            <script>"";</script>
            <offsetXPC><script>x;</script></offsetXPC>
            <offsetYPC><script>y;</script></offsetYPC>
            <heightPC><script>100 - 2*y;</script></heightPC>
        </text>
        <text redraw="yes" backgroundColor="<?=$colors->get('fgError')?>" offsetXPC="0" offsetYPC="<?=100 - $h?>"  widthPC="100" heightPC="<?=$h?>">
            <script>"";</script>
            <offsetXPC><script>x;</script></offsetXPC>
            <offsetYPC><script><?=100 - $h?> - y;</script></offsetYPC>
            <widthPC><script>100 - 2*x;</script></widthPC>
        </text>
        <text redraw="yes" backgroundColor="<?=$colors->get('fgError')?>" offsetXPC="<?=100 - $w?>" offsetYPC="0"  widthPC="<?=$w?>" heightPC="100">
            <script>"";</script>
            <offsetXPC><script><?=100 - $w?> - x;</script></offsetXPC>
            <offsetYPC><script>y;</script></offsetYPC>
            <heightPC><script>100 - 2*y;</script></heightPC>
        </text>

        <text offsetXPC="25" offsetYPC="30"  widthPC="50" heightPC="40" backgroundColor="<?=$colors->get('bgPanel1')?>" />
<? addBoundingFrame($w, $h, $colors->get('fgDark'), 25, 30, 50, 40) ?>

        <text offsetXPC="30" offsetYPC="35"  widthPC="40" heightPC="17"
            foregroundColor="<?=$colors->get('fgInfo')?>" fontSize="17" align="center"
            lines="3"><?=$lang->msg('Use arrows to adjust margins')?></text>
    </backgroundDisplay>

    <itemDisplay>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            backgroundColor="<?=$colors->get('bgPanel1')?>"
            foregroundColor="<?=$colors->get('fgNormal')?>"
            align="center" fontSize="18">
            <script>"marginX: " + x + ", marginY: " + y;</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<channel>
    <item><title>Ok</title></item>
</channel>
</rss>
