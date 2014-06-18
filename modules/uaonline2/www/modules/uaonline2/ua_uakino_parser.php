<?php
/*	------------------------------
	Ukraine online services 	
	uakino.net parser
	module v2.2
	------------------------------
	Created by Sashunya 2014
	wall9e@gmail.com			
	------------------------------ */
include_once ("ua_paths.inc.php");
//header('Content-Type: text/html; charset=utf-8');
set_time_limit(0);
ob_implicit_flush ();

// получение ссылки на фильм 11.11.2013
function get_playlink($view)
{
	$pref = "http://api.uakino.net/ifr_uakino.php?mid=";
	preg_match("/(\d+)\-/",$view,$ot);
	$digit = $ot[1];
	$parse_plink=$pref.$digit;
	$parse_plink = file_get_contents($parse_plink);
	preg_match("/(?<=file\"\:\")(.*?)(?=\")/",$parse_plink,$ot);
	return $ot[0];
}

function get_data($s,$view,$header=false)
{
global $tmp;
global $ua_path_link;
global $uakino_parser_filename;
global $tmpdescr;

$url = get_playlink($view);

$doc = new DOMDocument();
libxml_use_internal_errors( true );
$doc->loadHTML($s);

$divs= $doc->getElementsByTagName('div');
foreach( $divs as $div )
	{
	if( $div->hasAttribute('id'))
	{
		if( $div->getAttribute('id') == 'media_description')
		{
			$descr=uakino_utf8_check(trim(strip_tags($div->textContent)));
		} 
	}
	if( $div->hasAttribute('class'))
		{
			if( $div->getAttribute('class') == 'poster')
				{
					$imgs=$div->getElementsByTagName('img');
					foreach ($imgs as $img)
					{
						$purl = $img->getAttribute('src');
						$image= "/tmp/poster".rand(0, 1000);
						shell_exec("rm -f /tmp/poster*");
						shell_exec("wget -c -O ".$image." ".$purl);
						$item_name = uakino_utf8_check($img->getAttribute('title'));
					}
				}
//-------------------------------------------------------------------------------------

			if( $div->getAttribute('class') == 'tab')
			{
				$uls=$div->getElementsByTagName('ul');
				foreach ($uls as $ul)
					{
						if ($ul->hasAttribute('class'))
						{
							if( $ul->getAttribute('class') == 'media_info')
							{
							    
								$short=$ul->textContent;
								preg_match("/год\sвыхода\:\s?(.+)\n?/iu", $short, $year);
								preg_match("/жанр\:\s?(.+)\n/iu", $short, $genre);
								preg_match("/режиссер\:\s?(.+)\n/iu", $short, $director);
								preg_match("/в\sролях\:\s?(.+)\n/iu", $short, $cast);
								preg_match("/страна\:\s?(.+)\n/iu", $short, $country);
								preg_match("/киностудия\:\s?(.+)\n/iu", $short, $company);
								preg_match("/язык\:\s?(.+)\n/iu", $short, $language);
								preg_match("/перевод\:\s?(.+)\n/iu", $short, $trans);
								$ds = null;
								$cnt=8;
								if ($year) {$ds .= "Год: ".$year[1]."\n";  $cnt--;}
								if ($genre) {$ds .= "Жанр: ".$genre[1]."\n";  $cnt--;}
								if ($director) {$ds .= "Режиссер: ".$director[1]."\n";  $cnt--;}
								if ($country) {$ds .= "Страна: ".$country[1]."\n";  $cnt--;}
								if ($cast) {$ds .= "В ролях: ".$cast[1]."\n";  $cnt--;}
								if ($company) {$ds .= "Компания: ".$company[1]."\n";  $cnt--;}
								if ($trans) {$ds .= "Перевод: ".$trans[1]."\n";  $cnt--;}
								if ($language) {$ds .= "Язык: ".$language[1]."\n"; $cnt--;}
								for ($i=0; $i<$cnt; $i++) $ds.="\n";
								break;
								
							}
							
						}
					
					}
			}
//-----------------------------------------------------------------------------------------			
		}
	}

unset($doc);

$temps = $item_name."\n".$ds."\n".$image."\n".$purl."\n1\n".basename($url)."\n".$url."\n".$item_name."\n0\n".$item_name."\n";
if ($header)
{
	return array ("title"=>$item_name,"image"=>$purl) ;
}
else {
		file_put_contents( $tmp, $temps );
		file_put_contents( $tmpdescr,descr_split($descr,65,15));
		echo $tmp;
	 }
	 
}


	
//----------------------------------------------------------------
// это парсим страницу с ссылкой на кыно. Линк на фильм и описание
//----------------------------------------------------------------
if(isset($_GET["file"])) {
$view = $_GET["file"];
$s=file_get_contents("http://uakino.net/video/".$view);
if (isset($_GET["fav_refresh"])) 
			{
				$main=get_data($s,$view,true);			
				echo $main["title"]."\n".$main["image"];
				exit;
			}
echo get_data($s,$view);
}

