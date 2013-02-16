<?php

/*	------------------------------
	Ukraine online services 	
	FS.UA parser module v2.0
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	------------------------------ */

header( "Content-type: text/html; charset=utf-8" );
// подключаем разные константы пути кнопки и т.п.
include_once ("ua_paths.inc.php");

$url_prefix="http://fs.ua";
//---------------------------------------------------------------------------------
// проверяет есть ли $url_prefix в ссылке, если нет, то добавляет
//---------------------------------------------------------------------------------
function check_prefix($s)
{
	global $url_prefix;
	if (!preg_match("/(?<=http\:\/\/fs\.ua)(.*?)/",$s)) $s = $url_prefix.$s;
	return $s;
}

function check_fs_url($s)
{
	preg_match("/(.*?)(?=#)/",$s,$out);
	return $out[0];
}

function check_url2($s)
{
	preg_match("/(.*?)(?=\?folder)/",$s,$out);
	return $out[0];
}

// возвращает индекс папки из полной ссылки
function get_digit_url($link)
{
	preg_match("/(?<=\?folder\=)(\d*)/",$link,$out);
	return $out[0];
}

function get_name($s)
{
	preg_match("/\/([^\/\\:\"<>\*\?\|]+)$/",$s,$out);
	return $out[1];
}

// ПОИСК
if (isset($_GET['search'])){
		
	if (isset($_GET['page']))
		$page = $_GET['page'];
		$search_prefix=$_GET['search_prefix'];
		$title=$_GET["search"];
		$search=urlencode($title);
		$title="ПОИСК:".$title;
		
	if($page) {
			$nt= $page-1;
			$s = file_get_contents("http://fs.ua/".$search_prefix."/search.aspx?search=".$search."&page=".$nt);
		}
		else {
			$page = 1;
			$s = file_get_contents("http://fs.ua/".$search_prefix."/search.aspx?search=".$search);
		}
		
			$search=array ();
			$tmp_array=array();
			$doc = new DOMDocument();
			libxml_use_internal_errors( true );
			$doc->loadHTML($s);
			$videocount=0;		
			$temps = '';
			/*
			$as= $doc->getElementsByTagName('a');
			foreach( $as as $a )
			if( $a->hasAttribute('class'))
			if( $a->getAttribute('class') == 'title' )
				{
					$name=$a->textContent;
					$search[$name]=array();
				} 
			*/	
			$tds= $doc->getElementsByTagName('td');
			foreach( $tds as $td )
			if( $td->hasAttribute('class'))
			if( $td->getAttribute('class') == 'image-wrap' )
				{
					$as = $td->getElementsByTagName('a');
					foreach( $as as $a )
							{	
								$link = check_prefix($a->getAttribute('href'));
								$name = $a->getAttribute('title');
								$imgs = $a->getElementsByTagName('img');
								foreach( $imgs as $img ) $image = $img->getAttribute('src');
								$fav=$link; 
								$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($link."?ajax&folder")."&img=".$image;
								$temps.= $name."\n".$link."\n".$image."\n".$fav."?ajax&folder"."\nlist\n";		
								//$tmp_array[$name]=array("link"=>$link, "image"=>$image);
								$videocount++;
							}
					
				}
/*$temps = '';
foreach ($tmp_array as $name=>$val)
	{ 
		$link = $tmp_array[$count]["link"];
		$image = $tmp_array[$count]["image"];
		$fav=$link; $link=$ua_path_link.$fsua_parser_filename."?file=".$link."&enter=1";
		$temps.= $name."\n".$link."\n".$image."\n".$ua_path_link.$fsua_parser_filename."?file=".$fav."&fav_refresh=1\n";		
		
	}
*/
	$temps = $title."\n".$videocount."\n".$temps;
    file_put_contents( $tmp, $temps );
	
	echo $tmp;
	
		
}

