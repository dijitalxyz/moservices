<?php

$radio_top50_cats = array();
if( is_file( '/tmp/radio_top50_cat.php' ) )
{
	include( '/tmp/radio_top50_cat.php' );
}

$type = array(
	0     => null,
	1     => 'Онлайн радио - TOP-50',
	2     => 'Онлайн радио - Избранное',
	3    => 'Онлайн радио - Поиск',
);

function radioGetContent( $url )
{
	$opts = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => "User-Agent: Mozilla/5.0 (Windows NT 5.2; rv:19.0) Gecko/20100101 Firefox/19.0\r\n"
	));
	$context = stream_context_create( $opts );
    
    $s = @file_get_contents( $url, false, $context ); 
	return $s;
}

function xml_radio_top50_content()
{
global $mos; 
global $type;
global $radio_top50_cats;  
    if( isset( $_REQUEST['link'] ))
	{
		$url = $_REQUEST['link'];
	} else 	{
	$url = '/usr/local/etc/mos/www/modules/radio_top50/favorites.dat';	
	}

    if( isset( $_REQUEST['search'] ))
	{
		$url = 'http://guzei.com/online_radio/index.php/?s='. $_REQUEST['search'];
	}
//$url = 'http://guzei.com/online_radio/list/1_1.html';
//$url = 'http://guzei.com/online_radio/index.php?s=%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%B8%D0%B9';

	// top title
    if( isset( $_REQUEST['type'] ))
	{
		$type_id = $_REQUEST['type'];
	} else $type_id = 2; //1
   
	if ($type_id != 2) {

   // get html page   // POST   
    $s = radioGetContent( $url); 
	   
$items = null;

    if( !isset( $_REQUEST['genre'] ))
	{

     $ps = '/<p class="s"(.*?)<\/p>/s';  
	if( preg_match_all( $ps, $s, $ss ) > 0 ) {     
	   foreach( $ss[1] as $i => $u ) {
    $logo = null;
    $link = null;
    $name = null;
    $bitrate = null;
    $city = null;
    $language = null;
    $genre = null;
    $text = null;
    if( preg_match ('/<img .*? src="([^"]*)".*?>/s',$u,$temp))
    $logo = str_replace('./','http://guzei.com/online_radio/',$temp[1]);    
    if( preg_match ('/<a target=.*?href="([^"]*)"/s',$u,$temp))
    $link = str_replace('./','http://guzei.com/online_radio/',$temp[1]);    
    if( preg_match ('/<span class="name">(.*?)<\/span>/s',$u,$temp))
    $name = $temp[1];    
 
	if( preg_match ('/<\/a>(.*?)<a/s',$u,$temp)) {
	$bitrate = $temp[1]; 
    if( preg_match_all ('/<a.*?>(.*?)<\/a>/s',$u,$temp)) {
    $city = $temp[1][1];
    $language = $temp[1][2];
    $genre = $temp[1][3];        
    }
	} else if( preg_match ('/a>.*?:(.*?)<br>/s',$u,$temp)) {
   if( preg_match ('/(.*?):(.*?):(.*?)$/s',$temp[1],$temp_1)) {
	if( isset( $_REQUEST['debug'])) {print_r ($temp_1);}
    $city = trim ($temp_1[1]);
    $language = trim ($temp_1[2]);
    $bitrate = trim ($temp_1[3]);        
    }	
    }
	
    if( preg_match ('/<span class="c">(.*?)<\/span>/s',$u,$temp))
    $text = $temp[1]; 
       
	  $items[] = array(
		'logo' => $logo,
		'link' => $link,
		'name' => $name,
        'bitrate' => $bitrate,
        'city' => $city,
        'language' => $language,
        'genre' => $genre,
        'text' => $text,
        ); 
          
     } 
	 }  } else {
     $ps = '/<li>(.*?):.*?\s*<span class="r">(.*?)<\/span>.*?\s*\((.*?)\).*?\s*<a class="name" href="([^"]*)">.*?\s*(.*?)<\/a>.*?\s*:(.*?)<\/li>/s';  
	if( preg_match_all( $ps, $s, $ss ) > 0 ) 
	   foreach( $ss[1] as $i => $u ) {
	   $logo = null;
	   $logo_2 = null;
//	   $logo = preg_replace('/.*?id=/','http://guzei.com/online_radio/logo/',$ss[4][$i]);
//	   if(@fopen (($logo .'.png'),'r')) { $logo_2 = $logo .'.png'; } 
//	   else if(@fopen (($logo .'.jpg'),'r')) { $logo_2 = $logo .'.jpg'; } 
//	   else if(@fopen (($logo .'.gif'),'r')) { $logo_2 = $logo .'.gif'; }  

    $text = null;
	if( preg_match ('/<\/a> :(.*?)$/s',$ss[6][$i],$temp)) {
    $text = trim($temp[1]);    
	} else { if(!preg_match('/(https?:\/\/)?(guzei.com\/)/s', $ss[6][$i])) {
	$text = preg_replace('/:.*?/','',$ss[6][$i]); 
	} }
	   
	  $items[] = array(
		'logo' => 'null',
		'link' => $ss[4][$i],
		'name' => $ss[2][$i],
        'bitrate' => $ss[5][$i],
        'city' => $ss[3][$i],
        'language' => $radio_top50_cats[$_REQUEST['language']]['title'],
        'genre' => $radio_top50_cats[$_REQUEST['language']][$_REQUEST['genre']]['title'],
        'text' => $text, /* preg_replace('/.*?:/','',$ss[6][$i]), */
        );   
		}
		if( isset( $_REQUEST['debug'])) {print_r ($ss);}
		if( isset( $_REQUEST['debug'])) {print_r ($items);}
	}
	} else {

	$favorites_fc = dirname( __FILE__ ) .'/favorites.php';
if( is_file( $favorites_fc ) )
{
	include( $favorites_fc );
}
	
	  foreach( $favorites_table as $i => $u ) {
	  $items[] = array(
		'name' => $favorites_table[$i]['name'],
		'logo'=> $favorites_table[$i]['logo'],
		'link' => $favorites_table[$i]['link'].'&del=1'.'&id='.$favorites_table[$i]['id'],
		'bitrate' => $favorites_table[$i]['bitrate'],	
		'city' => $favorites_table[$i]['city'],
		'language' => $favorites_table[$i]['language'],
		'genre' => $favorites_table[$i]['genre'],			
        );		
	}	
	
	}
     	// generate list
	$s = '';
	
    if( isset( $_REQUEST['genre_top'] ))
	{
		$genre = $_REQUEST['genre_top'];
	} else $genre = null;

	if (isset( $_REQUEST['language'])) {
	if ($radio_top50_cats[$_REQUEST['language']]['title'] != null) {
	$s .= 'Язык - '. $radio_top50_cats[$_REQUEST['language']]['title'] .' ';
	}
	$s .= 'Жанр - '.$radio_top50_cats[$_REQUEST['language']][$_REQUEST['genre']]['title'] .PHP_EOL;
	} else $s .= $type[$type_id].' '.$genre . $_REQUEST['search'].PHP_EOL;
	$s .= PHP_EOL;

	// bottom title
	$s .= 
		'<< '    . 'меню'
		. ' OK '  . 'слушать '
		. ' DISPLAY' . ' обновить ';
	if( $type_id != '2' )
	 $s .= ' >> '  . ' добавить в избранное ';
	else
	 $s .= ' >> '  . ' удалить из избранного ';
		
	$s .= PHP_EOL;
	// number of items
	if (count( $items ) <50) {
	$s .= count( $items ) . PHP_EOL;
	} else { $s .= '50' . PHP_EOL;}

	foreach( $items as $item )
	{ 
		$text = null;
		$text = trim($item['bitrate']);
		$text = trim($text, ':');
		$text = trim($text);

		$s .= $item['img'] .PHP_EOL;
		$s .= $item['logo'] .PHP_EOL;
		$s .= $item['link'] .PHP_EOL;
		$s .= $item['name'] .PHP_EOL; 
        $s .= $text .PHP_EOL;
        $s .= $item['city'] .PHP_EOL;
        $s .= $item['language'] .PHP_EOL;
        $s .= $item['genre'] .PHP_EOL;
        $s .=  str_replace( '&quot;', ' ', $item['text'] ) .PHP_EOL;
		
	}
	
	if( isset( $_REQUEST['debug']))
	{
		echo $url;
		echo $s;
	}
	else
	{
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}
    
}

