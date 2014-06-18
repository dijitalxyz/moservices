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

include( 'load_config.inc.php' );
//
// ------------------------------------
function sendRodinaRespond( $s )
{
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
function parseRow( $x )
{
	$s = array();
	foreach( $x->children() as $el )
	 if(     (string)$el->getName() == 'item'  ) $s[ (string)$el['name'] ] = (string)$el;
	 elseif( (string)$el->getName() == 'array' ) $s[ (string)$el['name'] ] = parseArray( $el );
	return $s;
}

function parseArray( $x )
{
	$s = array();
	foreach( $x->children() as $el )
	 if(     (string)$el->getName() == 'item' )
	 {
		$n = (string)$el['name'];
		if( $n == '' ) $s[] = (string)$el;
		else $s[ $n ] = (string)$el;
	 }
	 elseif( (string)$el->getName() == 'row' ) $s[] = parseRow( $el );
	return $s;
}
//
// ------------------------------------
function getRodinaAPI( $req )
{
if( isset( $_REQUEST['debug'])) echo "request=$req\n";

	$s = file_get_contents ( $req );

	$x = new SimpleXMLElement($s);

	$s = parseArray($x);

	foreach( $x->attributes() as $n => $v )
	 $s[ (string)$n ] = (string)$v;

if( isset( $_REQUEST['debug'])) print_r( $s );

	return $s;
}
//
// ------------------------------------
function saveRodinaMessage( $s )
{
global $rodina_session;

	$rodina_session['ttl'] = 0;
	$rodina_session['message'] = $s;
	saveRodinaSession();
}
//
// ------------------------------------
function rodinaLogin()
{
global $rodina_config;
global $rodina_session;

	if( $rodina_session['ttl'] > time() ) return true;

	// reauthorization
	if( $rodina_config['login'] == '' )
	{
		saveRodinaMessage( getMsg( 'rodinaEntryOn' ) );
		return false;
	}

	// get sid
	$s = getRodinaAPI( 'http://api.rodina.tv/auth.xml' );
	if( $s['status'] == 'error' )
	{
		if(( $a = getMsg( 'rodinaErr'.$s[0]['code']  )) == 'rodinaErr'.$s[0]['code'] ) $a = $s[0]['message'];
		saveRodinaMessage( $a );
		return false;
	}

	$req = 'http://api.rodina.tv/auth.xml'
		.'?device=dune_hd'
		.'&version=1.0.0'
		.'&sid='. $s[0]['sid']
		.'&login=' .$rodina_config['login']
		.'&passwd=' .md5( $s[0]['rand'] . md5( $rodina_config['passwd'] ));

	if( $rodina_session['token'] != '' )
	 $req .= '&serial='. $rodina_session['token'];

	// get token
	$s = getRodinaAPI( $req );

	if( $s['status'] == 'error' )
	{
		if(( $a = getMsg( 'rodinaErr'.$s[0]['code']  )) == 'rodinaErr'.$s[0]['code'] ) $a = $s[0]['message'];
		saveRodinaMessage( $a );
		return false;
	}
	$rodina_session['portal'] = $s[0]['portal'];
	$rodina_session['token'] = $s[0]['token'];
	$rodina_session['ttl'] = time() + $s[0]['ttl'];
	saveRodinaSession();
	return true;
}
//
// ------------------------------------
function rodinaQuery( $req )
{
global $rodina_session;

exec( "echo $( date ) '". $rodina_session['token'] . " $req' >> /tmp/rodina.log" );

	if( ! rodinaLogin() ) return false;

	$x = getRodinaAPI( $rodina_session['portal']
		.'?token='. $rodina_session['token']
		.'&query='. $req );

	if( $x['status'] == 'error' )
	{
		if(( $a = getMsg( 'rodinaErr'.$x[0]['code']  )) == 'rodinaErr'.$x[0]['code'] ) $a = $x[0]['message'];
		saveRodinaMessage( $a );
		return false;
	}
	return $x;
}
//
// ------------------------------------
function get_rodina_content()
{
global $rodina_session;

	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['cid'] )) return;

	$cid = $_REQUEST['cid'];

	if( $rodina_session['channels'][ $cid ]['passwd'] == 1 )
	{
		$code = $rodina_session['code'];
		if( isset( $_REQUEST['code'] ))
		{
			$code = $_REQUEST['code'];
		}

		if( $code == '' )
		{
			echo 'protected';
			return;
		}

		$query = 'get_url&key=number|passwd&value='. $cid .'|'
			. md5( $rodina_session['token'] . md5( $code ));

		if(( $x = rodinaQuery( $query )) === false )
		{
			echo 'message';
			return;
		}

		if( $x['status'] == 'error' )
		{
			if(( $a = getMsg( 'rodinaErr'.$x[0]['code']  )) == 'rodinaErr'.$x[0]['code'] ) $a = $x[0]['message'];
			$rodina_session['code'] = '';
			$rodina_session['message'] = $a;
			saveRodinaSession();
			echo 'message';
			return;
		}
		else
		{
			if( $rodina_session['code'] != $code )
			{
				$rodina_session['message'] = '';
				$rodina_session['code'] = $code;
				saveRodinaSession();
			}
		}
	}
	else
	{
		$query = 'get_url&key=number&value='. $cid;
		if(( $x = rodinaQuery( $query )) === false )
		{
			echo 'message';
			return;
		}
	}

	$s = str_replace( 'ts://', '', $x[0]['url'] );

