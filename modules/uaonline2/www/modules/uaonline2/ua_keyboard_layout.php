<?php
/*	------------------------------
	Ukraine online services 	
	Layout part keyboard module v1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include ("ua_paths.inc.php");

function layout ($lang = "eng")	
{
global $tmp;
$keyboard = array(	
	
	"rus" => array 
		(
		"1","й","ф","я","2","ц","ы","ч","3","у","в","с","4","к","а","м","5","е","п","и",
		"OK","spac","del","lang",
		"6","н","р","т","7","г","о","ь","8","ш","л","б","9","щ","д","ю","0","з","ж","х","-","э",
		"ъ","#"
		),
		
	"eng" => array
		(
		"1","q","a","z","2","w","s","x","3","e","d","c","4","r","f","v","5","t","g","b",
		"OK","spac","del","lang",
		"6","y","h","n","7","u","j","m","8","i","k",",","9","o","l",".","0","p","!","@","-","?",
		"#"
		),
	"ukr" => array
		(
		"1","й","ф","я","2","ц","і","ч","3","у","в","с","4","к","а","м","5","е","п","и",
		"OK","spac","del","lang",
		"6","н","р","т","7","г","о","ь","8","ш","л","б","9","щ","д","ю","0","з","ж","х","-","є",
		"ї","#"		
		),
	"sym" => array
		(
		"1"," ","]"," "," ","2","&"," "," "," ","3","%"," "," "," ","4","^"," "," "," ","5","*"," "," "," ",
		"OK","spac","del","lang",
		"6","("," "," "," ","7",")"," "," "," ","8","$"," "," "," ","9","_"," "," "," ","0","+"," "," "," ",
		"-","["," "," "," "
		)
		);	
	
	$temps= count($keyboard[$lang])."\n";
	foreach ($keyboard[$lang] as $item) $temps.= $item."\n";
	file_put_contents( $tmp, $temps );
			echo $tmp;
	 		
}

if(isset($_GET["lang"])) layout($_GET["lang"]);

// тут сохраняем/читаем историю ввода с клавы
if(isset($_GET["search_history"])){
	if ($_GET["search_history"]=='req'){
	echo $search_history;
	}
	if ($_GET["search_history"]=='send')	{
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('other','search_history',$_GET["sh_val"]);
		$ini->updateFile();
		unset($ini);
		echo $_GET["sh_val"];
	}

}

/*if(isset($_GET["del_ch"])){								

echo substr($_GET["del_ch"], 0, strlen($str)-1);

}*/	
?>