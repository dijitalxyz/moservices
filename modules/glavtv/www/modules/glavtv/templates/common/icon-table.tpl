<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<? list($imgWidth, $imgOffset) = checkThumbsRatio($object); ?>

<mediaDisplay name="photoView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

    rowCount="3"
    columnCount="4"

    itemOffsetXPC="1"
    itemOffsetYPC="10"
    itemWidthPC="23.6"
    itemHeightPC="24"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

<? addCounter($keysAdded) ?>
<?
   $allParams = $object->getItemParams();
   addInfo(in_array(Item::RATING, $allParams) || in_array(Item::YEAR, $allParams))
?>
<? addDescription($object, $keysAdded) ?>

    <itemDisplay>
        <script>
            <?=calculateItemColors()?>
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="15">
            <backgroundColor><script>itemBg;</script></backgroundColor>
        </text>
        <image offsetXPC="<?=$imgOffset?>" offsetYPC="5" widthPC="<?=$imgWidth?>" heightPC="75">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
        <text offsetXPC="2" offsetYPC="80" widthPC="96" heightPC="20"
            align="center" fontSize="15">
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>
</rss>
