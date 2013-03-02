<?php

// get timezone
$a = exec( 'date -R | cut -d" " -f 6' );

if( $a[0] == '+' ) $c = 1;
elseif( $a[0] == '-' ) $c = -1;
else $c = 0;

if( $c != 0 )
{
	$offset = $c * (integer)$a[1] * 36000;
	$offset = $offset + $c * (integer)$a[2] * 3600;
	$offset = $offset + $c * (integer)$a[3] * 600;
	$offset = $offset + $c * (integer)$a[4] * 60;

	$tz = timezone_name_from_abbr( '', $offset, 0 );
}
else $tz = 'UTC';
date_default_timezone_set( $tz );

$sovok_config = array(
	'login'  => 0,
	'passwd' => 0,
);

if( is_file( $mos .'/www/modules/sovok/sovok.config.php' ) )
{
	include( $mos .'/www/modules/sovok/sovok.config.php' );
}

$sovok_session = array(
	'sid'     => '',
	'sid_name'=> '',
	'code'    => '',
	'message' => '',
	'gid'     => '',
	'groups'  => array(),
);

if( is_file( '/tmp/sovok.session.php' ) )
{
	include( '/tmp/sovok.session.php' );
}
//
// ------------------------------------
function saveSovokConfig()
{
global $mos;
global $sovok_config;

	file_put_contents( $mos .'/www/modules/sovok/sovok.config.php', '<?php $sovok_config = '.var_export( $sovok_config, true ).'; ?>' );
}
//
// ------------------------------------
function saveSovokSession()
{
global $sovok_session;

	file_put_contents( '/tmp/sovok.session.php', '<?php $sovok_session = '.var_export( $sovok_session, true ).'; ?>' );
}
//
// ------------------------------------
function sendSovokRespond( $s )
{
	if( isset( $_REQUEST['debug']))
	{
		echo $s;
	}
	else
	{
		header( "Content-type: text/plain" );
		file_put_contents( '/tmp/put.dat', $s );
		echo "/tmp/put.dat";
	}
}
//
// ------------------------------------
function getSovokAPI( $req )
{
global $sovok_session;

	$s = 'http://api.sovok.tv/v2.0/json/'. $req;

	if( $sovok_session['sid'] == '' )
	{
		return file_get_contents ( $s );
	}
	$opts = array(
		'http' => array(
			'method'  => 'GET',
			'user-agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0',
			'header' => 'Cookie: '. $sovok_session['sid_name'] .'='. $sovok_session['sid'] ."\n\r",
		)
	);
	$context = stream_context_create($opts);
	return file_get_contents ( $s, false, $context );
}
//
// ------------------------------------
function sovokLogin()
{
global $sovok_config;
global $sovok_session;

	$s = getSovokAPI( 'login?login=' . urlencode( $sovok_config['login'] ) .'&pass='. urlencode( $sovok_config['passwd'] ));
	$ss=json_decode($s, true);

	if( isset( $ss['error'] ))
	{
		$sovok_session['sid'] = '';
		$sovok_session['message'] = $ss['error']['message'];
		saveSovokSession();

		$s = 'rss' .PHP_EOL;
		$s .= getMosUrl().'?page=rss_sovok_message' .PHP_EOL;
		sendSovokRespond( $s );
		exit;
	}

	if( isset( $ss['sid'] ))
	{
		$sovok_session['sid'] = $ss['sid'];
		$sovok_session['sid_name'] = $ss['sid_name'];
	}
	else
	{
		$sovok_session['sid'] = '';
		$sovok_session['message'] = 'Unknown error';
		saveSovokSession();

		$s = 'rss' .PHP_EOL;
		$s .= getMosUrl().'?page=rss_sovok_message' .PHP_EOL;
		sendSovokRespond( $s );
		exit;
	}
}
//
// ------------------------------------
function get_sovok_content()
{
global $sovok_session;

	if( ! isset( $_REQUEST['cid'])) return;
	$cid = $_REQUEST['cid'];

	header( "Content-type: text/plain" );

	$code = $sovok_session['code'];
	if( isset( $_REQUEST['code']))
	{
		$code = $_REQUEST['code'];
		$sovok_session['code'] = $code;
		saveSovokSession();
	}

	$req = 'get_url?cid='. $cid;
	if( $code != '' ) $req .= '&protect_code=' . $code;

	$s = getSovokAPI( $req );
	$ss=json_decode($s, true);

file_put_contents( '/tmp/sovok_get.log', '<?php $ss = '.var_export( $ss, true ).'; ?>' );

	if( ! isset( $ss['url'] )) return;

	if( $ss['url'] == 'protected' )
	{
		echo 'protected';
		return;
	}

	if( preg_match( '#http[^ ]*#' , $ss['url'], $a ) > 0 )
	{
		$s = str_replace( '/ts://', '://', $a[0] );
		echo $s;
	}
}
//
// ------------------------------------
function get_sovok_epg_content()
{
	if( ! isset( $_REQUEST['cid'])) return;
	$cid = $_REQUEST['cid'];

	header( "Content-type: text/plain" );

	$s = getSovokAPI( 'epg_next?cid='. $cid );
	$ss=json_decode($s, true);

	$c = 0;
	foreach( $ss['epg'] as $item )
	{
		if( ( $epg = $item['progname'] ) != '' )
		{
			$epg = str_replace( '&quot;', '"', $epg );
			$epg = date( 'H:i', $item['ts'] ) .' '. $epg;
			echo $epg .PHP_EOL;
			$c += 1;
		}
	}
	while( $c < 3 )
	{
			echo PHP_EOL;
			$c += 1;
	}
}
//
// ------------------------------------
function xml_sovok_content()
{
global $sovok_config;
global $sovok_session;

if( isset( $_REQUEST['debug'])) header( "Content-type: text/plain" );

	if( isset( $_REQUEST['logout'] ))
	{
		$sovok_session['sid'] = '';
		$sovok_config['login'] = '';
		$sovok_config['passwd'] = '';

		saveSovokConfig();
		saveSovokSession();

		$s = 'Sovok.TV' .PHP_EOL;
		$s .= '0' .PHP_EOL;
		sendSovokRespond( $s );
		exit;
	}

	if( isset( $_REQUEST['logon'] ))
	{
		$sovok_session['sid'] = '';
		$sovok_config['login'] = '';
		$sovok_config['passwd'] = '';

		saveSovokConfig();
		saveSovokSession();
	}

	if( isset( $_REQUEST['login'] ))
	{
		$sovok_session['sid'] = '';
		$sovok_config['login'] = $_REQUEST['login'];
		$sovok_config['passwd'] = $_REQUEST['passwd'];

		saveSovokConfig();
	}

	if( $sovok_config['login'] == '' )
	{
		$s = 'rss' .PHP_EOL;
		$s .= getMosUrl().'?page=rss_sovok_login' .PHP_EOL;
		sendSovokRespond( $s );
		exit;
	}

	if( $sovok_session['sid'] == '' ) sovokLogin();

	// get channel list
	$s = getSovokAPI( 'channel_list' );
	$ss=json_decode($s, true);

	if( isset( $ss['error'] ))
	{
		sovokLogin();
		$s = getSovokAPI( 'channel_list' );
		$ss=json_decode($s, true);
	}

if( isset( $_REQUEST['debug'])) print_r( $ss );

	if( isset( $ss['groups'] )) $sovok_session['groups'] = $ss['groups'];

	$gid = $sovok_session['gid'];
	if( isset( $_REQUEST['gid'] ))
	{
		$gid = $_REQUEST['gid'];
	}

	if( $gid == '' )
	if( count( $sovok_session['groups'] ) > 0 )
	{
		reset( $sovok_session['groups'] );
		$gid = key( $sovok_session['groups'] );
	}
	$sovok_session['gid'] = $gid;

	saveSovokSession();


	$s = 'Sovok.TV - '. $sovok_session['groups'][ $gid ]['name'] .PHP_EOL;
	$s .= count( $sovok_session['groups'][ $gid ]['channels'] ) .PHP_EOL;

	foreach( $sovok_session['groups'][ $gid ]['channels'] as $item )
	{
		if( ( $epg = $item['epg_progname'] ) != '' )
		{
			$epg = str_replace( '&quot;', '"', $epg );
			$epg = date( 'H:i', $item['epg_start'] ) .'-'. date( 'H:i', $item['epg_end'] ) .' '. $epg;
		}

		$s .= $item['name'] . "\n";
		$s .= $item['id'] . "\n";
		$s .= 'http://sovok.tv'. $item['icon'] .PHP_EOL;
		$s .= $epg .PHP_EOL;
	}
	sendSovokRespond( $s );
}
//
// ------------------------------------
function rss_sovok_content()
{
	class rssSkinSovokView extends rssSkinHTile
	{
		//
		// ------------------------------------

		public $topTitle =
'
	<script>
	  pageTitle;
	</script>
';
		//
		// ------------------------------------

		const itemWidth		= 400;
		const itemHeight	= 78;

		const itemUnFocusBgColor = '0:0:0';

		//
		// ------------------------------------
		public $fields = array(
			0 => array(			// image
				'type'   => 'image',
				'posX'   => 10,
				'posY'   => 10,
				'width'  => 70,
				'height' => 40,
				'image'  => '
	<script>
	  getStringArrayAt(imgArray, idx);
	</script>'
			),
			1 => array(			// title
				'type'    => 'text',
				'posX'    => 90,
				'posY'    => 5,
				'width'   => 300,
				'height'  => 28,
				'lines'   => 0,
				'fontSize'=> 11,
				'align'   => 'left',
//				'bgColor' => '"100:100:100"',
				'text'    => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
			2 => array(			// epg
				'type'    => 'text',
				'posX'    => 82,
				'posY'    => 28,
				'width'   => 298,
				'height'  => 37,
				'lines'   => 2,
				'fontSize'=> 9,
				'align'   => 'left',
//				'bgColor' => '"100:100:100"',
				'text'    => '
	<script>
	  getStringArrayAt(epgArray, idx);
	</script>'
			),
		);
		//
		// ------------------------------------
		public function showItemDisplay()
		{

?>
    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();
	if (drawState == "unfocus")
	{
		color = "<?= static::unFocusFontColor ?>";
		bgcolor = "<?= static::itemUnFocusBgColor ?>";
	}
	else
	{
		color = "<?= static::focusFontColor ?>";
		bgcolor = "<?= static::itemFocusBgColor ?>";
	}
      </script>
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="4">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<?php
			foreach( $this->fields as $info )
			{
				$px = round( $info['posX'] / static::itemWidth * 100, 4);
				$py = round( $info['posY'] / static::itemHeight * 100, 4);
				$pw = round( $info['width']  / static::itemWidth  * 100, 4);
				$ph = round( $info['height'] / static::itemHeight * 100, 4);

				if( $info['type'] == 'image' )
				{

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $info['image'] ?> 
      </image>
<?php
				}
				else
				{
					$pa = isset( $info['align'] )    ? $info['align']     : static::itemTextAlign;
					$ps = isset( $info['fontSize'] ) ? $info['fontSize']  : static::itemTextFontSize;
					$pb = isset( $info['bgColor'] )  ? $info['bgColor']   : '"'. static::itemTextBackgroundColor .'"';
					$pf = isset( $info['fgColor'] )  ? $info['fgColor']   : 'color';

					$pl = isset( $info['lines'] ) ? $info['lines'] : static::itemTextLines;
					$pl = ( $pl > 0 ) ? ' lines="'. $pl .'"' : '';

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="<?= $pa ?>"<?= $pl ?> fontSize="<?= $ps ?>">
	<backgroundColor>
          <script>
            <?= $pb ?>;
          </script>
	</backgroundColor>
	<foregroundColor>
          <script>
            <?= $pf ?>;
          </script>
	</foregroundColor><?= $info['text'] ?> 
      </text>
<?php
				}
			}

?>
    </itemDisplay>
<?
		}
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

	else if (userInput == "<?= getRssCommand('stop') ?>" )
	{
		setRefreshTime(1);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_sovok_menu' ?>";
		url = doModalRss(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			setRefreshTime(1);
		}
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>" || userInput == "<?= getRssCommand('play') ?>")
	{
		showIdle();
		s = doModalRss( "<?= getMosUrl().'?page=rss_sovok_player' ?>" + "&amp;cid=" + i );
		if(( s != null )&amp;&amp;( s != "" ))
		{
			savedItem = s;
			redrawDisplay();
		}
		cancelIdle();
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
	moUrl = "<?= getMosUrl().'?page=xml_sovok' ?>";

	savedItem = 0;
	itemCount = 0;

	pageTitle = "Sovok.TV";

	setRefreshTime(100);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();

	respond = "list";

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		respond = getStringArrayAt(dlok, c); c += 1;

		if( respond == "rss" )
		{
			url = getStringArrayAt(dlok, c); c += 1;
			url = doModalRss( url );
			if(( url != null )&amp;&amp;( url != "" ))
			{
				moUrl = url;
			}
			else respond = "";
		}
		else
		{
			pageTitle = respond;
			itemCount = getStringArrayAt(dlok, c); c += 1;

			nameArray = null;
			imgArray  = null;
			urlArray  = null;
			epgArray  = null;

			count = 0;
			while( count != itemCount )
			{
				nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
				urlArray  = pushBackStringArray( urlArray,  getStringArrayAt(dlok, c)); c += 1;
				imgArray  = pushBackStringArray( imgArray,  getStringArrayAt(dlok, c)); c += 1;
				epgArray  = pushBackStringArray( epgArray,  getStringArrayAt(dlok, c)); c += 1;

				count += 1;
			}
		}
	}
	cancelIdle();

	if( respond == "rss" )
	{
		setRefreshTime(100);
	}
	else if( respond != "" )
	{
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
	$view = new rssSkinSovokView;

	$view->bottomTitle = 
	 getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' )
	 .' '.getRssCommandPrompt('enter') . getMsg( 'coreRssPromptPlay' )
	 .' '.getRssCommandPrompt('stop') . getMsg( 'sovokUpdate' )
	;

	$view->showRss();
}
//
// ------------------------------------
function rss_sovok_menu_content()
{
global $sovok_session;

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	if( $sovok_session['sid'] == '' )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'sovokEntry' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_sovok&amp;logon'
		);
	}
	else
	{
		foreach( $sovok_session[groups] as $item )
		{
			$view->items[] = array(
				'title'	=> $item['name'],
				'action'=> 'ret',
				'link'	=> getMosUrl().'?page=xml_sovok&amp;gid='.$item['id']
			);
		}

		$view->items[] = array(
			'title'	=> getMsg( 'sovokExit' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_sovok&amp;logout'
		);

	}
	$view->showRss();
}

?>
