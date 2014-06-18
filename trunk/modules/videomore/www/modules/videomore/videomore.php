<?php
// get config
$videomore_config = array(
	'type'    => 'files',
	'query'   => '#popular',
	'sort'    => 'no',
	'order'   => 'desc',	// desc asc
	'p'       => 1,		// page
	'quality' => '2',
	'keyboard'=> 'rss',
);

$videomore_config_path = dirname( __FILE__ ) .'/videomore.config.php';

if( is_file( $videomore_config_path ) )
{
	include( $videomore_config_path );
}

// get session categories
$videomore_session = array(
	'genres' => array(),
);

$videomore_session_path = '/tmp/videomore.session.php';

if( is_file( $videomore_session_path ) )
{
	include( $videomore_session_path );
}

//
// ------------------------------------
function getVideomoreConfigParameter( $name )
{
global $videomore_config;

	return $videomore_config[ $name ];
}
//
// ------------------------------------
function saveVideomoreConfig()
{
global $videomore_config;
global $videomore_config_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "saveVideomoreConfig\n";
	print_r( $videomore_config );
}

	file_put_contents( $videomore_config_path, '<?php $videomore_config = '.var_export( $videomore_config, true ).'; ?>' );
}
//
// ------------------------------------
function saveVideomoreSession()
{
global $videomore_session;
global $videomore_session_path;

if( isset( $_REQUEST['debug'] ))
{
	echo "saveVideomoreSession\n";
	print_r( $videomore_session );
}

	file_put_contents( $videomore_session_path, '<?php $videomore_session = '.var_export( $videomore_session, true ).'; ?>' );
}
//
// ------------------------------------
function getVideomoreContent( $url )
{
	$s = @file_get_contents( $url );
	return $s;
}
//
// ====================================
function rss_videomore_content()
{
	include('videomore.rss.main.php');

	$view = new rssVideomoreView;
	$view->showRss();
}
//
// ====================================
function getVideomoreVideo( $id )
{
global $videomore_config;
	$id = Param($id,'playvideo','');
	$hd = get_headers($id,true);
if( isset( $_REQUEST['debug'] )) print_r($hd);
	$id = @$hd['Location'];
	if( $id =='' ) return '';

if( isset( $_REQUEST['debug'] )) echo"\nvideoId=$id\n";

	return $id;
}
//
// ------------------------------------
function videomore_get_content()
{
global $mos;
global $videomore_config;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['id'])) return;
	$id = $_REQUEST['id'];
	$u = getVideomoreVideo( $id );
	if( $u == '' ) return;
	echo $u;
}
//
// ====================================
function getVideomoreRequest( $name )
{
global $videomore_config;

	$s = $videomore_config[ $name ];
	if( isset( $_REQUEST[ $name ] ))
	{
		$s = $_REQUEST[ $name ];
	}
	$videomore_config[ $name ] = $s;
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
function videomore_list_content()
{
global $videomore_config;
global $videomore_session;
global $videomore_sorts;

header( "Content-type: text/plain" );

	$url_base = 'http://m.videomore.ru';
	$url = $url_base;;
	$pars = array();

	// settings
	$quality  = getVideomoreRequest('quality');
	$keyboard = getVideomoreRequest('keyboard');

	// query
	$query = getVideomoreRequest('query');
	$type = getVideomoreRequest('type');

	if ( $type == '' )
	{
		$quality = 2;
		$type = 'files';
		$query = '#popular';
		$videomore_config['quality' ] = $quality;
		$videomore_config['type' ] = 'files';
		$videomore_config['query'] = '#popular';
	}
	$kach = $quality;
	$series_id ='';
	if (strpos($query,'%')!==false) $query = urldecode($query);
	if (strpos($query,'#serie')!==false) {
		$series_id = Param($query,'#serie','');
		$query = Param($query,'', '#serie');
	}
	//if( isset( $_REQUEST['debug'])) echo "22222222 ";
	//if( isset( $_REQUEST['debug'])) print_r($series_id).chr(10);
	
	$page = getVideomoreRequest('p');
	#if( $page != 1 ) $url .= 'page/'. $page .'/';

	$sort = getVideomoreRequest('sort');
	$order = getVideomoreRequest('order');
	
	// save config
	saveVideomoreConfig();


if( isset( $_REQUEST['debug'])) echo "url=$url\n";
	$url = 'http://m.videomore.ru';
	// get html page
	stream_context_set_default(
			array(  'http' => array(
					'method' => 'GET',
					'header'=>"User-Agent: Mozilla/5.0 (Linux; Android 4.1.1; Nexus 7 Build/JRO03D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166  Safari/535.19" ) ) );
	
	$s = getVideomoreContent( $url );

//if( isset( $_REQUEST['debug'])) echo "$s\n";

	// get genres
	$html = $s;
	$html = Param($html,'"bottommenu">','</div');
	$html = Tokens($html,'href= "','"','title = "','"');
	
	if( count($html)>0)
	{
		$videomore_session['genres'] = array();
		foreach( $html as $id => $_title ) $videomore_session['genres'][ $id ] = $_title;
		saveVideomoreSession();
	}

	// get items
	$array_category_id = array('#popular' => 0, '#shows' => 1 , '#serials' => 2,'#programmes' => 3,'#search' => 4);
	$items = array();
	$html = $s;
	$html = Param($html,'<script src="','"');
	$html = getVideomoreContent( $html );
	$category_id = $array_category_id[$query];
	#$app_id = Param($html,'var app_id','var'); #'html5_vm';
	#$app_id = str_replace("'",'"',$app_id);
	#$app_id = Param($app_id,'"','"');
	$app_id = 'html5_vm';
	$secret = Param($html,'var secret','var');
	$secret = str_replace("'",'"',$secret);
	$secret = Param($secret,'"','"');
	
	//$series_id='';
if    ( $type !== 'search' ) {
		$md5 = "app_id=$app_id&category_id=$category_id$secret";
		$md5 = md5($md5);
		$ipad_url = "http://videomore.ru/api/projects.json?category_id=$category_id&app_id=$app_id&sig=$md5";
		$chnls = getVideomoreContent( $ipad_url );
		$html = $chnls;
		
	if ($series_id!=='') {
		$chnls = Param($chnls, '"id":'.$series_id.',', '');
		$chnls = Param($chnls,'"overall_count":',','); $chnls = trim($chnls);
		$md4 = "app_id=$app_id&order=episode desc&page=$page&per_page=12&project_id=$series_id";
		$md5 = $md4.'secret';
		$md5 = md5($md5);
		$ipad_url = 'http://videomore.ru/api/tracks.json?'.$md4."&sig=".$md5;
		$html = getVideomoreContent( $ipad_url );
	}
	$page_name = Param($html,'"project_title":"','",');
} else {
		$series_id = '#search';
		$pmax = 4;
			$html ='';
			for ( $pp=1; $pp<=$pmax; $pp++) {
				$md4 = "app_id=$app_id&page=$pp&query=$query";
				$md5 = $md4.'secret';
				$md5 = md5($md5);
				$ipad_url = 'http://videomore.ru/search.json?'.$md4."&sig=".$md5;
				$ht = getVideomoreContent( $ipad_url );
				if ($ht=='') break;
				$html = $html.$ht;
			}
		$page_name = 'Поиск: '.$query;
}
	if( isset( $_REQUEST['debug'])) echo "1111111111111111111111111 ";
	if( isset( $_REQUEST['debug'])) print_r($ipad_url).chr(10);
	
	
	if ($series_id!=='') { $pmax = ceil ($chnls/12);} else $pmax = 1;
	
	$html = str_replace('{"id":','<<>>{"id":',$html);
	$html = Tokens($html.'<<>>','{"id":','<<>>');
	if ($type == 'search') $chnls = count ($html);
	//if( isset( $_REQUEST['debug'])) print_r(chr(10).count($html).chr(10));
	//if( isset( $_REQUEST['debug'])) print_r($html).chr(10);
	$elements = array();
	if( count($html)  > 0 )
	{
		$itemss = '';
		foreach( $html as $item )
		{
			// url, image
			$id  = Param($item,'',',');
			$img = Param($item,'"small_thumbnail":"','",');
			if ($img =='') $img=Param($item,'"small_thumbnail_url":"','",');
			// title
			$title = Param($item,'"title":"','",');
			$titlle = str_replace(array('<b>','</b>','\n','\p','\r','http://vk.com/videomore'),'',$title);
			$serie = Param($item,'"episode_of_season":',','); 
			$season_name = Param($item,'"season_title":"','",');
			$op = '';
			if ($season_name!=='') $op = $season_name;
			if ($serie!=='') $op = $op." Серия $serie";
			$op = str_replace('Серия 0','', $op);
			$op = trim($op);
			// year
			$year = Param($item,'"year":',',');
			if (strlen($year)>3) {$title .= ' ('. $year .')';}

			// desc
			$desc = Param($item,'"description":"','","'); 
			$desc = str_replace(array('<b>','</b>','\n','\p','\r','http://vk.com/videomore','http://vkontakte.ru/videomore'),'',$desc);
			$desc = trim($desc);

			// genres
			
			$genre = Param($item,'"overall_count":',','); 
			$genre = trim($genre);
			if ($genre!=='') {$genre = "Количество серий:$genre";} else {$genre = $op;}
			// qual
			$id  = "$query#serie$id";
			$work = 'dir';
			$id  = urlencode($id);
			if ($series_id!=''){
				$work = 'play';
				$quality = Tokens($item,'"q','":','"','"');
				$min = 10; $sel = $kach; 
				if( isset( $_REQUEST['debug'])) print_r($quality).chr(10);
				foreach ($quality as $qua => $video) {
					$a = abs($sel - $qua); 
					if ($a<$min) {$min = $a; $min_id =$qua; }
				}
				$id = $quality[$min_id];
				$id = playvideo.$id;
				$id =urlencode($id);
			}
			
			if( isset( $_REQUEST['debug'])) print_r($id);
			$itemss = $itemss."$title$id\n";
			$items[] = array(
				'id'   => $id,
				'title'=> $title,
				'image'=> $img,
				'desc' => $desc,
				'genre'=> $genre,
			);
		}
	}
	file_put_contents('/tmp/itemsss.dat',$itemss);

if( isset( $_REQUEST['debug'])) print_r( $items );

	// get navigation
	
	if( isset( $_REQUEST['debug'])) echo "Max page=$pmax\n";

	// generate list
	$s = '';

	// top title
	$s .= 'v1.1 videomore.ru  ';
	if ($page_name =='') {$page_name = $videomore_session['genres'][ $query ];}
	if    ( $type == 'search' )	$s .= ' - '. urldecode( $query );
	#elseif( $type == 'genre' )	$s .= ' - '. $videomore_session['genres'][ $query ];
	else				$s .= ' - '. $page_name ;
	$s .= PHP_EOL ;

	// bottom title
	$s .=
		'  << ' . getMsg('coreRssPromptMenu') . getRssCommandPrompt('enter')  . getMsg('coreRssPromptWatch') . getRssCommandPrompt('play') .getMsg('coreRssPromptWatch')." playlist". PHP_EOL;

	// sort
	if ($series_id=='')  $s .= count($items).' наим.' .PHP_EOL ; else $s .= "$chnls видео".PHP_EOL;

	// page
	if( $pmax > 1 || $series_id!=='') $s .= 'Возврат в жанр "HOME"     '.$page .'/'. $pmax;
	$s .= PHP_EOL ;

	// navs
	$url = getMosUrl().'?page=videomore_list';
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
function rss_videomore_menu_content()
{
global $videomore_config;

	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;

	$view->items = array();

	$i = 0;
	$cur = -1;

	$type  = $videomore_config['type'];
	$query = $videomore_config['query'];
	if (strpos($query,'%')!==false) $quer = urldecode($query);
	if (strpos($quer,'#serie')!==false) {
		$series_id = Param($query,'#serie','');
		$quer = Param($query,'', '#serie');
	}
	
	if( $type == 'genres') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('videomore_title_genre'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_videomore_genres'
	);

	if( $type == 'search') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('videomore_title_search'),
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=videomore_list&amp;type=search&amp;p=1&amp;query='
	);

	if( $series_id !== '') $cur = $i;
	$view->items[ $i++ ] = array(
		'title'	=> 'Страница',
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_videomore_page',
	);
	
	
	$view->items[ $i++ ] = array(
		'title'	=> getMsg('videomore_title_sets'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_videomore_sets',
	);

	if( $cur == -1) $cur = 0;
	if( isset( $_REQUEST['debug'])) print_r($view->items); 
	$view->currentItem = $cur;
	$view->showRss();
}
//
// ====================================
function rss_videomore_genres_content()
{
global $videomore_config;
global $videomore_session;

	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;

	$view->position = 1;

	$view->items = array();

	$i = 0;
	foreach( $videomore_session['genres'] as $id => $item )
	{
		if( $id == $videomore_config['query'] ) $view->currentItem = $i;
		$view->items[ $i++ ] = array(
			'title'	=> $item,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;p=1&amp;type=genre&amp;query='. urlencode($id)
		);
	}

	$view->showRss();
}

//
// ====================================
function rss_videomore_sets_content()
{
	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;

	$view->position = 1;

	$view->items = array(
		array(
			'title'	=> getMsg('videomoreQuality'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_videomore_quality'
		),
		array(
			'title'	=> getMsg('videomoreKeyboard'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_videomore_keyboard'
		),
	);

	$view->showRss();
}

//
// ------------------------------------
function rss_videomore_page_content()
{
global $videomore_config;
global $videomore_session;

	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;
	$cur_page = $videomore_config['p'];
	$query = $videomore_config['query'];
	$quer = $query;
	if (strpos($query,'%')!==false) $query = urldecode($query);
	if (strpos($query,'#serie')!==false) {
		$series_id = Param($query,'#serie','');
		$query = Param($query,'', '#serie');
	}
	
	$array_category_id = array('#popular' => 0, '#shows' => 1 , '#serials' => 2,'#programmes' => 3,'#search' => 4);
	$category_id = $array_category_id[$query];
	$app_id = 'html5_vm';
	$secret = 'secret';
	$md5 = "app_id=$app_id&category_id=$category_id$secret";
	$md5 = md5($md5);
	$ipad_url = "http://videomore.ru/api/projects.json?category_id=$category_id&app_id=$app_id&sig=$md5";
	$chnls = getVideomoreContent( $ipad_url );
	$chnls = Param($chnls, '"id":'.$series_id.',', '');
	$chnls = Param($chnls,'"overall_count":',','); $chnls = trim($chnls);
	$view->position = 1;
	$view->items = array();
	$chnls = ceil($chnls/12);
	for ($i = 0; $i < $chnls; $i++) {
		$pp = $i+1;
		$view->items[ $i ] = array(
				'title'	=> "Страница $pp",
				'action'=> 'ret',
				'link'	=> getMosUrl()."?page=videomore_list&amp;p=$pp&amp;type=film&amp;query=". urlencode($quer)
			);
	}
	$view->currentItem = $cur_page-1;
	$view->showRss();
}


//
// ------------------------------------
function rss_videomore_quality_content()
{
global $videomore_config;

	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;
	$view->position = 2;
	$qua = array(
		array(
			'title'	=> getMsg('videomoreLowQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;quality=4'
		),
		array(
			'title'	=> getMsg('videomoreMedQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;quality=5'
		),
		array(
			'title'	=> getMsg('videomoreHighQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;quality=1'
		),
		array(
			'title'	=> getMsg('videomoreExtraQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;quality=2'
		),
		array(
			'title'	=> getMsg('videomorePremQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;quality=3'
		),
	);
	$key = $videomore_config['quality' ];
	$i = 0;
	foreach ($qua as $item) { 
		if (strpos($item['link'],"=$key")!==false) {$key=$i; break;}
		$i =$i +1;
	}
	
	$view->items = $qua;
	$view->currentItem = $key;
	#$if( $videomore_config['quality'] == '4'  ) $view->currentItem = 1;
	$view->showRss();
}
//
// ------------------------------------
function rss_videomore_keyboard_content()
{
global $videomore_config;

	include( 'videomore.rss.menu.php' );
	$view = new rssVideomoreLeftView;

	$view->position = 2;

	$view->items = array(
		array(
			'title'	=> getMsg('videomoreEmbKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;keyboard=emb'
		),
		array(
			'title'	=> getMsg('videomoreRssKbrd'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=videomore_list&amp;keyboard=rss'
		),
	);

	$view->currentItem = 0;
	if( $videomore_config['keyboard'] == 'rss' ) $view->currentItem = 1;

	$view->showRss();
}
function videomore_deyst_content() {
	if( ! isset( $_REQUEST['id'])) {echo 3; return;}
	$id = $_REQUEST['id'];
	$idd = $id;
	if (strpos($id,'%')!==false) $id = urldecode($id);
	if (strpos($id,'playvideo')!==false) { $_SESSION['start']=$idd; echo 1;} else { unset($_SESSION['start']); echo 0;} 
	return;
}
function videomore_plist_content() {
	$itemss = file_get_contents('/tmp/itemsss.dat');
	$info = explode(chr(10),$itemss);
	$info = array_reverse ($info);
	unset($info[0]);
	$start_url = $_REQUEST['id'];
	$start_url = urlencode($start_url);
	$items = array();
	include( 'player.php' );
	$view = new rssMyPlayerView;
	
	foreach ($info as $item) {
		$title = Param($item,'','playvideo');
		$id = Param($item,$title,'');
		$title = trim($title).'.';
		if(count($items)<1) {$items[] = array( 'start'  => $start_url, 'link'  => $id, 'title' => $title, 'act'   => 'videomore_get'); }
		 else {$items[] = array('link'  => $id, 'title' => $title, 'act'   => 'videomore_get');}
	}
	$view->items = $items;
	$view->showRss();
}

function videomore_back_content() {
	global $videomore_config;
	$query = $videomore_config['query'];
	$type = $videomore_config['type'];
	if (strpos($query,'%')!==false) $query = urldecode($query);
	if (strpos($query,'#serie')!==false) {
		$series_id = Param($query,'#serie','');
		$query = Param($query,'', '#serie');
	}
	if ($type == 'search' || $series_id=='') return '';
	$url = urlencode($query);
	echo $url; return;
}

function Param($scp, $prf, $suf, $dft = null, $occ = 1) {
		if (! isset($scp) || ! is_string($scp)) { return $dft; }
		for ($start = 0; $occ > 0; $occ--) {
			$start = null == $prf || '' == $prf ? 0 : strpos($scp, $prf, $start);
			if (false === $start) { return $dft;}
			$start = $start + strlen($prf);
		}
		$stop =  null == $suf || '' == $suf ? strlen($scp) : strpos($scp, $suf, $start);
		if (false === $stop) { return $dft;}
		return substr($scp, $start, $stop - $start);
}

function Tokens($scp, $prf1, $suf1, $prf2 = null, $suf2 = null) {
		$tokens = array();
		while (true) {
			$start = strpos($scp, $prf1);
			if (false === $start) { break;}
			$start = $start + strlen($prf1);
			$stop  = strpos($scp, $suf1, $start);
			if (false === $stop) {break;}
			$token1 = substr($scp, $start, $stop - $start );
			$scp = substr($scp, $stop + strlen($suf1));
			if (null == $prf2 || null == $suf2) { $tokens[] = $token1; continue;}
			$start = strpos($scp, $prf2);
			if (false === $start) { break;}
			$start = $start + strlen($prf2);
			$stop  = strpos($scp, $suf2, $start);
			if (false === $stop) { break;}
			$token2 = substr($scp, $start, $stop - $start );
			$scp = substr($scp, $stop + strlen($suf2));
			$tokens[$token1] = $token2;
		}
		return $tokens;
}

?>
