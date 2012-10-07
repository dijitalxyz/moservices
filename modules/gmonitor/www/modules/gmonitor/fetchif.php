<?php
date_default_timezone_set('UTC');

header( 'Content-type: text/plain' );

echo date( 'D M j G:i:s T Y ' ) . PHP_EOL;

$ss = file( '/proc/net/dev' );
foreach( $ss as $s )
if( strpos( $s, $_SERVER['QUERY_STRING'] ) !== false ) echo $s;

?>
