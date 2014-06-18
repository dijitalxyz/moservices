<?php
global $fex_config;
global $fex_bookmarks;

$fex_config = array(
	'site'    => 'fex.net',
	'home'    => '23775',
	'quality' => 'lo',
	'start'   => 'home',
	'keyboard'=> 'rss',
);
$fex_bookmarks = array();

$fex_file_config = dirname( __FILE__ ) .'/fex.config.php';
$fex_file_bookmarks = dirname( __FILE__ ) .'/fex.bookmarks.php';

if( is_file( $fex_file_config    ) ) include( $fex_file_config );
if( is_file( $fex_file_bookmarks ) ) include( $fex_file_bookmarks );
//
// ------------------------------------
function getFexConfigParameter( $name )
{
global $fex_config;

	return $fex_config[ $name ];
}
//
// ------------------------------------
function rss_fex_content()
{
	class rssSkinFexView extends rssSkinHTile
	{
		const itemWidth		= 300;
		const itemHeight	= 120;

		const itemImageX	= 10;
		const itemImageY	= 10;
		const itemImageWidth	= 70;
		const itemImageHeight	= 100;

		const itemTextX		= 85;
		const itemTextY		= 10;
		const itemTextWidth	= 210;
		const itemTextHeight	= 80;

		const itemTextLines	= 3;
		//
		// ------------------------------------
		public $topTitle =
'
	<script>
	  pageTitle;
	</script>';
		//
		// ------------------------------------
		public $itemImage =
'
	<script>
	  getStringArrayAt(imgArray, idx);
	</script>
';
		// ----------------
		public $itemTitle =
'
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>
';
		//
		// ------------------------------------
		public function showOnUserInput()
		{
?>
    <onUserInput>
	url = "";
	ret = "false";
	i = getFocusItemIndex();
	stackItem = i;
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		if( ( i % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		if( ( ( i - -1 ) % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		if( pPage != "" )
		if( i &lt; <?= $this->_rowCount ?> )
		{
			toStack = 0;
			url = pPage;
			savedItem = i - ( <?= $this->_rowCount ?> - itemCount );
		}
	}
	else if (userInput == "<?= getRssCommand('right') ?>")
	{
		if( nPage != "" )
		if( i &gt; ( itemCount - <?= $this->_rowCount ?> - 1 ) )
		{
			toStack = 0;
			url = nPage;
			savedItem = i - ( itemCount - <?= $this->_rowCount ?> );
		}
	}
	else if (userInput == "<?= getRssCommand('return') ?>")
	{
		if( stackCount &gt; 0 )
		{
			toStack = 0;

			stackCount -= 1;
			url = getStringArrayAt(stackUrlArray, stackCount);
			savedItem = getStringArrayAt(stackItemArray, stackCount);

			stackUrlArray = deleteStringArrayAt(stackUrlArray, stackCount);
			stackItemArray = deleteStringArrayAt(stackItemArray, stackCount);
		}
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		toStack = 1;
		savedItem = 0;
		url  = getStringArrayAt(urlArray, i) + "&amp;play";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		toStack = 1;
		savedItem = 0;
		url  = getStringArrayAt(urlArray, i) + "&amp;info";
	}
	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		toStack = 1;
		url = "<?= getMosUrl().'?page=rss_fex_actions' ?>"
		 + "&amp;view=" + getStringArrayAt(urlArray, i)
		 + "&amp;title=" + urlEncode( getStringArrayAt(nameArray, i) )
		 + "&amp;image=" + urlEncode( getStringArrayAt(imgArray, i) )
		;
		url = doModalRss( url );
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
		toStack = 1;
	        url = "<?= getMosUrl().'?page=rss_fex_menu' ?>";
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			savedItem = 0;
		}
		ret = "true";
	}

	if(( url != null )&amp;&amp;( url != "" ))
	{
		moUrl = url;
		setRefreshTime(10);
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
		//
		// ------------------------------------
		function showMoreDisplay()
		{
?>
    <text redraw="yes" align="center" lines="1" fontSize="22"
     offsetXPC="18.2353" offsetYPC="15.625" widthPC="63.5294" heightPC="7.8125"
     backgroundColor="-1:-1:-1"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	msgText;
      </script>
    </text>
<?php

		}
		//
		// ------------------------------------
		public function showScripts()
		{
?>
  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_fex' ?>";
	savedItem = 0;

	toStack = 0;
	stackUrl = moUrl;
	stackItem = 0;
	stackCount = 0;
	stackUrlArray = null;
	stackItemArray = null;

	setRefreshTime(1);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		type = getStringArrayAt(dlok, c); c += 1;
		if( type == "ret" )
		{
			pageTitle = getStringArrayAt(dlok, c); c += 1;

			stack = getStringArrayAt(dlok, c); c += 1;
			pPage = getStringArrayAt(dlok, c); c += 1;
			nPage = getStringArrayAt(dlok, c); c += 1;

			itemCount = getStringArrayAt(dlok, c); c += 1;

			nameArray = null;
			imgArray  = null;
			urlArray  = null;

			count = 0;
			while( count != itemCount )
			{
				nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
				imgArray  = pushBackStringArray( imgArray,  getStringArrayAt(dlok, c)); c += 1;
				urlArray  = pushBackStringArray( urlArray,  getStringArrayAt(dlok, c)); c += 1;

				count += 1;
			}

			if( stack == "clear" ) toStack = -1;

			if( toStack == -1 )
			{
				stackUrlArray = null;
				stackItemArray = null;
				stackCount = 0;
			}
			if( toStack == 1 )
			{
				stackUrlArray = pushBackStringArray( stackUrlArray, stackUrl);
				stackItemArray = pushBackStringArray( stackItemArray, stackItem);
				stackCount += 1;
			}
			stackUrl = moUrl;

			msgText = "";
			if( itemCount == 0 )
			{
				msgText = "<?= getMsg('coreRssPromptNothing') ?>";
				setFocusItemIndex( 0 );
			}
			else
			{
				if( savedItem &gt; ( itemCount - 1 ))
				{
					setFocusItemIndex( itemCount - 1 );
				}
				else setFocusItemIndex( savedItem );
			}
			redrawDisplay();
		}

		else if( type == "rss" )
		{
			url = getStringArrayAt(dlok, c); c += 1;
			doModalRss( url );
		}
		else if( type == "play" )
		{
			url = getStringArrayAt(dlok, c); c += 1;
			playItemURL(url, 0);
		}
		else if( type == "last" )
		{
			toStack = 0;
			moUrl = stackUrl;
			savedItem = stackItem;
			setRefreshTime(100);
		}
	}
	cancelIdle();
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
  </onExit>
<?php
		}
		//
		// ------------------------------------
		public function showChannel()
		{
?>
  <channel>
    <itemSize>
      <script>
	itemCount;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}
	$view = new rssSkinFexView;

	$view->bottomTitle = 
		getRssCommandPrompt('menu')    . getMsg( 'coreRssPromptMenu' )
		. getRssCommandPrompt('play')  . getMsg( 'coreRssPromptWatch' )
		. getRssCommandPrompt('enter') . getMsg( 'coreRssPromptInfo' )
		. getRssCommandPrompt('stop')  . getMsg( 'coreRssPromptActs' )
	;

	$view->showRss();
}
//
// ------------------------------------
function fexGetList( $s )
{
	$links = array();

	if(( preg_match( '/<table .*? class=include_0>(.*?)<\/table>/s' , $s, $ss ) > 0 )
	 ||( preg_match( '/<table .*? class=panel>(.*?)<\/table>/s' , $s, $ss ) > 0 ))
	{

		$as = $ss[1];
		if( preg_match_all( '/<a .*?>.*?<\/a>/s' , $as, $ss ) === false ) return $links;
		foreach( $ss[0] as $a )
		{

if( isset( $_REQUEST['debug'] )) echo "$a\n";

			if( preg_match( '/<a href=\'\/(\d+).*?\'><img src=\'(.*?)\'.* alt=\'(.*?)\'.*<\/a>/' , $a, $as ) > 0 )
			{
				if( isset( $links[ $ss[1] ] )) continue;

				$links[ $as[1] ] = array(
					'type' => 'item',
					'image' => $as[2],
					'title' => $as[3]
				);
			}
			elseif( preg_match( '/<a href=\'\/view\/(\d+).*?\'><b>(.*?)<\/b><\/a>/' , $a, $as ) > 0 )
			{
				if( isset( $links[ $as[1] ] )) continue;

				$links[ $as[1] ] = array(
					'type' => 'group',
					'title' => $as[2]
				);
			}
		}
	}
	return $links;
}
//
// ------------------------------------
function fexGetPlaylist( $s = '' )
{
global $mos;
global $fex_config;

	$items = array();

	if( ! isset( $_REQUEST['view'])) return $items;
	$view = $_REQUEST['view'];

	if( $s == '' )
	{
		$s = file_get_contents( 'http://'. $fex_config['site'] .'/view/'. $view );
	}
	// get player's playlist
	$ls = array();
	if( preg_match( '/var player_list = \'(.*?)\';/' , $s, $ss ) > 0 )
	{
		$ss = json_decode( '['. $ss[1] .']', true );
		foreach( $ss as $item )
		 if( preg_match( '/\/show\/(\d*)\//' , $item['url'], $ss ) > 0 ) $ls[ $ss[1] ] = trim( $item['url'] );
	}
//print_r( $ls );
	// get playlist
	$ss = file( 'http://'. $fex_config['site'] .'/playlist/'. $view .'.m3u');
	$ps = array();
	foreach( $ss as $p )
	 if( preg_match( '/\/get\/(\d*)/' , $p, $ss ) > 0 ) $ps[$ss[1]] = trim( $p );
//print_r( $ps );
	// get title and infos
	foreach( $ps as $v => $p )
	{
		$title = '';
		$len = '';
		$info = '';

		if( preg_match( '/<a href=\'\/get\/'. $v .'\' title=\'(.+?)\' rel=\'nofollow\'>(.+?)<a href=\'\/load\/'. $v .'\' rel=\'nofollow\'>/s', $s, $ss ) > 0 )
		{
			$title = $ss[1];

			$a = $ss[2];
			if( preg_match( '/<td align=right width=200 class=small><b>(.+?)<\/b><p>/s', $a, $ss ) > 0 ) $len = (real)str_replace( ',', '', $ss[1] );
			if( preg_match( '/<td align=right width=200 class=small><b>.+?<\/b><p>.+?<br>.+?<br>(.+?)<p><span class=r_button_small>/s', $a, $ss ) > 0 ) $info = $ss[1];
		}

		$link = $ls[ $v ];
		if( $fex_config['quality'] == 'hi' ) $link = $ps[ $v ];

		$items[] = array(
			'action' => 'play',
			'link'   => getMosUrl() .'?page=get_fex&amp;url='. urlencode($link),
			'title'  => $title,
			'len'    => getHumanValue( $len ),
			'info'   => $info,
			'image'  => $mos .'/www/modules/fex/video.png',
			'dl'     => $ps[ $v ],
		);

	}
//print_r( $items );
	return $items;
}
//
// ------------------------------------
function get_fex_content()
{
	if( ! isset( $_REQUEST['url'])) return;

	$url = $_REQUEST['url'];

	// check for redirect
	$fp = fopen( $url, 'r' );
	$meta_data = stream_get_meta_data( $fp );
	$s = implode( "\n", $meta_data['wrapper_data'] );
	if( preg_match( '/[Ll]ocation:\s*(.*)/' , $s, $ss ) > 0 )
	 $url = $ss[1];

	$url = dirname( $url ) .'/'. urlencode( basename( $url ));
	header ( 'Location: '. $url );
}
//
// ------------------------------------
function fexSaveConfig()
{
global $fex_config;
global $fex_file_config;

	file_put_contents( $fex_file_config, '<?php $fex_config = '.var_export( $fex_config, true ).'; ?>' );
}
//
// ------------------------------------
function fexSaveBookmarks()
{
global $fex_bookmarks;
global $fex_file_bookmarks;

	file_put_contents( $fex_file_bookmarks, '<?php $fex_bookmarks = '.var_export( $fex_bookmarks, true ).'; ?>' );
}
//
// ------------------------------------
function xml_fex_content()
{
global $mos;
global $fex_config;
global $fex_bookmarks;

header( "Content-type: text/plain" );

	// sets home view
	if( ! isset( $_REQUEST['bookmarks'] )
	&&  ! isset( $_REQUEST['home'] )
	&&  ! isset( $_REQUEST['view'] ) )
	{
		if( $fex_config['start'] == 'home' )
		{
			$_REQUEST['view'] = $fex_config['home'];
		}
		elseif( count( $fex_bookmarks ) > 0 )
		{
			$_REQUEST['bookmarks'] = 'yes';
		}
		else $_REQUEST['view'] = $fex_config['home'];
	}

	if( isset( $_REQUEST['start']))
	{
		$fex_config['start'] = $_REQUEST['start'];
		fexSaveConfig();
		$s = 'none'.PHP_EOL;
	}

	if( isset( $_REQUEST['keyboard']))
	{
		$fex_config['keyboard'] = $_REQUEST['keyboard'];
		fexSaveConfig();
		$s = 'none'.PHP_EOL;
	}

	if( isset( $_REQUEST['quality']))
	{
		$fex_config['quality'] = $_REQUEST['quality'];
		fexSaveConfig();
		$s = 'none'.PHP_EOL;
	}

	elseif( isset( $_REQUEST['start']))
	{
		$fex_config['start'] = $_REQUEST['start'];
		fexSaveConfig();
		$s = 'none'.PHP_EOL;
	}

	elseif( isset( $_REQUEST['addmark']))
	{
		$view = $_REQUEST['addmark'];
		if( ! isset( $fex_bookmarks[ $view ] ))
		{
			$fex_bookmarks[ $view ] = array(
				'title' => $_REQUEST['title'],
				'image' => $_REQUEST['image']
			);
			fexSaveBookmarks();
		}
		$s = 'none'.PHP_EOL;
	}

	elseif( isset( $_REQUEST['delmark']))
	{
		$view = $_REQUEST['delmark'];
		if( isset( $fex_bookmarks[ $view ] ))
		{
			unset( $fex_bookmarks[ $view ] );
			fexSaveBookmarks();
		}
		$s = 'last'.PHP_EOL;
	}

	elseif( isset( $_REQUEST['bookmarks']))
	{
		// generate list
		$s = 'ret'.PHP_EOL ;

		// title
		$s .= $fex_config['site'] .' - '. getMsg('fexBookmarks') .PHP_EOL ;

		// stack
		$s .= PHP_EOL ;

		// navs
		$s .= PHP_EOL ;
		$s .= PHP_EOL ;

		// number of items
		$s .= count( $fex_bookmarks ) . PHP_EOL;

		foreach( $fex_bookmarks as $id => $item )
		{
			$s .= $item['title'] .PHP_EOL;
			$s .= $item['image'] .PHP_EOL;
			$s .= getMosUrl().'?page=xml_fex&view='. $id .PHP_EOL;
		}
	}
	else
	{
		// view page
		// --------------------

		$home = $fex_config['home'];
		if( isset( $_REQUEST['home']))
		{
			$a = $_REQUEST['home'];
			if( $a <> '' )
			{
				$fex_config['home'] = $a;
				$home = $a;
				fexSaveConfig();
			}
		}

		if( isset( $_REQUEST['view'] ))
		{
			$view = $_REQUEST['view'];
			$fex_config['view'] = $view;
		}
		else $view = $home;

		if( isset( $_REQUEST['site']))
		{
			$fex_config['site'] = $_REQUEST['site'];
			fexSaveConfig();
		}
		$url = 'http://'. $fex_config['site'];

		$search = '';
		if( isset( $_REQUEST['search']))
		{
			$search = $_REQUEST['search'];
			$url .= '/search/?s='. urlencode( $search );
		}
		else $url .= '/view/'. $view;

		$page = 0;
		if( isset( $_REQUEST['p'])) $page = $_REQUEST['p'];
		if( $page > 0 )
		{
			$url .= ( $search == '' ) ? '?' : '&';
			$url .= 'p='. $page;
		}

		// get html page
		$s = file_get_contents( $url );
       		$s = str_replace('/ru/video/foreign_series','/view/1988',$s);
		$s = str_replace('/ru/video/our_series','/view/422546',$s);
		$s = str_replace('/ru/video/foreign','/view/2',$s);
		$s = str_replace('/ru/video/our','/view/70538',$s);
		$s = str_replace('/ru/video/cartoon','/view/1989',$s);
		$s = str_replace('/ru/video/documentary','/view/1987',$s);
		$s = str_replace('/ru/video/short','/view/23785',$s);
		$s = str_replace('/ru/video/clip','/view/1991',$s);
		$s = str_replace('/ru/video/concert','/view/70533',$s);
		$s = str_replace('/ru/video/show','/view/28713',$s);
		$s = str_replace('/ru/video/trailer','/view/1990',$s);
		$s = str_replace('/ru/video/sport','/view/69663',$s);
		$s = str_replace('/ru/video/anime','/view/23786',$s);
		$s = str_replace('/ru/video/theater','/view/70665',$s);
		$s = str_replace('/ru/video/sermon','/view/371146',$s);
		$s = str_replace('/ru/video/commercial','/view/371152',$s);
		$s = str_replace('/ru/video/social_ad','/view/4313886',$s);
		$s = str_replace('/ru/video/training','/view/28714',$s);
		$s = str_replace('/ru/video/artist','/view/7513588',$s);
		$s = str_replace('/ru/video/mobile','/view/607160',$s);
		
		if(( preg_match( '/<table .*? class=include_0>/' , $s ) > 0 )
		 ||( preg_match( '/<table .*? class=panel>/' , $s ) > 0 ))
		{
			// group page
			// ====================

			// get title
			$title = '';
			if( preg_match( '/<h1>([^\[]*).*?<\/h1><br>/' , $s, $ss ) > 0 )
			 $title = $ss[1];

			// get content
			$links = fexGetList( $s );

			// get navigation
			$navs = array( 0 );
			if( preg_match( '/<table border=0 cellpadding=5 cellspacing=0>(.*?)<\/table>/s' , $s, $ss ) > 0 )
			 if( preg_match_all( '/<a .*?>.*?<\/a>/' , $ss[1], $ss ) > 0 )
			  foreach( $ss[0] as $a )
			   if( preg_match( '/<a href=.*?[\?\&]p=(\d*)\'>.*<\/a>/' , $a, $as ) > 0 )
			    $navs[] = $as[1];

			sort( $navs );
			$min_page = $navs[0];
			$max_page = $navs[ count( $navs ) - 1 ];

			// generate list
			$s = 'ret'.PHP_EOL ;

			// title
			$s .= $fex_config['site'];

			if( $search <> '' )
			{
				$s .= getMsg('fexSearchRezult'). $search;
			}
			else	$s .= ' - '. $title;

			if( $min_page !== $max_page ) $s .= ' ('. getMsg('coreRssPromptPage').( $page + 1 ).getMsg('coreRssPromptFrom').($max_page + 1 ).')';

			$s .= PHP_EOL ;

			// stack
			if( isset( $_REQUEST['home'])) $s .= 'clear';
			$s .= PHP_EOL ;

			// navs
			$url = getMosUrl().'?page=xml_fex';
			$url .= ( $search == '' ) ?  '&view='. $view : '&search='. urlencode( $search );

			if( ( $page - 1 ) >= $min_page ) $s .= $url.'&p='.($page - 1);
			$s .= PHP_EOL ;

			if( ( $page + 1 ) <= $max_page ) $s .= $url.'&p='.($page + 1);
			$s .= PHP_EOL ;

			// number of items
			$s .= count( $links ) . PHP_EOL;

			foreach( $links as $id => $item )
			{
				$image = $mos .'/www/modules/fex/folder.png';
				if( isset( $item['image'] )) $image = $item['image'];

				$s .= $item['title'] .PHP_EOL;
				$s .= $image.PHP_EOL;
				$s .= getMosUrl().'?page=xml_fex&view='. $id .PHP_EOL;
			}
		}
		// item page
		// ====================
		elseif( isset( $_REQUEST['info']))
		{
			$s = 'rss'.PHP_EOL ;
			$s .= getMosUrl().'?page=rss_fex_info&view='. $view .PHP_EOL;
		}
		else	// if( isset( $_REQUEST['play']))
		{
			$items = fexGetPlaylist( $s );

			if( count( $items ) == 0 ) return;
			if( count( $items ) > 1 )
			{
				$s = 'rss'.PHP_EOL ;
				$s .= getMosUrl().'?page=rss_player&mod=fex&view='. $view .PHP_EOL;
			}
			else
			{
				$s = 'play'.PHP_EOL ;
				$s .= $items[0]['link'] .PHP_EOL;
			}
		}
	}

	header( "Content-type: text/plain" );
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
function rss_fex_actions_content()
{
global $fex_bookmarks;

	if( ! isset( $_REQUEST['view'] )) return;

	$id = $_REQUEST['view'];

	include( 'modules/core/rss_view_popup.php' );
	$view = new rssSkinPopupView;

	$view->topTitle = $_REQUEST['title'];

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmWatch' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_fex&amp;view='. $id .'&amp;play'
	);

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmInfo' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_fex&amp;view='. $id .'&amp;info'
	);

	if( ! isset( $fex_bookmarks[ $id ] ))
	{
		$view->items[] = array(
			'title'	=> getMsg( 'fexAddMark' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;addmark='. $id
				 .'&amp;title='. urlencode( $_REQUEST['title'] )
				 .'&amp;image='. urlencode( $_REQUEST['image'] )
		);
	}
	else
	{
		$view->items[] = array(
			'title'	=> getMsg( 'fexDelMark' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;delmark='. $id
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_menu_content()
{
global $fex_bookmarks;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->items = array();
	$view->items[] = array(
		'title'	=> getMsg('coreCmSearch'),
		'action'=> 'search',
		'link'	=> getMosUrl().'?page=xml_fex&amp;search='
	);

	$view->items[] = array(
		'title'	=> getMsg('fexHome'),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_fex&amp;home'
	);

	if( count( $fex_bookmarks ) > 0 )
	 $view->items[] = array(
		'title'	=> getMsg('fexBookmarks'),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_fex&amp;bookmarks'
	 );

	$view->items[] = array(
		'title'	=> getMsg('fexLang'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_fex_lang'
	);
	$view->items[] = array(
		'title'	=> getMsg('coreSettings'),
		'action'=> 'rss',
		'link'	=> getMosUrl().'?page=rss_fex_sets'
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_lang_content()
{
global $fex_config;

	$url = 'http://'. $fex_config['site'] .'/ru/video';
	$s = file_get_contents( $url );


	// get languages
	if( preg_match( '/<a href=\'\/view\/(\d+)\'.*?<b>Видео на других языках<\/b>/' , $s, $ss ) === false ) return;

	$url = 'http://'. $fex_config['site'] .'/view/' . $ss[1];
	$s = file_get_contents( $url );
	$s = str_replace('/ru/video','/view/23775',$s);
	$s = str_replace('/uk/video','/view/80934',$s);
	$s = str_replace('/en/video','/view/80925',$s);
	$s = str_replace('/de/video','/view/45205',$s);
	$s = str_replace('/es/video','/view/187077',$s);
	$s = str_replace('/pl/video','/view/933750',$s);
	$s = str_replace('/ja/video','/view/1250406',$s);
	$links = fexGetList( $s );


	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 1;

	foreach( $links as $id => $item )
	{
		$title = $item['title'];
		if( preg_match( '/.*?\[(.*)\]/' , $title, $ss ) > 0 ) $title = $ss[1];

		$view->items[] = array(
			'title'	=> $title,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;home='. $id
		);
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_sets_content()
{
global $fex_config;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('fexSite'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_fex_site'
		),
		1 => array(
			'title'	=> getMsg('fexQuality'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_fex_qual'
		),
		2 => array(
			'title'	=> getMsg('fexStartPage'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_fex_start'
		),
		3 => array(
			'title'	=> getMsg('fexKeyboard'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_fex_keyboard'
		),
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_site_content()
{
global $fex_config;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> 'fex.net',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;site=fex.net'
		),
		1 => array(
			'title'	=> 'www.ex.ua',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;site=www.ex.ua'
		)
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_qual_content()
{
global $fex_config;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('fexLowQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;quality=lo'
		),
		1 => array(
			'title'	=> getMsg('fexHighQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;quality=hi'
		)
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_start_content()
{
global $fex_config;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('fexHomePage'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;start=home'
		),
		1 => array(
			'title'	=> getMsg('fexBookmarks'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;start=bookmarks'
		)
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_fex_keyboard_content()
{
global $fex_config;

	include( 'rss_fex_view_left.php' );
	$view = new rssFexLeftView;

	$view->position = 2;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('fexKbdRss'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;keyboard=rss'
		),
		1 => array(
			'title'	=> getMsg('fexKbdEmb'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_fex&amp;keyboard=emb'
		)
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
function rss_fex_info_content()
{
global $fex_config;

	if( ! isset( $_REQUEST['view'])) return;
	$view = $_REQUEST['view'];

header( "Content-type: text/plain" );

	$s = file_get_contents( 'http://'. $fex_config['site'] .'/view/'. $view );

	$infos = array();

	if( ! preg_match( '/<table width=100% cellpadding=0 cellspacing=0 border=0>(.*?)<\/table>/s' , $s, $ss ) ) rerurn;

	$t = $ss[1];
//echo $t;
	$title  = '';
	if( preg_match( '/<h1>(.*?)<\/h1>/' , $t, $ss ) > 0 )
	{
		$title = $ss[1];
	}

	$poster = '';
	$posterWidth  = 0;
	$posterHeight = 0;
	if( preg_match( '/<img src=\'(.*?)\' width=\'(\d+)\' height=\'(\d+)\' border=\'0\' align=\'left\'/' , $t, $ss ) > 0 )
	{
		$poster = $ss[1];
		$posterWidth  = $ss[2];
		$posterHeight = $ss[3];
	}

	$desc = '';
	if( preg_match( '/<p>(.*?)<span/s' , $t, $ss ) > 0 )
	{
		$desc = trim( $ss[1] );
		$desc = str_replace( '<p>', "\n\n", $desc );
		$desc = str_replace( '<br>', "\n", $desc );
		$desc = preg_replace( '/<.*?>/', '', $desc );
	}

//echo $poster.PHP_EOL;
//echo $title.PHP_EOL;
//echo $desc.PHP_EOL;

	$items = fexGetPlaylist( $s );

	$ph = 0;
	$pw = 0;

	if( count( $items ) == 1 )
	{
		include( 'modules/core/rss_view_info.php' );

		class rssFexInfoView extends rssSkinInfoView
		{
			//
			// ------------------------------------
			public $_link;

			function showOnUserInput()
			{
?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		playItemURL("<?= $this->_link ?>", 0);
		cancelIdle();
		ret = "true";
	}
	else
	if (userInput == "<?= getRssCommand('left') ?>" || userInput == "<?= getRssCommand('right') ?>")
	{
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
			}
		}

		$view = new rssFexInfoView;

		$view->_link = $items[0]['link'];

		$width = 1280;
	}
	else
	{
		include( 'modules/core/rss_view_info_list.php' );

		class rssFexInfoListView extends rssSkinInfoListView
		{
			const itemAreaX		= 820;
			const itemAreaY		= 128;
			const itemAreaWidth	= 480;
			const itemAreaHeight	= 620;

			const itemWidth		= 480;
			const itemHeight	= 80;

			const itemTextX		= 0;
			const itemTextY		= 0;
			const itemTextWidth	= 475;
			const itemTextHeight	= 40;
			const itemTextLines	= 1;
			//
			// ----------------------------
			public $itemImage = '';
			//
			// ----------------------------
			function showMoreItemDisplay()
			{
?>
      <text align="left" offsetXPC="1" offsetYPC="48" widthPC="50" heightPC="42"
       fontSize="10" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
        <script>
	  getItemInfo( idx, "info" );
	</script>
      </text>

      <text align="right" offsetXPC="50" offsetYPC="48" widthPC="46" heightPC="42"
       fontSize="10" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
        <script>
	  getItemInfo( idx, "len" );
	</script>
      </text>
<?php
			}
		}
		$view = new rssFexInfoListView;

		$view->items = $items;

		$width = 780;
	}

	if( $title <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 68,
			'width'  => 1280,
			'height' => 50,
			'fontSize' => 18,
			'lines' => 1,
			'text' => $title
		);
	}

	if( $poster <> '' )
	{
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
		$symbolWidth = 11;

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

	$view->topTitle = 'fex.ru';
	$view->bottomTitle = getRssCommandPrompt('enter') . getMsg( 'coreRssPromptWatch' );

	$view->showRss();
}

?>
