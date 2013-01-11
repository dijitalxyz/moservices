<?php

$developer_key = 'AI39si66A5mVc14wrUljOu4EkLrKJQRjhtqMJZZbnf5wZfRDtfE7xmPLHY2aRGMHuaKHyE655-FbHDZRNGKtfZ6CphHvhyHXMA';

$youtube_config = array(
	'feed'		=> '',
	'search'	=> '',
	'my'		=> '',
	'cat'		=> '',
	'period'	=> 'all_time',
	'region'	=> '',
	'quality'	=> 'lo',
	'keyboard'	=> 'rss',
	'username'	=> '',
	'cats'		=> array(),
);
if( is_file( $mos.'/www/modules/youtube/youtube.config.php' ) )
{
	include( $mos.'/www/modules/youtube/youtube.config.php' );
}

$youtube_regions = array(
	'AE' => 'ar',	'AR' => 'es',	'AU' => 'en',	'BE' => 'en',
	'BR' => 'pt',	'CA' => 'en',	'CL' => 'es',	'CO' => 'es',
	'CZ' => 'cs',	'DE' => 'de',	'DZ' => 'ar',	'EG' => 'ar',
	'ES' => 'es',	'FR' => 'fr',	'GB' => 'en',	'GR' => 'el',
	'HK' => 'zh',	'HU' => 'hu',	'IE' => 'en',	'IL' => 'iw',
	'IN' => 'hi',	'IT' => 'it',	'JO' => 'ar',	'JP' => 'ja',
	'KE' => 'en',	'KR' => 'ko',	'MA' => 'ar',	'MX' => 'es',
	'MY' => 'ms',	'NG' => 'en',	'NL' => 'nl',	'NZ' => 'en',
	'PE' => 'es',	'PH' => 'fil',	'PL' => 'pl',	'RU' => 'ru',
	'SA' => 'ar',	'SE' => 'sv',	'SG' => 'en',	'TN' => 'ar',
	'TW' => 'zh',	'UG' => 'en',	'US' => 'en',	'YE' => 'ar',
	'ZA' => 'en',
 );

$youtube_periods = array( 'today', 'this_week', 'this_month', 'all_time' );

$youtube_feeds = array(
	'top_rated' => array(
		'allowTime' => true
	),
	'top_favorites' => array(
		'allowTime' => true
	),
	'most_viewed' => array(
		'allowTime' => true
	),
	'most_shared' => array(
		'allowTime' => false
	),
	'most_popular' => array(
		'allowTime' => true
	),
	'most_recent' => array(
		'allowTime' => false
	),
	'most_discussed' => array(
		'allowTime' => true
	),
	'most_responded' => array(
		'allowTime' => true
	),
	'recently_featured' => array(
		'allowTime' => false
	),
);
//
// ------------------------------------
function getYoutubeConfigParameter( $name )
{
global $youtube_config;

	return $youtube_config[ $name ];
}
//
// ------------------------------------
function youtubeGetApi( $req )
{
global $developer_key;

	$opts = array(
		'http' => array(
			'method'  => 'GET',
			'user-agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0',
			'header' => 'X-GData-Key: key='. $developer_key ."\n\r",
		)
	);
	$context = stream_context_create($opts);
	return file_get_contents( "http://gdata.youtube.com/feeds/api/$req&alt=json", false, $context);
}
//
// ------------------------------------
function youtubeSaveConfig()
{
global $mos;
global $youtube_config;

	file_put_contents( $mos.'/www/modules/youtube/youtube.config.php', '<?php $youtube_config = '.var_export( $youtube_config, true ).'; ?>' );
}
//
// ------------------------------------
function youtubeDuration( $s )
{
	$m = (integer)($s/60);
	return sprintf( '%d:%02d', $m, $s-60*$m );
}

