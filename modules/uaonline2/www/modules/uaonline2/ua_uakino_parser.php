<?php
/*	------------------------------
	Ukraine online services 	
	uakino.net parser
	module v1.8
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	------------------------------ */

include_once ("ua_paths.inc.php");

set_time_limit(0);
ob_implicit_flush ();


function get_data($s,$view,$header=false)
{
global $tmp;
global $ua_path_link;
global $uakino_parser_filename;
$image="http://uakino.net/media/thumbs/306/".$view.".jpg";
$ua_file = $view.".mp4";
		
		$url = "http://uakino.net/play.php?mid=".$view;
		$fp = fopen( $url, 'r' );
		$meta_data = stream_get_meta_data( $fp );
		$tmps = implode( "\n", $meta_data['wrapper_data'] );
		if( preg_match( '/[Ll]ocation:\s*(.*)/' , $tmps, $tmpss ) > 0 )
		$url = $tmpss[1];
		$url = dirname( $url ) .'/'. urlencode( basename( $url ));


$doc = new DOMDocument();
libxml_use_internal_errors( true );
$doc->loadHTML($s);
$metas= $doc->getElementsByTagName('meta');
foreach( $metas as $meta )
	{
	/*
	if( $meta->hasAttribute('name'))
	if( $meta->getAttribute('name') == 'description' )
		{
			$descr=uakino_utf8_check($meta->getAttribute('content'));
		}
	*/
	if( $meta->hasAttribute('property'))
	if( $meta->getAttribute('property') == 'og:title' )
		{
			$item_name=uakino_utf8_check($meta->getAttribute('content'));
		}
	}	
$title=trim($item_name);
$divs= $doc->getElementsByTagName('div');
foreach( $divs as $div )
	if( $div->hasAttribute('id'))
	if( $div->getAttribute('id') == 'media_description')
	{
		$descr=uakino_utf8_check(trim(strip_tags($div->textContent)));
	}


unset($doc);

$temps = $title."\n".descr_split($descr)."\n".$image."\n1\n".$ua_file."\n".$url."\n".$item_name."\n0\n".$item_name."\n";
if ($header)
{
	return array ("title"=>$title,"image"=>$image) ;
}
else {
		file_put_contents( $tmp, $temps );
		echo $tmp;
	 }
}


	
//----------------------------------------------------------------
// это парсим страницу с ссылкой на кыно. Линк на фильм и описание
//----------------------------------------------------------------
if(isset($_GET["file"])) {
$view = $_GET["file"];
$s=file_get_contents("http://uakino.net/video/".$view);
//$s=iconv("utf-8","cp1251",$s);
//$s=str_replace( "\r", "", $s );

//file_put_contents( "/tmp/test.txt", $s );
if (isset($_GET["fav_refresh"])) 
			{
				$main=get_data($s,$view,true);			
				echo $main["title"];
				exit;
			}
echo get_data($s,$view);
}

//----------------------------------------------------------------
// функция получения списка фильмов для просмотра категорий
//----------------------------------------------------------------
function uakino_getlist($s)
{
global $tmp;
global $ua_path_link;
global $uakino_rss_list_filename;
global $uakino_rss_link_filename;
$temps = '';
$videocount=0;
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
		$cats = array();
		$films = array();
		
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
														$link =$ua_path_link.$uakino_rss_list_filename."?view=".$cat;
														$preview='http://uakino.net'.$preview;
														$type="list";
													}
										else 		{
														$link = $ua_path_link.$uakino_rss_link_filename."?file=".$cat."&image=".$preview;
														$type="link";
													}
										$temps.= fix_str(trim(uakino_utf8_check($titl)))."\n".$link."\n".$preview."\n".$cat."\n".$type."\n";
										$videocount++;
									}
							}
						}
					}
				}
	}
unset($doc);
$temps = $title."\n".$videocount."\n".$temps;
//echo $temps;
file_put_contents( $tmp, $temps );
return $tmp;
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
	if($page) {
		$nt= $page-1;
		
		$s=file_get_contents('http://uakino.net/search_result.php?search_id='.$search.'&search_type_id=search_videos&offset='.$nt*30);
   }
	else {
		$page = 1;
		$s=file_get_contents('http://uakino.net/search_result.php?search_id='.$search.'&search_type_id=search_videos');
		
    }

	echo uakino_getlist($s);
}

// приводит линки UAKINO.NET к правильному виду
function check_uakino_link($url)
{
preg_match( "/uakino.net\/video\/(\d*)/" , $url, $out);
if ($out) return $out[1]; else return $url;
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
