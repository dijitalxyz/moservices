<?php

$file = $mos.'/iconmenu/iconmenu.conf';
$xml   = simplexml_load_file($file);

$icon_menus = array('servicesHome','servicesDock','servicesGuide' );

$icon_services = array(
	'DTV'		=> 'Home_DTV.fsp',
	'GBrowser'	=> 'Home_FileManager.fsp',
	'GMovie'	=> 'Home_Movie.fsp',
	'GMusic'	=> 'Home_Music.fsp',
	'GPicture'	=> 'Home_Photo.fsp',
	'Favorites'	=> 'Home_Favorites.fsp',
	'moMenu'	=> 'moMenu.fsp',
	'mediaCenter'	=> 'Home_IPTV.fsp',
	'mediaCenter2'	=> 'Home_MediaCenter.fsp',
	'Transmission'	=> 'trans.fsp',
	'Aria'		=> 'aria.fsp',
	'WebKit'	=> 'Home_Webkit.fsp',
	'OnlineMedia'	=> 'OnlineMedia.fsp',
	'Setup'		=> 'Home_Setup.fsp',
	'Android'	=> 'Home_Android.fsp',
	'AndroidBrowser'=> 'Home_Android_WebBrowser.fsp',
	'PowerOff'	=> 'Home_Power.fsp',
);
//
// ====================================
function iconmenu_geticon_content()
{
global $mos;
global $icon_services;

	if( ! isset( $_REQUEST['id'] )) return;
	$id = $_REQUEST['id'];

	header("Content-Type: image/png");
	readfile( $mos .'/iconmenu/images/' .$icon_services[ $id ] );
}
//
// ====================================
function iconmenu_sets_actions( $act, $log )
{
global $xml;
global $file;

	if( $act != 'set' ) return;

	if( isset( $_REQUEST['bginfo'] )) $xml->bginfo = $_REQUEST['bginfo'];

	if( isset( $_REQUEST['hddinfo'] )) $xml->hddinfo = $_REQUEST['hddinfo']; 

	if( isset( $_REQUEST['fw'] )) $xml->fw = $_REQUEST['fw'];

	$xml->external_ip  = isset( $_REQUEST['external_ip']) ? "yes" : "no"; 

	$xml->webkit_ipad  = isset( $_REQUEST['webkit_ipad']) ? "yes" : "no"; 
		
		
// weather
	if( isset( $_REQUEST['currentcity'] )) $xml->weather->currentcity = $_REQUEST['currentcity'];

	if( isset( $_REQUEST['unit'] )) $xml->weather->unit = $_REQUEST['unit'];

	if( isset( $_REQUEST['wlanguage'] )) $xml->weather->language = $_REQUEST['wlanguage'];

	if( isset( $_REQUEST['wcolor'] )) $xml->weather->color = $_REQUEST['wcolor'];

// time
	if( isset( $_REQUEST['timeinfo_color'] )) $xml->timeinfo->color = $_REQUEST['timeinfo_color'];

	if( isset( $_REQUEST['timeinfo_size'] )) $xml->timeinfo->size = $_REQUEST['timeinfo_size'];

	if( isset( $_REQUEST['timezone'] )) $xml->timeinfo->timezone = $_REQUEST['timezone'];

// scrinsaver
	if( isset( $_REQUEST['ss_color'] )) $xml->screensaver->color = $_REQUEST['ss_color'];

	if( isset( $_REQUEST['ss_size'] )) $xml->screensaver->size = $_REQUEST['ss_size'];

	if( isset( $_REQUEST['ss_idle'] )) $xml->screensaver->idle = $_REQUEST['ss_idle'];

// menus
global $icon_menus;
global $icon_services;

	if( isset( $_REQUEST['lhcolor'] )) $xml->servicesHome->color = $_REQUEST['lhcolor'];

	foreach( $icon_menus as $m )
	 foreach( $icon_services as $id => $item)
	 {
			$el = $xml->{ $m }->{ $id };

			if( $el->count() > 0 )
			{
				$el[disable]  = isset( $_REQUEST[ $m.$id ]) ? "no" : "yes";
			}
	}

	$xml->asXML($file);
}

// ------------------------------------
function iconmenu_sets_head()
{
?>
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">

<style>
table.set_list
{
	padding:0;
	margin:0;
	white-space:nowrap;
}
.set_list th
{
	background-color: #d0d0d0;
	font-weight: bold;
	text-align:left;
}

.set_list td.set_delim
{
	border-top:1px solid #ccc;
}

.set_list div.set_img
{
	float:left;
}

div.set_card
{
	position:relative;
	display:block;
	float:left;
	padding:0px;
	margin:0px;

	background-color:white;

	border:1px solid #ccc;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;

}

div.menus_frame
{
	position:relative;
	padding:0;
	margin:4px;

	border:1px solid #ccc;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
}
div.menus_topic
{
	position: relative;
	background: #D0D0D0;
	padding:4px;
	margin :0;
	font-weight: bold;
	border:0;
}

div.menus_list
{
	display:table;
	position: relative;
	padding:4px;
	margin :0;
}
div.menus_icon
{
	display:table-cell;
	float:left;
	position: relative;
	padding:4px;
	margin :0;

	text-align:center;
}
</style>


<?php

}

