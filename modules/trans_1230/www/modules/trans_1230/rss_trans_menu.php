<?php

function rss_trans_menu_content()
{
	$trans_dir = dirname( __FILE__ );
	require_once( $trans_dir . '/TransmissionRPC.class.php' );

	$rpc = new TransmissionRPC('http://localhost:9091/transmission/rpc', 'torrent', '1234');
	//$rpc->username = 'torrent';
	//$rpc->password = '1234';

	try
	{
		$result = $rpc->sget();
  
	} catch (Exception $e) {
		die('[ERROR] ' . $e->getMessage() . PHP_EOL);
	}

	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items[] = array(
		'title'	=> getMsg( 'transPauseAll' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_trans&amp;act=pause_all'
	);
	$view->items[] = array(
		'title'	=> getMsg( 'transResumeAll' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_trans&amp;act=resume_all'
	);

	if( $result->arguments->alt_speed_enabled == 1 )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transAltSpeedDisable' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_trans&amp;act=alt_speed&amp;disable'
		);
	}
	else
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transAltSpeedEnable' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_trans&amp;act=alt_speed&amp;enable'
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmUList' ),
		'action'=> 'ret',
		'link'	=> getMosUrl().'?page=xml_trans'
	);

	$view->showRss();
}

?>