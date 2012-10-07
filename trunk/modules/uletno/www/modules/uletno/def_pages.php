<?php
// define pages
// = RSS pages ========================

$nav_pages['rss_uletno'] = array (
	'type'  => 'rss',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);

$nav_pages['rss_uletno_menu'] = array (
	'type'  => 'rss',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);

$nav_pages['rss_uletno_info'] = array (
	'type'  => 'rss',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);
/*
$nav_pages['rss_uletno_actions'] = array (
	'type'  => 'rss',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);
*/
// = XML pages ========================

$nav_pages['xml_uletno'] = array (
	'type'  => 'xml',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);

// = TXT pages ========================

$nav_pages['get_uletno'] = array (
	'type'  => 'txt',
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);

//
// = Functions ========================

$nav_funcs['uletnoGetPlaylist'] = array (
	'module'=> 'uletno',
	'load'	=> 'uletno.php'
);

?>
