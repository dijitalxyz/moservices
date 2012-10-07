<?php

/*	------------------------------
	Ukraine online services 	
	FS.UA parser module v1.3
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	------------------------------ */

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
								$fav=$link; $link=$ua_path_link.$fsua_parser_filename."?file=".$link."&enter=1&final=0";
								$temps.= $name."\n".$link."\n".$image."\n".$fav."?folder=0"."\nlist\n";		
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
			if(!preg_match("/folder/", $val)) $val.="?folder=0";
			$link=$ua_path_link.$fsua_parser_filename."?file=".$val."&enter=1&final=0"; 
			$fav=$val;
		}
		if ($key=="image") $image=$val;
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

function get_data($s,$file,$final,$header=false)
{
global $ua_path_link;
global $fsua_parser_filename;
global $ua_path_link2;
global $fsua_rss_link_filename;
global $ua_path_link2;
global $fsua_rss_list_filename;
global $tmp;
			$fav_folder=true;
			$temps = '';
			$videocount=0;
			$ds = '';
			$filelst=array();
			$folder = true;
			$single=false;
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
// получаем список файлов
		if ($final=="0")
			{
				if( $div->getAttribute('class') == 'header' )
				{		
					$as_tit = $div->getElementsByTagName('a');
					foreach( $as_tit as $a_tit )
					{
						$type_tit = $a_tit->getAttribute('class');
						if( $type_tit == 'link-simple title' )
						{
							$title_link=get_digit_url(check_fs_url($a_tit->getAttribute('href')));
							$main_link=get_digit_url($file);
							if ($title_link==$main_link) $title_name = trim(fix_str($a_tit->textContent));; 
						}
					}
				}
			if( $div->getAttribute('class') == 'b-filelist' )
							{
								$uls=$div->getElementsByTagName('ul');
								$m_current = false;
								foreach( $uls as $ul )
									if( $ul->hasAttribute('class'))
									if( $ul->getAttribute('class') == 'filelist m-current') 
									{	
										$m_current=true;
										break;
									}
								foreach( $uls as $ul )
									if( $ul->hasAttribute('class'))
									{
										if ($m_current) $check_filelist='filelist m-current'; else $check_filelist = 'filelist ';
										if( $ul->getAttribute('class') == $check_filelist)
										{	
											$lis = $ul->getElementsByTagName('li');
											foreach( $lis as $li )
											if( $li->hasAttribute('class'))
											{
												$li_class=$li->getAttribute('class');
												if( $li_class == 'folder')
												{	
													$q_c=0;
													$spans = $li->getElementsByTagName('span');
													foreach( $spans as $span )
														if( $span->getAttribute('class') == 'material-size' && $q_c<2 )
															{
																$qual.=" ".$span->textContent;
																$q_c++;	
															}
													$divs2 = $li->getElementsByTagName('div');
													foreach( $divs2 as $div2 )
													{
														if( $div2->hasAttribute('class'))
														{
															$class= $div2->getAttribute('class');
															if( $class  == 'header' )
																{		
																	$as = $div2->getElementsByTagName('a');
																	$file_list = false;	
																	foreach( $as as $a )
																	{
																		$type = $a->getAttribute('class');
																		if( $type == 'folder-filelist' ) $file_list = true;
																		if( $type == 'link-simple title'  )
																		{
																			$link=check_fs_url($a->getAttribute('href'));
																			$link=check_prefix(check_url2($file).$link);
																			$name = trim(fix_str($a->textContent));
																		} 

																	}
																	if ($file_list)
																	{
																		$fav=$link; $link=$ua_path_link.$fsua_parser_filename."?file=".$link."&enter=0&final=1"; 
																		$temps.= $name." ".$qual."\n".$link."\n".$image."\n".$fav."\nlink\n";
																	} else
																	{
																		$fav=$link; $link=$ua_path_link.$fsua_parser_filename."?file=".$link."&enter=0&final=0";
																		$temps.= $name." ".$qual."\n".$link."\n".$image."\n".$fav."\nlist\n";
																	}
																	$videocount++;
																	$qual="";
																}
														} else
														{	
															$as = $div2->getElementsByTagName('a');
															foreach( $as as $a )
															{
																$type = $a->getAttribute('class');
																if( $type == 'folder-filelist' )
																	{
																		$as = $div2->getElementsByTagName('a');
																		foreach( $as as $a )
																			{
																				$type2=$a->getAttribute('class');
																				if( $type2 == 'link-subtype title' || $type2 == 'link-subtype m-ru title' || $type2 == 'link-subtype m-ua title'|| $type2 == 'link-subtype m-en title' )
																				{
																					$link=check_fs_url(check_prefix(check_url2($file).$a->getAttribute('href')));
																					$name = fix_str($a->textContent);
																				}
																			}
																		$fav=$link; $link=$ua_path_link.$fsua_parser_filename."?file=".$link."&enter=0&final=1"; 
																		$temps.= $name." ".$qual."\n".$link."\n".$image."\n".$fav."\nlink\n";
																		$videocount++;
																		$qual="";
																	}
															}
														}	
													}
												} 
											}	
										}
									}
							} 
			}			
			}
