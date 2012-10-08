<?php

session_start();

if( isset( $_GET['view'] ) )
{
	$nav_view = $_GET['view'];
	$_SESSION['nav_view'] = $nav_view;
}
else
{
	$nav_view = isset( $_SESSION['nav_view'] ) ? $_SESSION['nav_view'] : '';
}

// ------------------------------------
function services_head()
{

?>
<link rel="stylesheet" href="modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<script src="modules/core/js/about.js" type="text/javascript" charset="utf-8"></script>
<?php

}

function modules_head()
{
	services_head();
}
// ------------------------------------

function showModulesList( $page, $roles )
{
global $nav_pages;
global $nav_view;

	// calculate avaiable memory
	$a = exec( " echo $( df | grep ' /usr/local/etc/mos' )| cut -d' ' -f4" );
	if( $a == '' ) $a = exec( " echo $( df | grep ' /usr/local/etc' )| cut -d' ' -f4" );
	if( $a == '' ) $a = exec( " echo $( df | grep ' /data' )| cut -d' ' -f4" );

	$fmem = $a * 1024;

?>
<div id="about_container">
<a href="#" onclick="closeAbout()"><div id="about_topic"><?= getMsg('coreCm_about') ?></div></a>
<div id="about_list">
</div></div>

<div id="container">

<table border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= $nav_pages[ $page ][ 'title' ] ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<div class="mod_toolbar">
<a class="mod_button" href="?page=<?= $page ?>&view=tile" title="<?= getMsg( 'coreTileView') ?>">
<img src="modules/core/images/btn_tile_view.png" /></a>
<a class="mod_button" href="?page=<?= $page ?>&view=list" title="<?= getMsg( 'coreListView') ?>">
<img src="modules/core/images/btn_list_view.png" /></a>
<a class="mod_button" href="?page=<?= $page ?>&act=getrep" title="<?= getMsg( 'coreCmUpdList') ?>">
<img src="modules/core/images/btn_page_refresh.png" /></a>
<a class="mod_button" href="?page=update_all&ret=<?= $page ?>&act=prepare" title="<?= getMsg( 'coreCmUpdAll') ?>">
<img src="modules/core/images/btn_update_all.png" /></a>
</div></td></tr></table>

<div class="memory">
<b><?= getMsg('coreFreeMem').getHumanValue( $fmem ) ?></b>
</div>

<?php
	if ( $nav_view == 'list' )
	{

?>
<table class="mod_listview" border="0" cellspacing="0" cellpadding="8">
<thead><tr>
<td><?= getMsg( 'coreHeadName') ?></td>
<td><?= getMsg( 'coreHeadDesc') ?></td>
<td align="center"><?= getMsg( 'coreHeadSize') ?></td>
<td align="center"><?= getMsg( 'coreHeadIRev') ?></td>
<td align="center"><?= getMsg( 'coreHeadARev') ?></td>
</tr></thead>
<?php
	}

	ksort( $roles );

	end( $roles );
	$lastRole = key( $roles );

	foreach( $roles as $role => $mods )
	{
		if ( $nav_view == 'list' )
		{

?>
<thead><tr><td>&nbsp;</td><td colspan="4"><?= getMsg( 'coreRole_'.$role ) ?></td></tr></thead>
<?php
		}
		else
		{

?>
<div class="mod_role">
<div class="mod_role_topic"><?= getMsg( 'coreRole_'.$role ) ?></div>
<div class="mod_role_list">
<?php
		}

		ksort( $mods );

		end( $mods );
		$lastMod = key( $mods );

		foreach( $mods as $mod => $item )
		{
			$sts  = $item[ 'status' ];

			if ( $nav_view == 'list' )
			{

?>
<tr class="mod_list_<?php
				echo $sts;
				if(( $role == $lastRole )&&( $mod ==$lastMod )) echo ' mod_list_last';

?>">
<td><?= $mod ?></td>
<td width="100%">
<?php
			}
			else
			{

?>
<div class="mod_card">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr valign="center">
<td><img src="modules/core/images/st_<?= $sts ?>.png" title="<?= getMsg( 'coreSt_'.$sts ) ?>"/></td>
<td width="100%">
<?php
			}
			drawRootMenu( $item[ 'menu' ] );

?>
</td>
<?php
			if ( $nav_view == 'list' )
			{

?>
<td align="right"><?= getHumanValue( $item[ 'size' ] ) ?>&nbsp;</td>
<td align="center"><?= $item[ 'irev' ] ?>&nbsp;</td>
<td align="center"><?php
				if (( $item['arev'] <> '' )
				&&( $item['irev'] <> '' )
				&&( $item['irev'] <> $item['arev'] ))
				{
					echo '<b>'.$item[ 'arev' ].'</b>'; 
				}
				else echo $item[ 'arev' ];

?>&nbsp;</td></tr>
<?php
			}
			else
			{

?>
</tr></table>
<?php
				if (( $item['arev'] <> '' )
				&&( $item['irev'] <> '' )
				&&( $item['irev'] <> $item['arev'] ))
				{
					echo "<div class=\"mod_update\">\n";
					echo "<a href=\"?page=$page&mod=$mod&act=update\" title=\"".getMsg( 'coreCm_update' )."\">".$item['arev']."</a>\n";
					echo "</div>\n";
				}

?>
</div>
<?php
			}
		}

		if ( $nav_view != 'list' )
		{

?>
</div></div>
<?php
		}
	}

	if ( $nav_view == 'list' )
	{

?>
</table>
<?php
	}

?>
</div>
<?php

}

