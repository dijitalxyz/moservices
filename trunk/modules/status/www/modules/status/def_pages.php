<?php
// define pages
$nav_pages[ 'processes'] = array (
	'title'	=> getMsg( 'statProcesses' ),
	'module'=> 'status',
	'load'	=> 'stat_processes.php'
);

$nav_pages[ 'statgetprocs'] = array (
	'type'  => 'ajax',
	'module'=> 'status',
	'load'	=> 'stat_processes.php'
);

?>
