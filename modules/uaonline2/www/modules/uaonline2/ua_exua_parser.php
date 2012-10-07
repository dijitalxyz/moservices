<?php

/*	------------------------------
	Ukraine online services 	
	EX.ua parser module v1.5
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */



// подключаем разные константы пути кнопки и т.п.
include_once ("ua_paths.inc.php");

//выбор сайта для просмотра контента
if ($exua_region=="0") $ex_site="http://www.ex.ua";
if ($exua_region=="1") $ex_site="http://fex.net";



function getHumanValue( $val )
{
	if( $val < 1024 ) return sprintf( "%01u", $val ) . 'b' ;
	if( $val < 1048576 ) return sprintf("%01.1f", $val/1024 ) . 'Kb';
	if( $val < 1073741824 ) return sprintf("%01.1f", $val/1048576 ) . 'Mb';
	return sprintf("%01.2f", $val/1073741824 ) . 'Gb';
}




// заголовок страницы
function ftitle($html)
{
global $exua_region;

		if (($exua_region=="0") && ( preg_match("/(?<=\<title\>)(.*?)(?=@\sEX\.UA\<\/title\>)/",$html,$ss) > 0 )) 
		return fix_str($ss[0]);
		else
		{
		unset($ss);
		if (($exua_region=="1") && ( preg_match("/(?<=\<title\>)(.*?)(?=@\sFEX\.NET\<\/title\>)/",$html,$ss) > 0 )) 
		return fix_str($ss[0]);
		}
}

// заголовок для ызбранного 
function favtitle($html)
{
		preg_match("/(?<=\<meta\sname\=\"title\"\scontent\=\")(.*?)(?=\">)/",$html,$ss);
		return fix_str($ss[0]);
} 
 
function prep_list($html)
{
global $ua_path_link;
global $exua_rss_list_filename;
global $exua_rss_link_filename;
global $tmp;	
global $exua_parser_filename;
$s = exGetList( $html );
	
	$title=ftitle($html);
		
	
	// далее названия фильмов
	// тут генерится список фильмов
	$temps = '';
	$videocount=0;
	foreach ($s as $item=>$video)
	{
			
			if ($video["type"]=='folder') {$link =$ua_path_link.$exua_rss_list_filename."?view=".$item; $type="list";}
			else {$link = $ua_path_link.$exua_rss_link_filename."?file=".$item."&image=".$video["image"]; $type="link";}
		$temps.= $video["title"]."\n".$link."\n".$video["image"]."\n".$item."\n".$type."\n";
		$videocount++;

	}

   $temps = $title."\n".$videocount."\n".$temps;
   
   file_put_contents( $tmp, $temps );
	
	return $tmp;

}

 
 // -------------------------------------------------------------
//	Поиск в разделе и глобальный поиск
// 	входные параметры: search - строка поиска
//	id - номер раздела (например, зарубежное видео - 2 )
//	page - номер страницы
//  На выходе будет файл /tmp/usbmounts/sda1/ex_tmp.dat
//	в котором первая строка : $header - заголовок экрана поиска
//	2-я строка - кол-во найденых фильмов
//	начиная с 3-й строки идет заголовок, ссылка, постер
// 
// -------------------------------------------------------------
function exuaSearch($search, $id, $page = 1){
global $ua_path_link;
global $exua_rss_list_filename;
global $exua_rss_link_filename;
global $ex_site;
global $tmp;	
	$header="ПОИСК - ".$search;
	$search=urlencode($search);
	
	if ($id) {
		if($page) {
			$nt= $page-1;
			$html = file_get_contents($ex_site."/search?s=".$search."&original_id=".$id."&p=".$nt);
		}	
		else {
			$page = 1;
			$html = file_get_contents($ex_site."/search?s=".$search."&original_id=".$id);
		}
    } else
	{
		if($page) {
			$nt= $page-1;
			$html = file_get_contents($ex_site."/search?s=".$search."&p=".$nt);
		}
		else 
		{
			$page = 1;
			$html = file_get_contents($ex_site."/search?s=".$search);
		}
	}
	
	return prep_list($html);
		
	
	
}

