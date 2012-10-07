<?php
// define pages
$nav_pages[ 'services'] = array (
	'title'	=> getMsg( 'coreServices' ),
	'module'=> 'core',
	'load'	=> 'mod_services.php'
);

$nav_pages[ 'modules'] = array (
	'title'	=> getMsg( 'coreModules' ),
	'module'=> 'core',
	'load'	=> 'mod_services.php'
);

$nav_pages[ 'sets'] = array (
	'title'	=> getMsg( 'coreSettings' ),
	'module'=> 'core',
	'load'	=> 'mod_sets.php'
);

$nav_pages[ 'delete'] = array (
	'title'	=> getMsg( 'coreSettings' ),
	'module'=> 'core',
	'load'	=> 'mod_sets.php'
);

$nav_pages[ 'info'] = array (
	'title'	=> getMsg( 'coreInfo' ),
	'module'=> 'core',
	'load'	=> 'mod_info.php'
);

$nav_pages[ 'reboot'] = array (
	'title'	=> getMsg( 'coreReboot' ),
	'module'=> 'core',
	'load'	=> 'mod_reboot.php'
);

$nav_pages[ 'frame'] = array (
	'module'=> 'core',
	'load'	=> 'mod_frame.php'
);

$nav_pages[ 'edit'] = array (
	'title'	=> getMsg( 'coreSettings' ),
	'module'=> 'core',
	'load'	=> 'mod_edit.php'
);

$nav_pages[ 'update_all'] = array (
	'title'	=> getMsg( 'coreUpdateAll' ),
	'module'=> 'core',
	'load'	=> 'mod_update_all.php'
);

$nav_pages[ 'test'] = array (
	'title'	=> 'Test',
	'module'=> 'core',
	'load'	=> 'mod_test.php'
);

// = ajax pages ===+===================

$nav_pages[ 'about'] = array (
	'type'	=> 'ajax',
	'module'=> 'core',
	'load'	=> 'mod_about.php'
);

// = XML pages ========================

$nav_pages[ 'xml_services'] = array (
	'type'  => 'xml',
	'module'=> 'core',
	'load'	=> 'xml_services.php'
);

$nav_pages[ 'xml_modules'] = array (
	'type'  => 'xml',
	'module'=> 'core',
	'load'	=> 'xml_services.php'
);

$nav_pages[ 'xml_menu'] = array (
	'type'  => 'xml',
	'module'=> 'core',
	'load'	=> 'rss_menu.php'
);

$nav_pages['xml_keyboard'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_keyboard.php'
);

// = RSS pages ========================

$nav_pages['rss_services'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_services.php'
);

$nav_pages['rss_services_actions'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_services.php'
);

$nav_pages['rss_services_menu'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_services.php'
);

$nav_pages['rss_menu'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_menu.php'
);

$nav_pages['rss_keyboard'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_keyboard.php'
);

$nav_pages['rss_keyboard_menu'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_keyboard.php'
);

$nav_pages['rss_player'] = array (
	'type'  => 'rss',
	'module'=> 'core',
	'load'	=> 'rss_player.php'
);

?>
