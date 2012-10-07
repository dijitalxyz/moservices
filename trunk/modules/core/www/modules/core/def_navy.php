<?php
// define menus

$nav_menu [ 'main' ] [ 'items' ] [ 'services' ] = array (
	'type'	=> 'item',
	'title' => getMsg( 'coreServices' ),
	'url'	=> '?page=services'
);

$nav_menu [ 'main' ] [ 'items' ] [ 'modules' ] = array (
	'type'	=> 'item',
	'title' => getMsg( 'coreModules' ),
	'url'	=> '?page=modules'
);

$nav_menu [ 'main' ] [ 'items' ] [ 'settings' ] = array (
	'type'	=> 'item',
	'title' => getMsg( 'coreSettings' ),
	'url'	=> '?page=sets'
);

$nav_menu [ 'srv' ] [ 'items' ] [ 'info' ] = array (
	'type'	=> 'item',
	'title' => getMsg( 'coreInfo' ),
	'url'	=> '?page=info'
);

$nav_menu [ 'srv' ] [ 'items' ] [ 'reboot' ] = array (
	'type'	=> 'item',
	'title' => getMsg( 'coreReboot' ),
	'url'	=> '?page=reboot'
);

function makeNavyItem( $mod, $item, $part, $nav )
{
global $nav_menu;
global $nav_modules;

	if ( isset( $item[ $part.'_title' ] ) )
	{
		if (( $item['_status'] <> 'stop' )
		&&( $item['_status'] <> 'disable' ))
		{
			if( isset( $item[ $part.'_frame'] ))
			$nav_menu [ $nav ] [ 'items' ] [ $mod ] = array (
				'type'	=> 'item',
				'title' => $item[ $part.'_title'],
				'url'	=> "?page=frame&mod=$mod"
			);
			if( isset( $item[ $part.'_link'] ))
			$nav_menu [ $nav ] [ 'items' ] [ $mod ] = array (
				'type'	=> 'item',
				'title' => $item[ $part.'_title'],
				'url'	=> $item[ $part.'_link']
			);
		}
	}
}

// create dynamic links from options Navy and Resource
foreach( $nav_modules as $mod => $item )
{
	makeNavyItem( $mod, $item, 'navy', 'nav' );
	makeNavyItem( $mod, $item, 'content', 'res' );
}

?>