function xml_radio_top50_link_content()
{
global $mos;    
    if( isset( $_REQUEST['link'] ))
	{
		$url = $_REQUEST['link'];
	} else return null;

	// get html page   // POST
	$guzei =  parse_url ($url);
	if( isset( $_REQUEST['debug'])) { print_r ($guzei); }

	if ($guzei['host'] == 'guzei.com') {
    $s = radioGetContent( $url); 
	}
    
$items = null;
     $ps = '/<title>(.*?)<\/title>\s*<meta name="description".*?content="([^"]*)".*?\s*img src="([^"]*)"/s';  
	if( preg_match( $ps, $s, $ss ) > 0 ) {
    $items['text'] = $ss[1];
    $items['genre'] = $ss[2];
    $items['logo']= str_replace('./','http://guzei.com/online_radio/',$ss[3]);
    }

     $ps = '/var stream = "([^"]*)"/s';  
	if( preg_match( $ps, $s, $ss ) > 0 ) {
    $items['link'] = $ss[1];
    } else 
//     $ps = '/<param name="FlashVars" value="mp3=([^"]*)"/s';  
	if( preg_match( '/<param name="FlashVars" value="mp3=([^"]*)"/s', $s, $ss ) > 0 ) {
	$output = explode ("&", $ss[1]);
    $items['link'] = $output[0]; 
	if( isset( $_REQUEST['debug'])) {print_r ($output);}
    } else  
//     $ps = '/<embed src="([^"]*)"/s';  
//	if( preg_match( '/<embed src="([^"]*)"/s', $s, $ss ) > 0 ) {
//    $items['link'] = $ss[1];       
//    } else 
	if( preg_match( '/<br><br><br>Click to:<br><br><a href="([^"]*)"/s', $s, $ss ) > 0 ) {
    $s_retro = radioGetContent( $ss[1]); 
		$ss = null;
	if( preg_match_all( '/<li class="button bitrate.*?data-url="([^"]*)"/s', $s_retro, $ss ) > 0 ) {	
    $count_retro = count ($ss[1])-1;
	$items['link'] = $ss[1][$count_retro ]; 	
	}
	if( isset( $_REQUEST['debug'])) {print_r ($ss); echo $s_retro; }
    } else {
	$items['link'] = $url;	
	}
 
 $path_info = pathinfo($items['link']);

if ($path_info['extension']==='asx') {
    $items['link'] = 'Flash-Access';
	$items['text'] = null;
	$items['genre'] = null;
	$items['logo'] = null;
}

$xml = null;		
$xml = '<?xml version="1.0" encoding="UTF-8"?><channel><item>';
$xml .= '<link>'.$items['link'].'</link>';
$xml .= '<text>'.$items['text'].'</text>';
$xml .= '<genre>'.$items['genre'].'</genre>';
$xml .= '<logo>'.$items['logo'].'</logo>';
$xml .= '</item>';
$xml .= '</channel>';
    
	if( isset( $_REQUEST['debug']))
	{
		echo $xml;
	}
	else
	{
		file_put_contents( '/tmp/link.dat', $xml );
		echo "/tmp/link.dat";
	}

}

