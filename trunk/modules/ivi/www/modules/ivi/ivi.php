<?php
// get config
$ivi_config = array(
	'cat'     => 'all',
	'sub'     => 'all',
	'sort'    => 'by_views',
	'page'    => 0,
	'search'  => '',
	'quality' => 'MP4-hi',
	'keyboard' => 'rss',
);

$ivi_fc = dirname( __FILE__ ) .'/ivi.config.php';
if( is_file( $ivi_fc ) )
{
	include( $ivi_fc );
}

// get session categories
$ivi_cats = array();
if( is_file( '/tmp/ivi.cats.php' ) )
{
	include( '/tmp/ivi.cats.php' );
}

// set sorting orders
$ivi_sorts = array(
	'by_views'   => 'Популярные сегодня',
	'by_alltime' => 'Самые популярные',
	'by_year'    => 'По новизне',
	'by_new'     => 'По дате добавления',
	'by_imdb'    => 'По рейтингу IMDB',
	'by_kp'      => 'По рейтингу Кинопоиска',
	'by_ivi'     => 'По рейтингу ivi.ru',
	'by_gross'   => 'По сборам',
	'by_budget'  => 'По бюджету',
);

// set qualities
$ivi_quals = array(
	'MP4-SHQ'	=> 'Высокое (MP4-SHQ)',
	'MP4-hi'	=> 'Среднее (MP4-hi)',
	'MP4-lo'	=> 'Низкое (MP4-lo)',
	'MP4-mobile'	=> 'Высокое (mobile)',
	'MP4-low-mobile'=> 'Низкое (mobile)',
);

