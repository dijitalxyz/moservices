<?php

// load list
$mounts = array();
$m_conf_file = $mos.'/etc/mounts.list';
if( file_exists( $m_conf_file ))
{
	$ss = file( $m_conf_file );
	foreach( $ss as $s )
	{
		$s = trim( $s );
		if( $s == '' ) continue;
		$m = explode( "\t", $s );
		$mounts[ $m[0] ] = array(
			'device'=> $m[1],
			'fs'    => $m[2],
			'opts'  => $m[3]
		);
	}
}

// load options
$m_options = array(
	'wAddr'  => 0,
	'wMount' => 0,
	'nTries' => 5
);
$m_opts_file = $mos.'/etc/mounts.ini';
if( file_exists( $m_opts_file )) $m_options = parse_ini_file( $m_opts_file, false );

// current point
$cPoint  = '/tmp/ramfs/volumes/mymount';
$cDevice = '';
$cFS     = 'nfs';
$cOpts   = '';

// ------------------------------------
function isDirMounted( $d )
{
	if( exec( "mount | grep $d" ) == '' ) return false;
	return true;
}

// ------------------------------------
function saveMountsConfig()
{
global $m_conf_file;
global $mounts;

	$s = '';
	foreach( $mounts as $m => $a )
		$s .= "$m\t".$a['device']."\t".$a['fs']."\t".$a['opts']."\n";

	file_put_contents( $m_conf_file, $s );
}
//
// ====================================
function mounts_actions( $act, $log )
{
global $mos;

global $mounts;
global $m_options;
global $m_opts_file;

global $cPoint;
global $cDevice;
global $cFS;
global $cOpts;

	if( $act == 'add' )
	{
		if( isset( $_REQUEST['point'  ] )) $cPoint  = stripslashes( $_REQUEST['point'] );
		if( isset( $_REQUEST['device' ] )) $cDevice = stripslashes( $_REQUEST['device'] );
		if( isset( $_REQUEST['fs'     ] )) $cFS     = $_REQUEST['fs'];
		if( isset( $_REQUEST['options'] )) $cOpts   = stripslashes( $_REQUEST['options'] );

		if( $cPoint and $cDevice and $cFS )
		{
			$mounts[ $cPoint ] = array(
				'device'=> $cDevice,
				'fs'    => $cFS,
				'opts'  => $cOpts
			);
			saveMountsConfig();
		}
	}
	elseif( isset( $_REQUEST['point'] ))
	{
		$p = stripslashes( $_REQUEST['point'] );
		if( ! array_key_exists( $p, $mounts )) return;

		$cPoint  = $p;
		$cDevice = $mounts[ $p ]['device'];
		$cFS     = $mounts[ $p ]['fs'];
		$cOpts   = $mounts[ $p ]['opts'];

		if( $act == 'delete' )
		{
			unset( $mounts[ $p ] );
			saveMountsConfig();
			if( is_dir( $p ))
			{
				if( isDirMounted( $p )) doCommand( "umount '$p'", true );
				 doCommand( "rmdir '$p'", true );
			}
		}
		elseif( $act == 'mount' )
		{
			if( $c = $cOpts ) $c = " -o $c";
			if( ! is_dir( $p )) doCommand( "mkdir -p '$p'", true );
			if( is_dir( $p )) doCommand( "mount -t $cFS$c '$cDevice' '$cPoint'", true );
		}
		elseif( $act == 'umount' )
		{
			if( is_dir( $p ))
			{
				if( isDirMounted( $p )) doCommand( "umount '$p'", true );
				 doCommand( "rmdir '$p'", true );
			}
		}
	}
}

// ------------------------------------
function mounts_head()
{

?>
<link rel="stylesheet" href="modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function mounts_body()
{
global $mos;

global $mounts;
global $m_options;

global $cPoint;
global $cDevice;
global $cFS;
global $cOpts;

?>
<div id="container">
<table class="ssk_top" border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= getMsg('mountTitle') ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<div class="mod_toolbar">
<a class="mod_button" href="?page=mount_sets" title="<?= getMsg( 'coreSettings') ?>">
<img src="modules/core/images/btn_settings.png" /></a>
</div></td>
</tr></table>
<?php

	if( count( $mounts ) > 0 )
	{
?>
<table class="mod_listview" border="0" cellspacing="0" cellpadding="8">
<thead><tr>
<td><?= getMsg( 'mountThPoint') ?></td>
<td width="100%"><?= getMsg( 'mountThDevice') ?></td>
<td><?= getMsg( 'mountThFS') ?></td>
<td><?= getMsg( 'mountThOpts') ?></td>
</tr></thead>
<?php

		end( $mounts );
		$last = key( $mounts );

		foreach( $mounts as $m => $s )
		{
			$acts = array();
			if( is_dir( $m ))
			{
				if( isDirMounted( $m ) )
				{
					$l='start';
					$acts[]='umount';
				}
				else
				{
					$l='enable';
					$acts[]='mount';
				}
			}
			else
			{
				$l='stop';
				$acts[]='mount';
			}
			$acts[]='edit';
			$acts[]='delete';

			$menu = array();
			$menu[$m] = array (
				'type'	=> 'node',
				'title'	=> $m,
				'items'	=> array()
			);
			foreach( $acts as $act )
				$menu[$m]['items'][ $act ] = array (
					'type'	=> 'item',
					'title' => getMsg( 'coreCm_'.$act ),
					'url'	=> "?page=mounts&act=$act&point=$m"
				);
?>
<tr class="mod_list_<?php
				echo $l;
				if( $m == $last ) echo ' mod_list_last';

?>">
<td><?php
			drawRootMenu( $menu );

?></td>
<td width="100%"><?= $s['device'] ?></td>
<td><?= $s['fs'] ?></td>
<td><?= $s['opts'] ?></td>
</tr>
<?php
		}
		echo "</table><br />\n";
	}

?>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<thead><tr><td colspan="2"><?= getMsg( 'mountEdTitle') ?></h4></tr></thead>
<form action="?page=mounts&act=add" method="post">
<tr><td><?= getMsg( 'mountPoint') ?></td>
<td><input name="point" type="text" value="<?= $cPoint ?>" size=40 /></td></tr>
<tr><td><?= getMsg( 'mountDev') ?></td>
<td><input name="device" type="text" value="<?= $cDevice ?>" size=40 /></td></tr>
<tr><td><?= getMsg( 'mountFS') ?></td>
<td><select name="fs" size=1>
<?php
	exec( "cat /proc/filesystems | sed 's/nodev//;s/	//'", $ss, $st );
	foreach( $ss as $s )
	{
		$sel = '';
		if( $s == $cFS ) $sel = ' selected';
		echo "<option value=\"$s\"$sel>$s</option>\n";
	}

?>
</select></td></tr>
<tr><td><?= getMsg( 'mountOpts') ?></td>
<td><input name="options" type="text" value="<?= $cOpts ?>" size=40 />&nbsp;</td></tr>
<tr><td /><td align="right">
<button class="buttons" type="submit"><?= getMsg( 'coreCmSave') ?></button>
</td></tr></form></table>
</div>
</div>
<?php

}

?>