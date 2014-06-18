{* View Area attributes *}
{function getViewArea x=0 y=0 w=100 h=100}
    viewAreaXPC="{$x + $config->get(marginLeft)}"
    viewAreaYPC="{$y + $config->get(marginTop)}"
    viewAreaWidthPC="{$w - ($config->get(marginLeft) + $config->get(marginRight))}"
    viewAreaHeightPC="{$h - ($config->get(marginTop) + $config->get(marginBottom))}"
{/function}

{* common mediaDisplay parameters *}
{function getMediaDisplayParams}
    showHeader="no"
    headerXPC="0"
    headerYPC="0"
    showDefaultInfo="no"

{*bottomYPC="86"*}

    sideTopHeightPC="0"
    sideColorTop="{$config->get(bgNormal)}"
    sideBottomHeightPC="0"
    sideColorBottom="{$config->get(bgNormal)}"
    sideLeftWidthPC="0"
    sideColorLeft="{$config->get(bgNormal)}"
    sideRightWidthPC="0"
    sideColorRight="{$config->get(bgNormal)}"

{*
    capXPC="0.1"
    capYPC="0.1"
    capWidthPC="0.1"
    capHeightPC="0.1"
    suffixXPC="0"
    suffixYPC="0"
    centerXPC="50"
    centerYPC="50"
*}
    imageFocus="null"
    imageParentFocus="null"

    backgroundColor="{$config->get(bgNormal)}"
    mainPartColor="0:0:0"
 {/function}

{* Idle image size and position *}
{function getIdleImageParams}
    idleImageXPC="4.5"
    idleImageYPC="76"
    idleImageWidthPC="10"
    idleImageHeightPC="16"
{/function}

{* Idle images *}
{function getIdleImages}
{for $i=1 to 8}
    <idleImage>{$config->get(cfg_resources_home,Runtime)}img/loading/loading_0{$i}.jpg</idleImage>
{/for}
{/function}

{* No idle images *}
{function disableIdleImages}
    <idleImage>{$config->get(cfg_resources_home,Runtime)}img/loading/empty.png</idleImage>
{/function}

{*
    link used by the jumpToLink command
    (must be outside of the mediaDisplay item)
*}
{function getRssLink tag=rssLink}
<{$tag}>
    <link><script>url;</script></link>
</{$tag}>
{/function}

{* calculate item colors for normal items and focus items *}
{function calculateItemColors}
    bgNormal  = "{$config->get(bgNormal)}";
    bgFocus   = "{$config->get(bgFocus)}";
    fgNormal  = "{$config->get(fgNormal)}";
    fgFocus   = "{$config->get(fgFocus)}";
    if (getFocusItemIndex() == getQueryItemIndex()) {
        itemBg = bgFocus;
        itemFg = fgFocus;
    } else {
        itemBg = bgNormal;
        itemFg = fgNormal;
    }
{/function}

{* calculate rating color based on its value *}
{function calculateRatingColors defColor='160:160:160'}
    if (null == ratingVal || 0 == ratingVal) {
        ratingColor = "{$defColor}";
    } else if (ratingVal &gt; 8.5) {
        ratingColor = "0:40:240";   /* 7. blue */
    } else if (ratingVal &gt; 8.0) {
        ratingColor = "0:160:220"; /* 6. light blue */
    } else if (ratingVal &gt; 7.5) {
        ratingColor = "0:128:0";   /* 5. green */
    } else if (ratingVal &gt; 7.0) {
        ratingColor = "60:220:0"; /* 4. salat */
    } else if (ratingVal &gt; 6.0) {
        ratingColor = "230:230:0"; /* 3. yellow */
    } else if (ratingVal &gt; 5.0) {
        ratingColor = "255:128:0"; /* 2. orange */
    } else {
        ratingColor = "220:30:0";   /* 1. red */
    }
{/function}


{* takes string representation of a key and returns its value as it was a variable *}
{function findKeyByName}
<findKeyByName>
    keyValue = null;
    if (keyName == null) {
    } else if (keyName == "key1") {
        keyValue = key1;
    } else if (keyName == "key2") {
        keyValue = key2;
    } else if (keyName == "key3") {
        keyValue = key3;
    } else if (keyName == "key4") {
        keyValue = key4;
    } else if (keyName == "key5") {
        keyValue = key5;
    } else if (keyName == "key6") {
        keyValue = key6;
    } else if (keyName == "key7") {
        keyValue = key7;
    } else if (keyName == "key8") {
        keyValue = key8;
    } else if (keyName == "key9") {
        keyValue = key9;
    } else if (keyName == "key10") {
        keyValue = key10;
    } else if (keyName == "keyVPlay") {
        keyValue = keyVPlay;
    } else if (keyName == "keyVStop") {
        keyValue = keyVStop;
    } else if (keyName == "keyFrWd") {
        keyValue = keyFrWd;
    } else if (keyName == "keyFfWd") {
        keyValue = keyFfWd;
    } else if (keyName == "keyPgUp") {
        keyValue = keyPgUp;
    } else if (keyName == "keyPgDn") {
        keyValue = keyPgDn;
    } else if (keyName == "keyUp") {
        keyValue = keyUp;
    } else if (keyName == "keyDown") {
        keyValue = keyDown;
    } else if (keyName == "keyLeft") {
        keyValue = keyLeft;
    } else if (keyName == "keyRight") {
        keyValue = keyRight;
    } else if (keyName == "keyEnter") {
        keyValue = keyEnter;
    } else if (keyName == "keyDisplay") {
        keyValue = keyDisplay;
    } else if (keyName == "keyReturn") {
        keyValue = keyReturn;
    } else if (keyName == "keyUser1") {
        keyValue = keyUser1;
    } else if (keyName == "keyUser2") {
        keyValue = keyUser2;
    } else if (keyName == "keyUser3") {
        keyValue = keyUser3;
    } else if (keyName == "keyUser4") {
        keyValue = keyUser4;
    }
</findKeyByName>
{/function}

<setEnvValue>
{if {$config->get(overrideEnviroment)} != "1" || {$config->get(envPath)} == ""}
	print("System::setEnv(" + envKey + "," +envValue+")");
	setEnv(envKey,envValue);
{else}
	print("TVonTop::setEnvValue(" + envKey + "," +envValue+")");
    writeStringToFile("{$config->get(envPath)}"+"tvontop_"+envKey,envValue);
{/if}
</setEnvValue>

<getEnvValue>
{if {$config->get(overrideEnviroment)} != "1" || {$config->get(envPath)} == ""}
	envValue = getEnv(envKey);
	print("System::getEnvValue(" + envKey + ") =>" +envValue);
{else}
    envValue = readStringFromFile("{$config->get(envPath)}"+"tvontop_"+envKey);
    print("TVonTop::getEnvValue(" + envKey + ") =>" +envValue);
{/if}
</getEnvValue>