//
// ------------------------------------
function getIviConfigParameter( $name )
{
global $ivi_config;

	return $ivi_config[ $name ];
}
//
// ------------------------------------
function iviGetContent( $url )
{
	$s = file_get_contents( $url );
	return $s;
}
//
// ------------------------------------
function iviGetPlaylist( $s = '' )
{
	$items = array();

	if( ! isset( $_REQUEST['url'])) return $items;
	$url = $_REQUEST['url'];

	if( $s == '' )
	{
		$s = iviGetContent( 'http://www.ivi.ru'. $url );
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	$uls = $doc->getElementsByTagName('ul');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('class')) 
	if ( $ul->getAttribute('class') == 'jcarousel-skin-seasons seasons-list' )
	{
		$lis = $ul->getElementsByTagName('a');
		foreach( $lis as $li )
		{
			$link = $li->getAttribute('href');
			$title_season = trim( $li->textContent );			

			$items[] = array(
				'link'  => getMosUrl() .'?page=rss_ivi_info&amp;url='. $link,
				'action'=> 'rss',
				'title' => trim( $title_season ),
				'image' => ''
			);
		}	
		break;
	}
	elseif( $ul->getAttribute('class') == 'episodes-gallery' )
	{
		$lis = $ul->getElementsByTagName('li');
		foreach( $lis as $li )
		{
			$as = $li->getElementsByTagName('strong');
			if( $as->length == 0 ) continue;

			$as = $as->item(0)->getElementsByTagName('a');
			if( $as->length == 0 ) continue;

			$link = $as->item(0)->getAttribute('href');
			$title = trim( $as->item(0)->textContent );

			$as = $li->getElementsByTagName('img');
			if( $as->length > 0 )
			{
				$poster = dirname( $as->item(0)->getAttribute('src') );
			}

			$items[] = array(
				'link'  => getMosUrl() .'?page=get_ivi&amp;url='. $link,
				'action'=> 'get',
				'title' => trim( $title ),
				'image' => $poster
			);
		}	
	}

	if( count( $items ) == 0 )
	{
		$title  = '';
		$poster = '';

		if( preg_match( '/<div class="image">.*?<img src="([^"]+)"/' , $s, $ss ) > 0 )
		{
			$poster = $ss[1];
		}

		if( preg_match( '/<meta name="mrc__share_title" content="([^"]+)"\/>/' , $s, $ss ) > 0 )
		{
			$title = $ss[1];
		}

		$items[] = array(
			'link'  => getMosUrl() .'?page=get_ivi&amp;url='. $url,
			'action'=> 'get',
			'title' => trim( $title ),
			'image' => $poster
		);
	}

if( isset( $_REQUEST['debug'] )) print_r( $items );

	return $items;
}
//
// ------------------------------------
function getIviVideo( $url )
{
global $mos;
global $ivi_config;

	$s = iviGetContent( 'http://www.ivi.ru'. $url );

	if( preg_match( '/<input type="text".*videoId=(\d*)\&/' , $s, $ss ) > 0 )
	{
		$videoId = $ss[1];
	}
	else return '';
if( isset( $_REQUEST['debug'] )) echo"videoId=$videoId\n";


	$p = '{"method":"da.content.get","params":["'.$videoId .'",{"site":1}]}';
	// POST
	$opts = array(
		'http' => array(
			'method'  => 'POST',
			'header'  => "User-Agent: Mozilla/5.0 (Windows NT 5.2; rv:19.0) Gecko/20100101 Firefox/19.0\r\n"
				."Referer: http://www.ivi.ru/images/da/skin1.64.swf?\r\n"
				."FlashAuth: Basic Zmxhc2hfcGxheWVyOmZsYXNoX3BsYXllcg==\r\n",
			'content' => $p
	));
	$context = stream_context_create( $opts );
	$s = file_get_contents( 'http://api.digitalaccess.ru/api/json/', false, $context );
	$ss=json_decode($s, true);

if( isset( $_REQUEST['debug'] )) print_r( $ss );

	if( ! is_array( $ss['result']['files'] )) return '';

	$link = '';
	foreach( $ss['result']['files'] as $item )
	{
		$link = (preg_replace ('/http:.*?,[0-9]{0,}\/\s*(.*?)/s','http://',$item['url']));
		if( $item['content_format'] == $ivi_config['quality'] ) break;
	}

	return $link;
}
//
// ------------------------------------
function getIviIconVideo( $url )
{
global $ivi_config;

	$id = basename( $url );

	// get uid
	$s = file_get_contents( 'http://files.iconbit.com/file/ivi/video.php?id=' . $id );
	if( preg_match( '/player\.php\?uid=([\d\.]*?)&/' , $s, $ss ) > 0 )
	 $uid = $ss[1];
	else return '';
if( isset( $_REQUEST['debug'] )) echo"uid=$uid\n";

	// get video url
	$s = file_get_contents( 'http://files.iconbit.com/file/ivi/player.php?uid='. $uid .'&id='. $id .'&q='. $ivi_config['quality'] );
	if( preg_match( '/video_url = "(.*?)";/' , $s, $ss ) > 0 )
	 $u = str_replace( '&amp;', '&', $ss[1] );
	else return '';
if( isset( $_REQUEST['debug'] )) echo"video=$u\n";

	if( preg_match( '/url = getURL\("(.+?)"\+video_url\);/' , $s, $ss ) > 0 )
	 $q = str_replace( '&amp;', '&', $ss[1] );
	else return '';
if( isset( $_REQUEST['debug'] )) echo"query=$q\n";

	$s = file_get_contents( $q . $u );

	if( $s === false || $s == '' ) return '';

	return $s;
}
//
// ------------------------------------
function get_ivi_content()
{
global $mos;
global $ivi_config;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['url'])) return;

	$url = $_REQUEST['url'];
	$u = getIviVideo( $url );
	if( $u == '' ) $u = getIviIconVideo( $url );
	if( $u == '' ) return;
	echo $u;
}
//
// ------------------------------------
function get_ivi_icon_player_content()
{
global $mos;
global $ivi_config;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['url'])) return;

	$id = basename( $_REQUEST['url'] );

	// get uid
	$s = file_get_contents( 'http://files.iconbit.com/file/ivi/video.php?id=' . $id );
	if( preg_match( '/player.php\?uid=([\d\.])+&/' , $s, $ss ) > 0 )
	 $uid = $ss[1];
	else return;


	echo 'http://files.iconbit.com/file/ivi/player.php?uid='. $uid .'&id='. $id .'&q='. $ivi_config['quality'];
}
//
// ------------------------------------
function xml_ivi_content()
{
global $mos;

global $ivi_config;
global $ivi_cats;
global $ivi_sorts;

global $ivi_fc;

header( "Content-type: text/plain" );

	$url = 'http://www.ivi.ru/videos/all/';

	if( isset( $_REQUEST['cat'])) $ivi_config['cat'] = $_REQUEST['cat'];
	$url .= $ivi_config['cat'] .'/';

	if( isset( $_REQUEST['sub'])) $ivi_config['sub'] = $_REQUEST['sub'];
	$url .= $ivi_config['sub'] .'/';

	if( isset( $_REQUEST['sort'])) $ivi_config['sort'] = $_REQUEST['sort'];
	$url .= $ivi_config['sort'] .'/';

	if( isset( $_REQUEST['quality'])) $ivi_config['quality'] = $_REQUEST['quality'];
		
	if( isset( $_REQUEST['search']))
	{
		$ivi_config['search'] = $_REQUEST['search'];
//		$url = 'http://www.ivi.ru/search/simple/?q='.$ivi_config['search'];
		$url = 'http://www.ivi.ru/search/?q='.$ivi_config['search'] .'&index[title]=1&index[description]=1';
	}
	else { $ivi_config['search'] ='';

	$ivi_config['page'] = 0;
	if( isset( $_REQUEST['p'])) $ivi_config['page'] = $_REQUEST['p'];

	$url .= '?al=1&offset='. $ivi_config['page']; }

	// save config
	file_put_contents( $ivi_fc, '<?php $ivi_config = '.var_export( $ivi_config, true ).'; ?>' );


if( isset( $_REQUEST['debug'])) echo "url=$url\n";

	// get html page
	$s = iviGetContent( $url );
	$doc = new DOMDocument();

	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	// get categoties
	$uls = $doc->getElementsByTagName('ul');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('class'))
	if( $ul->getAttribute('class') == 'main-nav' )
	{
		$ivi_cats = array();

		$lis = $ul->getElementsByTagName('li');
		foreach( $lis as $li )
		{
			$as = $li->getElementsByTagName('a');
			foreach( $as as $a )
			{
				$c = $a->getAttribute('href');		
				$name = $a->textContent;
				$ss = explode( '/', $c );
				$pref = $ss[0];
				if( $pref == 'http:' ) continue;	
				$cat = $ss[3];
				if( $cat == '' ) continue;			
				$sub = $ss[4];				
				if( $sub == 'all' )
				{
					$ivi_cats[ $cat ] = array(
						'name' => $name
					);
				}
				else
				{
					$ivi_cats[ $cat ]['subs'][ $sub ] = array(
						'name' => $name
					);
				}
			}
		}
		break;
	}
	file_put_contents( '/tmp/ivi.cats.php', '<?php $ivi_cats = '.var_export( $ivi_cats, true ).'; ?>' ); 

	// get content
	$links = array();

	if( $ivi_config['search'] != '' )
	{
		$dvs = $doc->getElementsByTagName('div');
		foreach( $dvs as $dv )
		if( $dv->hasAttribute('class'))
		if( $dv->getAttribute('class') == 'content-line best-content' )
		{
			$ls = $dv->getElementsByTagName('div');
			foreach( $ls as $li )
			if( $li->hasAttribute('class'))
			if( $li->getAttribute('class') == 'image' )
			{
				$link  = '';
				$title = '';
				$image = '';
				$price = '';

				$as = $li->getElementsByTagName('a');
				$link = $as->item(0)->getAttribute('href');

				$is = $li->getElementsByTagName('img');
				if( $is->length > 0 )
				{
					$image = $is->item(0)->getAttribute('src');
					$title = trim( $is->item(0)->getAttribute('alt') );
				}
				$is = $li->getElementsByTagName('span');
				if( $is->length > 0 )
				{
					$price = trim( $is->item( 0 )->textContent );
				}
				 $links[] = array(
					'image' => $image,
					'link'  => $link,
					'title' => $title,
			                'price' => $price
				 );
			}
		} 
		else if( $dv->getAttribute('class') == 'content-line clear-line-wrapper' )
		{
			$ls = $dv->getElementsByTagName('div');
			foreach( $ls as $li )
			if( $li->hasAttribute('class'))
			if( $li->getAttribute('class') == 'image' )
			{

				$as = $li->getElementsByTagName('a');
				$link = $as->item(0)->getAttribute('href');

				$is = $li->getElementsByTagName('img');
				if( $is->length > 0 )
				{
					$image = $is->item(0)->getAttribute('src');
					$title = trim( $is->item(0)->getAttribute('alt') );
				}
				$is = $li->getElementsByTagName('span');
				if( $is->length > 0 )
				{
					$price = trim( $is->item( 0 )->textContent );
				}
				 $links[] = array(
					'image' => $image,
					'link'  => $link,
					'title' => $title,
			                'price' => $price
				 );
			}
		}
		
	}
	else
	{
		$dvs = $doc->getElementsByTagName('ul');
		$abc='plus_promo_catalog';	
	
		foreach( $dvs as $dv )
		if( $dv->hasAttribute('id'))
		if( $dv->getAttribute('id') == $abc )
		{
			$lis = $dv->getElementsByTagName('li');
			foreach( $lis as $li )
			{
				$link  = '';
				$title = '';
				$image = '';

				$as = $li->getElementsByTagName('a');
				foreach( $as as $a )
				{
					$img = $a->getElementsByTagName('img');
					if( $img->length > 0 )
					{
						$image = $img->item( 0 )->getAttribute('src');
						$link = $a->getAttribute('href');
						break;
					}
				}

				$pric = $li->getElementsByTagName('span');
				if( $pric->length > 0 )
				{
					$price = trim( $pric->item( 0 )->textContent );			
				}

				$h = $li->getElementsByTagName('strong');
				if( $h->length > 0 )
				{
					$title = trim( $h->item( 0 )->textContent );			
				}
						
				if( $link != '' )
				 $links[] = array(
					'image' => $image,
					'link'  => $link,
					'title' => $title,
			                'price' => $price
				 );
			}
			break;
		}
	}
	
