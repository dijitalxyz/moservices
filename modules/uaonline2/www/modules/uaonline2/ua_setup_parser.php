<?php
/*	------------------------------
	Ukraine online services 2	
	setup parser module v1.4
	------------------------------
	Created by Sashunya 2014	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");
if(isset($_GET["oper"])  )
{
		if ($_GET["oper"] == "load")		
			echo $exua_region."\n".$exua_lang."\n".$uakino_decode."\n".$player_style."\n".$built_in_keyb."\n".$exua_quality."\n".$position."\n".$screensaver."\n".$exua_posters;
		if ($_GET["oper"] == "save")
		{
			$set = file("/tmp/ua_set");
			$ini = new TIniFileEx($confs_path.'ua_settings.conf');
			$ini->write('exua_setting','region',$set[0]);
			$ini->write('exua_setting','language',$set[1]);
			$ini->write('uakino','decode_strings',$set[2]);
			$ini->write('player','style',$set[3]);
			$ini->write('other','built_in_keyb',$set[4]);
			$ini->write('exua_setting','quality',$set[5]);
			$ini->write('other','save_position',$set[6]);
			$ini->write('other','screen_saver',$set[7]);
			$ini->write('exua_setting','posters',$set[8]);
			$ini->updateFile();
			unset($ini);
		}
}
?>