<?php
// get current repo
$repo = array();
$repo = parse_ini_file( $mos.'/etc/pm.conf', false );

if( isset( $repo['repository'] )) $repo = $repo['repository'];
else $repo = 'http://www.moservices.org/mos3';

// load list
$repos = array();

if( is_file( $mos .'/www/modules/repos/repos.config.php' ) )
{
	include( $mos .'/www/modules/repos/repos.config.php' );
}

// check for default repo present
$add = true;
foreach( $repos as $item )
if( $item['addr'] == $repo )
{
	$add = false;
	break;
}
if( $add )
 $repos[] = array(
		'addr' => $repo,
		'desc' => 'Default repository'
 );

// current item
$cAddr = 'http://';
$cDesc = '';
$cPos  = 0;
//
// ------------------------------------
function saveReposConfig()
{
global $mos;
global $repos;

	// save config
	file_put_contents( $mos .'/www/modules/repos/repos.config.php', '<?php $repos = '.var_export( $repos, true ).'; ?>' );
}
//
// ------------------------------------
function repos_actions( $act, $log )
{
global $mos;

global $repo;
global $repos;

global $cAddr;
global $cDesc;
global $cPos;

	if( $act == 'new' )
	{
		$cPos = count( $repos );

		$repos[ $cPos ] = array(
			'addr'=> 'http://',
			'desc'=> '',
		);

		$_REQUEST['act'] = 'edit';
	}
	elseif( $act == 'add' )
	{
		if( isset( $_REQUEST['addr'] )) $cAddr = stripslashes( $_REQUEST['addr'] );
		if( isset( $_REQUEST['desc'] )) $cDesc = stripslashes( $_REQUEST['desc'] );
		if( isset( $_REQUEST['pos' ] )) $cPos  = $_REQUEST['pos'];

		if( $cPos == 'cancel' ) return;
		$brepo = '';
		if( $cPos == 'add' ) $cPos = count( $repos );
		else $brepo = $repos[ $cPos ]['addr'];

		$repos[ $cPos ] = array(
			'addr'=> $cAddr,
			'desc'=> $cDesc,
		);
		saveReposConfig();

		if( $brepo == $repo )
		{
			file_put_contents( $mos.'/etc/pm.conf', "repository=\"$cAddr\"\n" );
			$repo = $cAddr;
		}
	}
	elseif( isset( $_REQUEST['id'] ))
	{
		$id = $_REQUEST['id'];
		if( ! array_key_exists( $id, $repos )) return;

		$cAddr = $repos[ $id ]['addr'];
		$cDesc = $repos[ $id ]['desc'];
		$cPos = $id;

		if( $act == 'select' )
		{
			file_put_contents( $mos.'/etc/pm.conf', "repository=\"$cAddr\"\n" );
			$repo = $cAddr;
		}
		if( $act == 'delete' )
		{
			unset( $repos[ $id ] );
			$repos = array_values( $repos );
			saveReposConfig();
		}
	}
}

// ------------------------------------
function repos_head()
{

?>
<link rel="stylesheet" href="modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<style type="text/css">

.form-text
{
	width:100%;
}
td.form-first
{
	border: 0px;
	margin-bottom: 0px;
	padding-bottom: 2px;
}
td.form-second
{
	margin-top: 0px;
	padding-top: 0px;
}
button.left
{
	float:left;
}
div.add
{
	position:relative;
	display:block;
	float:left;

	padding: 4px 8px 6px;
	margin: 0px 2px;

	background-color:white;

	border:1px solid #ccc;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
}
</style>
<?php

}

// ------------------------------------
function repos_body()
{
global $mos;
global $repos;
global $repo;

global $cAddr;
global $cDesc;
global $cPos;

?>
<div id="container">
<h3><?= getMsg('reposTitle') ?></h3>
<?php

	if( count( $repos ) > 0 )
	{
?>
<table class="mod_listview" width="100%" border="0" cellspacing="0" cellpadding="8">
<thead><tr>
<td><?= getMsg( 'reposThAddr') ?></td>
<td width="100%"><?= getMsg( 'reposThDesc') ?></td>
</tr></thead>
<?php
		$last = count( $repos ) - 1;

		foreach( $repos as $id => $item )
		{
			if( isset( $_REQUEST['act'] )
			&& $_REQUEST['act'] == 'edit'
			&& $id == $cPos )
			{

?>
<tr>
<form action="?page=repos&act=add" method="post">
<td class="form-first" width="30%"><div class="form-item"><input class="form-text" name="addr" type="text" value="<?= $cAddr ?>" /></div></td>
<td class="form-first" width="70%"><input class="form-text" name="desc" type="text" value="<?= $cDesc ?>" /></td>
</tr>
<?php
				echo '<tr';
				if( $id == $last ) echo ' class=" mod_list_last"';
				echo '>';

?>
<td class="form-second" colspan="2">
<button class="buttons left" type="submit" name="pos" value="<?= $cPos ?>"><?= getMsg( 'coreCmSave') ?></button>
<button class="buttons left" type="submit" name="pos" value="cancel"><?= getMsg( 'coreCmCancel') ?></button>
</td>
</form>
<?php
			}
			else
			{
				$acts = array();
				if( $item['addr'] == $repo )
				{
					$l='start';
				}
				else
				{
					$l='enable';
					$acts[]='select';
				}
				$acts[]='edit';
				$acts[]='delete';

				$menu = array();
				$menu[$id] = array (
					'type'	=> 'node',
					'title'	=> $item['addr'],
					'items'	=> array()
				);
				foreach( $acts as $i => $act )
				{
					if( $act == 'edit' && $i > 0 ) $cls = 'top_delim';
					else $cls = '';

					$menu[$id]['items'][ $act ] = array (
						'type'	=> 'item',
						'class'	=> $cls,
						'title' => getMsg( 'reposCm_'.$act ),
						'url'	=> "?page=repos&act=$act&id=$id"
					);
				}
				echo '<tr class="mod_list_'. $l;
				if( $id == $last ) echo ' mod_list_last';
				echo '">';

?>
<td>
<?php

				drawRootMenu( $menu );

?></td>
<td><?= $item['desc'] ?></td>
<?php
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

?>
<a href="?page=repos&act=new" title="<?= getMsg( 'reposCm_add') ?>"><div class="add">+</div></a>
</div>
<?php

}

?>