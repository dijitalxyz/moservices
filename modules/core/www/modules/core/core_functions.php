<?php

// ------------------------------------
function getMosUrl()
{
global $mos_url;

	return $mos_url;
}

// ------------------------------------
function showAbout( $mod )
{
global $mos;
global $nav_lang;

	$fa = $mos.'/etc/about/'.$mod.'.'.$nav_lang.'.html';
	if( file_exists( $fa )) return "?page=about&mod=$mod";
	$fa = $mos.'/etc/about/'.$mod.'.en.html';
	if( file_exists( $fa )) return "?page=about&mod=$mod";
	$fa = $mos.'/etc/about/'.$mod.'.ru.html';
	if( file_exists( $fa )) return "?page=about&mod=$mod";
	return false;
}

// ====================================
function getHumanValue( $val )
{
	if( $val < 1024 ) return sprintf( "%01u", $val ) . getMsg( 'coreHumanB' );
	if( $val < 1048576 ) return sprintf("%01.1f", $val/1024 ) . getMsg( 'coreHumanKB' );
	if( $val < 1073741824 ) return sprintf("%01.1f", $val/1048576 ) . getMsg( 'coreHumanMB' );
	return sprintf("%01.2f", $val/1073741824 ) . getMsg( 'coreHumanGB' );
}

// ------------------------------------
function getHumanPeriod( $val )
{
	if( $val < 60 ) return sprintf("%01u", $val ) . getMsg( 'coreHumanSec' );
	if( $val < 3600 )	// less hour
	{
		$m = floor( $val / 60 );
		$s = floor( $val - $m * 60 );
		return $m . getMsg( 'coreHumanMin' ) . $s . getMsg( 'coreHumanSec' );
	}
	if( $val < 86400 )	// less day
	{
		$h = floor( $val / 3600 );
		$m = floor( ( $val - $h * 3600 )/60 );
		return $h . getMsg( 'coreHumanHour' ) . $m . getMsg( 'coreHumanMin' );
	}
	if( $val < 604800 )	// less week
	{
		$d = floor( $val / 86400 );
		$h = floor( ( $val - $h * 86400 )/3600 );
		return $d . getMsg( 'coreHumanDay' ) . $h . getMsg( 'coreHumanHour' );
	}
	$w =  floor( $val / 604800 );
	return $w . getMsg( 'coreHumanWeek' );
}

// ====================================
function getPagesMsgs()
{
global $nav_lang;
global $nav_msg;
global $nav_pages;
global $nav_funcs;

	$nav_msg = array();
	$nav_pages = array ();
	$nav_funcs = array ();

	$d = opendir( 'modules' );
	while ( $mod = readdir( $d ) )
	{
		if( $m == '.' or $m == '..' ) continue;

		$w = "modules/$mod";
		if ( is_dir( $w ) )
		{
			$f = "$w/lang_$nav_lang.php";
			if( file_exists( $f ) )
			{
				include( $f );
			}
			else
			{
				$f = "$w/lang_en.php";
				if( file_exists( $f ) ) include( $f );
			}

			$f = "$w/def_pages.php";
			if( file_exists( $f ) ) include( $f );
		}
	}
}

// ====================================
function isInstalable( $mod, $opt )
{
global $mos;
global $nav_options;

	# enviroment
	if( isset( $opt['env'] ))
	if( $opt['env'] != $nav_options['enviroment'] ) return 1;

	# etc/pm/check
	$f = $mod;
	if( isset( $opt['check'] )) $f = $opt['check'];
	$f = $mos.'/etc/pm/check/'.$f;
	$s= explode( ' ', $f );

	if( ! file_exists( $s[0] )) return 0;
	if( ! is_executable( $s[0] )) return 0;
	exec( "$f $mod", $ls, $st );
	return $st;
}

