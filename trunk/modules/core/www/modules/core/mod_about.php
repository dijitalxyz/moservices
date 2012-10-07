<?php

function about_content()
{
global $mos;
global $nav_lang;

	if( ! isset( $_REQUEST['mod'] )) return;
	$mod = $_REQUEST['mod'];

	$fa = $mos.'/etc/about/'.$mod.'.'.$nav_lang.'.html';
	if( file_exists( $fa ))
	{
		readfile( $fa );
		return;
	}

	$fa = $mos.'/etc/about/'.$mod.'.en.html';
	if( file_exists( $fa ))
	{
		readfile( $fa );
		return;
	}

	$fa = $mos.'/etc/about/'.$mod.'.ru.html';
	if( file_exists( $fa ))
	{
		readfile( $fa );
	}
	else echo getMsg( 'coreNoAbout' );
}

?>
