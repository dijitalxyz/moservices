<?php
// define pages
$nav_pages[ 'sskin'] = array (
	'title'	=> getMsg( 'sskinTitle' ),
	'module'=> 'sskin',
	'load'	=> 'sskin.php'
);

$nav_pages[ 'sskin_sets'] = array (
	'title' => getMsg( 'sskinSettings' ),
	'module'=> 'sskin',
	'load'	=> 'sskin_sets.php'
);

// RSS pages ==========================

$nav_pages[ 'rss_sskin'] = array (
	'type'	=> 'rss',
	'module'=> 'sskin',
	'load'	=> 'rss_sskin.php'
);

$nav_pages[ 'rss_sskin_actions'] = array (
	'type'	=> 'rss',
	'module'=> 'sskin',
	'load'	=> 'sskin.php'
);

$nav_pages[ 'rss_sskin_menu'] = array (
	'type'	=> 'rss',
	'module'=> 'sskin',
	'load'	=> 'sskin.php'
);

// XML pages ==========================

$nav_pages[ 'xml_sskin'] = array (
	'type'	=> 'xml',
	'module'=> 'sskin',
	'load'	=> 'sskin.php'
);

?>
