{function displayHotKeys dynamic=false}
    {$hotKeyBalloons = $config->get(hotKeyBalloons)}
    {if isset($hotKeyBalloons) && $hotKeyBalloons == true}
        {call displayHotKeysBalloons dynamic=$dynamic}
    {else}
        {if $dynamic === true}
            {call displayDynamicHotKeysLine}
        {else}
            {call displayStaticHotKeysLine}
        {/if}
    {/if}
{/function}

{function displayStaticHotKeysLine}
    {if {$hotKeys|@sizeof} gt 0}
        {$width  = 24}
        {$xPos   = 100 - {$hotKeys|@sizeof} * $width}
        {$yPos   = 96}

        <text  offsetXPC="{$xPos-3}" offsetYPC="{$yPos}" widthPC="{103 - $xPos}" heightPC="4" backgroundColor="{$config->get(bgKeys)}" />
        <image offsetXPC="{$xPos-3}" offsetYPC="{$yPos}" widthPC="3.1" heightPC="4">
            {$config->get(cfg_resources_home,Runtime)}img/keys/KeyLineEnding.png
        </image>

        {foreach $hotKeys as $hotKey}
            {call displaySingleHotKey i=$hotKey@key bgColor=$config->get(bgKeys)}
            {$xPos = $xPos + $width}
        {/foreach}
    {/if}
{/function}

{function displayDynamicHotKeysLine}
    {$width  = 24}
    {$xPos   = 103}
    {$yPos   = 96}
    <script>
        if (null != getItemInfo("key_url3")) {
            xPos = 100 - 4 * {$width};
        } else if (null != getItemInfo("key_url2")) {
            xPos = 100 - 3 * {$width};
        } else if (null != getItemInfo("key_url1")) {
            xPos = 100 - 2 * {$width};
        } else if (null != getItemInfo("key_url0")) {
            xPos = 100 - 1 * {$width};
        } else {
            xPos = 103;
        }
        print("xPos: ", xPos);
    </script>
    <text  redraw="yes" offsetXPC="0"  offsetYPC="{$yPos}" widthPC="100" heightPC="4" backgroundColor="{$config->get(bgNormal)}" />
    <text  redraw="yes" offsetYPC="{$yPos}" heightPC="4" backgroundColor="{$config->get(bgKeys)}">
        <widthPC><script>103-xPos;</script></widthPC>
        <offsetXPC><script>xPos-3;</script></offsetXPC>
        <script>"";</script>
    </text>
    <image redraw="yes" offsetYPC="{$yPos}" widthPC="3"   heightPC="4">
        <offsetXPC><script>xPos-3;</script></offsetXPC>
        <script>"{$config->get(cfg_resources_home,Runtime)}img/keys/KeyLineEnding.png";</script>
    </image>
    {foreach $hotKeys as $hotKey}
        {call displaySingleHotKey i=$hotKey@key bgColor=$config->get(bgKeys) dynamic=true}
    {/foreach}
{/function}

{function displayHotKeysBalloons dynamic=false}
    {if $dynamic === true}
        <script>xPos = 2;</script>
    {/if}
    {if {$hotKeys|@sizeof} gt 0}
        {$width = 24.5}
        {$yPos  = 95.5}
        {for $xPos = 2 to 100 step $width}
            <text  offsetXPC="{$xPos}"     offsetYPC="{$yPos}"     widthPC="23"   heightPC="4"
                backgroundColor="{$config->get(bgKeys)}" cornerRounding="10" />
            <text  offsetXPC="{$xPos+0.1}" offsetYPC="{$yPos+0.1}" widthPC="22.77" heightPC="3.7"
                backgroundColor="{$config->get(bgNormal)}" cornerRounding="10" />
        {/for}
        {$xPos = 2}
        {foreach $hotKeys as $hotKey}
            {call displaySingleHotKey i=$hotKey@key bgColor="0:0:0" dynamic=$dynamic}
            {$xPos = $xPos + $width}
        {/foreach}
    {/if}
{/function}

{function displaySingleHotKey dynamic=false}
    {if $dynamic === true}
        {$redraw      = "redraw=\"yes\""}
        {$imageScript = "<offsetXPC><script>xPos + {$i * $width} + 0.5;</script></offsetXPC>"}
        {$textScript  = "<offsetXPC><script>xPos + {$i * $width} + 3.5;</script></offsetXPC>"}
    {else}
        {$redraw      = ""}
        {$imageScript = ""}
        {$textScript  = ""}
    {/if}
    {if ! isset($hotKey["icon"])}
        {$hotKey["icon"] = "{$config->get(cfg_resources_home,Runtime)}img/keys/{$config->get(platform)}/keyUser{$i + 1}.png"}
    {/if}
    {if ! isset($hotKey["key"])}
        {$hotKey["key"] = "keyUser{$hotKey@key + 1}"}
    {/if}

    <!-- Dynamic key {$hotKey["key"]} -->
    <image {$redraw} offsetXPC="{$xPos+0.5}" offsetYPC="{$yPos+0.4}" widthPC="3"  heightPC="3.2">{$hotKey["icon"]}
        {$imageScript}
    </image>
    <text  {$redraw} offsetXPC="{$xPos+3.5}" offsetYPC="{$yPos+0.3}" widthPC="19" heightPC="3.4"
        fontSize="13" foregroundColor="170:170:170" backgroundColor="{$bgColor}">{$hotKey["title"]}
        {$textScript}
    </text>
{/function}




{function onHotKeyPress}
    {foreach $hotKeys as $hotKey}
{if isset($hotKey["url"])}
        {if ! isset($hotKey["key"])}
            {$hotKey["key"] = "keyUser{$hotKey@key + 1}"}
        {/if}

        else if ( key == {$hotKey["key"]} ) {
            url = "{$hotKey["url"]}";
            print("goto: ", url);
            jumpToLink("rssLink");
            res = "true";
        }
{/if}
    {/foreach}
{/function}
