<?php

/*	------------------------------
	Ukraine online services 	
	FS.UA parser module v2.8
	------------------------------
	Created by Sashunya 2014	
	wall9e@gmail.com			
	------------------------------ */

header( "Content-type: text/html; charset=utf-8" );
//---------------------------------------------------------------------------------
// подключаем разные константы пути кнопки и т.п.
//---------------------------------------------------------------------------------
include_once ("ua_paths.inc.php");

$url_prefix="http://brb.to";
//---------------------------------------------------------------------------------
// проверяет есть ли $url_prefix в ссылке, если нет, то добавляет
//---------------------------------------------------------------------------------
function check_prefix($s)
{
	global $url_prefix;
	if (!preg_match("/(?<=".addcslashes($url_prefix,"/.:").")(.*?)/",$s)) $s = $url_prefix.$s;
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

//---------------------------------------------------------------------------------
// возвращает индекс папки из полной ссылки
//---------------------------------------------------------------------------------
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


//---------------------------------------------------------------------------------
// Changing size of the poster (Notice: qual is the quality number. Change for different size)
//---------------------------------------------------------------------------------
function big($url,$qual=10)
{
	$pattern = '/\/(\d+)\/(\d+)\/(\d+)\/(\d+)\/(\d+)./i';
	$replacement = '/$1/$2/$3/'.$qual.'/$5.';
	return preg_replace($pattern, $replacement, $url);
}

//---------------------------------------------------------------------------------
// ПОИСК
//---------------------------------------------------------------------------------
function fsua_search($search,$page)
{
global $url_prefix;
global $search_prefix;

if($page) {
			$nt= $page-1;
			//$s = file_get_contents($url_prefix."/".$search_prefix."/search.aspx?search=".$search."&page=".$nt);
			$s = file_get_contents($url_prefix."/search.aspx?search=".$search."&page=".$nt);
		}
		else {
			$page = 1;
			//$s = file_get_contents($url_prefix."/".$search_prefix."/search.aspx?search=".$search);
			$s = file_get_contents($url_prefix."/search.aspx?search=".$search);
		}
		
			$search_arr=array ();
			$tmp_array=array();
			$doc = new DOMDocument();
			libxml_use_internal_errors( true );
			$doc->loadHTML($s);
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
								if ( preg_match("/\/audio|video\//",$link))
								{
									if ( preg_match("/\/audio\//",$link)) $name .= "(Аудио)";
									if ( preg_match("/\/video\//",$link)) $name .= "(Видео)";
									$search_arr[]=	array (
													'link' => $link,
													'image' => $image,
													'title' => $name
									);
								}
							}
					
				}

return $search_arr;
}

if (isset($_GET['search'])){
		
	if (isset($_GET['page']))
		$page = $_GET['page'];
		$search_prefix=$_GET['search_prefix'];
		$title=$_GET["search"];
		$search=urlencode($title);
		$title="ПОИСК:".$title;
		$videocount=0;		
		$temps = '';
		$search_arr = fsua_search($search, $page);
		foreach ($search_arr as $val)
		{
			$fav=$val["link"]; 
			$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($val["link"]."?ajax&folder")."&img=".$val["image"]."&name=".$search;
			$temps.= $val["title"]."\n".$link."\n".$val["image"]."\n".$fav."?ajax&folder"."\nlist\n";		
			$videocount++;
		}
	$temps = $title."\n".$videocount."\n".$temps;
    file_put_contents( $tmp, $temps );
	
	echo $tmp;
}

//---------------------------------------------------------------------------------
// получаем список фильмов
//---------------------------------------------------------------------------------
function get_film_list($s,$title)
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
					$class= $div->getAttribute('class');
					
					if( preg_match("/b-poster-tile\s/",$class))
						{
							$as=$div->getElementsByTagName('a');
							foreach( $as as $a )
							{
								$qua = "(";
								$link=check_prefix($a->getAttribute('href'));	
								$spans=$a->getElementsByTagName('span');
								foreach( $spans as $span )
								{
									if( $span ->hasAttribute('class'))
									{
										$span_class = $span ->getAttribute('class');
										if ($span_class=="b-poster-tile__image")
										{
											$imgs=$span->getElementsByTagName('img');
											foreach( $imgs as $img )
											{
												$image=big($img->getAttribute('src'),9);
											}
										}
										if ($span_class == 'b-poster-tile__title-full')
										{
											$name=trim(fix_str($span->textContent));
										}
										if 	(preg_match("/(?<=quality\sm\-)(.+)/",$span_class, $out)) 
										{
										$q ="";
										switch ($out[0])
										{
											case "hd": 
												$q = "HD";
												break;
											case "hq": 
												$q = "Высокое";
												break;
											case "sq": 
												$q = "Среднее";
												break;
											case "lq": 
												$q = "Низкое";
												break;
										}
										if ($qua == "(") $qua.=$q; else $qua.=",".$q;
										
										}
									}
								}
							}
						if ($qua!='(') $qua.=" качество)"; else $qua='';
						$name .=  $qua;
						$films[$name]=array("link"=>$link, "image"=>$image);
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
	//echo $temps;
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
	$nam=$_GET['name'];
	
	if($page) {
		$nt= ($page-1)*20;
		$html = '<meta http-equiv="content-type" content="text/html; charset=utf-8">'.stripcslashes(file_get_contents($view."?scrollload=1&view=list&start=".$nt."&length=4&sort=".$fsua_sort));
    }
	
	echo get_film_list($html,$nam);
	
 }