// ====================================
// loading module option
//
function loadModuleOptions( $mod, &$opt )
{
global $mos;
global $nav_uses;

	if( ! isset( $opt['revision'] )) return false;
	if( $opt['revision'] == 'emb' ) return false;

	if( ! isset( $opt['role'] )) return false;

	$sts = 'install';
	$show = true;
	$act = array();

	if( $opt['role'] == 'package' ) $show = false;

	if( isset( $opt['init'] ) )
	{
		$init = $mos.'/etc/init/'.$opt['init'];
		if( is_executable( $init ) )
		{
			$sts = 'enable';
			$s = exec( $init.' status' );
			if( strpos( $s, 'stop'  ) > 0 )
			{
				$sts = 'stop';
				$show=false;
				$act[] = 'start';
			}
			elseif( strpos( $s, 'run' ) > 0 )
			{
				$sts = 'start';
				$act[] = 'stop';
				$act[] = 'restart';
			}

			$act[] = 'disable';
		}
		else {
			$sts = 'disable';
			$show=false;
			$act[] = 'enable';
		}
	}
	if ( ! isset( $nav_uses[ $mos ] )) $act[] = 'delete';

	if( $show )
	if( isset( $opt['actions'] ))
	{
		$a = explode( ' ', $opt['actions'] );
		foreach( $a as $s ) $act[] = $s;
	}

	$opt['_status']  = $sts;
	$opt['_show']    = $show;
	$opt['_actions'] = $act;

	return true;
}

// ------------------------------------
// loading modules options and statuses
//
function loadOptions( )
{
global $mos;
global $nav_uses;
global $nav_modules;
global $nav_installed;

	$nav_modules = array ();

	$nav_uses = array ();
	$nav_uses = parse_ini_file( $mos.'/etc/pm/uses', false );

	$nav_installed = array ();
	$nav_installed = parse_ini_file( $mos.'/etc/pm/installed', true );

	foreach( $nav_installed as $mod => $item )
	 if( loadModuleOptions( $mod, $item ) !== false )
	  $nav_modules[$mod] = $item;

	ksort( $nav_modules );
}

// ====================================
function getMsg( $msg )
{
global $nav_msg;
	if( isset( $nav_msg[ $msg ] ) ) return $nav_msg[ $msg ];
	return $msg;
}

// ====================================
// Draw menus
//
function drawMenuItem ( $node, $isRoot = false )
{
	if ( count( $node ) < 1 ) return;

	foreach( $node as $id => $item )
	{
		$url = '#';
		if( isset( $item['url'] ) ) $url = $item['url'];
		$class = '';
		if( isset( $item['class'] )) $class = 'class="'.$item['class'].'"';

		if( ( $item[ 'type' ] == 'node' ) && ( count( $item['items'] ) > 0 ))
		{
			echo "<li><a $class href=\"$url\" onclick=\"mopen('$id')\" onmouseout=\"mclosetime()\">".$item['title']."</a>\n";
			if ( count( $item[ 'items' ] ) > 0 ) {
				echo "<div class=\"menu\" id=\"$id\" onmouseover=\"mcancelclosetime()\" onmouseout=\"mclosetime()\">\n";
				drawMenuItem( $item[ 'items' ] );
				echo "</div>\n";
			}
			echo "</li>\n";
		}
		else {
			if( $isRoot ) echo "<li>";

			if( $item['type'] == 'about' )
			{
				echo "<a $class href=\"#\" onclick=\"openAbout('$url')\">".$item['title']."</a>\n";
			}
			elseif( $item['type'] == 'node' )
			{
				echo $item['title'];
			}
			else
			 echo "<a $class href=\"$url\" target=\"_self\">".$item[ 'title' ]."</a>\n";

			if( $isRoot ) echo "</li>\n";
		}
	}
}

function drawRootMenu ( $node )
{
	echo "<ul class=\"navbarmenu\">\n";
	drawMenuItem ( $node, true );
	echo "</ul>\n";
}

// ====================================
function doCommand ( $command, $log = false )
{
global $nav_log;

	if ( $log )
	{
		if( ! $nav_log )
		{
			ob_implicit_flush( true );

?>
<div id="log_container">
<a href="#" onclick="log_look()"><div id="log_topic"><?= getMsg( 'coreLog') ?></div></a>
<div id="log_list"><pre>
<?php
			$nav_log = true;
		}
		echo "<b>$command</b>\n";
		system( $command.' 2>&1', $status );
	}
	else
	{
		exec( $command.' 2>&1', $ls, $status );
	}
	return $status;
}

// ------------------------------------
function doActions ( $act_func, $log )
{
global $nav_log;

	if ( ! isset( $_REQUEST['act'] ) ) return;

	$act = $_REQUEST['act'];

	if( function_exists( $act_func ) )
	{
		call_user_func( $act_func, $act, $log );
	}
	elseif( function_exists( 'core_actions' ) )
	{
		core_actions( $act, $log );
	}

	if( $nav_log )
	{

?>
</pre></div></div>
<script> log_look(); </script>
<?php
		ob_implicit_flush( false );
	}
}

?>