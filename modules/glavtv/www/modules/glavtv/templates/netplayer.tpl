<?='<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dc="http://purl.org/dc/elements/1.1/">

<?
#global $cfg, $lang, $colors, $srv, $req;
global $description, $image;


#print_r( $_GET );
#print_r( $cfg );
#print_r( $lang );
#print_r( $srv );
#print_r( $req );
#print_r( $object );

print getRssChannel($object);

function getRssChannel($channel, $spaces = '') {
    global $description, $image;

    if( !empty($_GET['test']) ) { return logintest(); }

    $str = $spaces . '<channel>' . PHP_EOL;
    $tab = '    ';
    $tab2 = '    ';
    $hasitems = true;
    $items = $channel->getItems();
    if ( empty ( $items  ) ){
        $str .= $spaces . $tab2 . '<item>' . PHP_EOL;
        $tab .= $tab;
    }
    foreach ($channel->getParams() as $name => $value) {
        if ('' != $value) {

            if ( "description" == $name ) $description = $value;
            if ( "image" == $name ) $image = $value;

            $str .= $spaces . $tab . getRssParam($name, $value) . PHP_EOL;
        }
    }
    if ( empty ( $items  ) ) $str .= $spaces . $tab2 . '</item>' . PHP_EOL;


    foreach ($items  as $item) {
        if (null != $item) {
            $str .= getRssItem($item, $spaces . $tab) . PHP_EOL;
        }
    }
    $str .= $spaces . '</channel>' . PHP_EOL;
    return $str;
}

function getRssItem($item, $spaces = '') {
    global $description, $image, $req;
    $str = $spaces . '<item>' . PHP_EOL;
    $tab = '    ';
    $hasimage = false;
    $hasdescription = false;
    $title = "";
    foreach ($item->getParams() as $name => $value) {
        if ('' != $value) {
            if( Channel::IMAGE == $name ) $hasimage = true;
            if( Item::DESCRIPTION == $name ){ $hasdescription = true; $value = prepareDescription( $value ); }
            if( '' != $name && ( Item::TITLE == $name || "day" == $name || "month" == $name || "time" == $name || "timelength" == $name ) ){
                if( "channelHistory" != $req || Item::TITLE == $name )
                $title .= ( "" == $title ) ? $value :  " - " . $value;
            }else{
                $str .= $spaces . $tab . getRssParam($name, $value) . PHP_EOL;
            }
        }
    }
    $str .= $spaces . $tab . getRssParam(Item::TITLE, $title) . PHP_EOL;
    if( $image && !$hasimage ) $str .= $spaces . $tab . getRssParam(Item::THUMBNAIL, $image) . PHP_EOL;
    if( $description && !$hasdescription ){
        $str .= $spaces . $tab . getRssParam(Item::DESCRIPTION, prepareDescription( $description ) ) . PHP_EOL;
     }

    $str .= $spaces . "</item>" . PHP_EOL;
    return $str;
}

function getRssParam($name, $value) {
    switch ($name) {
        case Channel::IMAGE : // fall down
        case Item::ENCLOSURE: // fall down
        case Item::THUMBNAIL: return "<$name url=\"$value\" />";
        case Item::VIDEOSCRIPT: // fall down
        case Item::LINK     : return  ( "mms" == substr($value, 0, 3 ) ) ? "<enclosure url=\"$value\" type=\"video\" />" : "<enclosure url=\"$value\" type=\"text/xml\" />";
    }
    return "<$name>$value</$name>";
}


function prepareDescription( $description ){

    $aDescription = explode("\n", $description );
    array_unshift( $aDescription,  array_shift($aDescription), array_pop($aDescription) );
    $description = implode ( "\n", $aDescription);

    $search = array(  "Производство:"
                     ,"Режиссер:"
                     ,"В ролях:"
                     ,"Ведущие:"
                     ,"Ведущий:"
                     ,"\n"
                   );
    $replace = array(  '<b style="color: #BB5522;"><strong>Производство:</strong></b>'
                      ,'<b style="color: #BB5522;"><strong>Режиссер:</strong></b>'
                      ,'<b style="color: #BB5522;"><strong>В ролях:</strong></b>'
                      ,'<b style="color: #BB5522;"><strong>Ведущие:</strong></b>'
                      ,'<b style="color: #BB5522;"><strong>Ведущий:</strong></b>'
                      ,'<br/>'
                    );
    $description = str_replace($search, $replace, $description );

    return "<![CDATA[" . $description . "]]>";

}

function logintest()
{
return '

<channel>
    <title>Login</title>
    <description>Login to eTV.net provider</description>
    <item>
        <title><![CDATA[<b style="color: #99FFFF;">Вход</strong></b>]]></title>
        <description><![CDATA[Вход Регистрация в eTVnet]]></description>
        <enclosure url="http://192.168.178.11/media/sda1/EasyTT/?srv=etv&forcetpl=netplayer&req=auth&username=%USERNAME%&password=%PASSWORD%" type="login" />
        <media:thumbnail url="http://static.etvnet.com/images/logo.png" />

    </item>

</channel>

';
}


?>
</rss>
