<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $img = $object->getImage();
    if (empty($img)) {
        $img = '0,logo.png';
    } else if (1 !== stripos($img, ',')) {
        $img = "0,$img";
    }
    list($ratio, $img) = explode(',', $img);
    if (0 !== stripos($img, 'http://')) {
        $img = $cfg->get('resource_disk') . 'img/' . $img;
    }

    $x = 0;
    $y = 0;
    $w = 40;
    $h = 70;
    if (1 == $ratio) {
        $h = 50;
    } else if (2 == $ratio) {
        $x = 8;
        $w = 24;
    }

    $caption = $object->getCaption();
    if (empty($caption)) {
        $caption = $lang->msg('Help the project');
    }
?>

<onEnter>
    seconds = <?=$_SESSION['fmTime']?>;
    if (0 == seconds) {
        percent = 0;
    } else {
        percent = 100;
        setRefreshTime(seconds * 10);
    }
</onEnter>

<onRefresh>
    if (percent &gt; 0) {
        percent = percent - 1;
    }
</onRefresh>

<onExit>
    setRefreshTime(-1);
</onExit>


<mediaDisplay name="photoView"
<? showMediaDisplayParams(null, $colors->get('bgPanel1')) ?>
<? showViewAreaParams(15, 15, 70, 66) ?>
<? showIdleImageParams() ?>

    rowCount="1"
    columnCount="1"

    itemOffsetXPC="41"
    itemOffsetYPC="87"
    itemWidthPC="18"
    itemHeightPC="8"

    itemGapXPC="1"
    itemGapYPC="0"
    >
<? addIdleImages() ?>

    <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="82"
        backgroundColor="<?=$colors->get('bgNormal')?>" />
    <text offsetXPC="0" offsetYPC="82" widthPC="100" heightPC="1"
        backgroundColor="<?=$colors->get('fgError')?>" />

    <text offsetXPC="0" offsetYPC="82" widthPC="100" heightPC="1"
        backgroundColor="<?=$colors->get('bgPanel2')?>" redraw="yes">
        <script>"";</script>
        <offsetXPC><script>percent;</script></offsetXPC>
        <widthPC><script>100 - percent;</script></widthPC>
    </text>

<? addBoundingFrame(0.2, 0.4) ?>

    <onUserInput>
        if (percent &gt; 0) {
            "true";
        } else {
            "false";
        }
    </onUserInput>

    <image offsetXPC="<?=3+$x?>" offsetYPC="<?=4+$y?>" widthPC="<?=$w?>"
        heightPC="<?=$h?>"><?=$img?></image>

    <text offsetXPC="46" offsetYPC="7" widthPC="50" heightPC="60"
        foregroundColor="<?=$colors->get('fgDark')?>" lines="5"
        fontSize="20"><?=htmlspecialchars($object->getText())?></text>

    <text offsetXPC="40" offsetYPC="65" widthPC="55" heightPC="10"
        foregroundColor="<?=$colors->get('fgNormal')?>" align="right"
        fontSize="20"><?=htmlspecialchars($caption)?></text>

    <text offsetXPC="39" offsetYPC="87" widthPC="22" heightPC="8"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        backgroundColor="<?=$colors->get('bgPanel2')?>"
        align="center" fontSize="15" cornerRounding="5" redraw="yes">
        <script>
            if (percent == 0) {
                fgColor = "<?=$colors->get('fgFocus')?>";
                bgColor = "<?=$colors->get('bgFocus')?>";
                getItemInfo(getFocusItemindex(), "<?=Item::TITLE?>");
            } else {
                fgColor = "<?=$colors->get('fgNormal')?>";
                bgColor = "<?=$colors->get('bgPanel2')?>";
                Add(Integer(percent * seconds / 100), 1);
            }
        </script>
        <foregroundColor><script>fgColor;</script></foregroundColor>
        <backgroundColor><script>bgColor;</script></backgroundColor>
    </text>

    <itemDisplay>
        <script>
        <?=calculateItemColors()?>
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            align="center" fontSize="15" cornerRounding="5">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>
</mediaDisplay>

<channel>
    <item>
        <title>OK</title>
        <onClick>
            <script>
            <? if (null == $object->getForwardUrl()) { ?>
                postMessage("<?=$keys['return']?>");
            <? } else { ?>
                url = "<?=$object->getForwardUrl()?>";
                jumpToLink("rssLink");
            <? } ?>
                null;
            </script>
        </onClick>
    </item>
</channel>

</rss>
