<?php
// define pages
$nav_pages[ 'smarttool'] = array (
	'title'	=> getMsg( 'smartToolTitle' ),
	'module'=> 'smarttool',
	'load'	=> 'smarttool.php'
);

$nav_pages[ 'get_smart'] = array (
	'type'	=> 'ajax',
	'module'=> 'smarttool',
	'load'	=> 'smarttool.php'
);

?>
