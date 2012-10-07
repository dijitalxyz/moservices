<?php

function rss_player_content()
{
global $nav_funcs;

//header( "Content-type: text/plain" );

	$items = array();

	if( isset( $_REQUEST['mod'] ))
	{
		$mod = $_REQUEST['mod'];
		$func = $mod .'GetPlaylist';
		if( ! isset( $nav_funcs[ $func ] )) return;

		$n = 'modules/'. $nav_funcs[ $func ]['module'] .'/'. $nav_funcs[ $func ]['load'];
		if( ! file_exists( $n ) ) return;
		include( $n );
		if( ! function_exists( $func ) ) return;
		$items = call_user_func( $func );
	}
	else
	{
		$items[] = array(
			'link'  => $_REQUEST['url'],
			'title' => $_REQUEST['title'],
			'img'   => $_REQUEST['img']
		);
	}
	if( count( $items ) == 0 ) return;

	include( 'rss_view_player.php' );

	$view = new rssSkinPlayerView;

	$view->items = $items;
	$view->cItem = 0;
	if( isset( $_REQUEST['id'] )) $view->cItem = $_REQUEST['id'];

	$view->showRss();
}

?>
