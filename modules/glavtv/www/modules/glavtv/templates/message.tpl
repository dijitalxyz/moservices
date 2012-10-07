<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>

<?
    if (null == $object->getImage()) {
        $caption = $object->getCaption();
        if ($caption == 'Error' || $caption == $lang->msg('Error')) {
            $img = 'error.png';
        } else if ($caption == 'Warning' || $caption == $lang->msg('Warning')) {
            $img = 'warning.png';
        } else {
            $img = 'information.png';
        }
        $object->setImage($img);
    }
?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams(array('backgroundColor' => $colors->get('fgDark'))) ?>
<? showViewAreaParams(25, 35, 50, 30) ?>
<? showIdleImageParams() ?>
<? showRegionParams('itemImage', 35, 73) ?>
<? showRegionParams('item', 35, 73, 30, 15) ?>

    autoSelectMenu="no"
    autoSelectItem="no"
    cornerRounding="15"
    itemPerPage="1"
    itemGap="0"
    >
<? addIdleImages() ?>

    <text offsetXPC="0.3" offsetYPC="1" widthPC="99.7" heightPC="98.5"
        backgroundColor="<?=$colors->get('bgPanel1')?>" cornerRounding="15" />

    <text offsetXPC="3" offsetYPC="5" widthPC="90" heightPC="15"
        backgroundColor="<?=$colors->get('bgPanel1')?>"
        foregroundColor="<?=$colors->get('fgDark')?>"
        align="center" fontSize="14"><?=$object->getCaption()?></text>

    <image offsetXPC="3.5" offsetYPC="30" widthPC="14.5"
        heightPC="40"><?=$cfg->get('resource_disk') . 'img/' . $object->getImage()?></image>

    <text offsetXPC="18" offsetYPC="30" widthPC="79" heightPC="41"
        backgroundColor="<?=$colors->get('bgPanel1')?>"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        fontSize="14" lines="3"><?=htmlspecialchars($object->getText())?></text>

    <text offsetXPC="35" offsetYPC="73" widthPC="30" heightPC="15"
        backgroundColor="<?=$colors->get('bgFocus')?>"
        foregroundColor="<?=$colors->get('fgFocus')?>"
        align="center" fontSize="14" cornerRounding="5">OK</text>

    <itemDisplay>
        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            backgroundColor="<?=$colors->get('bgFocus')?>"
            foregroundColor="<?=$colors->get('fgFocus')?>"
            align="center" fontSize="14" cornerRounding="5">OK</text>
    </itemDisplay>
</mediaDisplay>

<channel>
    <item>
        <title>Ok</title>
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
