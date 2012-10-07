<?php
$ivi_config = array(
	'cat'     => 'all',
	'sub'     => 'all',
	'sort'    => 'by_new',
	'page'    => 1,
	'search'  => '',
	'quality' => 'lo',
	'cats'    => array(),
	'sorts'   => array(
		'by_views' => 'Самое популярное',
		'by_day'   => 'Популярное за день',
		'by_new'   => 'По новизне',
		'by_alph'  => 'По алфавиту'
	)
);

if( is_file( '/usr/local/etc/dvdplayer/ivi.config.php' ) )
{
	include( '/usr/local/etc/dvdplayer/ivi.config.php' );
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

	$title  = '';
	$poster = '';

	if( preg_match( '/<link rel="image_src" href="([^"]+)" \/>/' , $s, $ss ) > 0 )
	{
		$poster = $ss[1];
	}

	if( preg_match( '/<meta name="mrc__share_title" content="([^"]+)" \/>/' , $s, $ss ) > 0 )
	{
		$title = $ss[1];
	}

	$s = str_replace('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', '', $s );
	$s = str_replace('<head>', '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', $s );

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	$uls = $doc->getElementsByTagName('ol');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('itemprop'))
	if( $ul->getAttribute('itemprop') == 'seasons' )
	{
		$lis = $ul->getElementsByTagName('li');
		foreach( $lis as $li )
		{
			$as = $li->getElementsByTagName('a');
			$link = $as->item(0)->getAttribute('href');
			$as = $li->getElementsByTagName('img');
			$poster = $as->item(0)->getAttribute('src');
			//$as = $li->getElementsByTagName('strong');
			$title = $as->item(0)->getAttribute('alt');

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
		$items[] = array(
			'link'  => getMosUrl() .'?page=get_ivi&amp;url='. $url,
			'action'=> 'get',
			'title' => trim( $title ),
			'image' => $poster
		);
	}
	return $items;
}
//
// ------------------------------------
function get_ivi_content()
{
global $mos;
global $ivi_config;

	if( ! isset( $_REQUEST['url'])) return;

	$url = $_REQUEST['url'];

	$s = iviGetContent( 'http://www.ivi.ru'. $url );

	if( preg_match( '/<link rel="video_src".*videoId=(\d*)\&/' , $s, $ss ) > 0 )
	
	{
		$videoId = $ss[1];
	}
	else{
		
		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML($s);

		$uls = $doc->getElementsByTagName('link');
		foreach( $uls as $ul )
		if( $ul->hasAttribute('rel'))
		if( $ul->getAttribute('rel') == 'image_src' )
		{
			$href = $ul->getAttribute('href');
			$hrefrep = str_replace('http://img.ivi.ru/static/frames/', '', $href );
			$ss = explode( '/', $hrefrep );		
			$videoId = $ss[0];
			break;
		}
	}
	if( ! isset( $videoId )) return;

	$p = '{"method":"da.content.get","params":["'.$videoId .'",{"site":1}]}';
	$s = exec( "$mos/bin/curl --data-ascii '$p' --referer 'http://www.ivi.ru/images/da/skin1.64.swf?' --header 'FlashAuth: Basic Zmxhc2hfcGxheWVyOmZsYXNoX3BsYXllcg==' http://api.digitalaccess.ru/api/json/", $ss );

	$ss=json_decode($s, true);

	if( ! is_array( $ss['result']['files'] )) return;

	$link = '';
	foreach( $ss['result']['files'] as $item )
	{
		$link = $item['url'];
		if( $item['content_format'] == 'FLV-'. $ivi_config['quality'] ) break;
	}
	if( $link == '' ) return;
	
	header( "Content-type: text/plain" );
	echo $link;
}
//
// ------------------------------------
function xml_ivi_content()
{
global $mos;
global $ivi_config;

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
		$url = 'http://www.ivi.ru/search/simple/?q='.$ivi_config['search'];
	}
	else $ivi_config['search'] ='';

	$ivi_config['page'] = 1;
	if( isset( $_REQUEST['p'])) $ivi_config['page'] = $_REQUEST['p'];
	 $url .= '?&spage='. $ivi_config['page'];

