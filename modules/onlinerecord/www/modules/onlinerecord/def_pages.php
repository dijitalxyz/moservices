<?php
//
// define pages

// = HTLM pages =======================
$nav_pages['onlinerec_sets'] = array (
	'type'  => 'html',
	'title' => getMsg( 'onlinerecSetsTitle' ),
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

// = RSS pages ========================

$nav_pages['rss_lealta'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.rss.php'
);

$nav_pages['rss_onlinerec_channels'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.rss.channels.php'
);

$nav_pages['rss_onlinerec_player'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.rss.player.php'
);

$nav_pages['rss_onlinerec_sets'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

$nav_pages['rss_onlinerec_quality'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

$nav_pages['rss_onlinerec_cast'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

$nav_pages['rss_onlinerec_vfd'] = array (
	'type'  => 'rss',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

// = ajax pages ========================

$nav_pages['onlinerec_channels'] = array (
	'type'  => 'txt',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.php'
);

$nav_pages['onlinerec_list'] = array (
	'type'  => 'txt',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.php'
);

$nav_pages['onlinerec_get'] = array (
	'type'  => 'txt',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.php'
);

$nav_pages['onlinerec_get_epg'] = array (
	'type'  => 'txt',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.php'
);

$nav_pages['onlinerec_set'] = array (
	'type'  => 'txt',
	'module'=> 'onlinerecord',
	'load'	=> 'onlinerec.sets.php'
);

?>
