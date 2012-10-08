<?php

function rss_trans_actions_content()
{
	if( ! isset( $_REQUEST['id'] )) return;
	$id = $_REQUEST['id'];

	$trans_dir = dirname( __FILE__ );
	require_once( $trans_dir . '/TransmissionRPC.class.php' );

	$rpc = new TransmissionRPC();
	$rpc->username = 'torrent';
	$rpc->password = '1234';
//	$rpc->debug = true;
//	header( "Content-type: text/plain" );

	try
	{

		$ids = array( intval( $id ) );
		$fields = array(
			'id',
			'status',
			'name',
		);

		$result = $rpc->get( $ids, $fields );
  
	} catch (Exception $e)
	{
		die('[ERROR] ' . $e->getMessage() . PHP_EOL);
	}

//	print "GET\n";
//	print_r( $result );

	if( count( $result->arguments->torrents ) == 0 ) return;

	$st = $result->arguments->torrents[0]->status;

	// --------------------------
	// send RSS
	//
	include( 'modules/core/rss_view_popup.php' );

	$view = new rssSkinPopupView;

	$view->topTitle = $result->arguments->torrents[0]->name;

	if( $st == 16 )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transResume' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=resume"
		);
	}
	else
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transPause' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=pause"
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'transDelete' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=delete"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transRemove' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=remove"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transAnno' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=anno"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transCheck' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=check"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}

?>