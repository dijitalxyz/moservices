<?php

ini_set('user_agent', "Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0");

$di_config_path = dirname( __FILE__ ) .'/di.config.php';
$di_favs_path   = dirname( __FILE__ ) .'/di.favs.php';
$di_data_path = '/tmp/di.data.php';

$di_stations = array(
	'di' => array(
		'name' => 'DI.fm',
		'url'  => 'di.fm',
	),
	'sky' => array(
		'name' => 'Sky.fm',
		'url'  => 'sky.fm',
	),
	'jazzradio' => array(
		'name' => 'Jazz radio',
		'url'  => 'jazzradio.com',
	),
	'rockradio' => array(
		'name' => 'Rock radio',
		'url'  => 'rockradio.com',
	),
);

$di_config = array(
	'filter'	=> 'default',
	'station'	=> 'di',
	'cid'		=> '',
	'sid'		=> '',
	'playOnStart'	=> 'yes',
	'screensaver'	=> 0,
);
if( is_file( $di_config_path ) ) include( $di_config_path );

$di_favs = array();
if( is_file( $di_favs_path ) ) include( $di_favs_path );

$di_data = array();
if( is_file( $di_data_path ) ) include( $di_data_path );
//
// ------------------------------------
function diGetConfigParameter( $name )
{
global $di_config;

	return $di_config[ $name ];
}
//
// ------------------------------------
function diSaveConfig()
{
global $di_config;
global $di_config_path;

if( isset( $_REQUEST['debug']))
{
	echo "diSaveConfig:\n";
	print_r( $di_config );
}

	file_put_contents( $di_config_path, '<?php $di_config = '.var_export( $di_config, true ).'; ?>' );
}
//
// ------------------------------------
function diSaveFavs()
{
global $di_favs;
global $di_favs_path;

if( isset( $_REQUEST['debug']))
{
	echo "diSaveFavs:\n";
	print_r( $di_favs );
}

	file_put_contents( $di_favs_path, '<?php $di_favs = '.var_export( $di_favs, true ).'; ?>' );
}
//
// ------------------------------------
function diGetData( $station = 'di', $filter = 'default' )
{
global $di_stations;
global $di_config;
global $di_data;
global $di_data_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "diGetData:\n";
	echo "  filter=$filter\n";
	echo "  station=$station\n";
}
	$query = 'http://ephemeron:dayeiph0ne@pp@api.audioaddict.com/v1/' .$station .'/mobile/batch_update.json?stream_set_key=public3';

if( isset( $_REQUEST['debug'] ))
{
	echo "  query=$query\n";
}
	$s = @file_get_contents( $query );
	if( $s === false ) return $items;

	$a=json_decode($s, true);
/*
if( isset( $_REQUEST['debug'] ))
{
	echo "  raw data:\n";
	print_r( $a );
}
*/
	$di_data[ $station ] = array();

	// parse channels filters
	foreach( $a['channel_filters'] as $item )
	{
		if( $item['display'] != 1 ) continue;

		$id = $item['key'];
		$di_data[ $station ]['filters'][ $id ] = array(
			'channels' => array(),
			'name' => trim( $item['name'] ),
		);
		foreach( $item['channels'] as $ch )
		{
			$cid = trim( $ch['id'] );
			$di_data[ $station ]['filters'][ $id ]['channels'][] = $cid;

			$img = pathinfo( trim( $ch['asset_url'] ), PATHINFO_FILENAME );
			$img = "http://api.audioaddict.com/v1/assets/image/$img.jpg?size=100x100&quality=90";

			$di_data[ $station ]['channels'][ $cid ] = array(
				'id'      => $cid,
				'key'     => trim( $ch['key'] ),
				'name'    => trim( $ch['name'] ),
				'desc'    => trim( $ch['description'] ),
				'image'   => $img,
				'station' => $station,
			);
		}
	}

	// parse streamlist
	foreach( $a['streamlists']['public3']['channels'] as $ch )
	{
		$cid = $ch['id'];
		if( is_array( $di_data[ $station ]['channels'][ $cid ] ))
		 $di_data[ $station ]['channels'][ $cid ]['stream'] = trim( $ch['streams'][ 0 ]['url'] );
	}

	// parse track history
	foreach( $a['track_history'] as $ch )
	{
		$cid = $ch['channel_id'];
		if( is_array( $di_data[ $station ]['channels'][ $cid ] ))
		 $di_data[ $station ]['channels'][ $cid ]['title'] = trim( $ch['track'] );
	}


	// save data

if( isset( $_REQUEST['debug'] ))
{
	echo "diGetData:\n";
	echo "  di_data=";
	print_r( $di_data );
}
	file_put_contents( $di_data_path, '<?php $di_data = '.var_export( $di_data, true ).'; ?>' );

	// generate list of items
	$items = array();

	if( isset( $di_data[ $station ]['filters'][ $filter ] ))
	 foreach( $di_data[ $station ]['filters'][ $filter ]['channels'] as $id => $ch )
	  $items[ $id ] = $di_data[ $station ]['channels'][ $ch ];

