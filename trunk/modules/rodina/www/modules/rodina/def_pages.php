<?php
// define pages

$nav_pages['rodina_test'] = array (
	'title'	=> getMsg( 'rodinaTestTitle' ),
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

// = RSS pages ========================

$nav_pages['rss_rodina'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_main.php'
);

$nav_pages['rss_rodina_login'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_login.php'
);

$nav_pages['rss_rodina_demo'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_demo.php'
);

$nav_pages['rss_rodina_message'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_message.php'
);

$nav_pages['rss_rodina_menu'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

$nav_pages['rss_rodina_player'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_player.php'
);

$nav_pages['rss_rodina_channels'] = array (
	'type'  => 'rss',
	'module'=> 'rodina',
	'load'	=> 'rodina_channels.php'
);

// = XML pages ========================

$nav_pages['xml_rodina'] = array (
	'type'  => 'xml',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

$nav_pages['xml_rodina_demo'] = array (
	'type'  => 'xml',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

// = TXT pages ========================

$nav_pages['get_rodina'] = array (
	'type'  => 'txt',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

$nav_pages['get_rodina_epg'] = array (
	'type'  => 'txt',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

$nav_pages['get_rodina_token'] = array (
	'type'  => 'txt',
	'module'=> 'rodina',
	'load'	=> 'rodina.php'
);

?>
