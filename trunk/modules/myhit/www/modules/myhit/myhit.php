<?php

ini_set("user_agent", "Mozilla/5.0 (Windows NT 5.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1");

// config
$myhit_config = array(
	'genre'  => 'all',
	'search' => '',
	'page'   => 1,
	'genres' => array(
		'all' => 'Все фильмы'
	),
);

if( is_file( '/usr/local/etc/dvdplayer/myhit.config.php' ) )
{
	include( '/usr/local/etc/dvdplayer/myhit.config.php' );
}
//
// ------------------------------------
function get_myhit_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

	$url = 'http://my-hit.ru'. $_REQUEST['url'];
	$s = file_get_contents( $url );
/*
	if( preg_match( '#flashvars="([^"]*)"#', $s, $ss ) === false ) return;
	parse_str( $ss[1], $a );
	$s = $a['file'] .'?start=0&id='. $a['id'] .'&client=FLASH%20WIN%2011,0,1,152&version=4.2.90&width=640';
*/
	if( preg_match( "/url:\\s*'(.*?)'/", $s, $ss ) === false ) return;

	header ( 'Location: '. $ss[1] );
}
//
// ------------------------------------
function xml_myhit_content()
{
global $myhit_config;

	header( "Content-type: text/plain" );

	if( isset( $_REQUEST['genre']))
	{
		$myhit_config['genre'] = $_REQUEST['genre'];
		$myhit_config['search'] = '';
	}
	elseif( isset( $_REQUEST['search']))
	{
		$myhit_config['genre'] = '';
		$myhit_config['search'] = $_REQUEST['search'];
	}
	else
	{
		$myhit_config['genre'] = 'all';
		$myhit_config['search'] = '';
	}

	if( isset( $_REQUEST['p']))
	{
		$myhit_config['page'] = $_REQUEST['p'];
	}

	$url = 'http://my-hit.ru/';
	$retUrl = getMosUrl().'?page=xml_myhit';

	if( $myhit_config['genre'] != '' )
	{
		$url .= 'films';
		if( $myhit_config['genre'] != 'all' )
		 $url .= '/genre/'. $myhit_config['genre'];
		$title = $myhit_config['genres'][ $myhit_config['genre'] ];
		$retUrl .= '&genre='. $myhit_config['genre'];

		$url .= '/'. $myhit_config['page'];
	}
	elseif( $myhit_config['search'] != '' )
	{
		$url .= 'index.php?module=search&func=view&result_orderby=score&result_order_asc=0&search_string='. urlencode( iconv('utf-8', 'windows-1251', $myhit_config['search'] ));
		$title = getMsg('coreCmSearch') .' - '. $myhit_config['search'];
		$retUrl .= '&search='. urlencode( $myhit_config['search'] );
	}

	// get content html
	$s = file_get_contents( $url );
	$s = iconv('windows-1251', 'UTF-8', $s); 

	// get genres
	if( $myhit_config['genre'] == 'all' )
	 if( preg_match_all( '#<td nowrap valign="top" align="left" width="25%"><a href="/films/genre/([^"]*)">([^<]*)</a></td>#s', $s, $ss ) > 0 )
	 {
		$genres = array(
			'all' => 'Все фильмы'
		);
		foreach( $ss[1] as $id => $gen )
		 $genres[ $gen ] = $ss[2][ $id ];

		$myhit_config['genres'] = $genres;
	 }

	// save config
	file_put_contents( '/usr/local/etc/dvdplayer/myhit.config.php', '<?php $myhit_config = '.var_export( $myhit_config, true ).'; ?>' );

	// get content
	$items = array();

	$rs = '#<tr><td width="\d*" rowspan="3" height="\d*" align="center" valign="top" class="even"><a href="/film/([^"]*)"><img src="([^"]*)" width="\d*" height="\d*" alt="[^"]*" title="[^"]*" border="0" align="center" class="thumb"></a>.*?<table cellSpacing="0" cellPadding="0" border="0" width="[^"]*"><tr><td width="[^"]*">&nbsp;"<a href="[^"]*">([^>]*)</a>"</td>#s';
	if( $myhit_config['search'] != '' )
	{
		$rs = '#<td class="[^"]*"><b><a href="/index.php\?module=video\&amp;func=film_view\&amp;id=(\d*)">[^>]*</a></b></td></tr><tr><td class="[^"]*" colspan="2" height="[^"]*" valign="top"><a href="[^"]*"><img width="[^"]*" hspace="[^"]*" src="([^"]*)" align="left" border="0" alt="([^\(]*)\(фильм\)" class="thumb"></a>#s';
	}

	if( preg_match_all( $rs, $s, $ss ) > 0 )
	 foreach( $ss[1] as $id => $l )
	  $items[] = array(
		'link'  => $l,
		'title' => html_entity_decode( trim( $ss[3][ $id ]), ENT_QUOTES, 'utf-8' ),
		'image' => 'http://my-hit.ru'. $ss[2][ $id ]
	  );

	// get navigation
	$navs = array( 1 );

	if( preg_match( '#<b>Страницы: \((\d*)/(\d*)\) </b>#s', $s, $ss ) > 0 )
	{
		$navs[] = $ss[1];
		$navs[] = $ss[2];
	}
	sort( $navs );
	$min_page = $navs[0];
	$max_page = $navs[ count( $navs ) - 1 ];

	$page = $myhit_config['page'];

	// generate list

	// title
	$s = 'MY-HIT.ru - '. $title;

	if( $min_page !== $max_page ) $s .= ' - ' .getMsg( 'coreRssPromptPage' ) . $page . getMsg( 'coreRssPromptFrom' ) . $max_page;

	$s .= "\n" ;

	// navs
	if( ( $page - 1 ) >= $min_page ) { $s .= $retUrl.'&p='.($page - 1)."\n"; }
	else $s .= "\n" ;

	if( ( $page + 1 ) <= $max_page ) { $s .= $retUrl.'&p='.($page + 1)."\n"; }
	else $s .= "\n" ;
	
	// number of items
	$s .= count( $items ) . PHP_EOL;

	foreach( $items as $item )
	{
		$s .= $item['title'] .PHP_EOL;
		$s .= $item['image'] .PHP_EOL;
		$s .= $item['link']  .PHP_EOL;
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
function rss_myhit_menu_content()
{
global $myhit_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('coreCmSearch'),
			'action'=> 'search',
			'link'	=> getMosUrl().'?page=xml_myhit&amp;search='
		)
	);

	foreach( $myhit_config['genres'] as $gen => $name )
	{
		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_myhit&amp;genre='.$gen
		);
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
function rss_myhit_info_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

header( "Content-type: text/plain" );

	$url = 'http://my-hit.ru/film/'. $_REQUEST['url'];

	$s=file_get_contents( $url );
	$s = iconv('windows-1251', 'utf-8', $s); 

	$title = '';
	$year = '';
	if( preg_match( '#<h4>"([^"]*)" \(<a href="[^"]*">([^<]*)</a>\)</h4>#', $s, $ss ) > 0 )
	{
		$title = $ss[1];
		$year = $ss[2];
	}
	if( preg_match( '#<h5>([^<]*)</h5>#', $s, $ss ) > 0 )
	{
		$title .= ' / '. $ss[1];
	}
	if( $year != '' ) $title .= ' ('. $year .')';


	$poster = '';
	$link = '';
	if( preg_match( '#<div class="previewinfilm"><a href="([^"]*)" title="[^"]*"><img src="([^"]*)"#', $s, $ss ) > 0 )
	{
		$link = getMosUrl(). '?page=get_myhit&amp;url='. $ss[1];
		$poster = 'http://my-hit.ru'. $ss[2];
	}

	$desc = '';
	if( preg_match_all( '#<li><b>([^<]*)</b>(.*?)</li>#', $s, $ss ) > 0 )
	 foreach( $ss[1] as $i => $t )
	  if( in_array( $t, array(
		'Продолжительность:',
		'Жанр:',
		'Страна:',
		'Режиссер:',
		'В ролях:' )))
	   $desc .= $t . strip_tags( $ss[2][ $i ] ) .PHP_EOL;

	$desc .= PHP_EOL;
	if( preg_match_all( '#<p align="justify">(.*?)</p>#s', $s, $ss ) > 0 )
	 foreach( $ss[1] as $t )
	  $desc .= strip_tags( trim( $t ) ) .PHP_EOL;

	$desc = html_entity_decode( $desc, ENT_QUOTES, 'utf-8' );
/*
echo $title.PHP_EOL;
echo $poster.PHP_EOL;
echo $desc.PHP_EOL;
echo $link.PHP_EOL;
*/
	include( 'modules/core/rss_view_info.php' );

	$view = new rssSkinInfoView;

	$view->_link = $link;
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
		$posX = 50;
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

	$view->topTitle = 'MY-HIT.ru';
	$view->bottomTitle = getRssCommandPrompt('enter') . getMsg( 'coreRssPromptWatch' );

	$view->showRss();
}
//
// ------------------------------------
function rss_myhit_content()
{
	class rssSkinmyhitView extends rssSkinHTile
	{
		const itemWidth		= 400; // 300
		const itemHeight	= 120;

		const itemImageX	= 10;
		const itemImageY	= 10;
		const itemImageWidth	= 70;
		const itemImageHeight	= 100;

		const itemTextX		= 85;
		const itemTextY		= 10;
		const itemTextWidth	= 310; // 210
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
		url = "<?= getMosUrl().'?page=get_myhit' ?>"
		 + "&amp;url=/film/" + getStringArrayAt(urlArray, i) + "/online";
		playItemURL( url, 0 );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = "<?= getMosUrl().'?page=rss_myhit_info' ?>"
		 + "&amp;url="   + getStringArrayAt(urlArray, i);
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_myhit_menu' ?>";
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
	moUrl = "<?= getMosUrl().'?page=xml_myhit' ?>";
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
	$view = new rssSkinmyhitView;

	$view->bottomTitle = 
		getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' )
	.' '.	getRssCommandPrompt('play')  . getMsg( 'coreRssPromptPlay' )
	.' '.	getRssCommandPrompt('enter') . getMsg( 'coreRssPromptInfo' )
//	.' '.	getRssCommandPrompt('enter') . getMsg( 'coreRssPromptActs' )
	;

	$view->showRss();
}

?>
