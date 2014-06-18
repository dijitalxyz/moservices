<?php

// define RSS menu
$nav_rss['services'] = array (
	'page'	=> 'rss_services',
	'icon'  => 'rss/images/moServices.png',
	'title' => getMsg( 'coreServices' )
);

$nav_rss["aaa"] = array (
	"module"=> "core",
	"rss"	=> "rss_file:///usr/local/bin/scripts/menu.rss",
	"icon"  => "rss/images/OnlineMedia.png",
	"title" => "Online Media"
);

?>
