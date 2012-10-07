<?php

/*	------------------------------
	Ukraine online services 2	
	RSS update module ver.1.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");


	
class ua_rss_setup_const 
{
	const imageFocus 			= 	'ua_focus_category.bmp';
	const imageParentFocus 		= 	'ua_parent_focus_category.bmp';
	const imageUnFocus 			= 	'ua_unfocus_category.bmp';
	const background_image		=   'ua_setup_border.png';
}
class ua_rss_setup extends ua_rss_setup_const 
{

public function showDisplay($header1,$header2,$items)
{
	global $key_enter;
	global $key_left;
	global $key_right;
	global $key_return;
	global $ua_images_path;
	global $ua_setup_parser_filename;
	global $ua_path_link;
	global $ua_rss_update_filename;

	
?>	
<bookmark>dialog</bookmark>

<onEnter>
cancelIdle();
checkIndex = 0;
subMenuString = null;
subMenuValue = null;
<?php
foreach ($items as $name=>$val)
{
?>
subMenuString = pushBackStringArray(subMenuString, "<?=$name?>");
subMenuValue = pushBackStringArray(subMenuValue, "<?=$val?>");
<?php
}
?>
subMenuSize = <?=count($items)?>;
setFocusItemIndex(0);
redrawDisplay();
</onEnter>


<mediaDisplay name="onePartView"
 	
 viewAreaXPC=37
 viewAreaYPC=45
 viewAreaWidthPC=35
 viewAreaHeightPC=25
 sideColorRight=-1:-1:-1
 sideColorLeft=-1:-1:-1
 sideColorTop=-1:-1:-1
 sideColorBottom=-1:-1:-1 
 backgroundColor=0:0:0
 focusBorderColor=-1:-1:-1
 unFocusBorderColor=-1:-1:-1
 itemBackgroundColor=0:0:0
 showHeader="no"
 showDefaultInfo="no"
 itemPerPage=2
 itemWidthPC=30
 itemXPC=34
 itemHeightPC=30
 itemImageWidthPC=0
 itemImageHeightPC=0
 itemYPC=37
 itemImageXPC=0
 itemImageYPC=30
 idleImageXPC		="88"
 idleImageYPC		="80"
 idleImageWidthPC	="5"
 idleImageHeightPC	="8"
 >
<?php
	include("ua_rss_idle.inc.php");
?>
<backgroundDisplay>
	<image redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			<?=$ua_images_path.static::background_image?>
	</image>
</backgroundDisplay>
<text align="center" lines="1" offsetXPC="1" offsetYPC="3" widthPC="98" heightPC="20" fontSize="16" backgroundColor="0:0:0">
<?=$header1?></text>
<text align="center" lines="1" offsetXPC="1" offsetYPC="20" widthPC="98" heightPC="20" fontSize="16" backgroundColor="0:0:0">
<?=$header2?></text>

<itemDisplay>
	<text offsetXPC=0 offsetYPC=0 widthPC=95 heightPC=90 align=center fontSize=16 backgroundColor=-1:-1:-1 >
		<foregroundColor><script> fgcolor = "101:101:101"; if (getFocusItemIndex() == getQueryItemIndex()) { fgcolor = "255:255:255"; } fgcolor; </script></foregroundColor>
		<script> getStringArrayAt(subMenuString, getQueryItemIndex()); </script>
	</text>
	
	
</itemDisplay>

	
<onUserInput>
	handled = "false";
	userInput = currentUserInput();
	focusIndex = getFocusItemIndex();
	
	if ("<?= $key_enter ?>" == userInput) {
		data = getStringArrayAt(subMenuValue, focusIndex);
		if (data=="1") dlok = doModalRss("<?=$ua_path_link.$ua_rss_update_filename."?update=1"?>");
		if (dlok=="return") writeStringToFile("/tmp/env_return_message", "return");
		if (data=="2") writeStringToFile("/tmp/env_return_message", "return");
		postMessage("<?= $key_return ?>");
		handled = "true";
	}
	else if ("<?= $key_left ?>" == userInput || "<?= $key_right ?>" == userInput) {
		handled = "true";
	}
	handled;
</onUserInput>

</mediaDisplay>


<channel>
<title>Simple Menu Dialog</title>
<link>/tmp/usbmounts/sda1/scripts/uaonline/ua_rss_update.php</link>
<itemSize><script>subMenuSize;</script></itemSize>

</channel>
<?	
}

public function showRss($header1,$header2,$items)
	{
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay($header1,$header2,$items);
		
		echo '</rss>'.PHP_EOL;
	}
}




//-------------------------------
if ($_REQUEST["update"]=="1")
{
	shell_exec("wget -c -O /tmp/uaonline2.tar.bz2 ".$ua_update_url."/uaonline2.tar.bz2");
	shell_exec("mkdir -p /tmp/ua_get");
	shell_exec("tar -xjf /tmp/uaonline2.tar.bz2 -C /tmp/ua_get www/modules/uaonline2/ua_update.sh");
	shell_exec("rm -r /tmp/uaonline2.tar.bz2");
	if ($xtreamer) $param2='1'; else $param2='0';
	$res=shell_exec("/tmp/ua_get/www/modules/uaonline2/ua_update.sh ".$ua_path." ".$param2." ".$ua_update_url);
	//echo $res;
	shell_exec("rm -r /tmp/ua_get");
//	$ini = new TIniFileEx('/usr/local/etc/dvdplayer/ua_settings.conf');
//	$ini->write('paths','ua_standalone','1');
//	$ini->updateFile();
//	unset($ini);
	
	
	if(preg_match("/OK/", $res))
	{
		$ver_cur=file_get_contents($ua_path."ua_version");
		$view = new ua_rss_setup;
		$header1="Обновлено до";
		$header2="REV.".$ver_cur;
		$items=array("ОК"=>"2");
	} else
	{	
		$header1="ОШИБКА";
		$header2="ОБНОВЛЕНИЯ";
		$items=array("ОК"=>"0");
	}
} else
{
	shell_exec("wget -c -O /tmp/uaonline2.tar.bz2 ".$ua_update_url."/uaonline2.tar.bz2");
	shell_exec("mkdir -p /tmp/ua_get");
	shell_exec("tar -xjf /tmp/uaonline2.tar.bz2 -C /tmp/ua_get www/modules/uaonline2/ua_version");
	shell_exec("cp /tmp/ua_get/www/modules/uaonline2/ua_version /tmp");
	shell_exec("rm -r /tmp/ua_get");
	shell_exec("rm -r /tmp/uaonline2.tar.bz2");
	$ver_new=file_get_contents("/tmp/ua_version");
	shell_exec("rm -r /tmp/ua_version");
	$ver_cur=file_get_contents($ua_path."ua_version");
	if ($ver_cur<>$ver_new && $ver_new<>"")
	{
		$header1="Доступно обновление";
		$header2="REV.".$ver_new." Установить?";
		$items=array("ДА"=>"1","НЕТ"=>"0");
	} else
	{
		$header1="Обновлений нет";
		$header2="";
		$items=array("ОК"=>"0");
	}
}

$view = new ua_rss_setup;
$view->showRss($header1,$header2,$items);
exit;