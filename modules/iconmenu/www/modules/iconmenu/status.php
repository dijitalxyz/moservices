<?php
//
// ====================================
function im_status_content()
{
	header( "Content-type: text/plain" );

global $mos;

	$t = 0;
	$f = 0;

	$d = NULL;
	$conf = $mos.'/iconmenu/iconmenu.conf';
	if ( file_exists( $conf ))
	{
		$s = file_get_contents( $conf );
		$x = new SimpleXMLElement($s);
		$d = realpath( trim( (string)$x->hddinfo ));
	}
	if( is_dir( $d ))
	{
		$f = disk_free_space( $d );
		$t = disk_total_space( $d );
	}

	// calculate total, detect USB and SATA
	$isSata = false;
	$isUsb  = false;

	$tf = 0;
	$tt = 0;
	$ds = array();

	$d = opendir( '/tmp/ramfs/volumes' );
	while ( $m = readdir( $d ) )
	{
		if( ! is_link( '/tmp/ramfs/volumes/'. $m )) continue;

if( isset( $_REQUEST['debug'] )) echo "drive=$m\n";

		$l = file( '/tmp/ramfs/labels/'. $m );
		$s = trim($l[2]);

if( isset( $_REQUEST['debug'] )) echo "device=$s\n";

		if( $s == 'usb' ) $isUsb = true;
		elseif( $s == 'sata' ) $isSata = true;

		$m = realpath( '/tmp/ramfs/volumes/'. $m );
		if( ! in_array( $m, $ds ))
		{
			$ds[] = $m;
			$tf += disk_free_space( $m );
			$tt += disk_total_space( $m );
		}
	}
	if( $t == 0 )
	{
		$t = $tt;
		$f = $tf;
	}

if( isset( $_REQUEST['debug'] )) var_dump($isSata);
if( isset( $_REQUEST['debug'] )) var_dump($isUsb);

	// send respond
	$s  = time() .PHP_EOL;

	if( $t != 0 )
	 $s .= getHumanValue( $f ).'/'.getHumanValue( $t ).getMsg( 'imFree' );
	$s .= PHP_EOL;

	if( $isSata )
	 $s .= '/usr/local/etc/mos/iconmenu/images/icons/icon_03.png';
	else
	 $s .= '/usr/local/etc/mos/iconmenu/images/icons/icon_18.png';
	$s .= PHP_EOL;

	if( $isUsb )
	 $s .= '/usr/local/etc/mos/iconmenu/images/icons/icon_09.png';
	else
	$s .= '/usr/local/etc/mos/iconmenu/images/icons/icon_21.png';
	$s .= PHP_EOL;

	if( isset( $_REQUEST['debug'] ))
	{
		echo $s;
	}
	else
	{
		file_put_contents( '/tmp/im_status.dat', $s );
		echo "/tmp/im_status.dat";
	}
}

?>