//---------------------------------------------------------------------------------
// получаем список фильмов
//---------------------------------------------------------------------------------
function get_film_list($s,$now=false)
{
global $url_prefix;
global $tmp;
global $ua_path_link;
global $fsua_rss_list_filename;
global $fsua_parser_filename;
function get_img($img)
{
	preg_match("/(?<=url\()(.*?)(?=\))/",$img,$out);
	return $out[0];
}
			$films=array();
			$doc = new DOMDocument();
			libxml_use_internal_errors( true );
			$doc->loadHTML($s);
			$divs= $doc->getElementsByTagName('div');
			$videocount=0;
			foreach( $divs as $div )
					if( $div->hasAttribute('class'))
					{
// это класс для фильмов с сортировкой по рейтингу
					if (!$now)
					{
	/*				$class=$div->getAttribute('class');
					if( $class == 'b-poster b-poster_films'|| $class == 'b-poster b-poster_serials'
						|| $class == 'b-poster b-poster_clips' || $class == 'b-poster b-poster_music')
						{
							$videocount++;
							$image=get_img($div->getAttribute('style'));
							$as= $div->getElementsByTagName('a');
							foreach( $as as $a )
							if( $a->hasAttribute('class'))
							if( $a->getAttribute('class') == 'details-link' )
								{
									$link=$a->getAttribute('href');
									$name = trim($a->textContent);
								}
							$films[$name]=array("link"=>$link, "image"=>$image);
						}
						*/
// это класс для фильмов с сортировкой по дате
					$videocount=0;
					$class= $div->getAttribute('class');
					
					if( $class == 'b-poster-section ' || $class == 'b-poster-section b-poster-clip' )
						{
							$as= $div->getElementsByTagName('a');
							foreach( $as as $a )
							if( $a->hasAttribute('class'))
							if( $a->getAttribute('class') == 'subject-link' )
								{
									$videocount++;
									$link=check_prefix($a->getAttribute('href'));
									$bs=$a->getElementsByTagName('b');
									foreach( $bs as $b )
									{
										$imgs=$b->getElementsByTagName('img');
										foreach( $imgs as $img )
										{
											$image=$img->getAttribute('src');
											$name=$img->getAttribute('alt');
										}
									}
									$films[$name]=array("link"=>$link, "image"=>$image);
								
								}
						}	
						if( $div->getAttribute('class') == 'b-section-title' )
						{
							$title=trim(fix_str($div->textContent));
						}
						} else
						{
// это класс для списка часто просматриваемых фильмов
						if( $div->getAttribute('class') == 'b-posters m-section' )
						{
							$title="Сейчас смотрят";
							$as= $div->getElementsByTagName('a');
							$videocount=0;
							foreach( $as as $a )
							if( $a->hasAttribute('class'))
							if( $a->getAttribute('class') == 'b-poster m-video' )
								{
									$videocount++;
									$link=$a->getAttribute('href');
									$image=get_img($a->getAttribute('style'));
									$name = trim($a->textContent);
									$films[$name]=array("link"=>$link, "image"=>$image);
			
								}
						}
						}
				}
	// далее названия фильмов
	// тут генерится список фильмов
	$temps = '';
	$videocount=0;
	foreach ($films as $id=>$vid)
	{
		$name=$id;
		foreach ($vid as $key=>$val)
		{
			if ($key=="link") 
			{
				$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($val."?ajax&folder")."&name=".urlencode($name); 
				$fav=$val."?ajax&folder";
			}
			if ($key=="image") 
			{
				$image=$val;
				$link .="&img=".urlencode($image);
			}
		}
		$temps.= $name."\n".$link."\n".$image."\n".$fav."\nlist\n";
		$videocount++;
	}

   $temps = $title."\n".$videocount."\n".$temps;
   
   file_put_contents( $tmp, $temps );
	
	return $tmp;
}

