<?php

$developer_key = 'sh1iqrPHnhjFmXiT';

$shoutcast_config_path = dirname( __FILE__ ) .'/shoutcast.config.php';
$shoutcast_favs_path   = dirname( __FILE__ ) .'/shoutcast.favs.php';
$shoutcast_genres_path = '/tmp/shoutcast.genres.php';

$shoutcast_config = array(
	'type'		=> 'top',
	'query'		=> '',
	'keyboard'	=> 'emb',
	'limit'		=> 204,
	'sid'		=> '',
	'playOnStart'	=> 'yes',
	'screensaver'	=> 3,
);
if( is_file( $shoutcast_config_path ) ) include( $shoutcast_config_path );

$shoutcast_favs = array();
if( is_file( $shoutcast_favs_path ) ) include( $shoutcast_favs_path );

$shoutcast_genres = array();
if( is_file( $shoutcast_genres_path ) ) include( $shoutcast_genres_path );
//
// ------------------------------------
function shoutcastGetConfigParameter( $name )
{
global $shoutcast_config;

	return $shoutcast_config[ $name ];
}
//
// ------------------------------------
function shoutcastGetApi( $req, $pars = '' )
{
global $developer_key;

	$req = "http://api.shoutcast.com/$req?k=$developer_key&f=json";

	if( $pars != '' ) $req .= "&$pars";

if( isset( $_REQUEST['debug']))
{
	echo "shoutcastGetApi:\n";
	echo "  request=$req\n";
}

	return file_get_contents( $req );
}
//
// ------------------------------------
function shoutcastSaveConfig()
{
global $shoutcast_config;
global $shoutcast_config_path;

if( isset( $_REQUEST['debug']))
{
	echo "shoutcastSaveConfig:\n";
	print_r( $shoutcast_config );
}

	file_put_contents( $shoutcast_config_path, '<?php $shoutcast_config = '.var_export( $shoutcast_config, true ).'; ?>' );
}
//
// ------------------------------------
function shoutcastSaveFavs()
{
global $shoutcast_favs;
global $shoutcast_favs_path;

	file_put_contents( $shoutcast_favs_path, '<?php $shoutcast_favs = '.var_export( $shoutcast_favs, true ).'; ?>' );
}
//
// ------------------------------------
function shoutcastGetGenres( $id = 0 )
{
global $shoutcast_genres;
global $shoutcast_genres_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "shoutcastGetGenres:\n";
	echo "  genre id=";
	print_r( $id );
	echo "\n";
}

	if( $id === 0 || count( $shoutcast_genres ) ==  0 )
	{
		// primary list
		if( count( $shoutcast_genres ) ==  0 )
		{
			$s = shoutcastGetApi( 'genre/primary' );
			$ss=json_decode($s, true);
			if( is_array( $ss['response']['data']['genrelist']['genre'] ))

			 foreach( $ss['response']['data']['genrelist']['genre'] as $item )
			  $shoutcast_genres[ $item['id'] ] = array(
				'haschilds'	=> $item[ 'haschildren' ],
				'name'		=> $item[ 'name' ],
				'childs'	=> array()
			);
		}
		$ret = &$shoutcast_genres;
	}

	if( is_array( $id ))
	{
		// find child
		$cg = &$shoutcast_genres;
		foreach( $id as $i )
		{
			if( is_array( $cg[ $i ] ))
			if( $cg[ $i ]['haschilds'] ==  1 )
			if( count( $cg[ $i ]['childs'] ) ==  0 )
			{
				$s = shoutcastGetApi( 'genre/secondary', 'parentid='. $i );
				$ss=json_decode($s, true);
				if( is_array( $ss['response']['data']['genrelist']['genre'] ))
				 foreach( $ss['response']['data']['genrelist']['genre'] as $item )
				  $cg[ $i ]['childs'][ $item['id']] = array(
					'haschilds'	=> $item[ 'haschildren' ],
					'name'		=> $item[ 'name' ],
					'childs'	=> array()
				);
			}
			$cg = &$cg[ $i ]['childs'];
			$ret = &$cg;
		}
	}
	// save genres
	file_put_contents( $shoutcast_genres_path, '<?php $shoutcast_genres = '.var_export( $shoutcast_genres, true ).'; ?>' );


