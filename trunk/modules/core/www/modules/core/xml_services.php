<?php

doActions( 'core_actions', false );

// ------------------------------------
header( "Content-type: text/plain" );

if( $npage == 'xml_modules' )
{
	$ptitle = getMsg( 'coreModules' );
	$isMod = true;
}
else
{
	$ptitle = getMsg( 'coreServices' );
	$isMod = false;
}

// load options
loadOptions();

$packs = array();
$packs = parse_ini_file( $mos.'/etc/pm/packages', true );

$mods = array();

foreach( $nav_modules as $mod => $item )
{
	$irev = $item['revision'];
	$sts  = $item['_status'];
	$role = $item['role'];

	$hide = $role == 'core' || $role == 'package';

	$arev = '';
	if( isset( $packs[ $mod ] ))
	{
		$arev = $packs[ $mod ]['revision'];
		if(( $arev == $irev ) && $hide ) continue;
	}
	else if( $hide ) continue;

	$mods[ $mod ] = array(
		'status' => $sts,
		'title'  => $item['title']
	);
}
if( $isMod )
{
	// adding non installed modules

	foreach( $packs as $mod => $item )
	if( ! array_key_exists( $mod, $nav_modules ) )
	{
		if( $item['role'] == 'package') continue;
		$st = isInstalable( $mod, $item );
		if( $st != 0 ) continue;

		$mods[ $mod ] = array(
			'status' => 'noinstall',
			'title'  => $item['title']
		);
	}
}
ksort( $mods );

$s = '';
$s .= "moServices - $ptitle\n" ;
$s .= count( $mods ) . PHP_EOL;

foreach( $mods as $mod => $item )
{
	$sts  = $item[ 'status' ];

	$s .= $item['title'] . "\n";
	$s .= "$mos/www/modules/core/images/st_$sts.png\n";
	$s .= getMosUrl() ."?page=rss_services_actions&mod=$mod&ret=$npage\n";
}

if( isset( $_REQUEST['debug'] ))
{
	echo $s;
}
else
{
	file_put_contents( '/tmp/put.dat', $s );
	echo "/tmp/put.dat";
}

exit;

?>