// ------------------------------------
function makeAndShowModules( $page, $isMods = false )
{
global $mos;
global $nav_modules;
global $nav_installed;

	$isAll = false;
	if( isset( $_GET['show'] ) ) $isAll = $_GET['show'] == 'all';

	$packs = array();
	$packs = parse_ini_file( $mos.'/etc/pm/packages', true );

	$roles = array();

	foreach( $nav_modules as $mod => $item )
	{
		$acts = $item['_actions'];
		$irev = $item['revision'];
		$sts  = $item['_status'];

		$role = $item['role'];

		if( $hide = ( $role == 'core' || $role == 'package' ))
		{
			$acts = array();
			$hide = !( $role == 'core' && $isMods );
		}

		$arev = '';
		if( isset( $packs[ $mod ] ))
		{
			$arev = $packs[ $mod ]['revision'];
			if( $arev != $irev ) $acts[] = 'update';
			else if( ! $isAll && $hide ) continue;
		}
		else if( ! $isAll && $hide ) continue;

		$menu = array();
		$menu[$mod] = array (
			'type'	=> 'node',
			'title'	=> $item['title'],
			'items'	=> array()
		);

		foreach( $acts as $act )
		{
			$menu[$mod]['items'][ $act ] = array (
				'type'	=> 'item',
				'title' => getMsg( 'coreCm_'.$act ),
				'url'	=> "?page=$page&mod=$mod&act=$act"
			);
		}

		if( isset( $item['config_edit'] ) || isset( $item['config_link'] ))
		{
			$cls = 'top_delim';
			if( count( $menu[$mod]['items'] ) == 0 ) $cls = '';
			if( isset( $item['config_edit'] ))
			{
				$menu[$mod]['items']['edit'] = array (
					'type'	=> 'item',
					'class'	=> $cls,
					'title' => getMsg( 'coreSettings' ),
					'url'	=> "?page=edit&mod=$mod"
				);
			}
			elseif( isset( $item['config_link'] ))
			{
				$menu[$mod]['items']['link'] = array (
					'type'	=> 'item',
					'class'	=> $cls,
					'title' => getMsg( 'coreSettings' ),
					'url'	=> $item['config_link']
				);
			}
		}
		// about
		if( ( $url = showAbout( $mod )) !== false )
		{
			$menu[$mod]['items']['about_'.$mod] = array (
				'type'	=> 'about',
				'class'	=> 'top_delim',
				'title' => getMsg( 'coreCm_about' ),
				'url'   => $url
			);
		}

		$roles[ $role ][ $mod ] = array(
			'status' => $sts,
			'arev'   => $arev,
			'irev'   => $irev,
			'size'   => $item['size']*1024,
			'menu'   => $menu
		);
	}

	// adding embedded packages
	if( $isAll )
	 foreach( $nav_installed as $mod => $item )
	  if( ! array_key_exists( $mod, $nav_modules ) )
	   if( $item['revision'] == 'emb' )
	   {
		if( isset( $packs[ $mod ]['title'] )) $title = $packs[ $mod ]['title'];
		else $title = $mod;

		$menu = array();
		$menu[$mod] = array (
			'type'	=> 'node',
			'title'	=> $title,
			'items'	=> array()
		);

		$roles[ $packs[ $mod ]['role'] ][ $mod ] = array(
			'status' => 'embeded',
			'arev'   => '',
			'irev'   => '',
			'menu'   => $menu
		);
	}

	// adding non installed modules
	if( $isMods )
	 foreach( $packs as $mod => $item )
	  if( ! array_key_exists( $mod, $nav_modules ) )
	   if( ! array_key_exists( $mod, $nav_installed ) )
	   {
		$menu = array();

		$st = isInstalable( $mod, $item );

		if((( $st == 0 )and( $item['role'] <> 'package')) or $isAll )
		{
			$menu[$mod] = array (
				'type'	=> 'node',
				'title'	=> $item['title'],
				'items'	=> array()
			);
			if(( $st == 0 )and( $item['role'] <> 'package'))
			{
				$sts = 'noinstall';
				$menu[$mod]['items']['install'] = array (
					'type'	=> 'item',
					'title' => getMsg( 'coreCm_install' ),
					'url'	=> "?page=$page&mod=$mod&act=install"
				);
				// about
				if( ( $url = showAbout( $mod )) !== false )
				{
					$menu[$mod]['items']['about_'.$mod] = array (
						'type'	=> 'about',
						'class'	=> 'top_delim',
						'title' => getMsg( 'coreCm_about' ),
						'url'   => $url
					);
				}
			}
			elseif( $st == 0 )
			{
				$sts = 'noinstall';
			}
			elseif( $st == 2 )
			{
				$sts = 'embeded';
			}
			else $sts = 'inaccess';

			$roles[ $item['role'] ][ $mod ] = array(
				'status' => $sts,
				'arev'   => $item['revision'],
				'irev'   => '',
				'size'   => $item['size']*1024,
				'menu'   => $menu
			);
		}
	}

	showModulesList( $page, $roles );
}

// ------------------------------------
function services_body()
{
	makeAndShowModules( 'services', false );
}

// ------------------------------------
function modules_body()
{
	makeAndShowModules( 'modules', true );
}

?>