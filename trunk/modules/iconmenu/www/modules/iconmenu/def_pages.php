<?php
// define pages
// = Web pages ========================

$nav_pages[ 'iconmenu_sets'] = array (
	'title' => getMsg( 'Settings' ),
	'module'=> 'iconmenu',
	'load'	=> 'sets.php'
);
// = RSS pages ========================

$nav_pages['im_screensaver'] = array (
	'type'  => 'rss',
	'module'=> 'iconmenu',
	'load'	=> 'screensaver.php'
);

// = Other pages ======================

$nav_pages[ 'im_status'] = array (
	'type'	=> 'ajax',
	'module'=> 'iconmenu',
	'load'	=> 'status.php'
);

$nav_pages[ 'im_random'] = array (
	'type'	=> 'ajax',
	'module'=> 'iconmenu',
	'load'	=> 'random.php'
);

$nav_pages[ 'iconmenu_geticon'] = array (
	'type'	=> 'ajax',
	'module'=> 'iconmenu',
	'load'	=> 'sets.php'
);

?>