function rss_radio_top50_menu_content()
{
	include( 'radio_top50.rss.menu.php' );
	$view = new rssRadio_top50LeftView;

	$view->items[] = array(
		'title'	=> 'ТОР-50',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_radio_top50&amp;link='.'http://guzei.com/online_radio/&amp;type=1'
	);
	
	$view->items[] = array(
		'title'	=> 'Избранное',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_radio_top50&amp;link='.'/usr/local/etc/mos/www/modules/radio_top50/favorites.dat&amp;type=2'
	);
	
	$view->items[] = array(
		'title'	=> 'Жанры',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_genres'
	);

	$view->items[] = array(
		'title'	=> 'Поиск',
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=xml_radio_top50&amp;type=3&amp;search='
	);

	$view->items[] = array(
		'title'	=> 'По языкам',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_list'
	);
	
	// settings
	$view->items[] = array(
		'title'	=> 'Настройки',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_sets',
		'border' => 'top'
	);

	$view->showRss();
}

function rss_radio_top50_sets_content()
{
	include( 'radio_top50.rss.menu.php' );
	$view = new rssRadio_top50LeftView;
	
	$view->position = 1;

	$view->items[] = array(
		'title'	=> 'Об.информации',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_info'
	);
	
	$view->items[] = array(
		'title'	=> 'Скринсейвер',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_screen'
	);
	
	$view->items[] = array(
		'title'	=> 'Об.скринсейвера',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_radio_top50_screen_info'
	);
	
	$view->showRss();
}