// сохранение постера 
function save_poster($img)
{
	//$purl = big($img);
	$image= "/tmp/poster".rand(0, 1000);
	shell_exec("rm -f /tmp/poster*");
	shell_exec("wget -c -O ".$image." ".$img);
	return $image;
}
 
 // тут берем грузим главную страницу с фильмом и берем постер и описание
function get_description($url)
{
global $tmpdescr;
	$s=file_get_contents($url);
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);
	$divs= $doc->getElementsByTagName('div');
	foreach( $divs as $div )
	if( $div->hasAttribute('class'))
	{
		// ПОСТЕР
			$post_class = $div->getAttribute('class');
			if( $post_class == 'poster-main ' || $post_class == 'poster-main poster-main_type_audio' )
				{
					
					$as=$div->getElementsByTagName('a');
					foreach( $as as $a ) 
					{
						if ($post_class == 'poster-main poster-main_type_audio')
						{
							$purl=$a->getAttribute('style');
							preg_match("/(?<=url\()(.*?)(?=\)\;)/",$purl,$out);
							$purl = big($out[0]);
							$image = save_poster($purl);
							/*
							$purl = big($out[0]);
							$image= "/tmp/poster".rand(0, 1000);
							shell_exec("rm -f /tmp/poster*");
							shell_exec("wget -c -O ".$image." ".$purl);
							*/
						}
						else
						{
						$imgs=$a->getElementsByTagName('img');
						foreach( $imgs as $img ) 
						{
							$purl = big($img->getAttribute('src'));
							$image = save_poster($purl);
							/*
							$purl = big($img->getAttribute('src'));
							$image= "/tmp/poster".rand(0, 1000);
							shell_exec("rm -f /tmp/poster*");
							shell_exec("wget -c -O ".$image." ".$purl);
							*/
						}
						/*
						$image=$a->getAttribute('style');
						preg_match("/(?<=url\()(.*?)(?=\)\;)/",$image,$out);
						$image = big($out[0]);
						*/
						if ($image!="") break;
						}
					}
				}
			// ЗАГОЛОВОК
			if( $div->getAttribute('class') == 'b-tab-item__title-inner' )
				{
					//$title=trim(fix_str($div->textContent));
					// video
					$spans=$div->getElementsByTagName('span');
					foreach( $spans as $span ) 
					{
						$title=trim(fix_str($span->textContent));
						if ($title!="") break;
					}
					// audio
					if ($title=="")
					{
						$hs = $div->getElementsByTagName('h1');
						foreach( $hs as $h ) 
						{
							$title=trim(fix_str($h->textContent));
							if ($title!="") break;
						}
					}
				}
			// краткое описание фильма
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
						$ds = fix_str(trim($ds));
						/*
						$descr = '';
						$ps=$div->getElementsByTagName('p');
						foreach( $ps as $p ) $descr .= " ".$p->textContent." ";
						$descr = fix_str($descr);
							
						break;
						*/
						
					}
			// полное описание
			
			if ( $div->getAttribute('class') == 'b-tab-item__description' )
				{
					$descr = '';
					$ps=$div->getElementsByTagName('p');
					foreach( $ps as $p ) $descr .= " ".$p->textContent." ";
					$descr = fix_str($descr);
					break;
				}
					
	}
	$temps = "";
	//Year
	preg_match("/[Год\:\s+](\d{4})/", $ds, $year);
	//Genre
	preg_match("/(?<=Жанр\:\s{2})(.*?)(?=Год|Период)/", $ds, $genre);
	//Period
	preg_match("/(?<=Период показа:\s{2})(.*?)(?=Статус)/", $ds, $period);
	//Status
	preg_match("/(?<=Статус\:\s{2})(.*?)(?=Страна)/", $ds, $status);
	//country
	preg_match("/(?<=Страна\:\s{2})(.*?)(?=Режиссёр|Ведущие)/", $ds, $country);
	//managers
	preg_match("/(?<=Ведущие\:\s{2})(.+)/", $ds, $managers);
	//director
	preg_match("/(?<=Режиссёр\:\s{2})(.*?)(?=В\sролях)/", $ds, $director);
	//cast
	preg_match("/(?<=В\sролях\:\s{2})(.+)/", $ds, $cast);
	$cnt=8;
	if ($year) {$temps.= "Год: ".$year[0]."\n"; $cnt--;}
	if ($period) {$temps.= "Период показа: ".$period[0]."\n"; $cnt--;}
	if ($status) {$temps.= "Статус: ".$status[0]."\n"; $cnt--;}
	if ($genre) {$temps.= "Жанр: ".$genre[0]."\n"; $cnt--;}
	if ($country) {$temps.= "Страна: ".$country[0]."\n"; $cnt--;}
	if ($director) {$temps.= "Режиссёр: ".get_short_text($director[0])."\n"; $cnt--;}
	if ($cast) {$temps.= "В ролях: ".get_short_text($cast[0])."\n"; $cnt--;}
	if ($managers) {$temps.= "Ведущие: ".$managers[0]."\n"; $cnt--;}
	for ($i=0; $i<$cnt; $i++) $temps.="\n";
	file_put_contents( $tmpdescr,descr_split($descr,65,15));
