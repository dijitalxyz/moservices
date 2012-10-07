<?php
$woowza_config = array(
	'cat'     => 'mr',
	'genre'   => '',
	'page'    => 1,
	'search'  => '',
	'quality' => 'lo',
	'cats'    => array(),
	'genres'  => array()
);

if( is_file( '/usr/local/etc/dvdplayer/woowza.config.php' ) )
{
	include( '/usr/local/etc/dvdplayer/woowza.config.php' );
}
//
// ====================================
function getWoowzaUrl( $url )
{
global $nav_lang;

	$lang = 'en_US';
	if( $nav_lang == 'ru' ) $lang = 'ru_RU';

	$postdata = http_build_query(
		array(
			'session_language' => $lang,
		)
	);
	$opts = array(
		'http' => array(
			'method'  => 'POST',
			'header'  =>
			 "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0\r\n"
			."Content-type: application/x-www-form-urlencoded\r\n"
			."Accept-Encoding: gzip, deflate\r\n",
			'content' => $postdata
		)
	);

	$context = stream_context_create( $opts );

	$f = fopen( $url, 'r', false, $context );
	$metadata = stream_get_meta_data( $f );

	$coding = '';
	$s = implode( "\n", $metadata['wrapper_data'] );
	if( preg_match( '/Content-Encoding\:\s*([^\s]*)/' , $s, $ss ) > 0 )
	 $coding = $ss[1];

	$s = '';
	while ( !feof( $f )) $s .= fread( $f, 8192 );
	fclose( $f );

	if( $coding == 'gzip' )
	{
		// $s = gzdecode( $s );
		file_put_contents( '/tmp/woowza.gz', $s );
		exec( 'gunzip /tmp/woowza.gz' );
		$s = file_get_contents( '/tmp/woowza' );
		unlink( '/tmp/woowza' );
	}
	elseif ( $coding == 'deflate' ) $s = gzinflate( $s );
	return $s;
}
//
// ------------------------------------
function getWoowza( $s )
{
global $woowza_config;

	$q = ( $woowza_config['quality'] == 'hi' ) ? 'downloadHQLink' : 'downloadLQLink' ;

	$link = '';
	if( preg_match( '/var\s+'. $q .'\s*=\s*"(.*)";/' , $s, $ss ) > 0 )
	{
		$link = $ss[1];
	}
	if( substr( $link, 0, 1 ) == '/' ) $link = 'http://srv03.motubestore.com'. $link;

	return $link;
}
//
// ------------------------------------
function get_woowza_content()
{
global $woowza_config;

	if( ! isset( $_REQUEST['watch'])) return;
	$url = $_REQUEST['watch'];

	$s = getWoowzaUrl( 'http://woowza.com/watch/'. $url );

	$link = getWoowza( $s );
	if( $link == '' ) return;

	header( "Content-type: text/plain" );
	echo $link;
}
//
// ------------------------------------
function xml_woowza_content()
{
global $mos;
global $woowza_config;

header( "Content-type: text/plain" );

	$url = 'http://woowza.com/';

	if( isset( $_REQUEST['search']))
	{
		$woowza_config['search'] = $_REQUEST['search'];
		$url .= 'search_result.php?search_id='.urlencode( $woowza_config['search'] );
	}
	else
	{
		$woowza_config['search'] ='';

		if( isset( $_REQUEST['cat']))
		{
			$woowza_config['cat'] = $_REQUEST['cat'];
			$woowza_config['genre'] = '';
			$url .= 'video.php?category='. $woowza_config['cat'] .'&viewtype=basic';
		}
		else if( isset( $_REQUEST['genre']))
		{
			$woowza_config['genre'] = $_REQUEST['genre'];
			$woowza_config['cat'] = '';
			$url .= 'channel_detail.php?chid='. $woowza_config['genre'];
		}
		else
		{
			$woowza_config['cat'] = '';
			$woowza_config['genre'] = '';
			$url .= 'video.php?next=watch';
		}
	}

	if( isset( $_REQUEST['quality'])) $woowza_config['quality'] = $_REQUEST['quality'];

	$woowza_config['page'] = 1;
	if( isset( $_REQUEST['p'])) $woowza_config['page'] = $_REQUEST['p'];
	 $url .= '&page='. $woowza_config['page'];

	// get html page
	$s = getWoowzaUrl( $url );

	// get categoties
	$woowza_config['cats'] = array();
	if( preg_match( '/<div id="navsubbar">(.*?)<\/div>/s' , $s, $ss ) > 0 )
	 if( preg_match_all( '/<[aA] href=.*?category=(.*?)\&.*?>(.*?)<\/[aA]>/' , $ss[1], $ss ) > 0 )
	  foreach( $ss[1] as $i => $a )
	   $woowza_config['cats'][ $a ] = $ss[2][ $i ];

	// get genres
	$woowza_config['genres'] = array();
	if( preg_match_all( '/<[aA] href=.*?channel_detail\.php\?chid=(\d*?)\".*?>(.*?)<\/[aA]><br\/>/' , $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $a )
	  $woowza_config['genres'][ $a ] = $ss[2][ $i ];

	// save config
	file_put_contents( '/usr/local/etc/dvdplayer/woowza.config.php', '<?php $woowza_config = '.var_export( $woowza_config, true ).'; ?>' );

	// get content
	$links = array();

	if( preg_match_all( '#<td class="vid_blok_poster">\s*<div>\s*<a href="http://woowza.com/watch/([^"]*)">\s*<img src="([^"]*)"[^/]*/>\s*</a>.*?<table class="vid_blok_text">.*?<a href="http://woowza.com/watch/[^"]*">\s*([^<]*?)</a>#s' , $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $a )
	 {
		$image = $ss[2][ $i ];
		if( substr( $image, 0, 1 ) == '/' ) $image = 'http://srv03.motubestore.com'. $image;
		$links[] = array(
			'link'  => $a,
			'image' => $image,
			'title' => trim( $ss[3][ $i ] )
		  );
	 }

	// get navigation
	$navs = array( $woowza_config['page'] );
	if( preg_match( '/<div class="pagingnav">(.*?)<\/div>/s' , $s, $ss ) > 0 )
	 if( preg_match_all( '/<a href=".*?\&page=(.*?)">/' , $ss[1], $ss ) > 0 )
	  foreach( $ss[1] as $i => $a )
	   if( is_numeric( $a ) ) $navs[] = $a;

	sort( $navs );
	$min_page = $navs[0];
	$max_page = $navs[ count( $navs ) - 1 ];

	// generate list
	$s = '';

	// title
	$s .= 'woowza';

	$cat    = $woowza_config['cat'];
	$genre  = $woowza_config['genre'];
	$search = $woowza_config['search'];
	$page   = $woowza_config['page'];

	if( $woowza_config['search'] <> '' )
	{
		$s .= getMsg('woowzaSearchRezult') . $woowza_config['search'];
	}
	else
	{
		if( $cat <> '' )
		{
			$s .= ' - '. $woowza_config['cats'][ $cat ];
		}
		else if( $genre <> '' )
		{
			$s .= ' - '. $woowza_config['genres'][ $genre ];
		}
		else $s .= getMsg('woowzaAll');
	}

	if( $min_page !== $max_page ) $s .= ' ('. getMsg('coreRssPromptPage') . $page . getMsg('coreRssPromptFrom') . $max_page .')';

	$s .= PHP_EOL ;

	// navs
	$url = getMosUrl().'?page=xml_woowza';
	if( $search == '' )
	{
		if( $cat   <> '' ) $url .= '&cat='. $cat;
		if( $genre <> '' ) $url .= '&genre='. $genre;
	}
	else $url .= '&search='. urlencode( $search );

	if( ( $page - 1 ) >= $min_page ) { $s .= $url.'&p='.($page - 1).PHP_EOL; }
	else $s .= "\n" ;

	if( ( $page + 1 ) <= $max_page ) { $s .= $url.'&p='.($page + 1).PHP_EOL; }
	else $s .= "\n" ;
	
	// number of items
	$s .= count( $links ) . PHP_EOL;

	foreach( $links as $item )
	{
		$s .= $item['title'] .PHP_EOL;
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
function rss_woowza_menu_content()
{
global $woowza_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
/*
		0 => array(
			'title'	=> getMsg('coreCmSearch'),
			'action'=> 'search',
			'link'	=> getMosUrl().'?page=xml_woowza&amp;search='
		),
*/
		1 => array(
			'title'	=> getMsg('woowzaCats'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_woowza_cats'
		),
		2 => array(
			'title'	=> getMsg('woowzaGenres'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_woowza_genres'
		),
		3 => array(
			'title'	=> getMsg('coreSettings'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_woowza_sets'
		)
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_woowza_cats_content()
{
global $woowza_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	foreach( $woowza_config['cats'] as $cat => $name )
	{
		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_woowza&amp;cat='.$cat
		);
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_woowza_genres_content()
{
global $woowza_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('coreCmAll'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_woowza'
		)
	);

	foreach( $woowza_config['genres'] as $genre => $name )
	{
		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_woowza&amp;genre='. $genre
		);
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_woowza_sets_content()
{
global $woowza_config;


	if( $woowza_config['search'] <> '' )
	{
		$u = '&amp;search='. urlencode( $woowza_config[ $search ] );
	}
	else if( $woowza_config['cat'] <> '' )
	{
		$u = '&amp;cat='. $woowza_config['cat'];
	}
	else if( $woowza_config['genre'] <> '' )
	{
		$u = '&amp;genre='. $woowza_config['genre'];
	}
	else $u = '';

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('woowzaLowQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_woowza&amp;quality=lo'. $u
		),
		1 => array(

			'title'	=> getMsg('woowzaHighQual'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_woowza&amp;quality=hi'. $u
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
function rss_woowza_info_content()
{
global $woowza_config;

	if( ! isset( $_REQUEST['watch'])) return;
	$url = $_REQUEST['watch'];

header( "Content-type: text/plain" );

	$s = getWoowzaUrl( 'http://woowza.com/watch/'. $url );

	$title  = '';
	if( preg_match( '/<meta\s+property="og\:title"\s+content="(.*?)"\s*\/>/' , $s, $ss ) > 0 )
	{
		$title = $ss[1];
		$title = str_replace( ' & ', ' &amp; ', $title );
	}

	$poster = '';
	if( preg_match( '/<meta\s+property="og\:image"\s+content="(.*?)"\s*\/>/' , $s, $ss ) > 0 )
	{
		$poster = $ss[1];
	}

	$desc = '';
	if( preg_match( '/<table\s+width="100\%" style="border\: dashed 1px \#DEDEDE; padding\:15px;">(.*?)<\/table>/s' , $s, $ss ) > 0 )
	 if( preg_match_all( '/<td.*?>\s*(.*?)\s*<\/td>.*?<td.*?>\s*(.*?)\s*<\/td>/s' , $ss[1], $ss ) > 0 )
	{
		$c = array();
		foreach( $ss[1] as $i => $a )
		{
			$t = preg_replace( '/<.*?>/', '', $ss[2][ $i ] );
			$c[] = $a .' '. $t;
		}
		$c[ count( $c ) - 1 ] = $c[ count( $c ) - 2 ];
		$c[ count( $c ) - 2 ] = '';
		unset( $c[ 0 ] );
		$desc = implode( "\n", $c );
	}

	if( preg_match( '/\/images\/kinoposk.png".*?<td\s+style="margin\:3px\;">(.*?)<\/td>/s' , $s, $ss ) > 0 )
	{
		$desc = 'Kinopoisk: '. trim( $ss[1] ) ."\n\n". $desc;
	}
	if( preg_match( '/\/images\/imdb.png".*?<td\s+style="margin\:3px\;">(.*?)<\/td>/s' , $s, $ss ) > 0 )
	{
		$desc = 'IMDB: '. trim( $ss[1] ) ."\n". $desc;
	}
/*
echo $poster.PHP_EOL;
echo $title.PHP_EOL;
echo $desc.PHP_EOL;
return;
*/
	include( 'modules/core/rss_view_info.php' );

	$view = new rssSkinInfoView;

	$view->_link = getWoowza( $s );
	$view->_action = 'play';

	$width = 1280;

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

	$view->topTitle = getMsg('woowzaTitle');
	$view->bottomTitle = getRssCommandPrompt('enter') . getMsg( 'coreRssPromptWatch' );

	$view->showRss();
}
//
// ------------------------------------
function rss_woowza_content()
{
	class rssSkinWoowzaView extends rssSkinHTile
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
	ret = "false";
	i = getFocusItemIndex();
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
			moUrl = pPage;
			setRefreshTime(1);
			savedItem = i - ( <?= $this->_rowCount ?> - itemCount );
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('right') ?>")
	{
		if( nPage != "" )
		if( i &gt; ( itemCount - <?= $this->_rowCount ?> - 1 ) )
		{
			moUrl = nPage;
			setRefreshTime(1);
			savedItem = i - ( itemCount - <?= $this->_rowCount ?> );
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=get_woowza' ?>"
		 + "&amp;watch=" + url;
		url = getUrl(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			playItemURL( url, 0 );
		}
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=rss_woowza_info' ?>"
		 + "&amp;watch=" + url;
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_woowza_menu' ?>";
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			setRefreshTime(1);
		}
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
	moUrl = "<?= getMosUrl().'?page=xml_woowza' ?>";
	savedItem = 0;
	setRefreshTime(1);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();
	itemCount = 0;

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		pageTitle = getStringArrayAt(dlok, c); c += 1;

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
	}
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

	cancelIdle();
	redrawDisplay();
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
	$view = new rssSkinwoowzaView;

	$view->bottomTitle = 
		getRssCommandPrompt('menu')    . getMsg('coreRssPromptMenu')
		. getRssCommandPrompt('play')  . getMsg('coreRssPromptWatch')
		. getRssCommandPrompt('enter') . getMsg('coreRssPromptInfo')
	;

	$view->showRss();
}

?>
