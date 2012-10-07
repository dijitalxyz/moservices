<?php

function test_actions()
{

}
/*
fuction quoteName( $s )
{
	return addcslashes( $path, "'\"\` ?[]()$" );
}


$filesize_cache = array();

function _filesize( $path )
{
	$dir = dirmane( $path );
	$name = basename( $path );

	if( ! isset( $filesize_cache[ $dir ] ) {

		// caching
		$a = array();
		exec( 'ls -l '. quoteName( $dir ), $a );

		foreach( $a as $s )
		{
			

lrwxrwxrwx    1 root     root            18 Oct  2 15:27 libcrypto.so.1 -> libcrypto.so.1.0.0



	$fp = fopen($path, 'r');
	$f = @fstat($fp);
	fclose($fp);

	if( $f === false ) return false;

	$s = (float)( 512 * $f['blocks'] );

//	$n = addcslashes( $path, "'\"\` ?[]()$" );
//	$s = exec( "stat -c '%s' $n" );
	return $s;
}
*/

function test_body()
{
global $nav_modules;

	echo "<div id=\"container\">\n";
//	phpInfo();

echo "<pre>\n";

print_r( $nav_modules );
/*

echo "<table>\n";

$path = '/tmp/usbmounts/sdb1/Videos/Movies';

foreach (scandir($path) as $name)
{
	if ($name != '.' && $name != '..')
	{
		$f = $path.DIRECTORY_SEPARATOR.$name;
		$s =  (float)_filesize($f);
//		$s =  (float)filesize($f);
		echo "<tr><td>$name</td><td align=right>$s</td><td align=right>".getHumanValue( $s )."</td></tr>\n";
	}
}
echo "</table>\n";
*/

echo "</pre>\n";

	echo "</div>\n";

}
?>