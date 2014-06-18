<?php

function rss_services_content()
{
	class rssSkinSevicesView extends rssSkinHTile
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

	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_services_menu' ?>";
		url = doModalRss(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = i;
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
		public function showScripts()
		{
?>

  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_services' ?>";
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
    <onClick>
	idx = getFocusItemIndex();
	url = getStringArrayAt(urlArray , idx);
	ret = doModalRss(url);
	if(( ret != null )&amp;&amp;( ret != "" ))
	{
		moUrl = ret;
		savedItem = idx;
		setRefreshTime(1);
	}
	null;
    </onClick>
  </item_template>
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

	$view = new rssSkinSevicesView;

	$view->topTitle =
'
	<script>
	  pageTitle;
	</script>';

	$view->bottomTitle = 
		getRssCommandPrompt('rewind')  . getMsg( 'coreRssPromptMenu' ).'   '.
		getRssCommandPrompt('enter') . getMsg( 'coreRssPromptActs' );

	$view->showRss();
}
//
// ------------------------------------
function rss_services_actions_content()
{
global $mos;

	if( ! isset( $_REQUEST['mod'] )) return;

	$mod = $_REQUEST['mod'];
	$page = 'xml_services';
	if( isset( $_REQUEST['ret'] )) $page = $_REQUEST['ret'];

	$packs = parse_ini_file( $mos.'/etc/pm/packages', true );
	$installed = parse_ini_file( $mos.'/etc/pm/installed', true );

	$opts = $installed[ $mod ];

	if( loadModuleOptions( $mod, $opts ) === false )
	{
		if( isset( $packs[ $mod ] ))
		{
			$opts = array(
				'title' => $packs[ $mod ]['title'],
				'_actions' => array()
			);
			$irev = '';
		}
		else return;
	}
	else $irev = $opts['revision'];

	if(( $opts['role'] == 'core' )||( $opts['role'] == 'package' )) $opts['_actions'] = array();

	if( isset( $packs[ $mod ] ))
	{
		$arev = $packs[ $mod ]['revision'];
		if( $irev == '' ) $opts['_actions'][] = 'install';
		elseif( $arev != $irev ) $opts['_actions'][] = 'update';
	}

	// --------------------------
	// send RSS

	include( 'rss_view_popup.php' );

	$view = new rssSkinPopupView;

	$view->topTitle = $opts['title'];

	foreach( $opts['_actions'] as $act )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'coreCm_'.$act ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=$page&amp;mod=$mod&amp;act=$act"
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
function rss_services_menu_content()
{
	include( 'rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
		0 => array(
			'title'	=> getMsg( 'coreServices' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_services'
		),
		1 => array(
			'title'	=> getMsg( 'coreModules' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_modules'
		),
		2 => array(
			'title'	=> getMsg( 'coreCmUList' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_modules&amp;act=getrep'
		),
		3 => array(
			'title'	=> getMsg( 'coreCmUAll' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_modules&amp;act=update_all'
		),
		4 => array(
			'title'	=> getMsg( 'coreReboot' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?do=reboot'
		),
	);

	$view->showRss();
}

?>
