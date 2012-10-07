<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>
<? addRssLink() ?>

<? $steps = count($object->getItems()) ?>

<onEnter>
    setFocusItemIndex(1);

    error        = null;
    step         = 0;
    currentState = "<?=$lang->msg('Waiting')?>";
    currentCmd   = null;

    titlesArray  = null;
    detailsArray = null;
    linksArray   = null;
    resultsArray = null;
<?  foreach ($object->getItems() as $item) { ?>

    titlesArray  = pushBackStringArray(titlesArray,  "<?=$item->get(Item::TITLE)?>");
    detailsArray = pushBackStringArray(detailsArray, "<?=$item->get(Item::DESCRIPTION)?>");
    linksArray   = pushBackStringArray(linksArray,   "<?=str_replace('&', '&amp;', $item->get(Item::LINK))?>");
    resultsArray = pushBackStringArray(resultsArray,
        "<?=null == $item->get('expResult') ? '' : $item->get('expResult') ?>");
<?  } ?>
</onEnter>

<onExit>
    setRefreshTime(-1);
</onExit>

<onRefresh>
    setRefreshTime(-1);
    executeScript("runCmd");
    redrawDisplay();
</onRefresh>

<runCmd>
    showIdle();
    res = getURL(currentCmd);
    if (res == getStringArrayAt(resultsArray, step)) {
        step = Add(step, 1);
        currentState = "<?=$lang->msg('Waiting')?>";
    } else {
        error = res;
    }
    currentCmd = null;
    cancelIdle();
</runCmd>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(null, $colors->get('bgPanel1')) ?>
<? showViewAreaParams(15, 15, 70, 66) ?>
<? showIdleImageParams() ?>

    rowCount="1"
    columnCount="2"

    itemOffsetXPC="43"
    itemOffsetYPC="85"
    itemWidthPC="23"
    itemHeightPC="10"

    itemGapXPC="3"
    itemGapYPC="0"
    >
<? addIdleImages() ?>
<? addBoundingFrame() ?>

    <image offsetXPC="4" offsetYPC="16" widthPC="20"
        heightPC="37"><?=$object->get(Channel::IMAGE)?></image>

    <!-- title -->
    <text offsetXPC="3" offsetYPC="3" widthPC="94" heightPC="10"
        foregroundColor="<?=$colors->get('fgFocus')?>"
        align="center" fontSize="16">
        <script>
            if (step &lt; <?=$steps?>) {
                curr = Add(step, 1);
            } else {
                curr = <?=$steps?>;
            }
            "<?=$object->get(Channel::TITLE)?>, <?=$lang->msg('Step')?> " + curr + "/<?=$steps?>";
        </script>
    </text>

<?
    $offY  = 15;
    $diffY = 10;
    $lines = min(4, $steps);
    for ($i = 0; $i < $lines; $i++) {
?>
    <!-- line <?=$i+1?> -->
    <text offsetXPC="28" offsetYPC="<?=$offY?>" widthPC="12" heightPC="<?=$diffY?>"
        fontSize="15" redraw="yes">
        <script>
            if (step &lt; <?=$lines - 1?>) {
                index  = <?=$i?>;
                active = step;
            } else if (step &gt;= <?=$steps?>) {
                index  = <?=$steps - $lines + $i?>;
                active = <?=$lines?>;
            } else {
                index  = step - <?=$lines - 1 - $i?>;
                active = <?=$lines - 1?>;
            }

            if (active == <?=$i?>) {
                fgColor = "<?=$colors->get('fgFocus')?>";
            } else {
                fgColor = "<?=$colors->get('fgInfo')?>";
            }
            "<?=$lang->msg('Step')?> " + Add(index, 1);
        </script>
        <foregroundColor><script>fgColor;</script></foregroundColor>
    </text>
    <text offsetXPC="40" offsetYPC="<?=$offY?>" widthPC="43" heightPC="<?=$diffY?>"
        fontSize="15" redraw="yes">
        <script>
            if (active == <?=$i?>) {
                fgColor = "<?=$colors->get('fgFocus')?>";
            } else {
                fgColor = "<?=$colors->get('fgInfo')?>";
            }
            getStringArrayAt(titlesArray, index);
        </script>
        <foregroundColor><script>fgColor;</script></foregroundColor>
    </text>
    <text offsetXPC="80.5" offsetYPC="<?=$offY?>" widthPC="15" heightPC="<?=$diffY?>"
        fontSize="15" redraw="yes" align="right">
        <script>
            if (active == <?=$i?>) {
                if (null != error) {
                    fgColor = "<?=$colors->get('fgError')?>";
                    text = "<?=$lang->msg('Error')?>";
                } else {
                    fgColor = "<?=$colors->get('bgFocus')?>";
                    text = currentState;
                }
            } else {
                fgColor = "<?=$colors->get('fgInfo')?>";
                if (active &lt; <?=$i?>) {
                    text = "<?=$lang->msg('Waiting')?>";
                } else {
                    text = "OK";
                }
            }
            text;
        </script>
        <foregroundColor><script>fgColor;</script></foregroundColor>
    </text>

<?
        $offY += $diffY + 1;
    }
