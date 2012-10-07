<?php

ini_set("user_agent", "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");

// config
$horosho_config = array(
	'cat'    => 'all',
	'genre'  => '',
	'search' => '',
	'page'   => 1,
	'genres' => array(),
);

$horosho_cats = array(
	'all'      => 'Все сериалы',
	'byrating' => 'По рейтингу',
	'popular'  => 'По популярности',
	'latest'   => 'Новинки',
);



if( is_file( '/usr/local/etc/dvdplayer/horosho.config.php' ) )
{
	include( '/usr/local/etc/dvdplayer/horosho.config.php' );
}
//
// ------------------------------------
function xml_horosho_content()
{
global $mos;
global $horosho_config;
global $horosho_cats;

	header( "Content-type: text/plain" );

	$url = 'http://horoshotv.ru/';

	if( isset( $_REQUEST['cat']))
	{
		$horosho_config['cat']    = $_REQUEST['cat'];
		$horosho_config['genre']  = '';
		$horosho_config['search'] = '';
	}
	elseif( isset( $_REQUEST['genre']))
	{
		$horosho_config['cat']    = '';
		$horosho_config['genre']  = $_REQUEST['genre'];
		$horosho_config['search'] = '';
	}
	elseif( isset( $_REQUEST['search']))
	{
		$horosho_config['cat']    = '';
		$horosho_config['genre']  = '';
		$horosho_config['search'] = $_REQUEST['search'];
	}

	if( $horosho_config['cat'] != '' )
	{
		$url .= 'movies/'. $horosho_config['cat'] .'?listview=1';
		$retUrl = '&cat='. $horosho_config['cat'];
		$title = $horosho_cats[ $horosho_config['cat'] ];
	}
	elseif( $horosho_config['genre'] != '' )
	{
		$url .= 'movies/genre/'. $horosho_config['genre'] .'?listview=1';
		$retUrl = '&genre='. $horosho_config['genre'];
		$title = $horosho_config['genres'][ $horosho_config['genre'] ];
	}
	elseif( $horosho_config['search'] != '' )
	{
		$url .= 'msearch?key='. urlencode( $horosho_config['search'] );
		$retUrl = 'search='. urlencode( $horosho_config['search'] );
		$title = 'Поиск: '. $horosho_config['search'];
	}
	else
	{
		$horosho_config['cat']    = 'all';
		$url .= 'movies/all?listview=1';
		$retUrl = '&cat=all';
		$title = $horosho_cats['all'];
	}

	if( isset( $_REQUEST['p'])) $horosho_config['page'] = $_REQUEST['p'];
	$page = $horosho_config['page'];
	$url .= '&page='. $page;

	// get genres html page
	$s = file_get_contents( 'http://horoshotv.ru/movies/genres' );

	// get genres
	$items = array();

	if( preg_match_all( '/<div><b><a href="([^"]*?)"\s*>([^\/]*?)\/[^<]*?<\/a>/s', $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $l )
	 {
		$a = explode( '/', $l );
		$g = $a[ count( $a ) - 1 ];

		$items[ $g ] = trim( $ss[2][ $i ] );
	 }

	$horosho_config['genres'] = $items;

	// save config
	file_put_contents( '/usr/local/etc/dvdplayer/horosho.config.php', '<?php $horosho_config = '.var_export( $horosho_config, true ).'; ?>' );

	// get html page
	$s = file_get_contents( $url );

	// get content
	$items = array();

	$ps = '/<a href="([^"]*)"[^>]*?>\s*<span[^>]*?>[^<]*?<\/span>\s*<img src="(.*?)".*?\/>\s*<div class="sertitle_ua".*?>(.*?)<\/div>\s*<div class="sertitle_en".*?>(.*?)<\/div>/s';
	if( $horosho_config['cat'] == 'latest' )
	{
		$ps = '/<a href="([^"]*)"[^>]*?>\s*<span[^>]*?>[^<]*?<\/span>\s*<img src="(.*?)".*?\/>\s*<div class="sertitle_ua".*?>(.*?)<\/div>\s*<div class="sertitle_en".*?>.*?<\/div>\s*<div class="ser_describe".*?>(.*?)<\/div>/s';
	}
	elseif( $horosho_config['search'] != '' )
	{
		$s = preg_replace( '#<span class="highlight_word">(.*?)</span>#', '\1', $s );
		$ps = '/<a href="([^"]*)"[^>]*?>\s*<span[^>]*?>[^<]*?<\/span>\s*<img src="(.*?)".*?\/>\s*<div class="sertitle_ua".*?>([^\/]*?)\/(.*?)<\/div>/s';
	}

	if( preg_match_all( $ps, $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $u )
	  $items[] = array(
		'link'     => getMosUrl() .'?page=rss_horosho_info&url=' .$u,
		'image'    => 'http://horoshotv.ru'. $ss[2][ $i ],
		'title'    => trim( $ss[3][ $i ] ),
		'subtitle' => trim( $ss[4][ $i ] ),
	  );

	// get navigation
	$navs = array( 1 );
	if( preg_match( '/<div class="pager">\s*Страница (\d+) из (\d+)\s*<\/div>/s', $s, $ss ) > 0 )
	{
		$navs[] = $ss[1];
		$navs[] = $ss[2];
	}
	sort( $navs );
	$min_page = $navs[0];
	$max_page = $navs[ count( $navs ) - 1 ];

	// generate list
	$s = '';

	// top title
	$s .= 'horosho.tv - '. $title;
	if( $min_page != $max_page ) $s .= ' (страница '. $page .' из '. $max_page .')';

	$s .= PHP_EOL ;

	// bottom title
	$s .= 
		  getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' )
//		. getRssCommandPrompt('play')  . getMsg( 'coreRssPromptWatch' )
		. getRssCommandPrompt('enter') . getMsg( 'coreRssPromptInfo' )
//		. getRssCommandPrompt('stop')  . getMsg( 'coreRssPromptActs' )
		. PHP_EOL;

	// navs
	$url = getMosUrl() .'?page=xml_horosho'. $retUrl;

	if( ( $page - 1 ) >= $min_page ) { $s .= $url.'&p='.($page - 1).PHP_EOL; }
	else $s .= "\n" ;

	if( ( $page + 1 ) <= $max_page ) { $s .= $url.'&p='.($page + 1).PHP_EOL; }
	else $s .= "\n" ;
	
	// number of items
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['subtitle'] .PHP_EOL;
		$s .= $item['image'].PHP_EOL;
		$s .= $item['link'].PHP_EOL;
	}

	if( isset( $_REQUEST['debug']))
	{
		echo $s;
	}
	else
	{
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}
}
//
// ------------------------------------
function rss_horosho_menu_content()
{
global $horosho_cats;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	foreach( $horosho_cats as $cat => $title )
	 $view->items[] = array(
		'title'	=> $title,
		'action'=> 'ret',
		'link'	=> getMosUrl(). '?page=xml_horosho&amp;p=1&amp;cat='. $cat
	 );


	$view->items[] = array(
		'title'	=> 'Жанры',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_horosho_genres'
	);

	$view->items[] = array(
		'title'	=> 'Поиск',
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=xml_horosho&amp;p=1&amp;search='
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_horosho_genres_content()
{
global $horosho_config;

	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;
	$view->position = 1;

	foreach( $horosho_config['genres'] as $genre => $name )
	 $view->items[] = array(
		'title'	=> $name,
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_horosho&amp;p=1&amp;genre='.$genre
	 );

	$view->showRss();
}
//
// ------------------------------------
function cutStrWord( $s, $count )
{
	$len = mb_strlen( $s, 'utf8' );
	if( $len <= $count ) return $s;

	$pos = $count;
	while(( $pos >=0 )&&( mb_substr( $s, $pos, 1, 'utf8' ) <> ' ' )) $pos -= 1;
	return mb_substr( $s, 0, $pos, 'utf8' );
}
//
// ------------------------------------
function rss_horosho_info_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

	$url = $_REQUEST['url'];

header( "Content-type: text/plain" );

	$s=file_get_contents( 'http://www.horoshotv.ru'. $url );

	$title  = '';
	if( preg_match( '#<div class="left_col">\s*<h1>(.*?)</h1>#s' , $s, $ss ) > 0 )
	{
		$title = $ss[1];
	}

	$poster = '';
	if( preg_match( '#<div class="cover_serial"><img src="(.*?)"#s' , $s, $ss ) > 0 )
	{
		$poster = 'http://horoshotv.ru'. $ss[1];
	}

	$genre = '';
	if( preg_match( '#<div class="genre">(.*?)</div>#s' , $s, $ss ) > 0 )
	{
		$g = str_replace( array( '	', '  ', "\n" ), ' ', trim( $ss[1] ) );
		$ss = explode( '<br/>', $g );
		foreach( $ss as $i )
		{
			$i = trim( $i );
			if( $i != '' ) $genre .= $i .PHP_EOL;
		};
	}

	$desc = '';
	if( preg_match( '#<div class="describe">(.*?)</div>#s' , $s, $ss ) > 0 )
	{
		$desc = trim( $ss[1] );
		$desc = str_replace( array( '	', '  ', "\n" ), ' ', $desc );
	}

	$rating = '';
	if( preg_match( '#<div class="counter">(.*?)</div>#s' , $s, $ss ) > 0 )
	{
		$rating = $ss[1];
	}
/*
echo $poster.PHP_EOL;
echo $title.PHP_EOL;
echo $genre.PHP_EOL;
echo $desc.PHP_EOL;
echo $rating.PHP_EOL;
*/

	$desc = $genre ."\n". $desc ."\n\nРейтинг ". $rating;

	$desc = str_replace( '&frasl;', '/', $desc );
	$desc = str_replace( '&hellip;', '...', $desc );
	$desc = str_replace( '&mdash;', '-', $desc );
	$desc = str_replace( array( '&laquo;', '&raquo;' ), '"', $desc );

	// get seasons
	$items = array();

	if( preg_match_all( '#<div class="numbseason">\s*<a href="([^"]*)" class="numb">(.*?)</a>\s*<span class="numbseries">(.*?)</span>\s*<span class="dateseason">(.*?)</span>#s', $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $link )
	  $items[] = array(
		'link'  => getMosUrl() .'?page=rss_horosho_serial&amp;url='. $link,
		'action'=> 'rss',
		'title' => $ss[2][ $i ] .' - '. $ss[3][ $i ] .' ('. $ss[4][ $i ] .')',
	  );

	// generate RSS
	include( 'modules/core/rss_view_info_list.php' );

	class rssHoroshoInfoListView extends rssSkinInfoListView
	{
		const itemAreaX		= 820;
		const itemAreaY		= 70;
		const itemAreaWidth	= 480;
		const itemAreaHeight	= 640;

		const itemWidth		= 480;
		const itemHeight	= 48;

		const itemTextX		= 0;
		const itemTextY		= 4;
		const itemTextWidth	= 475;
		const itemTextHeight	= 40;
		const itemTextLines	= 1;
		//
		// ----------------------------
		public $itemImage = '';
	}
	$view = new rssHoroshoInfoListView;

	$view->items = $items;
	$width = 780;

	if( $title <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 68,
			'width'  => $width,
			'height' => 50,
			'fontSize' => 18,
			'lines' => 1,
			'text' => $title
		);
	}

	if( $poster <> '' )
	{
		list($posterWidth, $posterHeight) = getimagesize( $poster );

		if( $posterWidth > $posterHeight )
		{
			$posterHeight = floor( $posterHeight / $posterWidth * 340 );
			$posterWidth  = 340;
		}
		else
		{
			$posterWidth  = floor( $posterWidth / $posterHeight * 340 );
			$posterHeight = 340;
		}

		$view->infos[] = array(
			'type' => 'image',
			'posX' => 76,
			'posY' => 128,
			'width'  => $posterWidth,
			'height' => $posterHeight,
			'image' => $poster
		);
		$posterWidth  += 36;
	}

	if( $desc <> '' )
	{
		$posX = 40;
		$posY = 128;

		$lineHeight = 26;
		$symbolWidth = 11.5;

		$ds = explode( "\n", $desc );

		$px = $posX + $posterWidth;
		$py = $posY;

		foreach( $ds as $d )
		{
			$d = trim( $d );
			if( strlen( $d ) == 0 )
			{
				$py += $lineHeight;
			}
			else
			while( strlen( $d ) > 0 )
			{
				if( $py >= ( $posY + $posterHeight ) ) $px = $posX;
				$maxWidth = $width + 40 - $px;
				$maxSymbols = floor( $maxWidth / $symbolWidth );

				if( strlen( $d ) < $maxSymbols )
				{
					$dd = $d;
					$d = '';
				}
				else
				{
					$dd = cutStrWord( $d, $maxSymbols );
					$d = trim( substr( $d, strlen( $dd )));
				}
				$view->infos[] = array(
					'type' => 'text',
					'posX' => $px,
					'posY' => $py,
					'width'  => $maxWidth,
					'height' => $lineHeight,
					'lines' => 1,
					'fontSize' => 12,
					'text' => $dd
				);
				$py += $lineHeight;
				if( $py > ( 700 - $lineHeight )) break;
			}
			if( $py > ( 700 - $lineHeight )) break;
		}
	}

	$view->topTitle = 'Horosho.tv';
	$view->bottomTitle = getRssCommandPrompt('enter') . ' Просмотр эпизодов ';

	$view->showRss();
}
//
// ------------------------------------
function rss_horosho_content()
{
	include( 'modules/horosho/horosho_view.php' );
	$view = new rssHoroshoView;

	$view->urlXml  = getMosUrl(). '?page=xml_horosho';
	$view->urlMenu = getMosUrl(). '?page=rss_horosho_menu';

	$view->showRss();
}
//
// ------------------------------------
// Serial part
// ------------------------------------
function xml_horosho_serial_content()
{
	if( ! isset( $_REQUEST['url'] )) return;

	$aSerial = array();

	header( "Content-type: text/plain" );

	$a = explode( '/', $_REQUEST['url'] );
	$season = $a[3];

	$aSerial['season'] = $season;

	// get html page
	$s = file_get_contents( 'http://horoshotv.ru'. $_REQUEST['url'] );

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	// get title

	$as = $doc->getElementsByTagName('h1');
	$title = preg_replace( '#^(.*?)\s*/\s*(.*?)$#s', '\1 / \2', trim( $as->item(0)->textContent ) ) .' - '. $season .' сезон';
	$aSerial['title'] = $title;

	// get seasons
	$items = array();

	$ds = $doc->getElementsByTagName('div');

	foreach( $ds as $d )
	 if( $d->hasAttribute('class'))
	  if( $d->getAttribute('class') == 'numb_seasons' )
	  {
		$as = $d->getElementsByTagName('a');
		foreach( $as as $a )
		{
			$l = $a->getAttribute('href');
			$b = explode( '/', $l );
			$n = $b[3];
			$items[ $n ] = $l;
		}
		break;
	  }

	$aSerial['seasons'] = $items;

	// save config
	file_put_contents( '/usr/local/etc/dvdplayer/horosho.config.php', '<?php $horosho_config = '.var_export( $horosho_config, true ).'; ?>' );


	// get episodes
	$items = array();

	$ps = '#<a href="([^"]*)"[^>]*?>\s*<span class="episodimg box-shadow">\s*<img src="([^"]*?)"[^/]*?/></span>\s*<span class="sertitle">(.*?)</span>\s*<span class="origindate">(.*?)</span>#s';
	if( preg_match_all( $ps, $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $l )
	  $items[] = array(
		'link'     => $l,
		'image'    => 'http://horoshotv.ru'. $ss[2][ $i ],
		'title'    => trim( $ss[3][ $i ] ),
		'subtitle' => trim( $ss[4][ $i ] ),
	  );

	$aSerial['episodes'] = $items;
	file_put_contents( '/tmp/horosho.serial.php', '<?php $aSerial = '.var_export( $aSerial, true ).'; ?>' );

	// generate list
	$s = '';

	// top title
	$s .= 'horosho.tv - '. $title;

	$s .= PHP_EOL ;

	// bottom title
	$s .= 
		  getRssCommandPrompt('menu')  .' сезоны '
		. getRssCommandPrompt('enter') . getMsg( 'coreRssPromptWatch' )
		. PHP_EOL;

	// navs
	$s .= PHP_EOL ;
	$s .= PHP_EOL ;
	
	// number of items
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['subtitle'] .PHP_EOL;
		$s .= $item['image'] .PHP_EOL;
		$s .= getMosUrl(). '?page=rss_player&mod=horosho&url='. $item['link'] .PHP_EOL;
	}

	if( isset( $_REQUEST['debug']))
	{
		echo $s;
	}
	else
	{
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}

}
//
// ------------------------------------
function rss_horosho_serial_content()
{
	if( ! isset( $_REQUEST['url'] )) return;

	include( 'modules/horosho/horosho_view.php' );
	$view = new rssHoroshoView;

	$view->urlXml  = getMosUrl(). '?page=xml_horosho_serial&amp;url='. $_REQUEST['url'];
	$view->urlMenu = getMosUrl(). '?page=rss_horosho_seasons';

	$view->showRss();
}
//
// ------------------------------------
function rss_horosho_seasons_content()
{
	if( !is_file('/tmp/horosho.serial.php')) return;
	include('/tmp/horosho.serial.php');

	include( 'modules/core/rss_view_left.php' );
	$view = new rssSkinLeftView;

	foreach( $aSerial['seasons'] as $n => $l )
	 $view->items[] = array(
		'title'	=> $n .' сезон',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_horosho_serial&amp;url='.$l
	 );

	$view->showRss();
}
//
// ------------------------------------
function horoshoGetPlaylist()
{
	if( !is_file('/tmp/horosho.serial.php')) return;
	include('/tmp/horosho.serial.php');

	$items = array();

	foreach( $aSerial['episodes'] as $item )
	 $items[] = array(
		'link'  => getMosUrl() .'?page=get_horosho&amp;url='. $item['link'],
		'action'=> 'play',
		'title' => $aSerial['title'] .' / '. $item['title'],
		'image' => $item['image']
	);

	return $items;
}
//
// ------------------------------------
function get_horosho_content()
{
global $mos;

	if( ! isset( $_REQUEST['url'] )) return;

	$fp = fopen( 'http://horoshotv.ru'. $_REQUEST['url'], 'r' );
	$meta_data = stream_get_meta_data( $fp );

	$s = implode( "\n", $meta_data['wrapper_data'] );
	if( preg_match( '/Set-Cookie: (mbm3id=.*?);/' , $s, $ss ) > 0 )
	 $cook = $ss[1];

	$s = '';
	while ( !feof( $fp )) $s .= fread( $fp, 8192 );
	fclose( $fp );

	if( preg_match( '#var f_path = \'(.*?)\';#', $s, $ss ) === false ) return;

	$sc = "#!/bin/sh\n"
	. "$mos/bin/curl -s -i --cookie '$cook' --user-agent 'Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1' "
	. $ss[1] .PHP_EOL;

	file_put_contents( '/tmp/www/cgi-bin/horosho.cgi', $sc );
	chmod( '/tmp/www/cgi-bin/horosho.cgi', 0755 );

	$url = 'http://'. $_SERVER['SERVER_ADDR'] .':88/cgi-bin/horosho.cgi';

	header ( 'Location: ' .$url );
}

?>
