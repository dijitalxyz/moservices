<?php
//
// ------------------------------------
function getIcy( $url )
{

	$a = parse_url( $url );
	$sh = $a['host'];
	$sp = empty($a['port']) ? 80  : $a['port'];
	$sa = empty($a['path']) ? '/' : $a['path'];

	$fp = @fsockopen($sh, $sp, $errno, $errstr, 2);
	if (!$fp) return '';

	fputs($fp, "GET $sa HTTP/1.0\r\n");
	fputs($fp, "Host: $sh\r\n");
	fputs($fp, "Accept: */*\r\n");
	fputs($fp, "Icy-MetaData: 1\r\n");
	fputs($fp, "Connection: close\r\n\r\n");

	// read header metadata
	$c = '';
	$s = '';
	while ($c != Chr(255) ) //END OF MPEG-HEADER
	{
		if ( @feof( $fp ) || @ftell( $fp ) > 14096 ) break;	//Spezial, da my-Mojo am Anfang leere Zeichen hat

		$c = @fread( $fp, 1 );
		$s .= $c;
	}

	$int = 32768;
	if( preg_match( '/icy-metaint:(\d+)/' , $s, $ss ) > 0 ) $int = $ss[1];

	$s = '';
	// get metadata from icy stream
	while ( ! @feof( $fp ) && @ftell( $fp ) < ( $int + 512 ) )
	{
		$c = @fread( $fp, 256 );
		$s .= $c;
	}
	fclose($fp);

	$tag = '';
	if( preg_match( "/StreamTitle=\'(.*?)\';/" , $s, $ss ) > 0 ) $tag = $ss[1];

	$tag = trim( preg_replace( '/\[.*?\]/', '', $tag ));

if( isset( $_REQUEST['debug'] )) echo "stream_tag=$tag\n";

	return $tag;
}
//
// ====================================
function shoutcast_tags_content()
{
global $mos;    
	if( ! isset( $_REQUEST['url'] )) return;
	$url = urldecode( $_REQUEST['url'] );

	header('Content-type: text/plain');

	$a = parse_url( $url );
	$sh = $a['host'];
	$sp = empty($a['port']) ? 80  : $a['port'];

	$opts = array(
		'http' => array(
			'method'  => 'GET',
			'header' => "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0\r\n"
				. "Accept: text/html\r\n"
				. "Connection: close\r\n"
				. "\r\n"
		)
	);
	$context = stream_context_create($opts);
	$s = @file_get_contents( 'http://'. $sh .':'. $sp .'/7.html', false, $context);

if( isset( $_REQUEST['debug'] )) echo "s=$s\n";

	$tag = '';
	if( $s === false ) $tag = getIcy( $url );
	elseif( preg_match( '/<body>(.*?)<\/body>/', $s, $ss ) > 0 )
	{
		$a = explode( ',', $ss[1] );
		if( isset( $a[6] )) $tag = $a[6];

if( isset( $_REQUEST['debug'] )) echo "http_tag=$tag\n";

	}
	if( $tag == '' ) $tag = getIcy( $url );

	echo $tag;
}

?>