?>

    <text offsetXPC="28" offsetYPC="62" widthPC="68.5" heightPC="0.9" backgroundColor="<?=$colors->get('fgDark')?>" />
    <text offsetXPC="28" offsetYPC="64.5" widthPC="68.5" heightPC="18"
        fontSize="14" redraw="yes" lines="3" align="left">
        <script>
            if (null != error) {
                lines   = 11;
                bgColor = "<?=$colors->get('bgPanel2')?>";
                fgColor = "<?=$colors->get('fgError')?>";
                error;
            } else {
                lines   = 3;
                bgColor = "<?=$colors->get('bgPanel1')?>";
                if (step &gt;= <?=$steps?>) {
                    fgColor = "<?=$colors->get('fgNormal')?>";
                    "<?=$lang->msg('Installation successfully complete')?>";
                } else if (null != currentCmd) {
                    fgColor = "<?=$colors->get('bgFocus')?>";
                    "<?=$lang->msg('Please wait')?>!";
                } else {
                    fgColor = "<?=$colors->get('fgDark')?>";
                    getStringArrayAt(detailsArray, step);
                }
            }
        </script>
        <offsetYPC>
            <script>
                if (null != error) lines = 11; else lines = 3;
                Add(64.5, 18) - lines * 6;
            </script>
        </offsetYPC>
        <backgroundColor><script>bgColor;</script></backgroundColor>
        <foregroundColor><script>fgColor;</script></foregroundColor>
        <heightPC><script>lines * 6;</script></heightPC>
        <lines><script>lines;</script></lines>
    </text>


    <itemDisplay>
        <script>
            text = getItemInfo("<?=Item::TITLE?>");
            if (null != currentCmd) {
                itemBg = "<?=$colors->get('bgPanel2')?>";
                itemFg = "<?=$colors->get('bgPanel1')?>";
            } else if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
            } else {
                itemBg = "<?=$colors->get('bgPanel2')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
            }
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
            cornerRounding="10" fontSize="15" align="center">
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>text;</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<channel>
    <item>
        <title><?=$lang->msg('Cancel')?></title>
        <onClick>
            <script>
                postMessage("<?=$keys['return']?>");
                null;
            </script>
        </onClick>
    </item>
    <item>
        <title>
            <script>
                if (null != error) {
                    "<?=$lang->msg('Close')?>";
                } else if (step &gt;= <?=$steps?>) {
                    "<?=$lang->msg('Done')?>";
                } else {
                    "<?=$lang->msg('Next')?>";
                }
            </script>
        </title>
        <onClick>
            <script>
                if (null == currentCmd) {
                    if (null != error) {
                        postMessage("<?=$keys['return']?>");
                    } else if (step &gt;= <?=$steps?>) {
                        url = "<?=str_replace('&', '&amp;', $object->get(Channel::LINK))?>";
                        jumpToLink("rssLink");
                    } else {
                        showIdle();
                        currentState = "<?=$lang->msg('Processing')?>";
                        currentCmd = getStringArrayAt(linksArray, step);
                        setRefreshTime(700);
                    }
                    redrawDisplay();
                }
                null;
            </script>
        </onClick>
    </item>
</channel>

</rss>
