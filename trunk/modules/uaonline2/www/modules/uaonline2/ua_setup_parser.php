<?php
/*	------------------------------
	Ukraine online services 2	
	setup parser module v1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");
if(isset($_GET["load"])  ){
		echo $exua_region."\n".$exua_lang;
}
if(isset($_GET["save_region"])){
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('exua_setting','region',$_GET["save_region"]);
		$ini->write('exua_setting','language',$_GET["save_language"]);
		$ini->updateFile();
		unset($ini);
}

?>