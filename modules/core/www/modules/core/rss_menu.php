<?php

$nav_installed = parse_ini_file( $mos.'/etc/pm/installed', true );

$nav_rss = array ();

$d = opendir( 'modules' );
while ( $m = readdir( $d ) )
{
	$w = "modules/$m";
	if ( is_dir( $w ) )
	{
		$f = "$w/def_rss.php";
		if( file_exists( $f ) )
		{
			$opt = $nav_installed[ $m ];
			if( loadModuleOptions( $m, $opt ) !== false )
			 if( $opt['_show'] )
			  include( $f );
		}
	}
}
ksort( $nav_rss );
//
// ------------------------------------
function rss_menu_content()
{
	class rssSkinMainMenuView extends rssSkinHTile
	{
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
		public function showScripts()
		{
?>

  <script>
	moUrl = "<?= getMosUrl().'?page=xml_menu' ?>";
	savedItem = 0;
  </script>

  <onEnter>
	setRefreshTime(10);
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
	if( itemCount == 0 )
	{
		setFocusItemIndex( 0 );
	}
	else if( savedItem &gt; ( itemCount - 1 ))
	{
		setFocusItemIndex( itemCount - 1 );
	}
	else setFocusItemIndex( savedItem );

	cancelIdle();
	redrawDisplay();
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
  </onExit>

  <item_template>
    <link>
      <script>
	savedItem = getFocusItemIndex();
	url = getStringArrayAt(urlArray , savedItem);
      </script>
    </link>
  </item_template>
<?php
/*
  <item_template>
    <onClick>
	savedItem = getFocusItemIndex();
	url = getStringArrayAt(urlArray , savedItem);
	doModalRss(url);
	setRefreshTime(10);
	null;
    </onClick>
  </item_template>
*/
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

	$view = new rssSkinMainMenuView;

	$view->topTitle =
'
	<script>
	  pageTitle;
	</script>';

	$view->showRss();
}
//
// ------------------------------------
function xml_menu_content()
{
global $mos;
global $nav_rss;
global $nav_pages;

	$s = getMsg( 'coreRssMainMenu' ) . PHP_EOL;
	$s .= count( $nav_rss ) . PHP_EOL;

	foreach( $nav_rss as $rss => $item )
	{
		if( isset( $item['rss']) )
		{
			$mod = $item['module'];
			$url = $item['rss'];
		}
		else
		{
			$mod = $nav_pages[ $item['page'] ]['module'];
			$url = getMosUrl(). '?page='.$item['page'];
		}
		$s .= $item['title']  . PHP_EOL;
		$s .= $mos.'/www/modules/'.$mod.'/'.$item['icon'] . PHP_EOL;
		$s .= $url . PHP_EOL;
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

?>