function youtubeDate( $s )
{
	$t = strtotime( $s );
	return date( 'd.m.Y', $t );
}
//
// ------------------------------------
function xml_youtube_content()
{
global $youtube_config;
global $youtube_feeds;

	$itemsPerPage = 15; //20;

	header( "Content-type: text/plain" );

	$region = $youtube_config['region'];
	if( isset( $_REQUEST['region'] ))
	{
		$region = $_REQUEST['region'];
		if( $region == 'US' ) $region = '';
	}

	$quality = $youtube_config['quality'];
	if( isset( $_REQUEST['quality'] ))
	{
		$quality = $_REQUEST['quality'];
	}

	$keyboard = $youtube_config['keyboard'];
	if( isset( $_REQUEST['keyboard'] ))
	{
		$keyboard = $_REQUEST['keyboard'];
	}

	$username = $youtube_config['username'];
	if( isset( $_REQUEST['username'] ))
	{
		$username = $_REQUEST['username'];
	}

	$period = $youtube_config['period'];
	if( isset( $_REQUEST['period'] ))
	{
		$period = $_REQUEST['period'];
	}

	$cat = $youtube_config['cat'];
	if( isset( $_REQUEST['cat'] ))
	{
		$cat = $_REQUEST['cat'];
		if( $cat == 'all' ) $cat = '';
	}

	$my     = $youtube_config['my'];
	$feed   = $youtube_config['feed'];
	$search = $youtube_config['search'];
	if( isset( $_REQUEST['search'] ))
	{
		$my = '';
		$feed = '';
		$search = $_REQUEST['search'];
	}
	elseif( isset( $_REQUEST['feed'] ))
	{
		$my = '';
		$feed = $_REQUEST['feed'];
		$search = '';
	}
	elseif( isset( $_REQUEST['my'] ))
	{
		$my = 'yes';
		$feed = '';
		$search = '';
	}

	if( $search != '' )
	{
		$request = 'videos?q='. urlencode( $search ) .'&v=2';

		if( $cat != '' ) $request .= '&category='. $cat;
	}
	elseif( $my != '' )
	{
		$request = 'users/'. $username .'/newsubscriptionvideos?v=2';
	}
	else
	{
		if( $feed == '' ) $feed = 'recently_featured';

		$request = 'standardfeeds/';

		if( $region <> '' ) $request .= $region .'/';

		$request .= $feed;

		if( $cat != '' ) $request .= '_'. $cat;

		$request .= '?v=2';

		if( $youtube_feeds[ $feed ]['allowTime'] )
		 if( $period != 'all_time' ) $request .= '&time='. $period;
	}

	$youtube_config['feed']   = $feed;
	$youtube_config['search'] = $search;
	$youtube_config['cat']    = $cat;
	$youtube_config['period'] = $period;
	$youtube_config['region'] = $region;
	$youtube_config['quality'] = $quality;
	$youtube_config['keyboard'] = $keyboard;
	$youtube_config['username'] = $username;
	$youtube_config['my'] = $my;
	youtubeSaveConfig();

	$start = 1;
	if( isset( $_REQUEST['start'] ))
	 $start = $_REQUEST['start'];

	// get feed
	$s = youtubeGetApi( $request .'&max-results='. $itemsPerPage .'&start-index='. $start );
	$ss=json_decode($s, true);

	$start = $ss['feed']['openSearch$startIndex']['$t'];
	$total = $ss['feed']['openSearch$totalResults']['$t'];

	$items = array();
	foreach( $ss['feed']['entry'] as $item )
	{
		$items[] = array(
			'id'       => $item['media$group']['yt$videoid']['$t'],
			'title'    => $item['title']['$t'],
			'image'    => $item['media$group']['media$thumbnail'][ 0 ]['url'],
			'duration' => youtubeDuration( $item['media$group']['yt$duration']['seconds'] ),
		);
	}

	// navigation
	$min_page = 1;
	$max_page = floor( $total / $itemsPerPage ) + 1;
	$page = floor( $start / $itemsPerPage ) + 1;

	// generate list
	$s = '';

	// top title
	$s .= 'YouTube';

	if( $my <> '' )
	{
		$s .= ' - '. getMsg( 'youtubeSubscription' ) . $username ;
	}
	else
	{
		if( $feed <> '' )
		{
			if( $region <> '' )
			 $s .= ' '. getMsg( 'youtube_region_'. $region );

			$s .= ' - '. getMsg( 'youtube_'. $feed );

			if( $youtube_feeds[ $feed ]['allowTime'] )
			 if( $period != 'all_time' ) $s .= ' ('. getMsg( 'youtube_'.$period ) .')';
		}
		else
		{
			$s .= ' - '. urldecode( $youtube_config['search'] );
		}

		if( $cat <> '' )
		 $s .= ' - '. $youtube_config['cats'][ $cat ];
	}

	if( $min_page != $max_page ) $s .= ' ('. getMsg('coreRssPromptPage') . $page . getMsg('coreRssPromptFrom') . $max_page .')';

	$s .= PHP_EOL ;

	// bottom title
	$s .=
		'<< ' . getMsg('coreRssPromptMenu') .
		getRssCommandPrompt('enter')  . getMsg('coreRssPromptWatch') .
		PHP_EOL;

	// navs

	$url = getMosUrl().'?page=xml_youtube';
	if( $feed != '' )
	{
		$url .= '&feed='. $feed;
		if( $cat <> '' ) $url .= '&cat='. $cat;
	}
	elseif( $search != '' )
	{
		$url .= '&search='. urlencode( $search );
	}
	else $url .= '&my';

	if( ( $page - 1 ) >= $min_page ) { $s .= $url.'&start='.(( $page - 2 ) * $itemsPerPage + 1 ).PHP_EOL; }
	else $s .= "\n" ;

	if( ( $page + 1 ) <= $max_page ) { $s .= $url.'&start='.( $page * $itemsPerPage + 1 ).PHP_EOL; }
	else $s .= "\n" ;
	
	// number of items
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['title']  .PHP_EOL;
		$s .= $item['image']  .PHP_EOL;
		$s .= $item['id']     .PHP_EOL;
		$s .= $item['duration'] .PHP_EOL;
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
function youtubeGetStream( $id, $quality = 'lo' )
{
	$opts = array(
		'http' => array(
			'method'  => 'GET',
//			'user-agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0'
			'user-agent' => 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1C25 Safari/419.3'
		)
	);
	$context = stream_context_create($opts);

//	$s = file_get_contents( "http://www.youtube.com/get_video_info?video_id=$id&el=embedded&ps=default&hl=en_US", false, $context);
	$s = file_get_contents( "http://www.youtube.com/watch?v=$id&feature=player_embedded", false, $context);

	$streams = array();

	if( preg_match( '/yt.playerConfig\s*= \s*(.*?});/s' , $s, $ss ) > 0 )
	{
		$a = json_decode($ss[1], true);

		if( isset( $a['args']['url_encoded_fmt_stream_map'] ))
		{
			$s = $a['args']['url_encoded_fmt_stream_map'];
			$a = explode( ',', $s );


			foreach( $a as $s )
{
	$b = explode( '&', $s );
	if( isset( $_REQUEST['debug'])) print_r( $b );

	unset( $tag );
	unset( $url );
	unset( $sig );

	foreach( $b as $p )
	if(     strpos( $p, 'itag=' ) === 0 ) $tag = substr( $p, 5 );
	elseif( strpos( $p, 'url='  ) === 0 ) $url = substr( $p, 4 );
	elseif( strpos( $p, 'sig='  ) === 0 ) $sig = substr( $p, 4 );

	if( isset( $tag, $url, $sig ))
	 $streams[ $tag ] = urldecode( urldecode( $url )).'&signature='.$sig;
}
		}
	}
/*
	if( preg_match( '/url_encoded_fmt_stream_map=(.*?)&/s' , $s, $ss ) > 0 )
	{
		if( preg_match_all( '/url=(.*?)&.*?itag=(\d*)/' , urldecode( $ss[1] ).',', $ss ) > 0 )
		 foreach( $ss[2] as $i => $itag )
		  if( preg_match( '/([^;]*)/' , urldecode( $ss[1][ $i ] ), $a ) > 0 )
		   $streams[ $itag ] = urldecode( $a[1] );
	}
*/
	if( isset( $_REQUEST['debug'])) print_r( $streams );

	// 45 - hd720 webm
	// 22 - hd720 mp4
	// 44 - large webm
	// 35 - large flv
	// 43 - medium webm
	// 34 - medium flv
	// 18 - medium mp4
	//  5 - small flv
	// 36 - small 3gpp
	// 17 - small 3gpp

	# large
	if( $quality == 'hi' )
	{
		if( isset( $streams[ 22 ] )) return $streams[ 22 ];
		if( isset( $streams[ 35 ] )) return $streams[ 35 ];
	}
	# medium
	if( isset( $streams[ 18 ] )) return $streams[ 18 ];
	if( isset( $streams[ 34 ] )) return $streams[ 34 ];
	# small
	if( isset( $streams[  5 ] )) return $streams[  5 ];

	return false;
}
//
// ------------------------------------
function get_youtube_content()
{
global $mos;
global $youtube_config;

	if( ! isset( $_REQUEST['id'])) return;

	if( isset( $_REQUEST['debug'])) header( "Content-type: text/plain" );

	if( isset( $_REQUEST['debug'])) echo "id=". $_REQUEST['id'] ."\n";


	if(( $u = youtubeGetStream( $_REQUEST['id'], $youtube_config['quality'] )) === false ) return;

	if( isset( $_REQUEST['debug'])) echo 'Location: ' .$u ."\n" ;
	else header ( 'Location: ' .$u );

}
//
// ====================================
function rss_youtube_menu_content()
{
global $youtube_config;
global $youtube_feeds;

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->items = array();

	$i = 0;
	$cur = 0;

	$feed = $youtube_config['feed'];

	if( $feed != '' )
	 if( $youtube_feeds[ $feed ]['allowTime'] )
	  $view->items[ $i++ ] = array(
		'title'	=> getMsg('youtubePeriod'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_youtube_period'
	  );

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('youtubeCategories'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_youtube_cats'
	);

	foreach( $youtube_feeds as $f => $item )
	{
		if( $f == $feed ) $cur = $i;

		 $view->items[ $i++ ] = array(
			'title'	=> getMsg('youtube_'. $f ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;feed='. $f
		 );
	}

	if( $youtube_config['my'] != '' ) $cur = $i;

	if( $youtube_config['username'] == '' )
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('youtubeMy'),
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=xml_youtube&amp;my&amp;username='
	);
	else
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('youtubeMy'),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_youtube&amp;my'
	);

	if( $youtube_config['search'] != '' ) $cur = $i;

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('youtubeSearch'),
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=xml_youtube&amp;search='
	);

	$view->items[ $i++ ] = array(
		'title'	=> getMsg('coreSettings'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_youtube_sets'
	);

	$view->currentItem = $cur;

	$view->showRss();
}
//
// ------------------------------------
function rss_youtube_period_content()
{
global $youtube_config;
global $youtube_periods;

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 1;
	$view->items = array();

	$i = 0;
	$cur = 0;

	foreach( $youtube_periods as $period )
	{
		if( $period == $youtube_config['period'] ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> getMsg('youtube_'.$period),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;period='.$period
		);
	}

	$view->currentItem = $cur;

	$view->showRss();
}
//
// ------------------------------------
function rss_youtube_sets_content()
{
	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('youtubeRegions'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_youtube_region'
		),
		1 => array(
			'title'	=> getMsg('youtubeQuality'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_youtube_quality'
		),
		2 => array(
			'title'	=> getMsg('youtubeKeyboard'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_youtube_keyboard'
		),
		3 => array(
			'title'	=> getMsg('youtubeUsername'),
			'action'=> 'search',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;username='
		),
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_youtube_region_content()
{
global $youtube_config;
global $youtube_regions;

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 2;

	$items = array();
	foreach( $youtube_regions as $reg => $lang )
	 $items[ getMsg('youtube_region_'.$reg) ] = $reg;

	ksort( $items, SORT_LOCALE_STRING );

	$view->items = array();

	$i = 0;
	$cur = 0;

	foreach( $items as $title => $reg  )
	{
		if( $reg == $youtube_config['region'] ) $cur = $i;

		$view->items[ $i++ ] = array(
			'title'	=> $title,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;region='.$reg
		);
	}

	$view->currentItem = $cur;

	$view->showRss();
}
//
// ------------------------------------
function rss_youtube_quality_content()
{
global $youtube_config;

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('youtubeLowQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;quality=lo'
		),
		1 => array(
			'title'	=> getMsg('youtubeHighQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;quality=hi'
		)
	);

	$view->currentItem = 0;
	if( $youtube_config['quality'] == 'hi' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ------------------------------------
function rss_youtube_keyboard_content()
{
global $youtube_config;

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('youtubeEmbKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;keyboard=emb'
		),
		1 => array(
			'title'	=> getMsg('youtubeRssKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;keyboard=rss'
		)
	);

	$view->currentItem = 0;
	if( $youtube_config['keyboard'] == 'rss' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ====================================
function getYoutubeCategories()
{
global $nav_lang;
global $youtube_config;
global $youtube_regions;

	$lang = $nav_lang;
	if(( $reg = $youtube_config['region'] ) != '' )
	 $lang = $youtube_regions[ $reg ] .'-'. $reg;

	$s = file_get_contents( "http://gdata.youtube.com/schemas/2007/categories.cat?hl=$lang", false );

	if( preg_match_all( '/<atom:category.*?<\/atom:category>/s', $s, $ss ) > 0 )
	{
		$youtube_config['cats'] = array();
		foreach( $ss[0] as $a )
		{
			if( strpos( $a, 'yt:deprecated' ) ) continue;
			if( preg_match( '/term=\'(.*?)\' label=\'(.*?)\'/s', $a, $aa ) > 0 )
			 $youtube_config['cats'][ $aa[1] ] = $aa[2];
		}
		youtubeSaveConfig();
	}
	return $youtube_config['cats'];
}
//
// ------------------------------------
function rss_youtube_cats_content()
{
global $youtube_config;

	$items = getYoutubeCategories();

	include( 'rss_youtube_view_left.php' );
	$view = new rssYouTubeLeftView;

	$view->position = 1;

	$i = 0;
	$cur = 0;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('youtubeAllCats'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;cat=all'
		),
	);
	foreach( $items as $id => $title )
	{
		$i += 1;
		if( $id == $youtube_config['cat'] ) $cur = $i;

		$view->items[ $i ] = array(
			'title'	=> $title,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_youtube&amp;cat='.$id
		);
	}

	$view->currentItem = $cur;

	$view->showRss();
}

?>
