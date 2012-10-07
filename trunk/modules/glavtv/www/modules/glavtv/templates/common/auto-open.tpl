<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<onEnter>
    if (null == alreadyEntered) {
        alreadyEntered = 1;
        postMessage("<?=$keys['enter']?>");
        setRefreshTime(3000);
    } else {
        setRefreshTime(200);
    }
</onEnter>

<onRefresh>
    postMessage("<?=$keys['return']?>");
    setRefreshTime(2000);
</onRefresh>

<onExit>
    setRefreshTime(-1);
</onExit>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(array(
    'backgroundColor' => $colors->get('bgPanel1'),
    'itemBackgroundColor' => $colors->get('bgPanel1'))) ?>
<? showViewAreaParams(20, 40, 60, 20) ?>
<? showIdleImageParams() ?>

    rowCount="1"
    columnCount="1"

    itemOffsetXPC="13"
    itemOffsetYPC="2"
    itemWidthPC="85"
    itemHeightPC="96"

    itemGapXPC="0"
    itemGapYPC="0"
    >
<? addIdleImages() ?>
<? addBoundingFrame(0.2, 2) ?>

    <image offsetXPC="3.5" offsetYPC="28" widthPC="10"
        heightPC="55"><?=$cfg->get('resource_disk') . 'img/play.png'?></image>
    <itemDisplay>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            foregroundColor="<?=$colors->get('fgFocus')?>" fontSize="16">
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
