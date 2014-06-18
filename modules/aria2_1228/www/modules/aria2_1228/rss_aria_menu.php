<?php

function rss_aria_menu_content()
{
	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items[] = array(
		'title'	=> getMsg( 'ariaPauseAll' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_aria&amp;act=pause_all'
	);
	$view->items[] = array(
		'title'	=> getMsg( 'ariaResumeAll' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_aria&amp;act=resume_all'
	);

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmUList' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_aria'
	);

	$view->showRss();
}

?>