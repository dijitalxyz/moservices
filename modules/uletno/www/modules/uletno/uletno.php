<?php
$cats = array();
if( is_file( '/tmp/uletno.categories.php' ) )
{
	include( '/tmp/uletno.categories.php' );
}
//
// ------------------------------------
function rss_uletno_content()
{
	class rssSkinUletnoView extends rssSkinHTile
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
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=rss_player&amp;mod=uletno' ?>"
		 + "&amp;url=" + url;
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = "<?= getMosUrl().'?page=rss_uletno_info' ?>"
		 + "&amp;url="   + getStringArrayAt(urlArray, i)
		 + "&amp;image=" + getStringArrayAt(imgArray, i);
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_uletno_menu' ?>";
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
/*
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = "<?= getMosUrl().'?page=rss_uletno_actions' ?>"
		 + "&amp;url=" + getStringArrayAt(urlArray, i)
		 + "&amp;img=" + getStringArrayAt(imgArray, i)
		 + "&amp;title=" + urlEncode( getStringArrayAt(nameArray, i) );
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
*/
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
	moUrl = "<?= getMosUrl().'?page=xml_uletno' ?>";
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
	$view = new rssSkinUletnoView;

	$view->bottomTitle = 
		getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' )
	.' '.	getRssCommandPrompt('play')  . getMsg( 'coreRssPromptPlay' )
	.' '.	getRssCommandPrompt('enter') . getMsg( 'coreRssPromptInfo' )
//	.' '.	getRssCommandPrompt('enter') . getMsg( 'coreRssPromptActs' )
	;

	$view->showRss();
}
//
// ------------------------------------
function uletnoGetPlaylist( $s = '' )
{
	$items = array();

	if( ! isset( $_REQUEST['url'])) return $items;

	$url = $_REQUEST['url'];

	if( $s == '' )
	{
		$s=file_get_contents( $url );
		$s = iconv('windows-1251', 'utf-8', $s); 
	}

	if( preg_match( '/var flashvars =([^;]*);/' , $s, $ss ) > 0 )
	{
		$ss=json_decode($ss[1], true);
		$title  = $ss['comment'];
		$poster = ( $ss['poster'] != '' ) ? 'http://uletno.info'. $ss['poster'] : '';
		$link   = $ss['file'];
		if( substr( $link, 0, 1 ) == '/' ) $link = 'http://uletno.info'. $link;

		if( isset( $ss['pl'] ))
		{
			$a = $ss['pl'];
			$a = str_replace( "'", '"', $a );
			$ps = json_decode($a, true);
			foreach( $ps['playlist'] as $ss )
			 $items[] = array(
				'link'  => $ss['file'],
				'action'=> 'play',
				'title' => $ss['comment'],
				'image' => ( $ss['poster'] != '' ) ? 'http://uletno.info'. $ss['poster'] : '',
			 );

			if( preg_match( '/<a href="(http\:\/\/uletno\.info\/download\/.*?)" class="downloadbttn">/', $s, $ss ) > 0 )
			{
				$s=file_get_contents( $ss[1] );
				$s = iconv('windows-1251', 'utf-8', $s); 

				if( preg_match( '/Ваша ссылка на скачивание фильма:<br \/><br \/>(.*?)<br \/><br \/>/s', $s, $ss ) > 0 )
				 if( preg_match_all( '/<a href="(.*?)" target="_blank">/', $ss[1], $ss ) > 0 )
				  foreach( $ss[1] as $id => $l )
				   $items[ $id ]['link']  = $l;
			}
		}
		else
		{
			$items[] = array(
				'link'  => $link,
				'action'=> 'play',
				'title' => $title,
				'image' => $poster
			);
		}
	}
	return $items;
}
//
// ------------------------------------
function get_uletno_content()
{
	header( "Content-type: text/plain" );

	$items = uletnoGetPlaylist();
	if( count( $items ) == 0 ) return;
	echo $items[0]['link'];

/* vkontakte
	if( ! isset( $_REQUEST['url'])) return;
	$url = $_REQUEST['url'];

	header( "Content-type: text/plain" );

	$s=file_get_contents( $url );

	if( preg_match( '/<iframe src="(.*?)"/', $s, $ss ) > 0 )
	{
		$s=file_get_contents( $ss[1] );
		if( preg_match(
'/var video_host = \'(.*?)\';
var video_uid = \'(.*?)\';
var video_vtag = \'(.*?)\';
var video_no_flv = (.*?);
var video_max_hd = \'(.*?)\';/s', $s, $ss ) > 0 )
		{
			$res = '240';
			if     ($ss[5] >= 3) { $res = '720'; }
			elseif ($ss[5] >= 2) { $res = '480'; }
			elseif ($ss[5] >= 1) { $res = '360'; }

			$url = $ss[1] .'u'. $ss[2] .'/video/'. $ss[3] .'.'. $res .'.mp4';

			echo $url;

		}
	}
*/
}
//
// ------------------------------------
function xml_uletno_content()
{
global $mos;
global $cats;

	header( "Content-type: text/plain" );

	$url = 'http://uletno.info/';

	$category = '';
	if( isset( $_REQUEST['cat']))
	{
		$category = $_REQUEST['cat'];
		$url .= $category. '/';
	}

	$page = 1;
	if( isset( $_REQUEST['p']))
	{
		$page = $_REQUEST['p'];
		 $url .= 'page/'.$page;
	}

	$search = '';
	if( isset( $_REQUEST['search']))
	{
		$url = 'http://uletno.info/index.php?do=search';
		$search = $_REQUEST['search'];
		$s = iconv('utf-8', 'windows-1251', $search);
		$p = "do=search&subaction=search&search_start=$page&full_search=0&result_from=1&story=$s";
		exec( "$mos/bin/curl --data '$p' $url", $ss );
		$s = implode( "\n", $ss );
		$s = preg_replace( '/.*(<!DOCTYPE )/', '$1', $s );
	}
	else
	{
		$s = file_get_contents( $url );
	}
	$s = iconv('windows-1251', 'UTF-8', $s); 

	// get categoties
	$cats = array();

	if( preg_match( '/<ul class="orangemenu">(.+?)<\/td>/s', $s, $ss ) > 0 )
	 if( preg_match_all( '/<li.*?><a href="\/(.+?)\/">(.+?)<\/a><\/li>/s', $ss[1], $ss ) > 0 )
	  foreach( $ss[1] as $id => $cat )
	   $cats[ $cat ] = $ss[2][ $id ];

	if( preg_match_all( '/<ul class="greenmenu">(.+?)<\/td>/s', $s, $ss ) > 0 )
	 if( preg_match_all( '/<li.*?><a href="\/(.+?)\/">(.+?)<\/a><\/li>/s', $ss[1][1], $ss ) > 0 )
	  foreach( $ss[1] as $id => $cat )
	   $cats[ $cat ] = $ss[2][ $id ];

	file_put_contents( '/tmp/uletno.categories.php', '<?php $cats = '.var_export( $cats, true ).'; ?>' );

if( isset( $_REQUEST['debug'])) print_r( $cats );

	// get content
	$links = array();
	$cnt= 0;

	if( preg_match_all( '/class="roltitle">(<.*?>)?(.*?) смотреть.*?онлайн.*?<td\s+width="140".*?><a\s+href="(.*?)"\s*><img\s+src="(.*?)"\s+width="120"/s', $s, $ss ) > 0 )
	 foreach( $ss[3] as $id => $link )
	  $links[] = array(
		'link'  => $link,
		'title' => trim( $ss[2][ $id ] ),
		'image' => $ss[4][ $id ]
	  );

if( isset( $_REQUEST['debug'])) print_r( $links );

	// get navigation
	$navs = array( $page );

	if( preg_match( '/<div class="navigation".*?>(.+?)<\/div>/s', $s, $ss ) > 0 )
	 if( preg_match_all( '/<a .*?>(\d+?)<\/a>/s', $ss[1], $ss ) > 0 )
	  foreach( $ss[1] as $nav )
	   $navs[] = $nav;

if( isset( $_REQUEST['debug'])) print_r( $navs );

	sort( $navs );
	$min_page = $navs[0];
	$max_page = $navs[ count( $navs ) - 1 ];

	// generate list
	$s = '';

	// title
	$s .= 'Uletno.info';
	if( $search <> '' )
	{
		$s .= ' - '. getMsg('coreCmSearch') .' - '. $search;
	}
	else
	{
		if( $category <> '' ) $s .= ' - '.$cats[ $category ];
	}
	if( $min_page !== $max_page ) $s .= ' - '.getMsg( 'coreRssPromptPage' ).$page.getMsg( 'coreRssPromptFrom' ).$max_page;

	$s .= "\n" ;

	// navs
	$url = getMosUrl().'?page=xml_uletno';
	if( $search != '' )
	{
		$url .= '&search='. urlencode( $search );
	}
	else if( $category != '' ) $url .= '&cat='. $category;

	if( ( $page - 1 ) >= $min_page ) { $s .= $url.'&p='.($page - 1)."\n"; }
	else $s .= "\n" ;

	if( ( $page + 1 ) <= $max_page ) { $s .= $url.'&p='.($page + 1)."\n"; }
	else $s .= "\n" ;
	
	// number of items
	$s .= count( $links ) . PHP_EOL;

	foreach( $links as $item )
	{
		$s .= $item['title'] ."\n";
		$s .= 'http://www.uletno.info'.$item['image']."\n";
		$s .= $item['link']."\n";
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
function rss_uletno_menu_content()
{
global $cats;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('coreCmSearch'),
			'action'=> 'search',
			'link'	=> getMosUrl().'?page=xml_uletno&amp;search='
		),
		1 => array(
			'title'	=> getMsg('coreCmAll'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_uletno'
		)
	);

	foreach( $cats as $cat => $name )
	{
		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_uletno&amp;cat='.$cat
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
function rss_uletno_info_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

header( "Content-type: text/plain" );

	$url = $_REQUEST['url'];

	$s=file_get_contents( $url );

	$s = str_replace('windows-1251', 'utf-8', $s);
	$s = iconv('windows-1251', 'utf-8', $s); 


	$doc = new DOMDocument();

	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	$desc = '';

	$sts = $doc->getElementsByTagName('table');

	foreach( $sts as $st )
	if( $st->hasAttribute('class'))
	if( $st->getAttribute('class') == 'storyfinfo' )
	{
		$trs = $st->getElementsByTagName('tr');
		foreach( $trs as $tr )
		{
			$tds = $tr->getElementsByTagName('td');
			if( $tds->length != 2 ) continue;
			$key = trim($tds->item(0)->textContent);
			$val = trim($tds->item(1)->textContent);
			if( in_array( $key, array(
				'Оригинальное название:',
				'Жанр:',
				'Страна:',
				'Актёры:',
				'Режиссер:',
				'Слоган:',
				'Премьера в России:',
				'Продолжительность:',
				'Рейтинг IMDB:',
				'Описание:',
			) ) )
			{
				if( $key == 'Описание:' )
				{
					$desc .= PHP_EOL. $val .PHP_EOL;
				}
				else $desc .= $key .' '. $val .PHP_EOL;
			}
		}
		break;
	}

	$title = '';
	if( preg_match( '/<title>(.*?) - смотреть онлайн.*?<\/title>/', $s, $ss ) > 0 )
	 $title = $ss[1];

	$poster = '';
	if( isset( $_REQUEST['image']))
	 $poster = $_REQUEST['image'];

	$items = uletnoGetPlaylist( $s );

	if( count( $items ) > 1 )
	{
		include( 'modules/core/rss_view_info_list.php' );
		$view = new rssSkinInfoListView;

		$view->items = $items;

		$width = 780;
	}
	else
	{
		include( 'modules/core/rss_view_info.php' );

		$view = new rssSkinInfoView;

		$view->_link = $items[0]['link'];
		$view->_action = $items[0]['action'];

		$width = 1280;
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

	$view->topTitle = 'Uletno.info';
	if( count( $items ) > 0 )
	 $view->bottomTitle = getRssCommandPrompt('enter') . getMsg( 'coreRssPromptWatch' );

	$view->showRss();
}

?>
