<?php
//
// define pages

// = HTLM pages =======================

$nav_pages['peerstv_sets'] = array (
	'type'  => 'html',
	'title' => getMsg( 'peerstvSetsTitle' ),
	'module'=> 'peerstv',
	'load'	=> 'peerstv.sets.php'
);

// = RSS pages ========================

$nav_pages['rss_peerstv'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.rss.php'
);

$nav_pages['rss_peerstv_player'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.rss.player.php'
);

$nav_pages['rss_peerstv_channels'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.rss.channels.php'
);

$nav_pages['rss_peerstv_sets'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.sets.php'
);

$nav_pages['rss_peerstv_cast'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.sets.php'
);

$nav_pages['rss_peerstv_vfd'] = array (
	'type'  => 'rss',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.sets.php'
);

// = txt pages ========================

$nav_pages['peerstv_channels'] = array (
	'type'  => 'txt',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.php'
);

$nav_pages['peerstv_list'] = array (
	'type'  => 'txt',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.php'
);

$nav_pages['peerstv_get_epg'] = array (
	'type'  => 'txt',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.php'
);

$nav_pages['peerstv_get'] = array (
	'type'  => 'txt',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.php'
);

$nav_pages['peerstv_set'] = array (
	'type'  => 'txt',
	'module'=> 'peerstv',
	'load'	=> 'peerstv.sets.php'
);

?>
