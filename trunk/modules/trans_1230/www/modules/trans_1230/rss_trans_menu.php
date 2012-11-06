<?php

function rss_trans_menu_content()
{
	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
		0 => array(
			'title'	=> getMsg( 'transPauseAll' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_trans&amp;act=pause_all'
		),
		1 => array(
			'title'	=> getMsg( 'transResumeAll' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_trans&amp;act=resume_all'
		),
		2 => array(
			'title'	=> getMsg( 'coreCmUList' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_trans'
		),
	);

	$view->showRss();
}

?>