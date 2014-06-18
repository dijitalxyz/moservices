<?php
// get config
$yify_config = array(
	'type'    => 'files',
	'query'   => 'movies',
	'sort'    => 'no',
	'order'   => 'desc',	// desc asc
	'p'       => 1,		// page
	'quality' => 'hi',
	'keyboard'=> 'rss',
);

$yify_config_path = dirname( __FILE__ ) .'/yify.config.php';

if( is_file( $yify_config_path ) )
{
	include( $yify_config_path );
}

// get session categories
$yify_session = array(
	'genres' => array(),
	'langs'  => array(),
);

$yify_session_path = '/tmp/yify.session.php';

if( is_file( $yify_session_path ) )
{
	include( $yify_session_path );
}

// set sorts
$yify_sorts = array(
	'no'    => 'no',
	'title' => 'orderby=title',
	'rating'=> 'meta_key=rating&orderby=meta_value',
	'vote'  => 'meta_key=votes&orderby=meta_value',
	'imdb'  => 'meta_key=imdbRating&orderby=meta_value',
);

//
// ------------------------------------
function getYifyConfigParameter( $name )
{
global $yify_config;

	return $yify_config[ $name ];
}
//
// ------------------------------------
function saveYifyConfig()
{
global $yify_config;
global $yify_config_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "saveYifyConfig\n";
	print_r( $yify_config );
}

	file_put_contents( $yify_config_path, '<?php $yify_config = '.var_export( $yify_config, true ).'; ?>' );
}
//
// ------------------------------------
function saveYifySession()
{
global $yify_session;
global $yify_session_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "saveYifySession\n";
	print_r( $yify_session );
}

	file_put_contents( $yify_session_path, '<?php $yify_session = '.var_export( $yify_session, true ).'; ?>' );
}
//
// ------------------------------------
function getYifyContent( $url )
{
	$s = file_get_contents( $url );
	return $s;
}
//
// ====================================
function rss_yify_content()
{
	include('yify.rss.main.php');

	$view = new rssYifyView;
	$view->showRss();
}
//
// ====================================
function getYifyVideo( $id )
{
global $yify_config;

	$s = getYifyContent( 'http://www.yify.tv/'. $id .'/' );

	if( preg_match( '/showPkPlayer\("(.*?)"\);/s' , $s, $ss ) > 0 )
	{
		$videoId = $ss[1];
	}
	else return '';

if( isset( $_REQUEST['debug'] )) echo"videoId=$videoId\n";


	$s = getYifyContent( 'http://yify.tv/reproductor2/pk/pk/plugins/player_p.php?url=' .$videoId );

	$a=json_decode($s, true);

if( isset( $_REQUEST['debug'] )) print_r( $a );

	$minStream = 0;
	$maxStream = 0;

	$mi = 100500;
	$ma = 0;

	foreach( $a as $i => $st )
	{
		if( $st['type'] != 'video/mpeg4' &&
		    $st['type'] != 'application/x-shockwave-flash' ) continue;

		if( $st['height'] < $mi )
		{
			$mi = $st['height'];
			$minStream = $i;
		}

		if( $st['height'] > $ma )
		{
			$ma = $st['height'];
			$maxStream = $i;
		}
	}

	if( $yify_config['quality'] == 'hi' ) return $a[ $maxStream ]['url'];
	return $a[ $minStream ]['url'];
}
//
// ------------------------------------
function yify_get_content()
{
global $mos;
global $yify_config;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['id'])) return;

	$id = $_REQUEST['id'];
	$u = getYifyVideo( $id );
	if( $u == '' ) return;
	echo $u;
}
//
// ====================================
function getYifyRequest( $name )
{
global $yify_config;

	$s = $yify_config[ $name ];
	if( isset( $_REQUEST[ $name ] ))
	{
		$s = $_REQUEST[ $name ];
	}
	$yify_config[ $name ] = $s;
	return $s;
}
//
// ------------------------------------
function replaceHtmlEntity( $s )
{
	$s = str_replace( '&#8230;', '...', $s );
	$s = str_replace( '&#8217;', "'", $s );
	$s = str_replace( '&#8220;', '"', $s );
	$s = str_replace( '&#8221;', '"', $s );

	return $s;
}
//
// ------------------------------------
function yify_list_content()
{
global $yify_config;
global $yify_session;
global $yify_sorts;

header( "Content-type: text/plain" );

	$url = 'http://www.yify.tv/';
	$pars = array();

	// settings
	$quality  = getYifyRequest('quality');
	$keyboard = getYifyRequest('keyboard');

	// query
	$query = getYifyRequest('query');

	$type = getYifyRequest('type');
	if ( $type == '' )
	{
		$type = 'files';
		$query = 'movies';
		$yify_config['type' ] = 'files';
		$yify_config['query'] = 'movies';
	}

	if    ( $type == 'search'  ) $pars[] = 's='. $query;
	else $url .= $type .'/'. $query .'/';

	$page = getYifyRequest('p');
	if( $page != 1 ) $url .= 'page/'. $page .'/';

	$sort = getYifyRequest('sort');
	$pars[] = $yify_sorts[ $sort ];

	$order = getYifyRequest('order');
	$pars[] = 'order='. $order;

	$url .= '?'. implode( '&', $pars );

	// save config
	saveYifyConfig();


if( isset( $_REQUEST['debug'])) echo "url=$url\n";

	// get html page
	$s = getYifyContent( $url );

//if( isset( $_REQUEST['debug'])) echo "$s\n";

	// get genres
	if( preg_match( '/<select.*?id="genre"(.*?)select>/s', $s, $ss ) > 0 )
	 if( preg_match_all( '/value="([^"]*?)">(.*?)</s', $ss[1], $ss ) > 0 )
	 {
		$yify_session['genres'] = array();

		foreach( $ss[1] as $i => $id )
		 $yify_session['genres'][ $id ] = $ss[2][ $i ];

		saveYifySession();
	}

	// get items
	$items = array();

	if( preg_match( '|var posts = (\{.*?\});|s', $s, $a ) > 0 )
	{
		$a = json_decode( $a[1], true );

		foreach( $a['posts'] as $item )
		{
			// url, image
			$id  = str_replace( array( 'http://yify.tv/', '/' ), '', $item['link'] );
			$img = str_replace( 'https://', 'http://', $item['image'] );

			// title
			$title = replaceHtmlEntity( trim( $item['title'] ));

			// year
			$title .= ' ('. $item['year'] .')';

			// desc
			$desc = replaceHtmlEntity( trim( $item['post_content'] ));

			// genres
			$genre = trim( $item['genre'] );

			$items[] = array(
				'id'   => $id,
				'title'=> $title,
				'image'=> $img,
				'desc' => $desc,
				'genre'=> $genre,
			);
		}
	}

if( isset( $_REQUEST['debug'])) print_r( $items );

	// get navigation
	$pmax = $page;
	if( preg_match( '|<div class="pagenavi2">(.*?)</div>|s', $s, $a ) > 0 )
	 if( preg_match_all( '|/page/(.*?)/|s', $a[1], $ss ) > 0 )
	  foreach( $ss[1] as $m )
	   if( $pmax < $m ) $pmax = $m;

if( isset( $_REQUEST['debug'])) echo "Max page=$pmax\n";

	// generate list
	$s = '';

	// top title
	$s .= 'yify.tv';

	if    ( $type == 'search' )	$s .= ' - '. urldecode( $query );
	elseif( $type == 'genre' )	$s .= ' - '. $yify_session['genres'][ $query ];
	elseif( $type == 'languages' )	$s .= ' - '. $yify_session['langs'][ $query ];
	else				$s .= ' - '. getMsg( 'yify_title_'. $query );
	$s .= PHP_EOL ;

	// bottom title
	$s .=
		'<< ' . getMsg('coreRssPromptMenu') .
		getRssCommandPrompt('enter')  . getMsg('coreRssPromptWatch') .
		PHP_EOL;

	// sort
	if( $sort != 'no' ) $s .= getMsg( 'yify_sort_'. $sort ) .', ';
	$s .= getMsg( 'yify_sort_'. $order ) .PHP_EOL ;

	// page
	if( $pmax > 1 ) $s .= $page .'/'. $pmax;
	$s .= PHP_EOL ;

	// navs
	$url = getMosUrl().'?page=yify_list';
	if( $page != 1 ) $s .= $url.'&p='. ( $page - 1 );
	$s .= PHP_EOL;

	if( $page != $pmax ) $s .= $url.'&p='. ( $page + 1 );
	$s .= PHP_EOL;
	
	// number of items
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['id']   .PHP_EOL;
		$s .= $item['title'].PHP_EOL;
		$s .= $item['image'].PHP_EOL;
		$s .= $item['desc'] .PHP_EOL;
		$s .= $item['genre'].PHP_EOL;
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
// ====================================
function rss_yify_menu_content()
{
global $yify_config;

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->items = array();

	$i = 0;
	$cur = -1;

	$type  = $yify_config['type'];
	$query = $yify_config['query'];

	if( $type == 'files' && $query == 'movies') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_movies'),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=yify_list&amp;type=files&amp;query=movies&amp;p=1'
	);

	if( $type == 'files' && $query == 'releases') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_releases'),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=yify_list&amp;type=files&amp;query=releases&amp;p=1'
	);

	if( $type == 'genres') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_genre'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_yify_genres'
	);

	if( $type == 'languages') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_langs'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_yify_langs'
	);

	if( $type == 'search') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_search'),
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=yify_list&amp;type=search&amp;p=1&amp;query='
	);

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_sort'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_yify_sort',
		'border' => 'top',
	);

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_title_sets'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_yify_sets',
	);

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
//
// ====================================
function rss_yify_genres_content()
{
global $yify_config;
global $yify_session;

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 1;

	$view->items = array();

	$i = 0;
	foreach( $yify_session['genres'] as $id => $item )
	{
		if( $id == $yify_config['query'] ) $view->currentItem = $i;
		$view->items[ $i++ ] = array(
			'title'	=> $item,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;p=1&amp;type=genre&amp;query='. $id
		);
	}

	$view->showRss();
}
//
// ====================================
function rss_yify_langs_content()
{
global $yify_config;
global $yify_session;

	if( count( $yify_session['langs'] ) == 0 )
	{
		// get langs
		$s = getYifyContent( 'http://yify.tv/languages/' );

		if( preg_match_all( '|<td><a href="/languages/(.*?)/">(.*?)</a></td>|s', $s, $a ) > 0 )
		{
			$yify_session['langs'] = array();

			foreach( $a[1] as $i => $id )
			 $yify_session['langs'][ $id ] = $a[2][ $i ];

			saveYifySession();
		}
	}

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 1;

	$view->items = array();

	$i = 0;
	foreach( $yify_session['langs'] as $id => $item )
	{
		if( $id == $yify_config['query'] ) $view->currentItem = $i;
		$view->items[ $i++ ] = array(
			'title'	=> $item,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;p=1&amp;type=languages&amp;query='. $id
		);
	}

	$view->showRss();
}
//
// ====================================
function rss_yify_sort_content()
{
global $yify_config;
global $yify_sorts;

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 1;

	$view->items = array();

	$i = 0;
	foreach( $yify_sorts as $id => $item )
	{
		if( $id == $yify_config['sort'] ) $view->currentItem = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('yify_sort_'. $id ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;p=1&amp;sort='. $id
		);
	}

	if( $yify_config['order'] == 'desc' )
	 $view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_sort_asc' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=yify_list&amp;p=1&amp;order=asc',
		'border' => 'top',
	 );
	else
	 $view->items[ $i++ ] = array(
		'title'	=> getMsg('yify_sort_desc' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=yify_list&amp;p=1&amp;order=desc',
		'border' => 'top',
	 );

	$view->showRss();
}
//
// ====================================
function rss_yify_sets_content()
{
	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 1;

	$view->items = array(
		array(
			'title'	=> getMsg('yifyQuality'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_yify_quality'
		),
		array(
			'title'	=> getMsg('yifyKeyboard'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_yify_keyboard'
		),
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_yify_quality_content()
{
global $yify_config;

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('yifyLowQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;quality=lo'
		),
		array(
			'title'	=> getMsg('yifyHighQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;quality=hi'
		),
	);

	$view->currentItem = 0;
	if( $yify_config['quality'] == 'hi'  ) $view->currentItem = 1;

	$view->showRss();
}
//
// ------------------------------------
function rss_yify_keyboard_content()
{
global $yify_config;

	include( 'yify.rss.menu.php' );
	$view = new rssYifyLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('yifyEmbKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;keyboard=emb'
		),
		array(
			'title'	=> getMsg('yifyRssKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=yify_list&amp;keyboard=rss'
		),
	);

	$view->currentItem = 0;
	if( $yify_config['keyboard'] == 'rss' ) $view->currentItem = 1;

	$view->showRss();
}

?>
