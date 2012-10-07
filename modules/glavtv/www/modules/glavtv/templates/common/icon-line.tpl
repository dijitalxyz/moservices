<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<mediaDisplay name="photoFocusView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    focusItem="0"
    rowCount="1"
    columnCount="5"

    itemOffsetXPC="4"
    itemOffsetYPC="47"
    itemWidthPC="14"
    itemHeightPC="23"
    itemBackgroundWidthPC="15"
    itemBackgroundHeightPC="24"
    itemGapXPC="1.5"

    focusItemOffsetXPC="4"
    focusItemOffsetYPC="25.6"
    focusItemWidthPC="28"
    focusItemHeightPC="46"
    focusItemBackgroundWidthPC="29"
    focusItemBackgroundHeightPC="47"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

    <text redraw="yes" align="center" offsetXPC="18" offsetYPC="72" widthPC="60" heightPC="12"
        foregroundColor="<?=$colors->get('fgNormal')?>"
        backgroundColor="<?=$colors->get('bgNormal')?>" fontSize="24">
        <script>getPageInfo("focusItemIndex") + ". " + getItemInfo("<?=Item::TITLE?>");</script>
    </text>
    <text redraw="yes" align="center" tailDots="no" lines="<?=$keysAdded ? 2 : 3?>" fontSize="15"
        offsetXPC="0" offsetYPC="87.2" widthPC="100" heightPC="<?=$keysAdded ? 8.6 : 12.9?>"
        foregroundColor="<?=$colors->get('fgInfo')?>"
        backgroundColor="<?=$colors->get('bgFooter')?>">
        <script>
            value = getItemInfo("<?=Item::DESCRIPTION?>");
            if (null == value || "" == value) {
                value = "<?=str_replace('"', '″', $object->get(Channel::DESCRIPTION))?>";
            }
            value;
        </script>
    </text>

    <itemDisplay>
        <image offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
<? if (in_array("demo", $object->getItemParams())) {?>
        <image offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">
            <script>
                if (getItemInfo("demo") == "1") {
                    "<?=$cfg->get('resource_url')?>img/coming-soon.png";
                } else {
                    "";
                }
            </script>
        </image>
<? }?>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
