<?php

doActions( 'core_actions', false );

// ------------------------------------
if( $npage == 'xml_modules' )
{
	$ptitle = getMsg( 'coreModules' );
	$isAll = true;
}
else
{
	$ptitle = getMsg( 'coreServices' );
	$isAll = false;
}

// load options
loadOptions();

$mods = array();

foreach( $nav_modules as $mod => $item )
{
	$role = $item['role'];
	if(( ! $isAll )and( $role == 'core' )) continue;

	$irev = $item['revision'];
	$sts  = $item['_status'];

	$mods[ $mod ] = array(
		'status' => $sts,
		'title'  => $item['title']
	);
}
if( $isAll )
{
	$packs = array();
	$packs = parse_ini_file( $mos.'/etc/pm/packages', true );

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

file_put_contents( '/tmp/put.dat', $s );

header( "Content-type: text/plain" );
echo "/tmp/put.dat";

exit;

?>