// анализ полученных данных и вызов функции поиска
if (isset($_GET['search'])){
	if (isset($_GET['id']))
	$id = $_GET['id'];
	 else $id=false;
	
	if (isset($_GET['page']))
		$page = $_GET['page'];
	
//	$search = file_get_contents("/tmp/tmp_search.dat");
	$search = $_GET['search'];
	echo exuaSearch($search, $id, $page);
}
	

	
// функция получения списка фильмов
function exGetList( $s )
{
	$links = array();

	if(( preg_match( '/<table .*? class=include_0>(.*?)<\/table>/s' , $s, $ss ) > 0 )
	 ||( preg_match( '/<table .*? class=panel>(.*?)<\/table>/s' , $s, $ss ) > 0 ))
	{
		$as = $ss[1];
		if( preg_match_all( '/<a .*?>.*?<\/a>/s' , $as, $ss ) === false ) return $links;
		foreach( $ss[0] as $a )
		{
			if( preg_match( '/<a href=\'\/view\/(\d+).*?\'><img src=\'(.*?)\'.* alt=\'(.*?)\'.*<\/a>/' , $a, $as ) > 0 )
			{
				if( isset( $links[ $ss[1] ] )) continue;

				$tmp_link=$as[1];
				$links[ $as[1] ] = array(
					'type' => 'item',
					'image' => $as[2],
					'title' => fix_str($as[3])
				);
			}
			elseif( preg_match( '/<a href=\'\/view\/(\d+).*?\'><b>(.*?)<\/b><\/a>/' , $a, $as ) > 0 )
			{
				if( isset( $links[ $as[1] ] )) continue;

				$links[ $as[1] ] = array(
					'type' => 'group',
					'title' => $as[2]
				);
			}
	// туточки анализируется есть ли вложенные папки
			if (preg_match("/(?<=view\/)[^\s]*(?=' class\=info)/",$a,$out)>0)
				$links[ $tmp_link ]['type'] ='folder';
		}
	}
	return $links;
}	
	
  
// листаем по списку фильмов и подготавливаем плейлист

if (isset($_GET['view'])){
	$view = $_GET['view'];
	
	if (isset($_GET['page'])){
		$page = $_GET['page'];
	}
	
	if($page) {
		$nt= $page-1;
        $html = file_get_contents($ex_site."/view/".$view."?p=".$nt."&per=20");
    }
	else {
		$page = 1;
        $html = file_get_contents($ex_site."/view/".$view."?per=20");
    }
		
		echo prep_list($html);
		
   
}


//------------------------------------------------------------

function fexGetPlaylist( $s,$view,$qual)
{
global $ex_site;
	//$s = file_get_contents( 'http://www.ex.ua/view/'. $view );

	$items = array();

	// get player's playlist
	$ls = array();
	if( preg_match( '/var player_list = \'(.*?)\';/' , $s, $ss ) > 0 )
	{
		$ss = json_decode( '['. $ss[1] .']', true );
		foreach( $ss as $item )
		 if( preg_match( '/\/show\/(\d*)\//' , $item['url'], $ss ) > 0 ) $ls[ $ss[1] ] = trim( $item['url'] );
	}
//print_r( $ls );
	// get playlist
	$ss = file( $ex_site.'/playlist/'. $view .'.m3u');
	$ps = array();
	foreach( $ss as $p )
	 if( preg_match( '/\/get\/(\d*)/' , $p, $ss ) > 0 ) $ps[$ss[1]] = trim( $p );
//print_r( $ps );
	// get title and infos
	foreach( $ps as $v => $p )
	{
		$title = '';
		$len = '';
		$info = '';

		if( preg_match( '/<a href=\'\/get\/'. $v .'\' title=\'(.+?)\' rel=\'nofollow\'>(.+?)<a href=\'\/load\/'. $v .'\' rel=\'nofollow\'>/s', $s, $ss ) > 0 )
		{
			$title = $ss[1];

			$a = $ss[2];
			if( preg_match( '/<td align=right width=200 class=small><b>(.+?)<\/b><p>/s', $a, $ss ) > 0 ) $len = (real)str_replace( ',', '', $ss[1] );
			if( preg_match( '/<td align=right width=200 class=small><b>.+?<\/b><p>.+?<br>.+?<br>(.+?)<p><span class=r_button_small>/s', $a, $ss ) > 0 ) $info = $ss[1];
		}

		$link = $ls[ $v ];
		if( $qual=="1" ) $link = $ps[ $v ];

		$items[] = array(
			'link'   => $link,
			'title'  => $title,
			'len'    => getHumanValue( $len ),
			'info'   => $info,
			'dl'     => $ps[ $v ],
		);

	}
//print_r( $items );
	return $items;
}