if( isset( $_REQUEST['debug'] ))
{
	echo "  return=";
	print_r( $ret );
}

	return $ret;
}
//
// ------------------------------------
function shoutcastGetList( $id = 'top', $query = '' )
{
global $shoutcast_config;
global $shoutcast_favs;

if( isset( $_REQUEST['debug'] ))
{
	echo "shoutcastGetList:\n";
	echo "  id=$id\n";
	echo "  query=$query\n";
}

	$items = array();

	if( $id == 'top' || $id == 'search' )
	{
		if( $id == 'top' )
		 $s = shoutcastGetApi( 'legacy/Top500', 'limit='. $shoutcast_config['limit'] );
		else
		 $s = shoutcastGetApi( 'legacy/stationsearch', 'search='. $query .'&limit='. $shoutcast_config['limit'] );

		if( $s === false ) return $items;

		$x = new SimpleXMLElement($s);
		foreach( $x->station as $item )
		 $items[] = array(
			'id'	=> (string)$item['id'],
			'title'	=> (string)$item['name'],
			'genre'	=> (string)$item['genre'],
			'br'	=> (string)$item['br'],
			'ct'	=> (string)$item['ct'],
		);
	}
	elseif( $id == 'favs' )
	{
		foreach( $shoutcast_favs as $id => $item )
		 $items[] = array(
			'id'	=> $id,
			'title'	=> $item['title'],
			'genre'	=> $item['genre'],
			'br' 	=> $item['br'],
			'ct' 	=> '',
		);
	}
	else	// play or genre_id
	{
		if( $id == 'play' )
		 $s = shoutcastGetApi( 'station/nowplaying', 'ct='. $query .'&limit='. $shoutcast_config['limit'] );
		else
		 $s = shoutcastGetApi( 'station/advancedsearch', 'genre_id='. $id .'&limit='. $shoutcast_config['limit'] );

		if( $s === false ) return $items;

		$x=json_decode($s, true);
		$st = $x['response']['data']['stationlist']['station'];
		if( is_array( $st ))
		if( isset( $st['id']))
		 // one station
		 $items[] = array(
			'id'	=> $st['id'],
			'title'	=> $st['name'],
			'genre'	=> $st['genre'],
			'br'	=> $st['br'],
			'ct'	=> $st['ct'],
		);
		else
		 // station list
		foreach( $st as $item )
		 $items[] = array(
			'id'	=> $item['id'],
			'title'	=> $item['name'],
			'genre'	=> $item['genre'],
			'br'	=> $item['br'],
			'ct'	=> $item['ct'],
		);
	}

if( isset( $_REQUEST['debug'] ))
{
	echo "  return=";
	print_r( $items );
}

	return $items;
}
//
// ====================================
function shoutcast_list_content()
{
global $shoutcast_config;
global $shoutcast_genres;

	header( "Content-type: text/plain" );

	$query = $shoutcast_config['query'];
	if( isset( $_REQUEST['query'] ))
	{
		$query = $_REQUEST['query'];
	}

	$type = $shoutcast_config['type'];
	if( isset( $_REQUEST['type'] ))
	{
		$type = $_REQUEST['type'];
	}
	elseif ( $type == '' )
	{
		$type = 'top';
		$query = '';
	}

	$title = 'SHOUTCast';
	$genres = array();

	if( $type == 'genre' )
	{
		$genres = explode( ',', $query );
		$id = $genres[ count( $genres ) - 1 ];

		$cg = &$shoutcast_genres;
		$a = array();
		foreach( $genres as $i )
		{
			$a[] = $i;
			shoutcastGetGenres( $a );
			$title .= ' - '. $cg[ $i ]['name'];
			$cg = &$cg[ $i ]['childs'];
		}

	}
	else
	{
		$id = $type;
		$title .= ' - '. getMsg( 'sc_'. $type );
		if( $type == 'search' || $type == 'play' ) $title .= ' - '. urldecode( $query );
	}

	$shoutcast_config['type' ] = $type;
	$shoutcast_config['query'] = $query;
	shoutcastSaveConfig();

	// get items
	$items = shoutcastGetList( $id, $query );

	// generate list
	// top title
	$s = $title .PHP_EOL;

	// bottom title
	$s .=
		'<< ' . getMsg('coreRssPromptMenu')
		. ' OK '  . getMsg('scListen')
		. ' PLAY '  . getMsg('scUpdate')
		. ' INFO '  . getMsg('scSaver');

	if( $type == 'favs' )
	 $s .= ' >> '  . getMsg('scFavsRemove');
	else
	 $s .= ' >> '  . getMsg('scFavsAdd');

	$s .= PHP_EOL;

	// action on >> button
	if( $type == 'favs' ) $s .= getMosUrl().'?page=shoutcast_favs&act=remove' .PHP_EOL;
	else $s .= getMosUrl().'?page=shoutcast_favs&act=add' .PHP_EOL;

	// screensaver idle
	$s .= $shoutcast_config['screensaver'] .PHP_EOL;

	// number of items
	$s .= count( $items ) .PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['id']    .PHP_EOL;
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['genre'] .PHP_EOL;
		$s .= $item['br']    .PHP_EOL;
		$s .= $item['ct']    .PHP_EOL;
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
function shoutcast_favs_content()
{
global $shoutcast_favs;
global $shoutcast_favs_path;

	if( ! isset( $_REQUEST['act'])) return;
	$act = $_REQUEST['act'];

	if( ! isset( $_REQUEST['id'])) return;
	$id = $_REQUEST['id'];

	header( "Content-type: text/plain" );

if( isset( $_REQUEST['debug']))
{
	echo "request=";
	print_r( $_REQUEST );
}

	if( $act == 'add' )
	{
		if( ! isset( $shoutcast_favs[ $id ] ))
		{
			$shoutcast_favs[ $id ] = array(
				'title'	=> urldecode( $_REQUEST['name' ] ),
				'genre'	=> urldecode( $_REQUEST['genre'] ),
				'br' 	=> urldecode( $_REQUEST['br'   ] ),
			);
			shoutcastSaveFavs();
		}
	}
	elseif( $act == 'remove' )
	{
		if( isset( $shoutcast_favs[ $id ] ))
		{
			unset( $shoutcast_favs[ $id ] );
			shoutcastSaveFavs();

			echo 'load';
		}
	}
}
//
// ====================================
function shoutcast_get_content()
{
global $shoutcast_config;

	if( isset( $_REQUEST['id'])) $id = $_REQUEST['id'];
	else $id = '';

	header( "Content-type: text/plain" );

if( isset( $_REQUEST['debug'])) echo "id=$id\n";

	$title = '';

	if( $id != '' )
	{
		$s = file_get_contents( 'http://yp.shoutcast.com/sbin/tunein-station.pls?id='. $id );
		if( $s === false ) return;

		$a = array();
		if( preg_match_all( '/(.*?)\=(.*)/', $s, $ss ) > 0 )
		 foreach( $ss[1] as $i => $v )
		  $a[ $v ] = $ss[2][ $i ];

		if( ! isset( $a['numberofentries'] )) return;

		$n = 100000;
		$title = $a['Title1'];
		$url = $a['File1'];

		for( $i = 1; $i <= $a['numberofentries'] ; $i++ )
		{
			if( preg_match( '/\(\#\d+ - (\d+)\/(\d+)\) ?(.*)/', $a['Title'. $i ], $ss ) == 0 ) continue;

			$m = $ss[1] / $ss[2];

			if( $n < $m ) continue;

			$n = $m;
			$title = $ss[3];
			$url = $a['File'. $i ];
		}
	}

	$shoutcast_config['sid'] = $id;
	shoutcastSaveConfig();

	echo $title .PHP_EOL;
	echo $url   .PHP_EOL;
}
//
// ====================================
function rss_shoutcast_menu_content()
{
global $shoutcast_config;
global $shoutcast_favs;

	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->items = array();

	$i = 0;
	$cur = -1;

	$type  = $shoutcast_config['type'];
	$query = $shoutcast_config['query'];

	// favs
	if( count( $shoutcast_favs ) > 0 )
	{
		if( $type == 'favs' ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('sc_favs'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=favs'
		);
	}

	// top
	if( $type == 'top' ) $cur = $i;

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('sc_top'),
		'open'  => 'empty',
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=top'
	);

	// search
	if( $type == 'search' ) $cur = $i;

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('sc_search'),
		'open'  => 'empty',
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=search&amp;query='
	);

	// play
	if( $type == 'play' ) $cur = $i;

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('sc_play'),
		'open'  => 'empty',
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=play&amp;query=',
		'border' => 'bottom'
	);

	// genres
	$gens = shoutcastGetGenres();
	if( $type == 'genre' )
	{
		$a = explode( ',', $query );
		$cid = $a[ 0 ];
	}

	foreach( $gens as $id => $item )
	{
		if( $type == 'genre')
		if( $id == $cid ) $cur = $i;

		if( $item['haschilds'] == 1 ) $open = getMosUrl().'?page=rss_shoutcast_genres&amp;level=1&amp;gid='. $id;
		else $open = 'empty';

		$view->items[ $i++ ] = array(
			'title'	=> $item['name'],
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=genre&amp;query='. $id,
			'open'	=> $open,
		);
	}

	// settings
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('scSettings'),
		'open'  => 'empty',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_shoutcast_sets',
		'border' => 'top'
	);

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
//
// ====================================
function rss_shoutcast_genres_content()
{
global $shoutcast_config;

	if( ! isset( $_REQUEST['gid'] )) return;
	if( ! isset( $_REQUEST['level'] )) return;

	$gid = $_REQUEST['gid'];
	$level = $_REQUEST['level'];

	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->position = $level;
	$view->items = array();

	$i = 0;
	$cur = -1;

	$type  = $shoutcast_config['type'];
	$query = $shoutcast_config['query'];

	// genres
	$a = explode( ',', $gid );
	$gens = shoutcastGetGenres( $a );
	if( $type == 'genre' )
	{
		$a = explode( ',', $query );
		$cid = $a[ $level ];
	}

	foreach( $gens as $id => $item )
	{
		if( $type == 'genre')
		if( $id == $cid ) $cur = $i;

		if( $item['haschilds'] == 1 ) $open = getMosUrl().'?page=rss_shoutcast_genres&amp;level='. ($level + 1 ) .'&amp;gid='. $gid .','. $id;
		else $open = 'empty';

		$view->items[ $i++ ] = array(
			'title'	=> $item['name'],
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_list&amp;type=genre&amp;query='. $gid .','. $id,
			'open'	=> $open,
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
//
// ====================================
function shoutcast_sets_content()
{
global $shoutcast_config;

	header( "Content-type: text/plain" );

	if( isset( $_REQUEST['keyboard']    )) $shoutcast_config['keyboard'] = $_REQUEST['keyboard'];
	if( isset( $_REQUEST['limit']       )) $shoutcast_config['limit'] = $_REQUEST['limit'];
	if( isset( $_REQUEST['playonstart'] )) $shoutcast_config['playOnStart'] = $_REQUEST['playonstart'];

	shoutcastSaveConfig();
}
//
// ====================================
function rss_shoutcast_sets_content()
{
	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->position = 1;

	$view->items = array(
		array(
			'title'	=> getMsg('scKeyboard'),
			'open'  => 'empty',
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_shoutcast_keyboard'
		),
		array(
			'title'	=> getMsg('scPlayOnStart'),
			'open'  => 'empty',
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_shoutcast_playonstart'
		),
		array(
			'title'	=> getMsg('scScreensaver'),
			'open'  => 'empty',
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_shoutcast_setss'
		),
	);

	$view->showRss();
}
//
// ====================================
function rss_shoutcast_keyboard_content()
{
global $shoutcast_config;

	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('scEmbKbrd'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_sets&amp;keyboard=emb'
		),
		array(
			'title'	=> getMsg('scRssKbrd'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_sets&amp;keyboard=rss'
		)
	);

	$view->currentItem = 0;
	if( $shoutcast_config['keyboard'] == 'rss' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ====================================
function rss_shoutcast_playonstart_content()
{
global $shoutcast_config;

	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('scYes'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_sets&amp;playonstart=yes'
		),
		array(
			'title'	=> getMsg('scNo'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_sets&amp;playonstart=no'
		)
	);

	$view->currentItem = 0;
	if( $shoutcast_config['playOnStart'] == 'no' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ====================================
function rss_shoutcast_setss_content()
{
global $shoutcast_config;

	include( 'shoutcast.rss.view.left.php' );
	$view = new rssShoutcastLeftView;

	$view->position = 2;

	$i = 0;
	$cur = -1;

	$a = array( 0, 1, 3, 5 );
	foreach( $a as $t )
	{
		if( $t == $shoutcast_config['screensaver'] ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('scSS_'. $t ),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=shoutcast_sets&amp;screensaver=' .$t
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
?>