// ------------------------------------
function iconmenu_sets_body()
{
global $xml;

$hddinfos = array( 'total' );

	if ($dh = opendir( '/tmp/ramfs/volumes' ) )
	{
		while (( $f = readdir($dh) ) !== false )
		 if( is_link( '/tmp/ramfs/volumes/'.$f ))
		  $hddinfos[] = '/tmp/ramfs/volumes/'.$f;

		closedir($dh);
	}

$fws = array( 'other','mele','inext' );

$WUnits = array(
	'0' => '&nbsp;&nbsp;&deg;C&nbsp;&nbsp;&nbsp;',
	'1' => '&nbsp;&nbsp;&deg;F&nbsp;&nbsp;&nbsp;',
);

$WLangs = array(
	"en-us" => 'English (US)',
	"ru" => 'Русский',
	"es" => 'Espa&#241;ol',
	"fr" => 'Fran&#231;ais',
	"da" => 'Dansk',
	"pt" => 'Portugu&#234;s',
	"nl" => 'Nederlands',
	"no" => 'Norsk',
	"it" => 'Italiano',
	"de" => 'Deutsch',
	"sv" => 'Svenska',
	"fi" => 'Suomi',
	"zh-hk" => '中文 (HK)',
	"zh-cn" => '中文 (SIM)',
	"zh-tw" => '中文 (Taiwan)',
	"es-ar" => 'Espa&#241;ol (Argentina)',
	"es-mx" => 'Espa&#241;ol (Latin America)',
	"sk" => 'Slovenčinu',
	"ro" => 'Romana',
	"cs" => 'Čeština',
	"hu" => 'Magyar',
	"pl" => 'Polski',
	"ca" => 'Catal&#224;',
	"pt-br" => 'Portugu&#234;s (Brazil)',
	"hi" => 'हिन्दी',
	"ar" => 'عربي',
	"el" => 'Ελληνικά',
	"en-gb" => 'English (UK)',
	"ja" => '日本語',
	"ko" => '한국어',
	"tr" => 'T&#220;RK&#199;E',
	"fr-ca" => 'Fran&#231;ais (Canada)',
	"he" => 'עברית',
	"sl" => 'Slovenski',
	"uk" => 'Українське',
	"id" => 'Bahasa Indonesia',
	"bg" => 'Български',
	"et" => 'Eesti keeles',
	"hr" => 'Hrvatski',
	"kk" => 'Қазақша',
	"lt" => 'Lietuvių',
	"lv" => 'Latviski',
	"mk" => 'Македонски',
	"ms" => 'Bahasa Melayu',
	"tl" => 'Tagalog',
	"sr" => 'Srpski',
	"th" => 'ไทย',
	"vi" => 'Tiếng Việt',
);

$timezones = array( 'default',
	'-12','-11.5','-11','-10.5','-10','-9.5','-9','-8.5','-8','-7.5','-7','-6.5','-6','-5.5','-5','-4.5','-4','-3.5','-3','-2.5','-2','-1.5','-1','-0.5','0',
	'+0.5','+1','+1.5','+2','+2.5','+3','+3.5','+4','+4.5','+5','+5.5','+6','+6.5','+7','+7.5','+8','+8.5','+9','+9.5','+10','+10.5','+11','+11.5',
);
				  
?>
<div id="container">
<h3><?= getMsg( 'iconmenuSettings' ) ?></h3>

<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="?page=iconmenu_sets&act=set" method="post">

<tr>
<td><?= getMsg( 'iconBgInfo') ?></td>
<td width="100%"><input name="bginfo" type="text" size="60" value="<?= $xml->bginfo; ?>" /></td>
</tr>


<tr><td><?= getMsg( 'iconHddInfo') ?></td>
<td><select name="hddinfo" size=1>
<?php
	foreach( $hddinfos as $info)
	{
		if($info== $xml->hddinfo) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$info\"$sel>$info</option>\n";
	}

?>
</select></td></tr>

<tr><td><?= getMsg( 'iconFW') ?></td>
<td><select name="fw" size=1>
<?php
	foreach( $fws as $info)
	{
		if($info== $xml->fw) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$info\"$sel>$info</option>\n";
	}

?>
</select></td></tr>

<tr><td><?= getMsg( 'iconExtIP') ?></td>
<td><?php

	if( $xml->external_ip == "yes" ) $sel = ' checked';
	else $sel = '';
	echo '<input name="external_ip" type="checkbox"'.$sel.' />';

?></td></tr>


<tr><td><?= getMsg( 'iconWebkitIpad') ?></td>
<td><?php
	if( $xml->webkit_ipad == "yes" ) $sel = ' checked';
	else $sel = '';
	echo '<input name="webkit_ipad" type="checkbox"'.$sel.' />';
?></td></tr>

<tr><th colspan="2"><?= getMsg( 'iconWeather' ) ?></th></tr>
<tr><td><?= getMsg( 'iconCity') ?></td>
<td>
<div class="set_img"><input name="currentcity" type="text" size="30" value="<?= $xml->weather->currentcity; ?>" /></div>
<div class="set_img"><a href="http://www.accuweather.com" target="_blank">&nbsp;&nbsp;<img src="/modules/iconmenu/AccuWeather-icon.png" height="20px"/></a></div>
</td></tr>

<tr><td><?= getMsg( 'iconUnit') ?></td>
<td><select name="unit" size=1>
<?php
	foreach( $WUnits as $id => $info)
	{
		if( $id == $xml->weather->unit ) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$id\"$sel>$info</option>\n";
	}

?>
</select></td></tr>

<tr><td><?= getMsg( 'iconWLang') ?></td>
<td><select name="wlanguage" size=1>
<?php
	foreach( $WLangs as $id => $info)
	{
		if( $id == $xml->weather->language ) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$id\"$sel>$info</option>\n";
	}

?>
</select></td></tr>

<tr><td><?= getMsg( 'iconWColor') ?></td>
<td>
<div class="set_img"><input name="wcolor" type="text" size="30" value="<?= $xml->weather->color; ?>" /></div>
<div class="set_img">
<a href="#" onclick="window.open('/modules/iconmenu/colpic.html','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=1,Width=800,Height=428');">&nbsp;&nbsp;<img src="/modules/iconmenu/color-wheel-icon.png" height="20px"/></a></div>
</td></tr>

<tr><th colspan="2"><?= getMsg( 'iconTime' ) ?></th></tr>
<tr><td><?= getMsg( 'iconTimeColor') ?></td>
<td>
<div class="set_img"><input name="timeinfo_color" type="text" size="30" value="<?= $xml->timeinfo->color; ?>" /></div>
<a href="#" onclick="window.open('/modules/iconmenu/colpic.html','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=1,Width=800,Height=428');">&nbsp;&nbsp;<img src="/modules/iconmenu/color-wheel-icon.png" height="20px"/></a></div>
</td></tr>

<tr><td><?= getMsg( 'iconTimeSize') ?></td>
<td><input name="timeinfo_size" type="text" size="30" value="<?= $xml->timeinfo->size; ?>" /></td>
</tr>

<tr><td><?= getMsg( 'iconTimeZone') ?></td>
<td><select name="timezone" size=1>
<?php
	foreach( $timezones as $info)
	{
		if($info== $xml->timeinfo->timezone) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$info\"$sel>$info</option>\n";
	}

?>
</select></td></tr>

<tr><th colspan="2"><?= getMsg( 'iconSS' ) ?></th></tr>

<tr><td><?= getMsg( 'iconSSIdle') ?></td>
<td><input name="ss_idle" type="text" size="30" value="<?= $xml->screensaver->idle; ?>" /><?= getMsg( 'iconSSOff' ) ?></td>
</tr>

<tr><td><?= getMsg( 'iconSSColor') ?></td>
<td>
<div class="set_img"><input name="ss_color" type="text" size="30" value="<?= $xml->screensaver->color; ?>" /></div>
<a href="#" onclick="window.open('/modules/iconmenu/colpic.html','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=1,Width=800,Height=428');">&nbsp;&nbsp;<img src="/modules/iconmenu/color-wheel-icon.png" height="20px"/></a></div>
</td></tr>

<tr><td><?= getMsg( 'iconSSSize') ?></td>
<td><input name="ss_size" type="text" size="30" value="<?= $xml->screensaver->size; ?>" /></td>
</tr>

<?php

global $mos_url;
global $icon_menus;
global $icon_services;


	foreach( $icon_menus as $m )
	{
?>
<tr><th colspan="2"><?= getMsg( 'icon_'.$m ) ?></th></tr>
<?php
		if( $m == 'servicesHome' )
		{

?>
<tr><td><?= getMsg( 'iconLColor') ?></td>
<td>
<div class="set_img"><input name="lhcolor" type="text" size="30" value="<?= $xml->servicesHome->color; ?>" /></div>
<a href="#" onclick="window.open('/modules/iconmenu/colpic.html','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=1,Width=800,Height=428');">&nbsp;&nbsp;<img src="/modules/iconmenu/color-wheel-icon.png" height="20px"/></a></div>
</td></tr>


</tr>
<?php
		}

?>
<tr><td colspan="2"><div class="menus_list">
<?php
		foreach( $icon_services as $id => $item )
		{
			$el = $xml->{ $m }->{ $id };

			if( $el->count() > 0 )
			{

?>
<div class="menus_icon"><img src="<?= $mos_url.'?page=iconmenu_geticon&id='.$id ?>" width="60"/><br/><?= getMsg( 'icon_'.$id ) ?><br/>
<?php
				if( $el[disable] != "yes" ) $sel = ' checked';
				else $sel = '';
				echo '<input name="'. $m . $id .'" type="checkbox"'.$sel.' /></div>'.PHP_EOL;
			}
		}

?>
</div></td></tr>
<?php
	}

?>
</tr>

<tr><td colspan="2" class="set_delim">
<button class="buttons" type="submit" style="text-align=right"><?= getMsg( 'coreCmSave') ?></button>
</td></tr>

</form></table>
</div>
</div>

<?php
}
?>