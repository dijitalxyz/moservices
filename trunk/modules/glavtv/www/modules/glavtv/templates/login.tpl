<? require_once 'rss-funcs.inc'  ?>
<? require_once 'rss-design.inc' ?>
<? addCommonInit() ?>
<? addRssLink() ?>

<onEnter>
    username = "<?=$object->get("username")?>";
    password = "<?=$object->get("password")?>";
</onEnter>

<mediaDisplay name="photoView"
<? showMediaDisplayParams(null, $colors->get('bgPanel1')) ?>
<? showViewAreaParams(15, 15, 70, 66) ?>
<? showIdleImageParams() ?>

    rowCount="3"
    columnCount="1"

    itemOffsetXPC="54"
    itemOffsetYPC="24"
    itemWidthPC="38"
    itemHeightPC="20"

    itemGapXPC="0"
    itemGapYPC="0"
    >
<? addIdleImages() ?>
<? addBoundingFrame() ?>

    <image offsetXPC="3" offsetYPC="35" widthPC="20"
        heightPC="37"><?=$cfg->get('resource_disk') . 'img/keys.png'?></image>

    <!-- title -->
    <text offsetXPC="3" offsetYPC="5" widthPC="94" heightPC="16"
        foregroundColor="<?=$colors->get('fgDark')?>"
        align="center" fontSize="15"><?=$object->get(Channel::TITLE)?></text>

    <!-- message -->
    <text offsetXPC="3" offsetYPC="82" widthPC="94" heightPC="14"
        foregroundColor="<?=$colors->get('fgError')?>"
        align="center" fontSize="15"><?=htmlspecialchars($object->get(Channel::DESCRIPTION))?></text>

    <!-- username and password -->
    <text offsetXPC="25" offsetYPC="24" widthPC="28" heightPC="15"
        fontSize="15"><?=$lang->msg('User name')?></text>
    <text offsetXPC="25" offsetYPC="44" widthPC="28" heightPC="15"
        fontSize="15"><?=$lang->msg('Password')?></text>


    <itemDisplay>
        <script>
            text = getItemInfo("<?=Item::TITLE?>");
            if (getFocusItemIndex() == getQueryItemIndex()) {
                itemBg = "<?=$colors->get('bgFocus')?>";
                itemFg = "<?=$colors->get('fgFocus')?>";
                if (getQueryItemIndex() &lt;= 1) {
                    text += "_";
                }
            } else {
                itemBg = "<?=$colors->get('bgPanel2')?>";
                itemFg = "<?=$colors->get('fgNormal')?>";
                if (getQueryItemIndex() &lt;= 1) {
                    itemBg = "<?=$colors->get('bgFocusInactive')?>";
                }
            }
            if (getQueryItemIndex() &gt; 1) {
                align = "center";
            } else {
                align = "left";
            }
        </script>

        <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="70"
            cornerRounding="10" fontSize="15">
            <align><script>align;</script></align>
            <backgroundColor><script>itemBg;</script></backgroundColor>
            <foregroundColor><script>itemFg;</script></foregroundColor>
            <script>text;</script>
        </text>
    </itemDisplay>

</mediaDisplay>

<channel>
    <item>
        <title><script>username;</script></title>
        <onClick>
            modalUrl = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=keyboard";
            ret = doModalRss(modalUrl);
            /* ret = getInput(); */
            if (null != ret &amp;&amp; "" != ret) {
                username = ret;
            }
            redrawDisplay();
        </onClick>
        </item>
    <item>
        <title><script>if (null == password || "" == password) ""; else "****";</script></title>
        <onClick>
            modalUrl = "<?=$cfg->get('home_url')?>?srv=utils&amp;req=keyboard&amp;nohist=1";
            ret = doModalRss(modalUrl);
            /* ret = getInput(); */
            if (null != ret &amp;&amp; "" != ret) {
                password = ret;
            }
            redrawDisplay();
        </onClick>
    </item>
    <item>
        <title><?=$lang->msg('Enter')?></title>
        <onClick>
            url  = "<?=$this->cfg->get('service_url')?>&amp;req=auth";
            url += "&amp;username=" + urlEncode(username);
            url += "&amp;password=" + urlEncode(password);
            jumpToLink("rssLink");
            redrawDisplay();
        </onClick>
    </item>
</channel>

</rss>
