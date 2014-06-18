<?php
//
// ====================================
function im_random_content()
{
	header( "Content-type: text/plain" );


	if( ! isset( $_REQUEST['min'] )) $mn = 0.0;
	else $mn = ( $_REQUEST['min'] );

	if( ! isset( $_REQUEST['max'] )) $mx = 100.0;
	else $mx = ( $_REQUEST['max'] );

	echo round( ( lcg_value() * ( $mx - $mn ) ) + $mn , 4 );
}
?>