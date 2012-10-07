<? require_once 'templates/rss-funcs.inc'  ?>
<? require_once 'templates/rss-design.inc' ?>
<? addCommonInit() ?>

<?
    $allParams = $object->getItemParams();
    $thumbWidth = in_array(Item::THUMBNAIL, $allParams) ? 5 : 0;
    $fields = array_diff($allParams, array(
        Item::TITLE, Item::DESCRIPTION, Item::THUMBNAIL, Item::LINK, Item::ID,
        Item::INFO,  Item::VIDEOSCRIPT, Item::REDIRECT));
    if (0 == count($fields)) {
        $fields[] = Item::DESCRIPTION;
    }
    $sepsHeight = 0.4;
?>

<mediaDisplay name="onePartView"
<? showMediaDisplayParams() ?>
<? showViewAreaParams() ?>
<? showIdleImageParams() ?>
<? showRegionParams('menu') ?>
<? showRegionParams('header') ?>

<? showRegionParams('itemImage', 0, 10, 0, 0) ?>
<? showRegionParams('item', 0, 10, 100, 6.3) ?>

    itemPerPage="12"
    >
<? addIdleImages() ?>
<? addUserInputHandling($object) ?>

<? addHeader($object) ?>
<? addFooter($object) ?>

<? $keysAdded = addHotkeysLine($object) ?>

<? addCounter($keysAdded) ?>
<? addInfo(in_array(Item::RATING, $allParams) || in_array(Item::YEAR, $allParams)) ?>
<? addDescription($object, $keysAdded) ?>

    <itemDisplay>
        <script>
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                infoFg = itemFg;
            } else {
                itemBg = "<?=$colors->get('bgPanel1')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
                infoFg = "<?=$colors->get('fgInfo')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="<?=$sepsHeight?>" widthPC="100" heightPC="<?=100 - $sepsHeight?>">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <script>"";</script>
        </text>
        <? if ($thumbWidth != 0) {?>
        <image offsetXPC="3" offsetYPC="5" widthPC="4" heightPC="90">
            <script>getItemInfo("<?=Item::THUMBNAIL?>");</script>
        </image>
        <?}?>
        <text offsetXPC="<?=2 + $thumbWidth?>" offsetYPC="0" widthPC="<?=48.1 - $thumbWidth?>" heightPC="100" fontSize="15" >
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>getItemInfo("<?=Item::TITLE?>");</script>
        </text>

        <!-- dynamic fields -->
        <?
        $off = 50;
        $width = (98.0 - $off) / count($fields);
        foreach ($fields as $field) {?>
        <text align="right" offsetXPC="<?=$off?>" offsetYPC="0" widthPC="<?=$width + 0.1?>" heightPC="100" fontSize="15" >
            <foregroundColor><script>infoFg;</script></foregroundColor>
            <script>getItemInfo("<?=$field?>") + "  ";</script>
        </text>
        <?
            $off += $width;
        }
        ?>
    </itemDisplay>
</mediaDisplay>

<?=str_replace('&', '&amp;', $object)?>

</rss>