// грузит страницу
function load_page($view)
{
global $ex_site;
$s = file_get_contents($ex_site."/view/".$view);
return $s;
}

// приводит линки EX к правильному виду
function check_ex_link($url)
{
preg_match( "/view\/(\d*)/" , $url, $out);
if ($out) return $out[1]; else return $url;
}

// анализируем страницу - это ссылки на папки или файлы
function analyze_page($s)
{
if(( preg_match( '/<table .*? class=include_0>(.*?)<\/table>/s' , $s, $ss ) > 0 )
	 ||( preg_match( '/<table .*? class=panel>(.*?)<\/table>/s' , $s, $ss ) > 0 ))
	$folder=true; else $folder=false;
return $folder;
}


function get_poster_and_descr($s)
{
		if( preg_match( '/<table width=100% cellpadding=0 cellspacing=0 border=0>(.*?)<\/table>/s' , $s, $ss ) >0)
		{
		$t = $ss[1];
		if( preg_match( '/<p>(.*?)<span/s' , $t, $ss ) > 0 )
			{
				$desc = trim( $ss[1] );
				$desc = str_replace( '<p>', " ", $desc );
				$desc = str_replace( '<br>', " ", $desc );
				$desc = str_replace( "\n", "", $desc );
				$desc = str_replace( "\r", "", $desc );
				$desc = preg_replace( '/<.*?>/', '', $desc );
				$desc = fix_str($desc);
			}
		if( preg_match( "/<img src='(.*?)\?\d*\'\swidth/" , $t, $ss ) > 0 )
			{
				$image=$ss[1]."?200";
			}			
		}
return array("image"=>$image,"desc"=>$desc);
}


// тут подготавливается список линков (из плейлиста)
//----------------------------------------------------------------------
	if(isset($_GET["file"])) {
		if(isset($_GET["quality"])) $qual=$_GET["quality"];
		$view = $_GET["file"];
		$s=load_page($view);
		if (isset($_GET["fav_refresh"])) {
			echo favtitle($s);
			exit;
		}
		$title=ftitle($s);
		$descr_image=get_poster_and_descr($s);
		$image=$descr_image["image"];
		$desc=$descr_image["desc"];
		
		
		$items=fexGetPlaylist($s,$view,$qual);
		$temps = '';
		$videocount=0;
		foreach ($items as $val)
		{
			$videocount++;		
			$ex_title=$videocount.".".$val['title'];
			$temps.=$ex_title."\n".$ua_path_link.$exua_parser_filename."?play=".$val['link']."\n".$val['dl']."\n".$val['title']."\n".$ex_title."\n".$ua_path_link.$exua_parser_filename."?file=".$view."&fav_refresh=1\n";
		}

		$temps = $title."\n".descr_split($desc)."\n".$image."\n".$videocount."\n".$temps;
	 
		file_put_contents( $tmp, $temps );
	
		echo $tmp;	
}	 
//--------------------------------------------------------------------------
// туточки вычисляем кол-во страниц при просмотре
if(isset($_GET["pitemCount"]) && isset($_GET["num"])) {
	$pitemCount = (int)$_GET["pitemCount"];
	$num = (int)$_GET["num"];
	$res = ceil ($pitemCount / $num);
	echo $res;
}
//--------------------------------------------------------------------------
// тут сохраняем/читаем настройки качества для ex.ua с файла конфигурации
if(isset($_GET["exua_quality"])){
	if ($_GET["exua_quality"]=='req'){
	echo $exua_quality;
	}
	if ($_GET["exua_quality"]=='send')	{
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('exua_setting','quality',$_GET["exua_quality_val"]);
		$ini->updateFile();
		unset($ini);
	}

}


	
?>