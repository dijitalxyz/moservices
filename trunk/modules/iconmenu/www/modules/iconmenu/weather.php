<?php
header( "Content-type: text/plain" );

if( ! isset( $_REQUEST['url'] )) exit;

$s = file_get_contents( urldecode( $_REQUEST['url'] ));
if( $s === false ) exit;

$x = new SimpleXMLElement($s);

$s  = (string)$x->local->adminArea .', '. (string)$x->local->country .PHP_EOL; // cityArea + cityCountry
$s .= (string)$x->local->city .PHP_EOL;				// cityName
$s .= (string)$x->currentconditions->temperature .' °'. (string)$x->units->temp .PHP_EOL; // cityTemp + cityUnit
$s .= (string)$x->currentconditions->weathertext .PHP_EOL;	// cityCond
$s .= '/usr/local/etc/mos/iconmenu/images/icons/'. (string)$x->currentconditions->weathericon .'.png' .PHP_EOL; // cityIcon

$g = file_get_contents( "http://ip-api.com/xml" );
if( $g === false ) $s .= 'no IP';
else
{
	$x = new SimpleXMLElement($g);
	$s .= (string)$x->query .PHP_EOL;
}

file_put_contents( '/tmp/im_weather.dat', $s );
echo "/tmp/im_weather.dat";

?>