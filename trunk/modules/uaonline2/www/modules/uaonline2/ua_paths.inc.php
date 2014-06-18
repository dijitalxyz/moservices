<?php
/*	------------------------------
	Ukraine online services 	
	configuration manager module v2.2
	------------------------------
	Created by Sashunya 2014
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

// Пути разные читаем их из файла конфигурации
// если файла нет, то он создастся автоматом с путями по-умолчанию
error_reporting(0);
$tmpPath=dirname(__FILE__);
// путь для хранения настроек и избранного
$confs_path="/usr/local/etc/dvdplayer/";
include ($tmpPath."/ua_ini.inc.php");
$ini = new TIniFileEx($confs_path.'ua_settings.conf');
$ua_path = $tmpPath."/";
preg_match("/[a-z0-9.\/]*(?=\/)/",$_SERVER["REQUEST_URI"],$out);
$ua_path_link = "http://".$_SERVER['HTTP_HOST'].$out[0]."/";
$ua_path_link2 = $out[0]."/";
$ua_path_link3 = "http://".$_SERVER["SERVER_ADDR"]."/";
$ua_images_foldername =  $ini->read('paths','ua_images_foldername',"images/");
$ua_tmp_path = $ini->read('paths','ua_tmp_path','/tmp/');
$ua_images_path = $ua_path.$ua_images_foldername;
$ua_full_description = 'ua_rss_full_description.php';
// путь к WGET
$ua_wget_path = $ini->read('paths','ua_wget_path','');
// линк на картинки категорий из репозитория
$ua_images_category_path = "http://www.moservices.org/modules/uaonline2/images/";
$sites_logos_filename = $tmpPath."/ua_sites.conf";
//----------------------------------------------
// все для апдейта
$ua_update_url = 'http://www.moservices.org/mos3';
$ua_rss_update_filename = 'ua_rss_update.php';
if (file_exists($confs_path.'ua_standalone.conf'))
	$ua_update_standalone = true;
	else
	$ua_update_standalone = false;
//----------------------------------------------
$ua_player_parser_filename = 'ua_player_parser.php';
$exua_rss_cat_filename = 'ua_exua_rss_cat.php';
$exua_rss_list_filename = 'ua_exua_rss_list.php';
$exua_rss_link_filename = 'ua_exua_rss_link.php';
$exua_parser_filename = 'ua_exua_parser.php';
$uakino_parser_filename = 'ua_uakino_parser.php';
$uakino_rss_cat_filename = 'ua_uakino_rss_cat.php';
$uakino_rss_list_filename = 'ua_uakino_rss_list.php';
$uakino_rss_link_filename = 'ua_uakino_rss_link.php';

$fsua_parser_filename = 'ua_fsua_parser.php';
$fsua_rss_cat_filename = 'ua_fsua_rss_cat.php';
$fsua_rss_list_filename = 'ua_fsua_rss_list.php';
$fsua_rss_link_filename = 'ua_fsua_rss_link.php';


$ua_rss_keyboard_filename = 'ua_keyboard_rss.php';
$ua_rss_favorites_filename = 'ua_favorites_rss.php';
$ua_rss_history_filename = 'ua_rss_history.php';
$ua_favorites_filename = $confs_path.'ua_favorites.conf';
if (!is_dir($confs_path."ua_history")) mkdir($confs_path."ua_history",0777,true);
$ua_history_main_filename = $confs_path.'ua_history/ua_history_main.conf';
$history_length = $ini->read('other','history_length',"20");
$ua_rss_setup_filename = 'ua_rss_setup.php';
$ua_setup_parser_filename = 'ua_setup_parser.php';
$tmpfilename = 'ua_temp.tmp';
$tmpdescr = $ua_tmp_path.'ua_full_descr.tmp';
$goto_time = $ua_tmp_path.'ua_goto.tmp';

$tmp = $ua_tmp_path.$tmpfilename;
$tmp_down = $ua_tmp_path."ua_down.tmp";
$d_path = $ini->read('downloads','path','/tmp/ramfs/volumes/C:/');
$auto_path = $ini->read('downloads','auto_path','1');
$built_in_keyb = $ini->read('other','built_in_keyb','0');
$position = $ini->read('other','save_position','0');
$screensaver = $ini->read('other','screen_saver','0');
$no_device=false;
if ($auto_path == "1") 
{
	$download_path = down_path("name"); 
	if ($download_path =="none") 
	{
		$download_path = $d_path;
		$no_device=true;
	}
	
}
else $download_path = $d_path;

$download_log_filename = 'downlist';

$ua_rss_download_filename = 'ua_download_rss.php';
$ua_download_parser_filename = 'ua_download_parser.php';

$exua_quality = $ini->read('exua_setting','quality','1');
$exua_region = $ini->read('exua_setting','region','0');
$exua_lang = $ini->read('exua_setting','language','0');
$exua_posters = $ini->read('exua_setting','posters','1');
$ua_sort = $ini->read('uakino','sort','date');
$uakino_decode = $ini->read('uakino','decode_strings','1');
$fsua_sort = $ini->read('fsua','sort','rating');


$player_style=$ini->read('player','style','0');

// checking player for setting keys /sbin/www - for Xtreamer else other Realtek
// if HDP_R1_R3=1 then using keys for HDP_R1/R3 firmware
$hdpr1=$ini->read('keys','HDP_R1_R3','0');
if ($hdpr1=='1')
{
	$key_ffwd='video_quick_stop';
	$key_frwd='video_play';
	$key_play='video_completed';
	$key_pause='video_stop';
	$key_up='up';
	$key_down='down';
	$key_left='left';
	$key_right='right';
	$key_return='return';
	$key_enter='enter';
	$key_display='video_frwd';
	$key_pageup='pageup';
	$key_pagedown='pagedown';
}
else
{
if( is_dir('/sbin/www'))
{
	$xtreamer=true;
	$key_up='U';
	$key_down='D';
	$key_left='L';
	$key_right='R';
	$key_return='RET';
	$key_display='DISPLAY';
	$key_enter='ENTR';
	$key_pageup='PG';
	$key_pagedown='PD';
}
else
{
	$xtreamer=false;
	$key_up='up';
	$key_down='down';
	$key_left='left';
	$key_right='right';
	$key_return='return';
	$key_display='display';
	$key_enter='enter';
	$key_pageup='pageup';
	$key_pagedown='pagedown';
}
	$key_pause='video_pause';
	$key_play='video_play';
	$key_frwd='video_frwd';
	$key_ffwd='video_ffwd';
}
unset($ini);

function write_conf()
{
global $confs_path;
global $ua_images_foldername;
global $ua_tmp_path;
global $ua_wget_path;
global $d_path;
global $exua_quality;
global $exua_region;
global $exua_lang;
global $ua_sort;
global $uakino_decode;
global $fsua_sort;
global $player_style;
global $hdpr1;
global $search_history;
global $auto_path;
global $history_length;
global $built_in_keyb;
global $position;
global $screensaver;
global $exua_posters;

$ini = new TIniFileEx($confs_path.'ua_settings.conf');
$ini->write('paths','ua_images_foldername',$ua_images_foldername);
$ini->write('paths','ua_tmp_path',$ua_tmp_path);
$ini->write('paths','ua_wget_path',$ua_wget_path);

$ini->write('downloads','path',$d_path);
$ini->write('downloads','auto_path',$auto_path);

$ini->write('exua_setting','quality',$exua_quality);
$ini->write('exua_setting','region',$exua_region);
$ini->write('exua_setting','language',$exua_lang);
$ini->write('exua_setting','posters',$exua_posters);

$ini->write('uakino','sort',$ua_sort);
$ini->write('uakino','decode_strings',$uakino_decode);
$ini->write('fsua','sort',$fsua_sort);

$ini->write('player','style',$player_style);
$ini->write('keys','HDP_R1_R3',$hdpr1);


$ini->write('other','history_length',$history_length);
$ini->write('other','built_in_keyb',$built_in_keyb);
$ini->write('other','save_position',$position);
$ini->write('other','screen_saver',$screensaver);


$ini->updateFile(); // скидываем информацию в ini файл
unset($ini);
}

if (!file_exists($confs_path.'ua_settings.conf')){
write_conf();
}

// создаем симлинк на файл настроек
if (!file_exists($tmpPath."ua_settings.conf"))
   shell_exec("ln -s ".$confs_path."ua_settings.conf ".$tmpPath);

// создаем симлинк на закладки
if (!file_exists($ua_favorites_filename))
	file_put_contents($ua_favorites_filename,"");

if (!file_exists($tmpPath.'ua_favorites.conf'))
	shell_exec("ln -s ".$ua_favorites_filename." ".$tmpPath);


// тут пошли глобальные функции
//----------------------------------------------------------------
// скачиваем веб страницу. Используем это для того, чтобы послать серверу 
// Accept-Language: ru, иначе страница прийдет на укр. языке
// ну и рефер нужен для fs.ua
//----------------------------------------------------------------
function get_page($site,$page,$isheaders=false,$ref='',$cookie='',$post='')
{
$page=trim($page);
$site=trim($site);
$socket = fsockopen($site, 80, $errno, $errstr, 15);
if ($socket){
  
    if ($post!=''){
        $send  = "POST $page HTTP/1.0\r\n";
        $send .= "Content-Length: ". strlen($post) ."\r\n";
        $send .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";  
    }  else $send  = "GET $page HTTP/1.0\r\n";
    $send .= "User-Agent:  Mozilla/5.0 Firefox/3.6.12\r\n";
    $send .= "Host: $site\r\n";
    $send .= "Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1\r\n";
    if($ref!='') $send .= "Referer: $ref\r\n";
    if($cookie!=''){
        $send .= "Cookie: $cookie\r\n";
        $send .= 'Cookie2: $Version=1'."\r\n";
    }
    $send .= "Accept-Language: ru,en;q=0.9,ru-RU;q=0.8\r\n";
    $send .= "Connection: close\r\n\r\n".$post;
  
    if(fputs($socket,$send)) {
        if(!$isheaders) while(fgets($socket,1024)!="\r\n" && !feof($socket));
        $he="";
        while(!feof($socket)) $he.=fread($socket,10240);
    };
    fclose($socket);
}
//file_put_contents("/data/apps/uaonline2/temp.txt",$he);
return $he;
};	




// убирает спец символы
function fix_str($s)
{
 //return $s;
 
 $search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript
                 "'<[\/\!]*?[^<>]*?>'si",           // Вырезает HTML-теги
                 "'([\r\n])[\s]+'",                 // Вырезает пробельные символы
                 "'&(quot|#34);'i",                 // Заменяет HTML-сущности
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e",                    // интерпретировать как php-код
				 "\"\n\"",
				 "\"\r\"");


$replace = array ("",
                  "",
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  ">",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)",
				  " ",
				  " ");

return preg_replace($search, $replace, $s);
 
 
}

// делит описалово на строки
function descr_split($desc,$len=79,$cnt=9)
{
$d=explode(' ',$desc);
$ds="";
$count=0;
$tmp_ds="";
foreach ($d as $dd)			
	{
		$dd=trim($dd);
		if (strlen(utf8_decode($dd))>0) 
		{
			$tmp2=$tmp_ds." ".$dd;		
			if (strlen(utf8_decode($tmp2))<=$len) $tmp_ds.=" ".$dd; else
			{
				$ds .=trim($tmp_ds); $tmp_ds=$dd; $count++;
				if ($count == $cnt) 
				{
					return $ds;	
					break;
				} else $ds.= "\n";
			}
			
		
		}
		
	}
$ds .=trim($tmp_ds);
if ($count<$cnt)
	{
		while ($count<$cnt-1)
		{
			$count++;
			$ds.= "\n";
		}
	return $ds;
	}
}


// функция обрезает строку до нужного кол-ва символов
function get_short_text($text,$len=79)
{
	return iconv_substr($text, 0, $len, 'UTF-8');
}


// правка линков для воспроизведения 
function check_link($url)
{
		$mov = true;
		while ($mov) 
		{
			// check for redirect
			$fp = fopen( $url, 'r' );
			$meta_data = stream_get_meta_data( $fp );
			//echo $meta_data['wrapper_data'][0]."<br>";
			if (preg_match('/HTTP\/1\.1\s*30[12]\s*[Mm]oved/',$meta_data['wrapper_data'][0],$ss)) $mov = true; else $mov=false;
			//if (preg_match('/HTTP(.*)200/',$meta_data[0],$ss)) $mov = false;
			//print_r($meta_data);
			$s = implode( "\n", $meta_data['wrapper_data'] );
			if( preg_match( '/[Ll]ocation:\s*(.*)/' , $s, $ss ) > 0 )
			$url = $ss[1];
			$url = dirname( $url ) .'/'. urlencode( basename( $url ));
			fclose($fp);
			//echo $url."---<br>";
		//readfile($url);
		}
		//return $url;
		header ( 'Location: '. $url );	
		
}

if(isset($_GET["play"])) 
	{
		$url = $_GET["play"];
		check_link($url);
		
	}

// функция для получения полных линков для избранного
function get_fav_site($site,$type)
{
global $ua_path_link;
global $ua_path_link2;
global $exua_rss_list_filename;
global $exua_rss_link_filename;
global $exua_parser_filename;
global $fsua_parser_filename;
global $filmix_parser_filename;
global $uakino_rss_list_filename;
global $uakino_rss_link_filename;
global $uakino_parser_filename;
	switch ($site)
	{
		case 0: {
					if ($type=="list") 
						$fav_site=$ua_path_link.$exua_rss_list_filename."?view=";
					if ($type=="link")
						$fav_site=$ua_path_link.$exua_rss_link_filename."?file=";
					if ($type=="parser")
						$fav_site=$ua_path_link.$exua_parser_filename."?file=";
					break;
				}
		case 2: {
					if ($type=="parser")
						$fav_site=$ua_path_link.$fsua_parser_filename."?file=";
					if ($type=="link")
						$fav_site=$ua_path_link.$fsua_parser_filename."?file=";
					if ($type=="list")
						$fav_site=$ua_path_link.$fsua_parser_filename."?file=";
					break;
				}
		
		case 3: {
					if ($type=="list")
						$fav_site=$ua_path_link.$uakino_rss_list_filename."?view=";
					if ($type=="link")
						$fav_site=$ua_path_link.$uakino_rss_link_filename."?file=";
					if ($type=="parser")
						$fav_site=$ua_path_link.$uakino_parser_filename."?file=";
					break;
				}
				
		}
	
	return $fav_site;
}	

function get_site_logo($site,$rss=false)
{
global $ua_path_link;
global $ua_path_link2;
global $ua_images_foldername;
if (!$rss)
	$url_prefix=$ua_path_link2.$ua_images_foldername;
	else
	$url_prefix=$ua_path_link.$ua_images_foldername;
switch ($site)
					{
						case 0: {$image=$url_prefix."ua_exua_ukr.png"; $url_site="http://www.ex.ua"; break;}
						case 2: {$image=$url_prefix."ua_fsua.png"; $url_site="http://fs.ua";break;}						
						case 3: {$image=$url_prefix."ua_uakinonet.png"; $url_site="http://uakino.net";}						
					}
if (!$rss)
	return array("image"=>$image,"site_url"=>$url_site); 
	else
	return $image;

}
if(isset($_GET["get_site_logo"])) 
	{
		echo get_site_logo($_GET["get_site_logo"],$rss=true);
	}
if(isset($_GET["get_fav_site"])) 
	{
		echo get_fav_site($_GET["get_fav_site"],$_GET["get_fav_type"]);
		exit;
	}

// функция проверки переключателя utf8 для uakino	
function uakino_utf8_check($s)
{
global $uakino_decode;
if ($uakino_decode == "0") $s=utf8_decode($s);
//if (LIBXML_VERSION >= 20632) $s=utf8_decode($s);
return $s;
}

// функция переводит в тип human readable размер устройства
function format_fsize($size){  
	 $size = $size * 1024;
	 $metrics[0] = 'байт';  
	 $metrics[1] = 'КБ';  
	 $metrics[2] = 'МБ';  
	 $metrics[3] = 'ГБ';  
	 $metrics[4] = 'ТБ';  
	 $metric = 0;  
	 while(floor($size/1024) > 0){  
		 ++$metric;  
		 $size /= 1024;  
	 }  
	 $ret =  round($size,1)." ".(isset($metrics[$metric])?$metrics[$metric]:'??');  
	 return $ret;  
 }  

// функция ищет самый большой носитель для закачек
function down_path($type)
{
	$res=shell_exec('df | grep \'sd?*\' | awk \'{print($2"\n"$4"\n"$6)}\'');
	if ($res != "")
	{
		$res = explode("\n",$res);
		$full_size=array();
		$free_space=array();
		$dev_name=array();
		$i=0;
		while ($i <= count($res) )
		{
			$full_size[]=$res[$i]; $i++;
			$free_space[]=$res[$i]; $i++;
			$dev_name[]=$res[$i]."/uaonline/"; $i++;
		}
		$device=$dev_name[0];
		$size=$full_size[0];
		if (count($full_size)>0)
		{
			foreach ($full_size as $id => $val)
			{
				if ($val>$size)	
				{
					$size=$val;
					$device=$dev_name[$id];
				}
			}
		}
	if (!is_dir($device)) mkdir($device,0777,true);
	if ($type == "name"	) return $device; else return $size;
	
	} else
	return "none";
}

if(isset($_GET["del"])) 
	{
		unlink($confs_path.'ua_history/'.$_GET["del"].".conf");
		exit;
	}
if(isset($_GET["clear"]))	
	{
		$res=shell_exec('rm -r '.$confs_path.'ua_history');
	}

?>