function rss_radio_top50_info_content()
{
$radio_top50_fc = dirname( __FILE__ ) .'/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}

	include( 'radio_top50.rss.menu.php' );
	$view = new rssRadio_top50LeftView;
	
	$view->position = 2;

	$i = 0;
	$cur = -1;

	$a = array( 0, 10, 20, 30 );
	foreach( $a as $t )
	{
		if( $t == $radio_top50_config['refresh_time_chag'] ) { $cur = $i;}

		if ($t == 0) { $t_t = 'Выключено'; } else { $t_t=$t; }
		$view->items[$i++] = array(
			'title'	=>  $t_t ,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=radio_top50_sets_config&amp;refresh_time_chag=' .$t
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;
	
	$view->showRss();
}

function rss_radio_top50_screen_content()
{
$radio_top50_fc = dirname( __FILE__ ) .'/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}

	include( 'radio_top50.rss.menu.php' );
	$view = new rssRadio_top50LeftView;
	
	$view->position = 2;

	$i = 0;
	$cur = -1;

	$a = array( 0, 30, 60, 90, 120, 180);
	foreach( $a as $t )
	{
		if( $t == $radio_top50_config['screen_time'] ) { $cur = $i;}

		if ($t == 0) { $t_t = 'Выключено'; } else { $t_t=$t; }
		$view->items[$i++] = array(
			'title'	=>  $t_t ,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=radio_top50_sets_config&amp;screen_time=' .$t
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;
	
	$view->showRss();
}

function rss_radio_top50_screen_info_content()
{
$radio_top50_fc = dirname( __FILE__ ) .'/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}

	include( 'radio_top50.rss.menu.php' );
	$view = new rssRadio_top50LeftView;
	
	$view->position = 2;

	$i = 0;
	$cur = -1;

	$a = array( 2, 5, 10, 15, 20);
	foreach( $a as $t )
	{
		if( $t == $radio_top50_config['screensaver_time'] ) { $cur = $i;}

		$view->items[$i++] = array(
			'title'	=>  $t ,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=radio_top50_sets_config&amp;screensaver_time=' .$t
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;
	
	$view->showRss();
}

function radio_top50_sets_config_content()
{
$radio_top50_fc = dirname( __FILE__ ) .'/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}

	header( "Content-type: text/plain" );

	if( isset( $_REQUEST['refresh_time_chag'] )) $radio_top50_config['refresh_time_chag'] = $_REQUEST['refresh_time_chag'];
	if( isset( $_REQUEST['screen_time'] )) $radio_top50_config['screen_time'] = $_REQUEST['screen_time'];
	if( isset( $_REQUEST['screensaver_time'] )) $radio_top50_config['screensaver_time'] = $_REQUEST['screensaver_time'];

if( isset( $_REQUEST['debug']))
{
	echo "radio_top50SaveConfig:\n";
	print_r( $radio_top50_config );
}

	file_put_contents( $radio_top50_fc, '<?php $radio_top50_config = '.var_export( $radio_top50_config, true ).'; ?>');
	
}

function rss_radio_top50_genres_content()
{

$radio_top50_cats = null;
	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;
	$view->position = 1;

$url = 'http://guzei.com/online_radio/list/';
 
	// get html page   // POST
    $s = radioGetContent( $url); 
    
preg_match_all("/.*<tr>(.*)<\/tr>+.*/Ui", $s, $match); //print_r($match);
// /<td title="([^"]*)".*<a.*href="([^"]*)">(.*)<\/a><\/td>/Ui spisok

	 foreach( $match[1] as $i => $u ) {
     $a +=1;
     preg_match_all('/<td title="([^"]*)".*<a.*href="([^"]*)">(.*)<\/a><\/td>/Ui', $u, $ss);
     preg_match('/<td class="c1">(.*)<td/Ui', $u, $sss);
     $a +=1;
     foreach( $ss[1] as $ii => $uu ) {
      if ($sss == null) {
//      $radio_top50_cats[$i]['title']= 'Все';  } else { $radio_top50_cats[$i]['title']= $sss[1];}
      $radio_top50_cats[$i]['title']= null;  } else { $radio_top50_cats[$i]['title']= $sss[1];}
      preg_match('/.*?&quot;(.*?)&quot;/s', trim($ss[1][ $ii ]), $title); 
      if ($title == null) { $title [1]='Все'; }
	  $radio_top50_cats[$i][] = array(
		'title'     => $title[1],
		'link'    =>  str_replace('./','http://guzei.com/online_radio/list/',$ss[2][ $ii ]),
		'subtitle'    => trim( $ss[3][ $ii ]),	//4
	  );
    }
    }
    
    preg_match_all('/<th.*>(.*)<\/th>/Ui', $match[1][0], $table);

/*	  foreach( $radio_top50_cats as $i => $u ) {
	  if ( $radio_top50_cats[$i]['title'] == null) {
	  $view->items[] = array(
		'title' => $radio_top50_cats[$i]['title'].' ('.$radio_top50_cats[$i][0]['subtitle'].')',
		'action'=> 'rss',
		'link' => getMosUrl().'?page=rss_radio_top50_list_genre&amp;id='.$i
        );		
	} }
*/	
	$id = 1;
	  foreach( $radio_top50_cats[$id] as $i => $u ) {

	  if (preg_match('/http:\/\/.*?/s',$radio_top50_cats[$id][$i]['link']) && $radio_top50_cats[$id][$i]['title'] != 'Все') {
	  $view->items[] = array(
		'title' => $radio_top50_cats[$id][$i]['title'].' ('.$radio_top50_cats[$id][$i]['subtitle'].')',
		'action'=> 'ret',
		'link' => getMosUrl().'?page=xml_radio_top50&amp;link='.$radio_top50_cats[$id][$i]['link'].'&amp;genre='.$i.'&amp;language='.$id.'&amp;type=0'
        );		
	} }

	file_put_contents( '/tmp/radio_top50_cat.php', '<?php $radio_top50_cats = '.var_export( $radio_top50_cats, true ).'; ?>' );
	
	$view->showRss();

/*
	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;
	$view->position = 1;

$url = 'http://guzei.com/online_radio/';
 
	// get html page   // POST
    $s = radioGetContent( $url); 
    
     $ps = '/option value="(.*?)".*?\s*>(.*?)</s';  
	if( preg_match_all( $ps, $s, $ss ) > 0 ) 
	  foreach( $ss[1] as $i => $u )
	  $view->items[] = array(
		'title' => $ss[2][$i],
		'action'=> 'ret',
		'link' => getMosUrl().'?page=xml_radio_top50&amp;type=1&amp;link='.'http://guzei.com/online_radio/index.php?radio_format='.$ss[1][$i] .'&amp;genre_top='.urlencode ($ss[2][$i])
        );		

	$view->showRss();
	*/
}

function rss_add_favorites_menu_content()
{	
    if( isset( $_REQUEST['link'] ))
	{
		$link = $_REQUEST['link'];
	}
    if( isset( $_REQUEST['logo'] ))
	{
		$logo = $_REQUEST['logo'];
	}
    if( isset( $_REQUEST['name'] ))
	{
		$name = urlencode ($_REQUEST['name']);
	}
    if( isset( $_REQUEST['del'] ))
	{
		$del = $_REQUEST['del'];
	}
    if( isset( $_REQUEST['bitrate'] ))
	{
		$bitrate = $_REQUEST['bitrate'];
	}
    if( isset( $_REQUEST['city'] ))
	{
		$city = urlencode ($_REQUEST['city']);
	}
    if( isset( $_REQUEST['language'] ))
	{
		$language = urlencode ($_REQUEST['language']);
	}
    if( isset( $_REQUEST['genre'] ))
	{
		$genre = urlencode ($_REQUEST['genre']);
	}
    if( isset( $_REQUEST['id'] ))
	{
		$id = $_REQUEST['id'];
	}

	include( 'modules/core/rss_view_popup.php' );
	$view = new rssSkinPopupView;
 
	if ($del == false ) {
	$view->topTitle = 'Добавить в избранное';
	$view->items[] = array(
		'title'	=> 'Добавить',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_add_favorites&amp;link='.$link 
		.'&amp;logo='. $logo 
		. '&amp;name='. $name 
		. '&amp;bitrate='. $bitrate
		. '&amp;city='. $city
		. '&amp;language='. $language
		. '&amp;genre='. $genre		
	);

	$view->items[] = array(
		'title'	=> 'Отмена',
		'action'=> 'ret',
		'link'	=> null
	);
	} else {
	$view->topTitle = 'Удалить из избранного';
	$view->items[] = array(
		'title'	=> 'Удалить',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_del_favorites&amp;id='.$id 
	);

	$view->items[] = array(
		'title'	=> 'Отмена',
		'action'=> 'ret',
		'link'	=> null
	);	
	}

	$view->showRss();
}

function xml_del_favorites_content()
{
    if( isset( $_REQUEST['id'] ))
	{
		$id = $_REQUEST['id'];
	} 

	$favorites_fc = dirname( __FILE__ ) .'/favorites.php';
if( is_file( $favorites_fc ) )
{
	include( $favorites_fc );
}

$key = remove_dups ($favorites_table, 'id', $id);
if ($key === false) return;
unset ($favorites_table [$key]);
file_put_contents( $favorites_fc, '<?php $favorites_table = '.var_export( $favorites_table, true ).'; ?>' );

}

function remove_dups($array, $key, $row_element) {
     $new_array[0] = $array[0];
     foreach ($array as $item => $current) {
             if ($current[$key]==$row_element) {
					return $item;
             }

     }
     return false;
 }

function xml_add_favorites_content()
{
    if( isset( $_REQUEST['link'] ))
	{
		$link = $_REQUEST['link'];
	} 

    if( isset( $_REQUEST['logo'] ))
	{
		$logo = $_REQUEST['logo'];
	}
    if( isset( $_REQUEST['name'] ))
	{
		$name = $_REQUEST['name'];
	}
    if( isset( $_REQUEST['bitrate'] ))
	{
		$bitrate = $_REQUEST['bitrate'];
	}
    if( isset( $_REQUEST['city'] ))
	{
		$city = $_REQUEST['city'];
	}
    if( isset( $_REQUEST['language'] ))
	{
		$language = $_REQUEST['language'];
	}
    if( isset( $_REQUEST['genre'] ))
	{
		$genre = $_REQUEST['genre'];
	}

	// generate list
	$favorites_fc = dirname( __FILE__ ) .'/favorites.php';
if( is_file( $favorites_fc ) )
{
	include( $favorites_fc );
}

$id = md5($name);	
$favorites_table []= array(
	'id' => $id,
	'name'    => $name,
	'logo' => $logo,
	'link' => $link,	
	'bitrate' => $bitrate,
	'city' => $city,
	'language' => $language,
	'genre' => $genre,	
	);

@file_put_contents( $favorites_fc, '<?php $favorites_table = '.var_export( $favorites_table, true ).'; ?>');
	
}

function rss_radio_top50_list_content()
{
$radio_top50_cats = null;
	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;
	$view->position = 1;

$url = 'http://guzei.com/online_radio/list/';
 
	// get html page   // POST
    $s = radioGetContent( $url); 
    
preg_match_all("/.*<tr>(.*)<\/tr>+.*/Ui", $s, $match); //print_r($match);
// /<td title="([^"]*)".*<a.*href="([^"]*)">(.*)<\/a><\/td>/Ui spisok

	 foreach( $match[1] as $i => $u ) {
     $a +=1;
     preg_match_all('/<td title="([^"]*)".*<a.*href="([^"]*)">(.*)<\/a><\/td>/Ui', $u, $ss);
     preg_match('/<td class="c1">(.*)<td/Ui', $u, $sss);
     $a +=1;
     foreach( $ss[1] as $ii => $uu ) {
      if ($sss == null) {
//      $radio_top50_cats[$i]['title']= 'Все';  } else { $radio_top50_cats[$i]['title']= $sss[1];}
      $radio_top50_cats[$i]['title']= null;  } else { $radio_top50_cats[$i]['title']= $sss[1];}
      preg_match('/.*?&quot;(.*?)&quot;/s', trim($ss[1][ $ii ]), $title); 
      if ($title == null) { $title [1]='Все'; }
	  $radio_top50_cats[$i][] = array(
		'title'     => $title[1],
		'link'    =>  str_replace('./','http://guzei.com/online_radio/list/',$ss[2][ $ii ]),
		'subtitle'    => trim( $ss[3][ $ii ]),	//4
	  );
    }
    }
    
    preg_match_all('/<th.*>(.*)<\/th>/Ui', $match[1][0], $table);

	  foreach( $radio_top50_cats as $i => $u ) {
	  if ( $radio_top50_cats[$i]['title'] != null) {
	  $view->items[] = array(
		'title' => $radio_top50_cats[$i]['title'].' ('.$radio_top50_cats[$i][0]['subtitle'].')',
		'action'=> 'rss',
		'link' => getMosUrl().'?page=rss_radio_top50_list_genre&amp;id='.$i
        );		
	} }

	file_put_contents( '/tmp/radio_top50_cat.php', '<?php $radio_top50_cats = '.var_export( $radio_top50_cats, true ).'; ?>' );
	
	$view->showRss();
}

function rss_radio_top50_list_genre_content()
{
global $radio_top50_cats;
    if( isset( $_REQUEST['id'] ))
	{
		$id = $_REQUEST['id'];
	} else return null;

	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;
	$view->position = 2;
	
	  foreach( $radio_top50_cats[$id] as $i => $u ) {

	  if (preg_match('/http:\/\/.*?/s',$radio_top50_cats[$id][$i]['link']) && $radio_top50_cats[$id][$i]['title'] != 'Все') {
	  $view->items[] = array(
		'title' => $radio_top50_cats[$id][$i]['title'].' ('.$radio_top50_cats[$id][$i]['subtitle'].')',
		'action'=> 'ret',
		'link' => getMosUrl().'?page=xml_radio_top50&amp;link='.$radio_top50_cats[$id][$i]['link'].'&amp;genre='.$i.'&amp;language='.$id.'&amp;type=0'
        );		
	} }
	$view->showRss();
}

function rss_radio_top50_information_content()
{

header( "Content-type: text/plain" );


		$view = new rssSkinRadioTop50InformationView;
	$view->showRss();
}

function rss_radio_top50_screensaver_content()
{
	if( ! isset( $_REQUEST['url_screensaver'] )) return;
	$url_screensaver = $_REQUEST['url_screensaver'];
//	$url_screensaver = getMosUrl().'modules/radio_top50/button.png'; 
	

	$radio_top50_fc = '/usr/local/etc/mos/www/modules/radio_top50/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}
	include( 'rss_screensaver_radio_top50_2.php' );
	$view = new rssSkinRadioTop50ScreensaverView;
    $view->screensaver_time = $radio_top50_config['screensaver_time']; 
    $view->url_screensaver = $url_screensaver; 
      
	$view->showRss();
}

?>