return array("title"=>$title,"poster"=>$image,"purl"=>$purl,"desc"=>$temps);
}
 
// парсим сезоны, переводы, список файлов
function get_fs_data($file,$img,$nam,$header=false)
{
global $url_prefix;
global $ua_path_link;
global $fsua_parser_filename;
global $ua_path_link2;
global $fsua_rss_link_filename;
global $ua_path_link2;
global $fsua_rss_list_filename;
global $tmp;
$s=file($file);
preg_match("/(.*?)(?=\?ajax)/",$file,$out);
$empty_link=$out[0];
$main_link=$empty_link."?ajax&folder=";			
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

	if (!$folder && preg_match("/(?=href\=\"(.*?)\"\sclass\=\"b-file-new__link-material-)/",$val,$out))
	{
		$name = basename($out[1]);
		$cont_arr[$out[1]]=array("name"=>$name,"type"=>"file");
	}

	// file size
/*
	if (!$folder && preg_match("/(?<=b-file-new__link-material-size\"\>)(.*?)(?=\<\/span)/",$val,$out2))
	{
		$cont_arr[$curr_idx]=array("name"=>$curr_name,"type"=>"file","size"=>$out2[0]);
	}
*/	
	// тут получаем список качества и переводов
	if ($li_cnt==1 && preg_match("/link-subtype.*?\stitle/",$val))
	{
		preg_match("/(?<=parent_id\:\s\')(\d+)\'\}\".*?\>(.*?)(?=\<\/a\>)/",$val,$out);
		$id=$main_link.$out[1];
		$cont_arr[$id]=array("name"=>$out[2],"type"=>"perevod");
		$fnd=true;
		$folder=true;

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
		$s_name = $out[0];
		$cont_arr[$id]=array("name"=>$s_name,"type"=>"season");
		$folder=true;
	}
	// other file links
/*
	if ($li_cnt==1 && $fnd && $season && preg_match("/(?<=folder\=)(\d+)(?=\"\sclass\=\"folder\-filelist\")/",$val,$out))
	{
		$cont_arr[$id]=array("name"=>$s_name,"type"=>"perevod");
	}
*/
	
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

$final = false;
foreach ($cont_arr as $key=>$val)
{
	if ($val["type"]=='file') 
	{
		$name=$val["name"];
		$link=urlencode($url_prefix.$key);
		$temps .= ($videocount+1).".".fix_str(urldecode($name))."\n".$ua_path_link.$fsua_parser_filename."?play=".$link."\n".$link."\n".$name."\n".$name."\n".$ua_path_link.$fsua_parser_filename."?file=".$link."&fav_refresh=1\n";	
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
	$purl = $desc["purl"];
	$temps = $file."\n".$title."\n".$ds."\n".$image."\n".$purl."\n".$videocount."\n".$temps;
	$redirect = $ua_path_link2.$fsua_rss_link_filename;
} else
{
	//$desc=get_description($empty_link);
	//$title = $desc["title"];
	$title = file_get_contents("/tmp/ua_title.tmp");
	$temps = $title."\n".$videocount."\n".$temps;
	$redirect=$ua_path_link2.$fsua_rss_list_filename;
}

file_put_contents( $tmp, $temps );

if ($header)
{
	$desc=get_description($empty_link);
	$title = $desc["title"];
	$image = $desc["purl"];
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
		$main=get_fs_data($file,$image,$name,true);			
		echo $main["title"].$main["name1"]."\n".$main["image"];
		exit;
	}	
	
$redirect=get_fs_data($file,$image,$name);
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