if( isset( $_REQUEST['debug'])) echo "url=$url\n";

	// get html page
	$s = iviGetContent( $url );
	$doc = new DOMDocument();

	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	// get categoties
	$uls = $doc->getElementsByTagName('ul');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('id'))
	if( $ul->getAttribute('id') == 'main-menu' )
	{
		$ivi_config['cats'] = array();

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
					$ivi_config['cats'][ $cat ] = array(
						'name' => $name
					);
				}
				else
				{
					$ivi_config['cats'][ $cat ]['subs'][ $sub ] = array(
						'name' => $name
					);
				}
			}
		}
		break;
	}
	file_put_contents( '/usr/local/etc/dvdplayer/ivi.config.php', '<?php $ivi_config = '.var_export( $ivi_config, true ).'; ?>' );

	// get content
	$links = array();

	$dvs = $doc->getElementsByTagName('div');

	foreach( $dvs as $dv )
	if( $dv->hasAttribute('id'))
	if( $dv->getAttribute('id') == 'tab_all' )
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
					continue;
				}
				break;
			}
			$h = $li->getElementsByTagName('h3');
			if( $h->length > 0 )
			{
				$title = trim( $h->item( 0 )->textContent );			
			}
						
			if( $link != '' )
			 $links[] = array(
				'image' => $image,
				'link'  => $link,
				'title' => $title
			 );
		}
		break;
	}

	// get navigation
	$navs = array( $ivi_config['page'] );

	$uls = $doc->getElementsByTagName('ul');

	foreach( $uls as $ul )
	if( $ul->hasAttribute('class'))
	if( $ul->getAttribute('class') == 'pager' )
	{
		$as = $ul->getElementsByTagName('a');
		foreach( $as as $a )
		{
			$n = trim( $a->textContent );
			if( is_numeric( $n ) ) $navs[] = $n;
		}
		break;
	}

	sort( $navs );
	$min_page = $navs[0];
	$max_page = $navs[ count( $navs ) - 1 ];

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
			$s .= ' - '. $ivi_config['cats'][ $cat ]['name'];
			if( $sub <> 'all' ) $s .= ' - '. $ivi_config['cats'][ $cat ]['subs'][ $sub ]['name'];
		}
		else $s .= ' - Все';
		$s .= ' ('. $ivi_config['sorts'][ $sort ] .')';
	}
	if( $min_page !== $max_page ) $s .= ' (страница '. $page .' из '. $max_page .')';

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
function rss_ivi_menu_content()
{
global $ivi_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

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

	foreach( $ivi_config['cats'] as $cat => $item )
	{
		$view->items[] = array(
			'title'	=> $item['name'],
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_ivi_category&amp;cat='.$cat
		);
	}

	$view->items[] = array(
		'title'	=> 'Настройки',
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

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	foreach( $ivi_config['sorts'] as $sort => $name )
	{
		$view->items[] = array(
			'title'	=> $name,
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;sort='.$sort
		);
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_category_content()
{
global $ivi_config;

	if( ! isset( $_REQUEST['cat'] )) return;
	$cat = $_REQUEST['cat'];

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> 'Все',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;cat='. $cat .'&amp;sub=all'
		)
	);

	foreach( $ivi_config['cats'][ $cat ]['subs'] as $sub => $item )
	{
		$view->items[] = array(
			'title'	=> $item['name'],
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;cat='. $cat .'&amp;sub='. $sub
		);
	}

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_sets_content()
{
global $ivi_config;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> 'Среднее качество',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;quality=lo'
		),
		1 => array(
			'title'	=> 'Высокое качество',
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_ivi&amp;quality=hi'
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
function addStr( &$s, $adds )
{
	if( strlen( $s ) == 0 )
	{
		$s = $adds;
	}
	else $s .= ', '.$adds;
}
//
// ------------------------------------
function rss_ivi_info_content()
{
	if( ! isset( $_REQUEST['url'])) exit;

	$url = $_REQUEST['url'];

header( "Content-type: text/plain" );

	$s=iviGetContent( 'http://www.ivi.ru/'. $url );

	$infos = array();
	
	$doc = new DOMDocument();

	libxml_use_internal_errors( true );
	$doc->loadHTML($s);

	$divs = $doc->getElementsByTagName('div');
	foreach( $divs as $div )
	if( $div->hasAttribute('class'))
	if( $div->getAttribute('class') == 'content-main clearfix' )
	{			
		$img = $div->getElementsByTagName('img');
		break;
	}

	$title  = '';
	$poster = '';
	if( preg_match( '/<meta name="mrc__share_title" content="(.*?)" \/>/' , $s, $ss ) > 0 )
	{
		$title = $ss[1];
	}
	else{
		if( $img->length > 0 )
		{
			$title = trim( $img->item(0)->getAttribute('alt'));
		}		
	}
		
	if( preg_match( '/<link rel="image_src" href="(.*?)" \/>/' , $s, $ss ) > 0 )
	{
		$poster = $ss[1];
	}
	else{
		if( $img->length > 0 )
		{			
			$poster = $img->item(0)->getAttribute ('src');
		}		
	}
	$duration = '';
	if( preg_match( '/<meta property="og:duration" content="(.*?)" \/>/' , $s, $ss ) > 0 )
	{
		$duration = $ss[1];
		$h = intval( $duration / 3600 );
		$m = intval( ( $duration - $h * 3600 ) / 60 );
		$sec = $duration - $h * 3600 - $m * 60;
		$duration ='';
		if( $h > 0 )
		{
			if( $h < 10 ) $duration .= '0';
			$duration .= $h .':';
		}
		if( $h > 0 || $m > 0 )
		{
			if( $m < 10 ) $duration .= '0';
			$duration .= $m .':';
		}
		$duration .= $sec;
	}

	$desc = '';
	if( preg_match( '/<meta name="mrc__share_description" content="([^"]+)" \/>/' , $s, $ss ) > 0 )
	{
		$desc = $ss[1];
	}
	else{	
		$dess = $div->getElementsByTagName('div');
		foreach( $dess as $des )
		if( $des->hasAttribute('class'))
		if( $des->getAttribute('class') == 'description' )
		{			
			$desc= $des->textContent;
			break;
		}		
	}
	
	$title_orig  = '';
	if( preg_match( '/<h1>.*<small> &mdash; (.*?)<\/small><\/h1>/' , $s, $ss ) > 0 )
	{
		$title_orig  = $ss[1];
	}	

	$year  = '';
	$country  = '';
	$genre  = '';
	if( preg_match( '/<div class="tags">(.*?)<\/div>/s' , $s, $ss ) > 0 )
	{
		$t =$ss[1];
		if( preg_match_all( '/<a.*?>(.*?)<\/a>/' , $t, $ss ) > 0 )
		{
			foreach( $ss[0] as $id => $item )
			if( strpos( $item, 'year'))
			{
				addStr( $year, $ss[1][ $id ] );
			}
			elseif( strpos( $item, 'country'))
			{
				addStr( $country, $ss[1][ $id ] );
			}
			else{
				addStr( $genre, $ss[1][ $id ] );
			}
		}
	}

	$imdb = '';
	if( preg_match( '/<li class="imdb"><strong>.*?<\/strong>\s*(.*?)<\/li>/' , $s, $ss ) > 0 )
	{
		$imdb = $ss[1];
	}

	$kinopoisk = '';
	if( preg_match( '/<li class="kinopoisk.*?"><strong>.*?<\/strong>\s*(.*?)<\/li>/' , $s, $ss ) > 0 )
	{
		$kinopoisk = $ss[1];
	}
/*
echo $poster.PHP_EOL;
echo $title.PHP_EOL;
echo $title_orig.PHP_EOL;
echo $desc.PHP_EOL;
echo $duration.PHP_EOL;
echo $year.PHP_EOL;
echo $country.PHP_EOL;
echo $genre.PHP_EOL;
echo $imdb.PHP_EOL;
echo $kinopoisk.PHP_EOL;
*/
	$items = iviGetPlaylist( $s );

//print_r( $items );

	if( count( $items ) == 1 )
	{
		include( 'modules/core/rss_view_info.php' );

		$view = new rssSkinInfoView;

		$view->_link = $items[0]['link'];
		$view->_action = $items[0]['action'];

		$width = 1280;
	}
	else
	{
		include( 'modules/core/rss_view_info_list.php' );
		$view = new rssSkinInfoListView;

		$view->items = $items;

		$width = 780;
	}

	if( $title <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 68,
			'width'  => $width,
			'height' => 50,
			'fontSize' => 18,
			'text' => $title
		);
	}

	if( $title_orig <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 40,
			'posY' => 118,
			'width'  => $width,
			'height' => 36,
			'fontSize' => 12,
			'fgColor' => '180:180:180',
			'text' => $title_orig
		);
	}

	if( $poster <> '' )
	{
		$view->infos[] = array(
			'type' => 'image',
			'posX' => 60,
			'posY' => 156,
			'width'  => 180,
			'height' => 135,
			'image' => $poster
		);
	}

	if( $duration <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 242,
			'posY' => 156,
			'width'  => 578,
			'height' => 30,
			'lines' => 1,
			'fontSize' => 10,
			'text' => $duration
		);
	}

	addStr( $country, $year );
	if( $country <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 242,
			'posY' => 190,
			'width'  => 578,
			'height' => 30,
			'lines' => 1,
			'fontSize' => 10,
			'text' => $country
		);
	}

	if( $genre <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 242,
			'posY' => 224,
			'width'  => 578,
			'height' => 30,
			'lines' => 1,
			'fontSize' => 10,
			'text' => $genre
		);
	}

	$rating = '';
	if( $imdb <> '' ) $rating .= 'IMDB: '.$imdb;
	if( $kinopoisk <> '' ) $rating .= ' Kinopoisk: '.$kinopoisk;
	if( $rating <> '' )
	{
		$view->infos[] = array(
			'type' => 'text',
			'posX' => 242,
			'posY' => 258,
			'width'  => 578,
			'height' => 30,
			'lines' => 1,
			'fontSize' => 10,
			'text' => $rating
		);
	}

	if( $desc <> '' )
	{
		$posX = 40;
		$posY = 294;

		$lineHeight = 26;
		$symbolWidth = 12;

		$ds = explode( "\n", $desc );

		$px = $posX;
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

	$view->topTitle = 'ivi.ru';
	$view->bottomTitle = getRssCommandPrompt('enter') . ' смотреть ';

	$view->showRss();
}
//
// ------------------------------------
function rss_ivi_content()
{
	class rssSkinIviView extends rssSkinHTile
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
		url = "<?= getMosUrl().'?page=rss_player&amp;mod=ivi' ?>"
		 + "&amp;url=" + url;
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=rss_ivi_info' ?>"
		 + "&amp;url=" + url;
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_ivi_menu' ?>";
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
	else if (userInput == "<?= getRssCommand('play') ?>")

	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = "<?= getMosUrl().'?page=rss_ivi_actions' ?>"
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
	moUrl = "<?= getMosUrl().'?page=xml_ivi' ?>";
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
	$view = new rssSkinIviView;

	$view->bottomTitle = 
		getRssCommandPrompt('menu')    . ' меню '
		. getRssCommandPrompt('play')  . ' смотреть '
		. getRssCommandPrompt('enter') . ' информация '
	;

	$view->showRss();
}

?>
