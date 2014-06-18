<?php
// define pages
$nav_pages[ 'capture'] = array (
	'title'	=> getMsg( 'captureTitle' ),
	'module'=> 'capture',
	'load'	=> 'capture.php'
);

$nav_pages[ 'get_capture'] = array (
	'type'	=> 'ajax',
	'module'=> 'capture',
	'load'	=> 'capture.php'
);

$nav_pages[ 'send_command'] = array (
	'type'	=> 'ajax',
	'module'=> 'capture',
	'load'	=> 'capture.php'
);

?>
