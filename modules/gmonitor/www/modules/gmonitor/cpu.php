<?php
header( "Content-type: text/plain" );
readfile( '/proc/stat' );
?>