exec( "echo $( date ) 'url=". $x[0]['url'] . "' >> /tmp/rodina.log" );

	echo $s;
}
//
// ------------------------------------
function get_rodina_epg_content()
{
	header( "Content-type: text/plain" );

	if( ! isset( $_REQUEST['cid'] )) return;

	$cid = $_REQUEST['cid'];

	// get epg
	if(( $x = rodinaQuery( 'get_epg&key=count|number&value=2|'. $cid )) === false )
	{
		echo 'fail';
		return;
	}

	$epgs = array();

	foreach( $x[0]['programmes'] as $p )
	 $epgs[] = date( 'H:i', $p['ut_start'] ) .'-'. date( 'H:i', $p['ut_stop'] ) .' '. $p['title'];

	if( isset( $epgs[0] )) echo $epgs[0]; echo PHP_EOL;
	if( isset( $epgs[1] )) echo $epgs[1]; echo PHP_EOL;
}
//
// ------------------------------------
function get_rodina_token_content()
{
	if(( $x = rodinaQuery('token_status')) === false )
	{
		echo 'fail';
		return;
	}
}
//
// ------------------------------------
function xml_rodina_content()
{
global $rodina_config;
global $rodina_session;

	header( "Content-type: text/plain" );

	if( isset( $_REQUEST['logout'] ))
	{
		$rodina_session['ttl'] = 0;
		$rodina_config['login'] = '';
		$rodina_config['passwd'] = '';

		saveRodinaConfig();
		saveRodinaSession();

		$s = 'rodina.TV' .PHP_EOL;
		$s .= '0' .PHP_EOL;
		sendRodinaRespond( $s );
		return;
	}

	if( isset( $_REQUEST['logon'] ))
	{
		$rodina_session['ttl'] = 0;
		$rodina_config['login'] = '';
		$rodina_config['passwd'] = '';

		saveRodinaConfig();
		saveRodinaSession();
	}

	if( isset( $_REQUEST['login'] ))
	{
		$rodina_session['ttl'] = 0;
		$rodina_config['login'] = $_REQUEST['login'];
		$rodina_config['passwd'] = $_REQUEST['passwd'];

		saveRodinaConfig();
	}

	if(( $x = rodinaQuery( 'get_channels' )) === false )
	{
		$s = 'rss' .PHP_EOL;
		$s .= getMosUrl().'?page=rss_rodina_login' .PHP_EOL;
		sendRodinaRespond( $s );
		return;
	}

	// prepare categories list
	$cats = array();
	foreach( $x[0]['categories'] as $c )
	 $cats[ $c['number'] ] = array(
			'name' =>  $c['title'],
			'channels' =>  $c['channels'],
	 );

	$rodina_session['categories'] = $cats;

	// get gid
	$gid = $rodina_session['gid'];
	if( isset( $_REQUEST['gid'] ))
	{
		$gid = $_REQUEST['gid'];
	}

	if( $gid == '' )
	if( count( $cats ) > 0 )
	{
		reset( $cats );
		$gid = key( $cats );
	}
	$rodina_session['gid'] = $gid;

	// prepare channels list
	$allchs = array();
	foreach( $x[0]['channels'] as $ch )
	 $allchs[ $ch['number'] ] = array(
			'name'   =>  $ch['title'],
			'icon'   =>  $ch['icon_45_45'],
			'passwd' =>  $ch['has_passwd'],
			'epg'    =>  '',
	 );

	$rodina_session['channels'] = $allchs;

	saveRodinaSession();

	// get category channels list
	$channels = array();
	$numbers = array();
	foreach( $cats[ $gid ]['channels'] as $cid )
	{
		if( ! isset( $allchs[ $cid ] )) continue;
		$channels[ $cid ] = $allchs[ $cid ];
		$numbers[]  = $cid;
	}

	// get epg
	if(( $x = rodinaQuery( 'get_epg&key=count|number&value=1|' . implode( ',', $numbers ))) === false ) return;
	foreach( $x as $ps )
	{
		if( ! is_array( $ps )) continue;
		if( ! isset( $channels[ $ps['number']] )) continue;
		$p = $ps['programmes'][0];
		$channels[ $ps['number'] ]['epg'] = date( 'H:i', $p['ut_start'] ) .'-'. date( 'H:i', $p['ut_stop'] ) .' '. $p['title'];
	}

	// send respond
	$s = 'Rodina.TV - '. $cats[ $gid ]['name'] .PHP_EOL;
	$s .= count( $channels ) .PHP_EOL;

	foreach( $channels as $cid => $item )
	{
		$s .= $item['name'] .PHP_EOL;
		$s .= $cid          .PHP_EOL;
		$s .= $item['icon'] .PHP_EOL;
		$s .= $item['epg']  .PHP_EOL;
	}
	sendRodinaRespond( $s );
}
//
// ------------------------------------
function rss_rodina_menu_content()
{
global $rodina_session;

	include( 'rss_view_left.php' );

	$view = new rssRodinaLeftView;
	$cur = 0;

	if( $rodina_session['gid'] == '' )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'rodinaEntry' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_rodina&amp;logon'
		);
	}
	else
	{
		$i = 0;
		foreach( $rodina_session['categories'] as $gid => $item )
		{
			if( $rodina_session['gid'] == $gid ) $cur = $i;
			$view->items[] = array(
				'title'	=> $item['name'],
				'action'=> 'ret',
				'link'	=> getMosUrl().'?page=xml_rodina&amp;gid='.$gid
			);
			$i++;
		}

		$view->items[] = array(
			'title'	=> getMsg( 'rodinaExit' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_rodina&amp;logout'
		);

	}
	$view->currentItem = $cur;
	$view->showRss();
}
//
// ====================================
function getRodinaTest( $data = '' )
{
	// get cookies
	if( file_exists('/tmp/rodina.cookie')) $c = "Cookie: ". file_get_contents('/tmp/rodina.cookie') ."\r\n";
	else $c = '';

	if( $data === '' )
	{
		$opts = array(
			'http' => array(
				'method'  => 'GET',
				'header' => "Mozilla/5.0 (Windows NT 5.2; rv:19.0) Gecko/20100101 Firefox/19.0\r\n".$c,
		));
	}
	else
	{	
		// POST
		$postdata = http_build_query( $data );
		$opts = array(
			'http' => array(
				'method'  => 'POST',
				'header'  => "User-Agent: Mozilla/5.0 (Windows NT 5.2; rv:19.0) Gecko/20100101 Firefox/19.0\r\n". $c,
				'content' => $postdata
		));
	}
	$context = stream_context_create( $opts );
	$s = file_get_contents( 'http://www.rodina.tv/generate_test_login', false, $context );

	// set cookies
	$c = '';
	foreach( $http_response_header as $a )
	if( preg_match( '/^Set-Cookie:\s+(.*?)$/' , $a, $ss ) > 0 )
	{
		$c = $ss[1];
		break;
	}
	if( $c != '' ) file_put_contents('/tmp/rodina.cookie', $c );

	return $s;
}
//
// ------------------------------------
function getRodinaCaptchaToken()
{
	$s = getRodinaTest();
	// find captcha
	if( preg_match( '/img\s+src="data:image\/png;base64,(.*?)"/s' , $s, $ss ) === false ) return false;
	$a = base64_decode( $ss[1] );
	file_put_contents( '/tmp/www/capcha.png', $a );

	// find token
	if( preg_match( '/name="form\[_token\]"\s+value="(.*?)"/s' , $s, $ss ) === false ) return false;
	return $ss[1];
}
//
// ------------------------------------
function getRodinaTestLogin()
{
global $rodina_config;

	$s = getRodinaTest( array(
		'form[captcha]' => $_REQUEST['captcha'],
		'form[_token]' => $_REQUEST['token'],
	));

	if( preg_match( '/<tr>\s*<td>.*?<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>\s*<tr>\s*<td>.*?<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>/s' , $s, $ss ) == 0 ) return false;

	$rodina_config['login'] = $ss[1];
	$rodina_config['passwd'] = $ss[2];
	saveRodinaConfig();

	return true;
}
//
// ====================================
function xml_rodina_demo_content()
{
	if( isset( $_REQUEST['token'] ))
	{
		# try code
		if( getRodinaTestLogin() )
		{
			$s = 'ok' .PHP_EOL;
			$s .= getMosUrl().'?page=xml_rodina' .PHP_EOL;
			sendRodinaRespond( $s );
			return;
		}
		$s = getMsg('rodinaTestFail') .PHP_EOL;
	}
	else
	{
		$s = getMsg('rodinaTestCaptcha') .PHP_EOL;
	}

	# initial entry
	if(( $t = getRodinaCaptchaToken()) === false )
	{
		$s = 'ok' .PHP_EOL;
		$s .= getMosUrl().'?page=xml_rodina' .PHP_EOL;
	}
	else
	{
		$s .= $t .PHP_EOL;
		$s .= '/tmp/www/capcha.png';
	}
	sendRodinaRespond( $s );
}
//
// ====================================
function rodina_test_head()
{

?>
<link rel="stylesheet" href="/modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}
//
// ------------------------------------
function rodina_test_body()
{
global $rodina_config;

?>
<div id="container">
<div class="set_card">
<h3><?= getMsg( 'rodinaTestTitle' ) ?></h3>
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<tr><td align="center"><?php

	if( isset( $_REQUEST['act'] ))
	{
		if( getRodinaTestLogin() )
		{
			 echo getMsg( 'rodinaTestDone' );

?></td></tr>
<tr><td align="center"><?= getMsg( 'rodinaLogin') ?> <b><?= $rodina_config['login'] ?></b></td></tr>
<tr><td align="center"><?= getMsg( 'rodinaPasswd') ?> <b><?= $rodina_config['passwd'] ?></b>
<?php
		}
		else
		{
			 echo getMsg( 'rodinaTestFail' );
		}

?></td></tr>
<tr><td align="right">
<a class="buttons" href="?page=rodina_test"><?= getMsg( 'rodinaTestRepeat') ?></a>
</td></tr></table>
<?php
	}
	else
	{
		if(( $s = getRodinaCaptchaToken() ) === false )
		{
			echo "Don't get captcha";
			return;
		}

?>
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<tr><td colspan="2" align="center"><?= getMsg('rodinaTestCaptcha') ?></td></tr>
<tr><td colspan="2" align="center"><img src="capcha.png" width="180" height="80" title="captcha" /></td></tr>
<form action="?page=rodina_test&act=get" method="post">
<tr><td align="center">
<input type="text" name="captcha" size="6" />
<input type="hidden" name="token" value="<?= $s ?>" />
</td><td align="right">
<button class="buttons" type="submit"><?= getMsg('rodinaTestGen') ?></button>
</td></tr></form></table>
<?php
	}

?>
</div>
</div>
<?php

}

?>