if( isset( $_REQUEST['debug'] ))
{
	echo "  return=";
	print_r( $items );
}

	return $items;
}
//
// ====================================
function di_list_content()
{
global $di_stations;
global $di_config;
global $di_favs;
global $di_data;

	header( "Content-type: text/plain" );

	$station = $di_config['station'];
	if( isset( $_REQUEST['station'] ))
	{
		$station = $_REQUEST['station'];
	}
	elseif ( $station == '' ) $station = 'di';

	$filter = $di_config['filter'];
	if( isset( $_REQUEST['filter'] ))
	{
		$filter = $_REQUEST['filter'];
	}
	elseif ( $filter == '' ) $filter = 'default';

	if( $filter == 'favs' )
	{
		$items = array();

		foreach( $di_favs as $id => $item )
		 $items[] = array(
			'id'      => $id,
			'name'    => $item['name'],
			'desc'    => $item['desc'],
			'image'   => $item['image'],
			'title'   => $di_stations[ $item['station'] ]['name'],
			'station' => $item['station'],
		 );

		$title = getMsg( 'diFavs' );
	}
	else
	{
		// get content
		$items = diGetData( $station, $filter );

		$title = $di_stations[ $station ]['name'] .' - '. $di_data[ $station ]['filters'][ $filter ]['name'];
	}

	if( $di_config['station' ] != $station
	|| $di_config['filter'  ] != $filter )
	{
		$di_config['station' ] = $station;
		$di_config['filter'  ] = $filter;
		diSaveConfig();
	}

	// generate list
	// top title
	$s = $title .PHP_EOL;

	// bottom title
	$s .=
		'<< ' . getMsg('coreRssPromptMenu')
		. ' OK '  . getMsg('diListen')
		. ' PLAY '  . getMsg('diUpdate')
		. ' INFO '  . getMsg('diSaver');

	if( $filter == 'favs' )
	 $s .= ' >> '  . getMsg('diFavsRemove');
	else
	 $s .= ' >> '  . getMsg('diFavsAdd');

	$s .= PHP_EOL;

	// action on >> button
	if( $filter == 'favs' ) $s .= getMosUrl().'?page=di_favs&act=remove' .PHP_EOL;
	else $s .= getMosUrl().'?page=di_favs&act=add' .PHP_EOL;

	// screensaver idle
	$s .= $di_config['screensaver'] .PHP_EOL;

	// number of items
	$s .= count( $items ) .PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['id']      .PHP_EOL;
		$s .= $item['name']    .PHP_EOL;
		$s .= $item['desc']    .PHP_EOL;
		$s .= $item['image']   .PHP_EOL;
		$s .= $item['title']   .PHP_EOL;
		$s .= $item['station'] .PHP_EOL;
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
function di_favs_content()
{
global $di_favs;
global $di_favs_path;

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
		if( ! isset( $di_favs[ $id ] ))
		{
			$di_favs[ $id ] = array(
				'id'	 => $id,
				'name'	 => urldecode( $_REQUEST['name'] ),
				'desc'	 => urldecode( $_REQUEST['desc'] ),
				'image'	 => urldecode( $_REQUEST['image'] ),
				'station'=> urldecode( $_REQUEST['station'] ),
			);
			diSaveFavs();
		}
	}
	elseif( $act == 'remove' )
	{
		if( isset( $di_favs[ $id ] ))
		{
			unset( $di_favs[ $id ] );
			diSaveFavs();

			echo 'load';
		}
	}
}
//
// ====================================
function di_get_content()
{
global $di_config;
global $di_data;

	$id = '';
	$station = '';

	if( isset( $_REQUEST['id']     )) $id =      $_REQUEST['id'];
	if( isset( $_REQUEST['station'])) $station = $_REQUEST['station'];

	header( "Content-type: text/plain" );

if( isset( $_REQUEST['debug']))
{
	echo "di_get:\n";
	echo "station=$station\n";
	echo "id=$id\n";
}

	$url = '';
	$name = '';
	$title = '';

	if( $id != '' )
	{
		if( ! isset( $di_data[ $station ] )) diGetData( $station );

		if( is_array( $di_data[ $station ]['channels'][ $id ] ))
		{
			if( isset( $di_data[ $station ]['channels'][ $id ]['stream'] ))
			 $url = $di_data[ $station ]['channels'][ $id ]['stream'];

			if( isset( $di_data[ $station ]['channels'][ $id ]['name'] ))
			 $name = $di_data[ $station ]['channels'][ $id ]['name'];

			if( isset( $di_data[ $station ]['channels'][ $id ]['title'] ))
			 $title = $di_data[ $station ]['channels'][ $id ]['title'];
		}		
	}

	$di_config['cid'] = $id;
	$di_config['sid'] = $station;
	diSaveConfig();

	echo $url   .PHP_EOL;
	echo $name  .PHP_EOL;
	echo $title .PHP_EOL;
}
//
// ====================================
function di_track_content()
{
global $di_data;

	if( ! isset( $_REQUEST['station'])) return;
	if( ! isset( $_REQUEST['id'])) return;

	$st = $_REQUEST['station'];
	$id = $_REQUEST['id'];

	header( "Content-type: text/plain" );

if( isset( $_REQUEST['debug']))
{
	echo "di_track:\n";
	echo "  st=$st\n";
	echo "  id=$id\n";
}
	$s = @file_get_contents( 'http://api.audioaddict.com/v1/'. $st .'/track_history/channel/'. $id );
	if( $s === false ) return;

	$a=json_decode($s, true);

if( isset( $_REQUEST['debug']))
{
	echo "  tracks:";
	print_r( $a );
}
	$track = '';
	$image = '';
	foreach( $a as $tr )
	{
		if( isset( $tr['ad'] )) continue;

		if( isset( $tr['track'] ))
		{
			$track = $tr['track'];
			if( isset( $tr['art_url'] )) $image = $tr['art_url'];
			break;
		}
	}

	if( $image == '' )
	{
		if( ! isset( $di_data[ $st ] )) diGetData( $st );
		$image = $di_data[ $st ]['channels'][ $id ]['image'];
	}
	echo $track .PHP_EOL;
	echo $image .PHP_EOL;
}
//
// ====================================
function rss_di_menu_content()
{
global $di_stations;
global $di_config;
global $di_favs;
global $di_data;

	include( 'di.rss.menu.php' );
	$view = new rssDiLeftView;

	$view->items = array();

	$i = 0;
	$cur = -1;

	$filter  = $di_config['filter'];
	$station = $di_config['station'];

	// favs
	if( count( $di_favs ) > 0 )
	{
		if( $filter == 'favs' ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('diFavs'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=di_list&amp;filter=favs',
			'border' => 'bottom'
		);
	}

	// stations
	foreach( $di_stations as $id => $item )
	{
		if( $filter != 'favs')
		if( $id == $stations ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> $item['name'],
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_di_filters&amp;station='. $id,
		);
	}

	// settings
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('diSettings'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_di_sets',
		'border' => 'top'
	);

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
//
// ====================================
function rss_di_filters_content()
{
global $di_config;
global $di_data;

	if( ! isset( $_REQUEST['station'] )) return;

	$station = $_REQUEST['station'];
	$filter = $di_config['filter'];

	include( 'di.rss.menu.php' );
	$view = new rssDiLeftView;

	$view->position = 1;
	$view->items = array();

	$i = 0;
	$cur = -1;

	// filters
	if( ! isset( $di_data[ $station ] )) diGetData( $station );

	foreach( $di_data[ $station ]['filters'] as $id => $item )
	{
		if( $id == $filter ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> $item['name'],
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=di_list&amp;station='. $station .'&amp;filter='. $id,
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
//
// ====================================
function di_sets_content()
{
global $di_config;

	header( "Content-type: text/plain" );

	if( isset( $_REQUEST['playonstart'] )) $di_config['playOnStart'] = $_REQUEST['playonstart'];
	if( isset( $_REQUEST['screensaver'] )) $di_config['screensaver'] = $_REQUEST['screensaver'];

	diSaveConfig();
}
//
// ====================================
function rss_di_sets_content()
{
	include( 'di.rss.menu.php' );
	$view = new rssDiLeftView;

	$view->position = 1;

	$view->items = array(
		array(
			'title'	=> getMsg('diPlayOnStart'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_di_playonstart',
		),
		array(
			'title'	=> getMsg('diScreensaver'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_di_setss',
		),
	);

	$view->showRss();
}
//
// ====================================
function rss_di_playonstart_content()
{
global $di_config;

	include( 'di.rss.menu.php' );
	$view = new rssDiLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('diYes'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=di_sets&amp;playonstart=yes'
		),
		array(
			'title'	=> getMsg('diNo'),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=di_sets&amp;playonstart=no'
		)
	);

	$view->currentItem = 0;
	if( $di_config['playOnStart'] == 'no' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ====================================
function rss_di_setss_content()
{
global $di_config;

	include( 'di.rss.menu.php' );
	$view = new rssDiLeftView;

	$view->position = 2;

	$i = 0;
	$cur = -1;

	$a = array( 0, 1, 3, 5 );
	foreach( $a as $t )
	{
		if( $t == $di_config['screensaver'] ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('diSS_'. $t ),
			'open'  => 'empty',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=di_sets&amp;screensaver=' .$t
		);
	}

	if( $cur == -1) $cur = 0;
	$view->currentItem = $cur;

	$view->showRss();
}
?>