if ($single)
{
	$temps = $title." ".$title_name."\n".descr_split($ds)."\n".$image."\n".$videocount."\n".$temps;
	$redirect = $ua_path_link2.$fsua_rss_link_filename;
}
else
{
if ($final=="0")
{
	$temps = $title." ".$title_name."\n".$videocount."\n".$temps;
	$redirect=$ua_path_link2.$fsua_rss_list_filename;
} else
{
// Тут парсим страницу на предмет файлов. ОТКЛЮЧЕНО. Лучше брать список файлов из файла (&flist)
/* 
	$uls=$doc->getElementsByTagName('ul');
	foreach( $uls as $ul )
		if( $ul->hasAttribute('class'))
		if( $ul->getAttribute('class') == 'filelist m-current')
			{
				$lis = $ul->getElementsByTagName('li');
				foreach( $lis as $li )
					if( $li->hasAttribute('class'))
						{
							$li_class=$li->getAttribute('class');	
							if( $li_class == 'file mpg' || $li_class == 'file mp4' || $li_class == 'file avi' || $li_class == 'file wmv' || $li_class == 'file mkv' || $li_class == 'file ts')
								{
									$as = $li->getElementsByTagName('a');
									foreach( $as as $a )
										{
											$type = $a->getAttribute('class');
											if( $type == 'link-material' )
												{
													$link=$a->getAttribute('href');
													$name = trim(fix_str($a->textContent));
												}
										}
									$videocount++;
									$fs_title=$videocount.".".$name;
									$temps.=$fs_title."\n".$ua_path_link.$fsua_parser_filename."?play=".urlencode($link)."\n".$link."\n".$name."\n".$fs_title."\n".$ua_path_link.$fsua_parser_filename."?file=".$link."&fav_refresh=1\n";
							}
						}
			}
	$temps = $title." ".$title_name."\n".descr_split($ds)."\n".$image."\n".$videocount."\n".$temps;
	*/
					
					
					$flist=file(check_prefix($file)."&flist");
					foreach ($flist as $links)
					{
						$name=trim(get_name($links));
						$videocount++;	
						$fs_title=$videocount.".".$name;
						$links=trim($links);
						$temps.=$fs_title."\n".$ua_path_link.$fsua_parser_filename."?play=".urlencode($links)."\n".$links."\n".$name."\n".$fs_title."\n".$ua_path_link.$fsua_parser_filename."?file=".$links."&fav_refresh=1\n";
					}
	$temps = $title." ".$title_name."\n".descr_split($ds)."\n".$image."\n".$videocount."\n".$temps;
	
	
	
	
	
	$redirect = $ua_path_link2.$fsua_rss_link_filename;
}
}
file_put_contents( $tmp, $temps );
if ($header)
return array("image"=>$image, "title"=>$title." ".$title_name, "fav_folder"=>$fav_folder,"name1"=>$name1);
else 
return $redirect;
}
 

//---------------------------------------------------------------------------------
// Парсим содержимое фильмов, сериалов (получаем линки на фильмы)
//---------------------------------------------------------------------------------
if(isset($_GET["file"])) {
$file=$_GET["file"];
if (isset($_GET['enter'])) {
if ($_GET['enter']=='1') $enter = true;  else $enter=false;
}
if (isset($_GET['final'])) $final = $_GET["final"];


				if ($main["fav_folder"]) $fav_arr[$num]["type"]="list"; else $fav_arr[$num]["type"]="link";
				$fav_arr[$num]["poster"]=$main["image"];
				$fav_arr[$num]["name"]=$main["title"].$main["name1"];


			if ($enter) 
			{
			$file.="?folder=0";
			$s=file_get_contents(check_prefix($file));
			}
			else $s=file_get_contents(check_prefix($file));
			//file_put_contents("/tmp/test.txt",$s);
			if (isset($_GET["fav_refresh"])) 
			{
				$main=get_data($s,$file,$final,true);			
				echo $main["title"].$main["name1"];
				exit;
			}	
			$redirect=get_data($s,$file,$final);
			header('Location: '.$redirect); 
	
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