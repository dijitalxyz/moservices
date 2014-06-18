<?php

include 'onlinerec.init.php';

//
// ------------------------------------
function onlinerecSet()
{
global $onlinerec_config;

	if( isset( $_REQUEST['quality'] ))
	 $onlinerec_config['quality'] = $_REQUEST['quality'];

	if( isset( $_REQUEST['cast'] ))
	 $onlinerec_config['cast'] = $_REQUEST['cast'];

	if( isset( $_REQUEST['vfd'] ))
	 $onlinerec_config['vfd'] = $_REQUEST['vfd'];


	// save config
	onlinerecSaveConfig();
}
//
// ====================================
function onlinerec_set_content()
{
	header( "Content-type: text/plain" );
	onlinerecSet();

	echo "ok";
}
//
// ====================================
function onlinerec_sets_actions( $act, $log )
{
	if( $act == 'set' ) onlinerecSet();
}
// ------------------------------------
function onlinerec_sets_head()
{

?>
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function onlinerecShowOption( $opt, $val, $title )
{
global $onlinerec_config;

	if( $onlinerec_config[ $opt ] == $val ) $sel = ' selected'; else $sel = '';
	echo '<option value="'. $val .'"'. $sel .'>'. getMsg( $title) ."</option>\n";
}

// ------------------------------------
function onlinerec_sets_body()
{
global $onlinerec_config;

?>
<div id="container">
<h3><?= getMsg( 'onlinerecSetsTitle' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="?page=onlinerec_sets&act=set" method="post">
<tr><td><?= getMsg( 'onlinerecQuality') ?></td>
<td><select name="quality" size=1>
<?php
	onlinerecShowOption( 'quality', 'low',    'onlinerecQualLow' );
	onlinerecShowOption( 'quality', 'middle', 'onlinerecQualMiddle' );
	onlinerecShowOption( 'quality', 'high',   'onlinerecQualHigh' );

?>
</select></td></tr>

<tr><td><?= getMsg( 'onlinerecCast') ?></td>
<td><select name="cast" size=1>
<?php
	onlinerecShowOption( 'cast', 'stream', 'onlinerecCastStream' );
	onlinerecShowOption( 'cast', 'list',   'onlinerecCastList' );

?>
</select></td></tr>

<tr><td><?= getMsg( 'onlinerecVfd') ?></td>
<td><select name="vfd" size=1>
<?php
	onlinerecShowOption( 'vfd', 'none',  'onlinerecVfdNone' );
	onlinerecShowOption( 'vfd', 'mele',  'onlinerecVfdMele' );
	onlinerecShowOption( 'vfd', 'inext', 'onlinerecVfdInext' );

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
function rss_onlinerec_sets_content()
{
	include( 'onlinerec.rss.menu.php' );
	$view = new rssOnlineRecLeftView;

	$view->position = 0;

	$view->items = array(
		array(
			'title'	=> getMsg('onlinerecQuality'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_onlinerec_quality'
		),
		array(
			'title'	=> getMsg('onlinerecCast'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_onlinerec_cast'
		),
		array(
			'title'	=> getMsg('onlinerecVfd'),
			'action'=> 'rss',
			'link'	=> getMosUrl().'?page=rss_onlinerec_vfd'
		),
	);

	$view->showRss();
}
//
// ------------------------------------
function rss_onlinerec_quality_content()
{
global $onlinerec_config;

	include( 'onlinerec.rss.menu.php' );
	$view = new rssOnlineRecLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('onlinerecQualHigh'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;quality=high'
		),
		1 => array(
			'title'	=> getMsg('onlinerecQualMiddle'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;quality=middle'
		),
		2 => array(
			'title'	=> getMsg('onlinerecQualLow'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;quality=low'
		),
	);

	$view->currentItem = 0;
	if(     $onlinerec_config['quality'] == 'middle' ) $view->currentItem = 1;
	elseif( $onlinerec_config['quality'] == 'low'    ) $view->currentItem = 2;

	$view->showRss();
}
//
// ------------------------------------
function rss_onlinerec_cast_content()
{
global $onlinerec_config;

	include( 'onlinerec.rss.menu.php' );
	$view = new rssOnlineRecLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('onlinerecCastStream'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;cast=stream'
		),
		1 => array(
			'title'	=> getMsg('onlinerecCastList'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;cast=list'
		),
	);

	$view->currentItem = 0;
	if(     $onlinerec_config['cast'] == 'list' ) $view->currentItem = 1;

	$view->showRss();
}
//
// ------------------------------------
function rss_onlinerec_vfd_content()
{
global $onlinerec_config;

	include( 'onlinerec.rss.menu.php' );
	$view = new rssOnlineRecLeftView;

	$view->position = 1;

	$view->items = array(
		0 => array(
			'title'	=> getMsg('onlinerecVfdNone'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;vfd=none'
		),
		1 => array(
			'title'	=> getMsg('onlinerecVfdMele'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;vfd=mele'
		),
		2 => array(
			'title'	=> getMsg('onlinerecVfdInext'),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=onlinerec_set&amp;vfd=inext'
		),
	);

	$view->currentItem = 0;
	if(     $onlinerec_config['vfd'] == 'mele' ) $view->currentItem = 1;
	elseif( $onlinerec_config['vfd'] == 'inext') $view->currentItem = 2;

	$view->showRss();
}

?>
