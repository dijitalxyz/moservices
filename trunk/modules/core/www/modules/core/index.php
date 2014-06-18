<?php
//error_reporting( E_ERROR ); // Set E_ALL for debuging

ini_set( 'upload_tmp_dir', '/tmp' );
ini_set( 'magic_quotes_gpc', 0 );

$tz = 'UTC';
date_default_timezone_set( $tz );

// www path
$mos_url = dirname( $_SERVER['SCRIPT_NAME'] );
if( $mos_url == '.' ) $mos_url = '';
if( $mos_url == '/' ) $mos_url = '';

if(( $s = $_SERVER['HTTP_HOST'] ) == '' ) $s = '127.0.0.1';
$mos_url = 'http://'.$s.$mos_url.'/';

// sets defaults
$_default_page='services';

// initial arrays
$nav_msg = array();
$nav_pages = array ();
$nav_funcs = array ();
$nav_modules = array ();
$nav_installed = array ();
$nav_uses = array();

// get options
$nav_options = array_merge(
	parse_ini_file( $mos.'/etc/core.ini', false ),
	parse_ini_file( $mos.'/etc/pm.conf', false )
);

// get SDK version
if( file_exists( '/tmp/CGI_request' )) $nav_options['sdk_version'] = 4;
else $nav_options['sdk_version'] = 3;

// define language
$nav_lang = 'en';
if( isset( $nav_options['language'] )) $nav_lang = $nav_options['language'];

// doing non-html actions
$nav_log = false;
include( 'core_functions.php' );
include( 'core_actions.php' );

if( isset( $_REQUEST[ 'do' ] ) ) include( 'core_does.php' );

// ------------------------------------
// define pages and msg arrays
getPagesMsgs();

// ------------------------------------
// define actual page
$npage = isset( $_GET['page'] ) ? $_GET['page'] : $_default_page ;
$nav_page = isset( $nav_pages[ $npage ] ) ? $nav_pages[ $npage ] : $nav_pages[ $_default_page ];

// ------------------------------------
// load module
if( isset( $nav_page['load'] ) )
{
	$fn = 'modules/'.$nav_page['module'].'/'.$nav_page['load'];
	if( file_exists( $fn ) ) { include( $fn ); }
}

// ------------------------------------
// define type of page
$ntype = isset( $nav_page['type'] ) ? $nav_page['type'] : 'html';

// ------------------------------------
// begin of html
if( $ntype == 'html' )
{
	$nav_title = $nav_page['title'];
	// cookies
	if( function_exists( $npage.'_session' ) ) call_user_func( $npage.'_session' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>moServices - <?= $nav_title ?></title>
<?php
	$a = substr( exec( 'cat /sys/realtek_boards/cpu_id' ), -1 );
	$u = 'http://www.moservices.org/modules/icons/';
	if( $a == '2' ) $u .= 'mars';
	elseif( $a == '3' ) $u .= 'jupiter';
	elseif( $a == '4' ) $u .= 'saturn';
	else $u = '';
	if( $u != '' )
	{

?>
<link href="<?= $u ?>/apple-touch-icon.png" rel="apple-touch-icon" />
<link href="<?= $u ?>/apple-touch-icon-76x76.png" rel="apple-touch-icon" sizes="76x76" />
<link href="<?= $u ?>/apple-touch-icon-120x120.png" rel="apple-touch-icon" sizes="120x120" />
<link href="<?= $u ?>/apple-touch-icon-152x152.png" rel="apple-touch-icon" sizes="152x152" />
<?php
	}

?>
<link rel="shortcut icon" href="favicon.ico" />
<script src="modules/core/js/navbar.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/core/js/log.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="modules/core/css/main.css" type="text/css" media="screen" charset="utf-8">
<?php

	if( function_exists( $npage.'_head' ) ) call_user_func( $npage.'_head' );

?>
<body>
<div id="main_container">
<?php
	// actions
	doActions( $npage.'_actions', true );

	loadOptions();

	// define navy menu
	$nav_menu  = array (
		'main' => array (
			'type'	=> 'node',
			'title' => getMsg( 'navManage' ),
			'items'	=> array ()
		),
		'nav' => array (
			'type'	=> 'node',
			'title' => getMsg( 'navigation' ),
			'items'	=> array ()
		),
		'res' => array (
			'type'	=> 'node',
			'title' => getMsg( 'navResouces' ),
			'items'	=> array ()
		),
		'srv' => array (
			'type'	=> 'node',
			'title' => getMsg( 'navService' ),
			'items'	=> array ()
		)
	);
	$d = opendir( 'modules' );
	while ( $m = readdir( $d ) )
	{
		if($nav_modules[ $m ]['_show'])
		{
			$f = 'modules/'.$m.'/def_navy.php';
			if( file_exists( $f ) ) include( $f );
		}
	}
?>
<div id="header">
<center><table><tr><td>
<?php

	// draw menu
	drawRootMenu( $nav_menu );

?>
</td></tr></table></center></div>

<div id="content_container">
<?php

	// draw content
	if( function_exists( $npage.'_body' ) )
	{
		call_user_func( $npage.'_body' );
	}

?>
</div></div>
</body></html>
<?php
}
else {
	// non-html page

	// define RSS commandset
	$nav_cmdset = 'sdk';
	if( isset( $nav_options['commands'] )) $nav_cmdset = $nav_options['commands'];
	include( 'rss_commands.php' );

	if( $ntype == 'rss' )
	{
		// define rss skin
		$rss_skin = 'default';
		if ( isset( $nav_options['skin'] ) ) $rss_skin = $nav_options['skin'];

		$rss_images = $mos.'/www/modules/core/rss/images/';
		$rss_skin_path = $mos.'/www/modules/core/rss/skins/'.$rss_skin.'/';
		include( 'rss_skins.php' );
	}

	doActions( $npage.'_actions', false );

	if( function_exists( $npage.'_content' ) )
	{
		call_user_func( $npage.'_content' );
	}

}
?>