//---------------------------------------------------------------------------------
// Парсим разделы ФИЛЬМЫ, СЕРИАЛЫ и т.п.
//---------------------------------------------------------------------------------
if (isset($_GET['view'])){
	$view = $_GET['view'];
	if (isset($_GET['page'])){
		$page = $_GET['page'];
	}
		
	if($page) {
		$nt= $page-1;
        
		$html = file_get_contents($url_prefix.$view."&view=list&page=".$nt."&sort=".$fsua_sort);
		
    }
	else {
		$page = 1;
		$html = file_get_contents($url_prefix.$view."&view=list&sort=".$fsua_sort);
		
    }
		echo get_film_list($html);
	
 }

 // тут берем грузим главную страницу с фильмом и берем постер и описание
 function get_description($url)
 {
	$s=file_get_contents($url);
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);
	$divs= $doc->getElementsByTagName('div');
	foreach( $divs as $div )
	if( $div->hasAttribute('class'))
	{
		// ПОСТЕР
			if( $div->getAttribute('class') == 'poster-main' )
				{
					$imgs=$div->getElementsByTagName('img');
					foreach( $imgs as $img ) 
					{
						$image=$img->getAttribute('src');
						if ($image!="") break;
					}
				}
			// ЗАГОЛОВОК
			if( $div->getAttribute('class') == 'head m-themed' )
				{
					$hs=$div->getElementsByTagName('h1');
					foreach( $hs as $h ) 
					{
						$title=trim(fix_str($h->textContent));
						if ($title!="") break;
					}
				}
			// описание фильма
			if( $div->getAttribute('class') == 'item-info' )
					{
						$tds= $div->getElementsByTagName('td');
						foreach( $tds as $td ) 
						{
							$temp_d=trim(fix_str($td->textContent));
							if (strlen($temp_d)>0)
							$ds .= " ".$temp_d." ";
						}
						$ds .=".";
						$ds = trim($ds);
						$ps=$div->getElementsByTagName('p');
						foreach( $ps as $p ) $ds .= " ".$p->textContent." ";
						$ds = fix_str($ds);
					}
	}
 return array("title"=>$title,"poster"=>$image,"desc"=>$ds);
 }
 
