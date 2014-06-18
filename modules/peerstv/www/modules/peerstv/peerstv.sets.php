<?php

include 'peerstv.init.php';

//
// ------------------------------------
function peerstvSet()
{
global $peerstv_config;

	if( isset( $_REQUEST['cast'] ))
	 $peerstv_config['cast'] = $_REQUEST['cast'];

	if( isset( $_REQUEST['vfd'] ))
	 $peerstv_config['vfd'] = $_REQUEST['vfd'];


	// save config
	peerstvSaveConfig();
}
//
// ====================================
function peerstv_set_content()
{
	header( "Content-type: text/plain" );
	peerstvSet();

	echo "ok";
}
//
// ====================================
function peerstv_sets_actions( $act, $log )
{
	if( $act == 'set' ) peerstvSet();
}
// ------------------------------------
function peerstv_sets_head()
{

?>
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function peerstvShowOption( $opt, $val, $title )
{
global $peerstv_config;

	if( $peerstv_config[ $opt ] == $val ) $sel = ' selected'; else $sel = '';
	echo '<option value="'. $val .'"'. $sel .'>'. getMsg( $title) ."</option>\n";
}

// ------------------------------------
function peerstv_sets_body()
{
global $peerstv_config;

?>
<div id="container">
<h3><?= getMsg( 'peerstvSetsTitle' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="?page=peerstv_sets&act=set" method="post">

<tr><td><?= getMsg( 'peerstvCast') ?></td>
<td><select name="cast" size=1>
<?php
	peerstvShowOption( 'cast', 'patch', 'peerstvCastPatch' );
	peerstvShowOption( 'cast', 'list',  'peerstvCastList' );

?>
</select></td></tr>

<tr><td><?= getMsg( 'peerstvVfd') ?></td>
<td><select name="vfd" size=1>
<?php
	peerstvShowOption( 'vfd', 'none',  'peerstvVfdNone' );
	peerstvShowOption( 'vfd', 'mele',  'peerstvVfdMele' );
	peerstvShowOption( 'vfd', 'inext', 'peerstvVfdInext' );

?>
</select></td></tr>

<tr><td /><td align="right">
<button class="buttons" type="submit"><?= getMsg( 'coreCmSave') ?></button>
</td></tr></form></table>
</div>
</div>
<?php

}
//
// ====================================
function rss_peerstv_sets_content()
{
	include( 'peerstv.rss.menu.php' );
	$view = new rssPeerstvLeftView;

	$view->position = 0;

	$view->items = array(
		array(
			'title'	=> getMsg('peerstvCast'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_peerstv_cast'
		),
		array(
			'title'	=> getMsg('peerstvVfd'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_peerstv_vfd'
		),
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_peerstv_cast_content()
{
global $peerstv_config;

	include( 'peerstv.rss.menu.php' );
	$view = new rssPeerstvLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('peerstvCastPatch'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=peerstv_set&amp;cast=patch'
		),
		1 => array(
			'title'	=> getMsg('peerstvCastList'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=peerstv_set&amp;cast=list'
		),
	);

	$view->currentItem = 0;
	if(     $peerstv_config['cast'] == 'list' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ------------------------------------
function rss_peerstv_vfd_content()
{
global $peerstv_config;

	include( 'peerstv.rss.menu.php' );
	$view = new rssPeerstvLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('peerstvVfdNone'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=peerstv_set&amp;vfd=none'
		),
		1 => array(
			'title'	=> getMsg('peerstvVfdMele'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=peerstv_set&amp;vfd=mele'
		),
		2 => array(
			'title'	=> getMsg('peerstvVfdInext'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=peerstv_set&amp;vfd=inext'
		),
	);

	$view->currentItem = 0;
	if(     $peerstv_config['vfd'] == 'mele' ) $view->currentItem = 1;
	elseif( $peerstv_config['vfd'] == 'inext') $view->currentItem = 2;

	$view->showRss();
}

?>
