<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

if( isset( $_GET["name"] ) ) 
{
	unlink( $_GET["name"] );
}

echo "done";
?>