// парсим сезоны, переводы, список файлов
function get_data($file,$img,$nam,$header=false)
{
global $ua_path_link;
global $fsua_parser_filename;
global $ua_path_link2;
global $fsua_rss_link_filename;
global $ua_path_link2;
global $fsua_rss_list_filename;
global $tmp;
//$file="http://fs.ua/item/i6odgT61l6CKZsTwmzpJmr7?ajax&folder";
$s=file($file);
//echo $s;
preg_match("/(.*?)(?=\?ajax)/",$file,$out);
$empty_link=$out[0];
//echo $empty_link."<br>";
$main_link=$empty_link."?ajax&folder=";			
//echo $main_link."<br>";
$temps = '';
$videocount=0;
$final = false;
$perevod = false;
$cont_arr=array();
$season=false;
$cnt_cont=0;
$li_cnt=0;
$folder=false;
foreach ($s as $key=>$val)
{
	if (preg_match("/\<li\sclass\=\"folder\"\>/",$val))
	{
		$li_cnt++;
	}
	if (preg_match("/\<\/li\>/",$val) && !preg_match("/\<li\sclass\=\"b-transparent-area\"\>\<\/li\>/",$val))
	{
		$li_cnt--;
	}
	
	
	// тут получаем список линков на файлы

	if (!$folder && preg_match("/(?<=href\=\")(.*?)\"\sclass\=\"link-material\"\\s\>\<span\sstyle\=\"\"\>(.*?)(?=\<\/span\>)/",$val,$out))
	{
		$cont_arr[$out[1]]=array("name"=>$out[2],"type"=>"file");
		
	}

	
	// тут получаем список качества и переводов
	if ($li_cnt==1 && preg_match("/link-subtype.*?\stitle/",$val))
	{
		preg_match("/(?<=parent_id\:\s\')(\d+)\'\}\".*?\>(.*?)(?=\<\/a\>)/",$val,$out);
		//echo $out[2]."<br>";
		$id=$main_link.$out[1];
		$cont_arr[$id]=array("name"=>$out[2],"type"=>"perevod");
		$fnd=true;
		$folder=true;
		//$cnt_cont++;
		//$perevod=true;
	}
	// тут получаем список сезонов
	if (preg_match("/link-simple\stitle/",$val))
	{
		preg_match("/(?<=parent_id\:\s)(\d+)(?=\})/",$val,$out);
		$fnd=true;
		$id=$main_link.$out[0];
	} 
	// тут получаем названия сезонов
	if ($li_cnt==1 && $fnd && preg_match("/(?<=\s\<b\>)(.*?)(?=\<\/b\>)/",$val,$out))
	{
		$season = true;
		$cont_arr[$id]=array("name"=>$out[0],"type"=>"season");
		$folder=true;
		//$cnt_cont++;
	}
	// тут берется информация о размере контента и качестве
	if ($fnd && $material<2 && preg_match("/(?<=material-size\"\>)(.*?)(?=\<\/span\>)/",$val,$out))
	{
		$cont_arr[$id]["name"].=" (".$out[0].")";
		$material++;
		if ($season || $material==2) 
		{
			$fnd=false; 
			$material=0;
		}
		
	}
}
//print_r($cont_arr);
// тут результаты парсинга сохраняем в файл
$final = false;
foreach ($cont_arr as $key=>$val)
{
	if ($val["type"]=='file') 
	{
		$name=$val["name"];
		$link=urlencode($key);
		$temps .= ($videocount+1).".".fix_str($name)."\n".$ua_path_link.$fsua_parser_filename."?play=".$link."\n".$link."\n".$name."\n".$name."\n".$ua_path_link.$fsua_parser_filename."?file=".$link."&fav_refresh=1\n";	
		$videocount++;
		$final = true;
	}
	if ($val["type"]=='perevod' || $val["type"]=='season')  
	{
		if ($val["type"]=='perevod') 
		{
			$name=$val["name"];
			$link=$key;
			$fav=$link;
			$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($link); 
			$temps.= fix_str($name)."\n".$link."\n".$img."\n".$fav."\nlink\n";
			$videocount++;
		}
		if ($val["type"]=='season') 
		{
			$name=$val["name"];
			$link=$key;
			$fav=$link;
			$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($link)."&img=".urlencode($img)."&name=".urlencode($name);
			$temps.= fix_str($name)."\n".$link."\n".$img."\n".$fav."\nlist\n";
			$videocount++;
		}
		$final = false;
	}
}
// тут сгружаем все данные в файл
if ($final)			
{
	$desc=get_description($empty_link);
	$title = $desc["title"];
	$ds = $desc["desc"];
	$image = $desc["poster"];
	$temps = $file."\n".$title."\n".descr_split($ds)."\n".$image."\n".$videocount."\n".$temps;
	$redirect = $ua_path_link2.$fsua_rss_link_filename;
} else
{
	$desc=get_description($empty_link);
	$title = $desc["title"];
	$temps = $nam." ".$title."\n".$videocount."\n".$temps;
	$redirect=$ua_path_link2.$fsua_rss_list_filename;
}
//echo $temps."<br>";
//echo $redirect."<br>";

file_put_contents( $tmp, $temps );
if ($header)
{
	$desc=get_description($empty_link);
	$title = $desc["title"];
	$image = $desc["poster"];
	return array("image"=>$image, "title"=>$title);
}
else 
return $redirect;
}
 

//---------------------------------------------------------------------------------
// Парсим содержимое фильмов, сериалов (получаем линки на фильмы)
//---------------------------------------------------------------------------------
if(isset($_GET["file"])) {
if (isset($_GET["img"])) $image = urldecode($_GET["img"]);
if (isset($_GET["name"])) $name = urldecode($_GET["name"]);
$file=urldecode($_GET["file"]);
if (isset($_GET["fav_refresh"])) 
	{
		$main=get_data($file,$image,$name,true);			
		echo $main["title"].$main["name1"];
		exit;
	}	
	
$redirect=get_data($file,$image,$name);
header('Location: '.$redirect."?param=".urlencode($file)); 
}
 
//---------------------------------------------------------------------------------
//  тут переключаем режимы сортировки категорий
//---------------------------------------------------------------------------------
if(isset($_GET["sort"])){
	if ($_GET["sort"]=='req'){
	echo $fsua_sort;
	}
	if ($_GET["sort"]=='send')	{
		$fsua_sort=$_GET["fs_sort_val"];
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('fsua','sort',$fsua_sort);
		$ini->updateFile();
		unset($ini);
	}

}

?>