if( isset( $_REQUEST['debug'] )) print_r( $links );

	// get navigation
	$page = $ivi_config['page'];

	$prev_page = -1;
	if( $page >= 20 ) $prev_page = $page - 20;

	$next_page = -1;

	$uls = $doc->getElementsByTagName('a');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('id'))
	if( $ul->getAttribute('id') == 'plus_more_button' )
	{
		$link_page = 'http://www.ivi.ru'.($ul->getAttribute('href'));
	        preg_match ('/.*?offset=([0-9]+)/', $link_page, $ss) ;
	        $next_page = $ss [1];
	}

	// generate list
	$s = '';

	// title
	$s .= 'ivi.ru';

	$cat    = $ivi_config['cat'];
	$sub    = $ivi_config['sub'];
	$sort   = $ivi_config['sort'];
	$search = $ivi_config['search'];
	$page   = $ivi_config['page'];

	if( $ivi_config['search'] <> '' )
	{
		$s .= ' - Результаты поиска: '. $ivi_config['search'];
	}
	else
	{
		if( $cat <> 'all' )
		{
			$s .= ' - '. $ivi_cats[ $cat ]['name'];
			if( $sub <> 'all' ) $s .= ' - '. $ivi_cats[ $cat ]['subs'][ $sub ]['name'];
		}
		else $s .= ' - Все';
		$s .= ' ('. $ivi_sorts[ $sort ] .')';
	}
	$s .= PHP_EOL ;

	// navs
	$url = getMosUrl().'?page=xml_ivi';
	if( $search == '' )
	{
		if( $cat  <> '' ) $url .= '&cat='. $cat;
		if( $sub  <> '' ) $url .= '&sub='. $sub;
		if( $sort <> '' ) $url .= '&sort='. $sort;
	}
	else $url .= '&search='. urlencode( $search );

	if( $prev_page != -1 ) $s .= $url.'&p='. $prev_page;
	$s .= PHP_EOL;

	if( $next_page != -1 ) $s .= $url.'&p='. $next_page;
	$s .= PHP_EOL;
	
	// number of items
	$s .= count( $links ) . PHP_EOL;

	foreach( $links as $item )
	{
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['image'].PHP_EOL;
		$s .= $item['link'].PHP_EOL;
		$s .= $item['price'].PHP_EOL;
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
function rss_ivi_menu_content()
{
global $ivi_cats;

	include( 'rss_view_left.php' );

	$view = new rssIviLeftView;

	$view->items = array(
		0 => array(
			'title'	=> 'Поиск',
			'action'=> 'search',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;search='
		),
		1 => array(
			'title'	=> 'Сортировка',
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_ivi_sort'
		),
		2 => array(
			'title'	=> 'Все',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;cat=all&amp;sub=all'
		)
	);

	foreach( $ivi_cats as $cat => $item )
	{
		if ($item['name'] != null)
		$view->items[] = array(
			'title'	=> $item['name'],
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_ivi_category&amp;cat='.$cat
		);
	}

	$view->items[] = array(
		'title'	=> 'Качество',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_ivi_sets'
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_sort_content()
{
global $ivi_config;
global $ivi_sorts;

	include( 'rss_view_left.php' );

	$view = new rssIviLeftView;

	$view->position = 1;

	$i = 0;
	foreach( $ivi_sorts as $sort => $name )
	{
		if( $sort == $ivi_config['sort'] ) $view->currentItem = $i;

		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;sort='.$sort
		);
		$i++;
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_category_content()
{
global $ivi_config;
global $ivi_cats;

	if( ! isset( $_REQUEST['cat'] )) return;
	$cat = $_REQUEST['cat'];

	include( 'rss_view_left.php' );

	$view = new rssIviLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> 'Все',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;cat='. $cat .'&amp;sub=all'
		)
	);

	$i = 1;
	foreach( $ivi_cats[ $cat ]['subs'] as $sub => $item )
	{
		if( $sub == $ivi_config['sub'] ) $view->currentItem = $i;
		$view->items[] = array(
			'title'	=> $item['name'],
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;cat='. $cat .'&amp;sub='. $sub
		);
		$i++;
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_sets_content()
{
global $ivi_config;
global $ivi_quals;

	include( 'rss_view_left.php' );

	$view = new rssIviLeftView;

	$view->position = 1;

	$i = 0;
	foreach( $ivi_quals as $qual => $title )
	{
		if( $qual == $ivi_config['quality'] ) $view->currentItem = $i;
		$view->items[] = array(
			'title'	=> $title,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;quality='. $qual
		);
		$i++;
	}

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
function rss_ivi_info_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

	$url = $_REQUEST['url'];

header( "Content-type: text/plain" );

	$s=iviGetContent( 'http://www.ivi.ru'. $url );

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	$infos = array();
	
	$title  = '';
	if( preg_match( '/<meta name="mrc__share_title" content="(.*?)"\/>/' , $s, $ss ) > 0 )
	{
		$title = $ss[1];
	}
		
	$title_orig  = '';
	if( preg_match( '/<span itemprop="alternativeHeadline">(.*?)<\/span>/' , $s, $ss ) > 0 )
	{
		$title_orig  = $ss[1];
	}	


	$poster = '';	// ссылка на картинку
	if( preg_match( '/<link rel="image_src" href="(.*?)" \/>/' , $s, $ss ) > 0 )
	{
		$poster = $ss[1];
	}
	elseif( preg_match( '/<div class="image">.*?<img src="([^"]+)"/' , $s, $ss ) > 0 )
	{
		$poster = dirname( $ss[1] );
	}

	$desc = '';

	// Tags: year, country, genres, duration
	if( preg_match( '/<div class="tags">(.*?)<\/div>/s' , $s, $ss ) > 0 )
	{
		$t =$ss[1];
		// duration
		if( preg_match( '/<\/span>,.*?([0-9]+).*?<meta/s' , $t, $ss ) > 0 )
		{
			$desc .= $ss[1] .' минут'.PHP_EOL;
		}
		// year, country
		if( preg_match( '/.*?(.*?),(.*?),/' , $t, $ss ) > 0 )
		{
			$desc .= trim( $ss[2] ) .', '. trim( $ss[1] ) .PHP_EOL;
		}
		// genres
		if( preg_match_all( '/<span itemprop="genre">(.*?)<\/span>/' , $t, $ss ) > 0 )
		{
			$desc .= implode( ', ', $ss[1] ) .PHP_EOL;
		}
	}

	// Raitings
	$rates = array();
	if( preg_match( '/IMDb:.*?<strong.*?>(.*?)<\/strong>/' , $s, $ss ) > 0 )
	{
		$rates[] = 'IMDb '. $ss[1];
	}
	if( preg_match( '/Кинопоиск:.*?<strong.*?>(.*?)<\/strong>/' , $s, $ss ) > 0 )
	{
		$rates[] = 'Кинопоиск '. $ss[1];
	}
	if( preg_match( '/ivi.ru:.*?<meta itemprop="ratingValue".*?>(.*?)<\/strong>/' , $s, $ss ) > 0 )
	{
		$rates[] = 'ivi.ru '. $ss[1];
	}
	if( count( $rates ) > 0 )
	 $desc .= implode( ' ', $rates ) .PHP_EOL;


	// Description
	$desc .= PHP_EOL;

	$d = '';
	$dess = $doc->getElementsByTagName('div');
	foreach( $dess as $des )
	if( $des->hasAttribute('class'))
	if( $des->getAttribute('class') == 'description' )
	{
		$ss = explode( "\n", trim( $des->textContent ));
		foreach( $ss as $ts )
		{
			$t = trim( $ts );
			if( $t == '' ) continue;
			$d .= $t .PHP_EOL;
		}
		break;
	}
	if( $d != '' ) $desc .= $d;
	elseif( preg_match( '/<meta name="mrc__share_description" content="([^"]+)"\/>/' , $s, $ss ) > 0 )
	{
		$desc .= $ss[1];
	}
if( isset( $_REQUEST['debug'] )) file_put_contents( '/tmp/dom.dat', $s );;
if( isset( $_REQUEST['debug'] )) echo "Description:$desc\n";

	// Prepare view
	$items = iviGetPlaylist( $s );

	if( count( $items ) == 1 && $items[0]['action'] == 'get')
	{
		include( 'modules/core/rss_view_info.php' );
	
		$view = new rssSkinInfoView;

		$view->_link = $items[0]['link'];
		$view->_action = $items[0]['action'];

		$width = 1280;
	}
	else
	{
		include( 'rss_view_info_list.php' );

		$view = new rssSkinInfoListView;

		$view->items = $items;

		$width = 770;
	}

	if( $title <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 68,
			'width'  => $width,
			'height' => 44,
			'fontSize' => 18,
			'text' => $title
		);
	}

	if( $title_orig <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 112,
			'width'  => $width,
			'height' => 28,
			'fontSize' => 12,
			'fgColor' => '180:180:180',
			'text' => $title_orig
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
			'posX' => 60,
			'posY' => 140,
			'width'  => $posterWidth,
			'height' => $posterHeight,
			'image' => $poster
		);
		$posterWidth  += 36;
	}

	if( $desc <> '' )
	{
		$posX = 40;
		$posY = 140;

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
			while( mb_strlen( $d, 'utf8' ) > 0 )
			{
				if( $py >= ( $posY + $posterHeight ) ) $px = $posX;
				$maxWidth = $width + 40 - $px;
				$maxSymbols = floor( $maxWidth / $symbolWidth );

				if( mb_strlen( $d, 'utf8' ) < $maxSymbols )
				{
					$dd = $d;
					$d = '';
					$align = 'left';
				}
				else
				{
					$dd = cutStrWord( $d, $maxSymbols );
					$d = trim( mb_substr( $d, mb_strlen( $dd, 'utf8' ), 2048, 'utf8' ));
					$align = 'justify';
				}
				$view->infos[] = array(
					'type' => 'text',
					'posX' => $px,
					'posY' => $py,
					'width'  => $maxWidth,
					'height' => $lineHeight,
					'lines' => 1,
					'align' => $align,
					'fontSize' => 12,
					'text' => $dd
				);
				$py += $lineHeight;
				if( $py > ( 700 - $lineHeight )) break;
			}
			if( $py > ( 700 - $lineHeight )) break;
		}
	}

	$view->topTitle = 'ivi.ru';
	$view->bottomTitle = getRssCommandPrompt('enter') . ' смотреть ';

	$view->showRss();
}

?>
