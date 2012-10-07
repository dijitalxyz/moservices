<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<?
    if (null == $object->getImage()) {
        $object->setImage('logo.png');
    }
?>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(null, $colors->get('bgPanel2')) ?>
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

    <text offsetXPC="0" offsetYPC="0" widthPC="20" heightPC="100"
        backgroundColor="<?=$colors->get('bgPanel1')?>" />

<? addBoundingFrame(0.2, 0.4) ?>

    <text offsetXPC="3" offsetYPC="5" widthPC="94" heightPC="10"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        align="center" fontSize="16"><?=$object->getCaption()?></text>

    <image offsetXPC="3" offsetYPC="18" widthPC="14"
        heightPC="25"><?=$cfg->get('resource_disk') . 'img/' . $object->getImage()?></image>

    <!-- Splitted information text -->
<?
    $lines = explode("\n", htmlspecialchars($object->getText()));
    $max   = min(10, count($lines));
    $delta = 6.5;
    for ($i = 0; $i < $max; $i++) {
?>
    <text offsetXPC="21" offsetYPC="<?=18+$i*$delta?>" widthPC="76" heightPC="<?=$delta?>"
        foregroundColor="<?=$colors->get('fgInfo')?>"
        fontSize="14"><?=trim($lines[$i])?></text>
<? } ?>

    <itemDisplay>
        <script>
        <?=calculateItemColors()?>
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            cornerRounding="5" fontSize="15" align="center">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <foregroundColor><script>itemFg;</script></foregroundColor>
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
