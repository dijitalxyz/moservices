<?php
//
// ------------------------------------
function iconmedia_content()
{
	$imgPath = dirname( __FILE__ ).'/';

	$view = new rssSkinHTile;

	$view->topTitle = 'IconBit media';

	$view->items = array(

		0 => array(
			'link'	=> 'http://files.iconbit.com/file/ivi/',
			'image'  => $imgPath .'ivi.png',
			'title' => 'ivi.ru'
		),
		1 => array(
			'link'	=> 'http://files.iconbit.com/file/tvigle/index.php',
			'image'  => $imgPath .'tvigle.png',
			'title' => 'Tvigle.ru'
		),
		2 => array(
			'link'	=> 'http://files.iconbit.com/file/video/russia.rss',
			'image'  => $imgPath .'russia.png',
			'title' => 'Russia.ru'
		),
		3 => array(
			'link'	=> 'http://files.iconbit.com/file/video/vesti.php',
			'image'  => $imgPath .'vesti.png',
			'title' => 'Vesti.ru'
		),
		4 => array(
			'link'	=> 'http://files.iconbit.com/file/xLive/menu.rss',
			'image'  => $imgPath .'xlive.png',
			'title' => 'xLive'
		),
	);

	$view->showRss();
}

?>