//----------------------------------------------------------------
// функция получения списка фильмов для просмотра категорий
//----------------------------------------------------------------
function uakino_preplist($s)
{
$doc = new DOMDocument();
libxml_use_internal_errors( true );
$doc->loadHTML($s);
$titles=$doc->getElementsByTagName('title');	
foreach( $titles as $title2 ) $title=uakino_utf8_check($title2->textContent);
$divs= $doc->getElementsByTagName('div');
foreach( $divs as $div )
	if( $div->hasAttribute('class'))
	if( $div->getAttribute('class') == 'tab media_line' || $div->getAttribute('class') == 'media_line')
	{
		$links = array();
		
				$divs2 = $div->getElementsByTagName('div');
				foreach( $divs2 as $div2 )
				if( $div2->hasAttribute('class'))
				{
					$items = $div2->getAttribute('class');
					if( $items == 'media_line_item odd' || $items == 'media_line_item even' || $items == 'media_line_item' )
					{
						$as = $div2->getElementsByTagName('a');
						foreach( $as as $a )
						{
							if ($a->hasAttribute('class'))
							if ( $a->getAttribute('class') == 'fleft thumb' )
							{
								$cat = $a->getAttribute('href');
								if(preg_match("/category\/video\//", $cat))
								{
									$categ=true;
									$cat = str_replace( 'category/video/','', $cat );	
					
								} else
								{
									$categ=false;
									$cat = str_replace( 'video/','', $cat );

								}
								$imgs = $a->getElementsByTagName('img');
								foreach ($imgs as $img)
									{
										$titl = 	$img->getAttribute('alt');
										$preview = 	$img->getAttribute('src');
								
										if ($categ) {
														$preview='http://uakino.net'.$preview;
														$type="list";
													}
										else $type="link";
										$links[]= array(
											'link' => $cat,
											'image' => $preview,
											'type' => $type,
											'title' => fix_str(trim(uakino_utf8_check($titl))),
										);
										
									}
							}
						}
					}
				}
	}
unset($doc);
return array('links'=>$links, 'title' => $title);
}

// функция формирует выходной файл
function uakino_getlist($s)
{
global $tmp;
global $ua_path_link;
global $uakino_rss_list_filename;
global $uakino_rss_link_filename;
$temps = '';
$videocount=0;
$links = uakino_preplist($s);
foreach ($links['links'] as $val)
{
	if ($val["type"]=='list') 
		{
			$link =$ua_path_link.$uakino_rss_list_filename."?view=".$val["link"];
			$preview='http://uakino.net'.$val["image"];
			$type="list";
		}
	else
		{
			$link = $ua_path_link.$uakino_rss_link_filename."?file=".$val["link"]."&image=".$val["image"];
			$type="link";
		}
	$temps.= $val["title"]."\n".$link."\n".$val["image"]."\n".$val["link"]."\n".$type."\n";
	$videocount++;
}
$temps = $links['title']."\n".$videocount."\n".$temps;
file_put_contents( $tmp, $temps );
return $tmp;
}

function uakino_search($search, $page, $global=false)
{
	if($page) {
		$nt= $page-1;
		
		$s=file_get_contents('http://uakino.net/search_result.php?search_id='.$search.'&search_type_id=search_videos&offset='.$nt*30);
   }
	else {
		$page = 1;
		$s=file_get_contents('http://uakino.net/search_result.php?search_id='.$search.'&search_type_id=search_videos');
		
    }
	if ($global) return uakino_preplist($s);
	else
	return uakino_getlist($s);
}


//---------------------------------------------------------------------------------
// тут парсим странцы, когда попадаем в нужную категорию, например в Зарубежные сериалы
// эту функцию вызывать до тех пор пока категория не будет равна 0
// last - последние поступления на сайте
//---------------------------------------------------------------------------------
if (isset($_GET['view'])){
	$view = $_GET['view'];


if (isset($_GET['page'])){
	$page = $_GET['page'];
	}

	if($page) {
		$nt= $page-1;
		if ($view=="last")
		{
			$s=file_get_contents('http://uakino.net/video/?order='.$ua_sort.'&offset='.$nt*16);			
		}else

			$s=file_get_contents('http://uakino.net/category/video/'.$view.'?order='.$ua_sort.'&offset='.$nt*16);			
    }
	else {
		$page = 1;
       	if ($view=="last")
		{
			$s=file_get_contents('http://uakino.net/video/?order='.$ua_sort.'&offset=0');
		} else
			$s=file_get_contents('http://uakino.net/category/video/'.$view.'?order='.$ua_sort.'&offset=0');
    }
	echo uakino_getlist($s);
}

//---------------------------------------------------------------------------------
// Тут поиск
//---------------------------------------------------------------------------------
if (isset($_GET['search'])){
	$search = $_GET['search'];
	$search=urlencode($search);
	
if (isset($_GET['page'])){
	$page = $_GET['page'];
	}
	echo uakino_search($search, $page);
}



// приводит линки UAKINO.NET к правильному виду
function check_uakino_link($url)
{
preg_match( "/uakino.net\/video\/(.*?.html)/" , $url, $out);
if ($out) return $out[1]; else return $url;
return $out;
}



//---------------------------------------------------------------------------------
//  тут переключаем режимы сортировки категорий
//---------------------------------------------------------------------------------
if(isset($_GET["sort"])){
	if ($_GET["sort"]=='req'){
	echo $ua_sort;
	}
	if ($_GET["sort"]=='send')	{
		$ua_sort=$_GET["ua_sort_val"];
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('uakino','sort',$ua_sort);
		$ini->updateFile();
		unset($ini);
	}

}



?>
