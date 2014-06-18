<?php
/*	------------------------------
	Ukraine online services 	
	Layout part keyboard module v1.1
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include ("ua_paths.inc.php");
header( "Content-type: text/html; charset=utf-8" );
function layout ($lang = "eng")	
{
global $tmp;
$keyboard = array(	
	
	"rus" => array 
		(
		"1","й","ф","я","э","2","ц","ы","ч","ъ","3","у","в","с","/","4","к","а","м","\\","5","е","п","и","?",
		"OK","spac","del","lang","clear",
		"6","н","р","т",".","7","г","о","ь","@","8","ш","л","б","*","9","щ","д","ю","-","0","з","ж","х","_"
		),
		
	"eng" => array
		(
		"1","q","a","z","?","2","w","s","x",".","3","e","d","c","/","4","r","f","v","\\","5","t","g","b","(",
		"OK","spac","del","lang","clear",
		"6","y","h","n",")","7","u","j","m","@","8","i","k",",","*","9","o","l","+","-","0","p","%","$","_"
		),
	"ukr" => array
		(
		"1","й","ф","я","є","2","ц","і","ч","ї","3","у","в","с","/","4","к","а","м","\\","5","е","п","и","?",
		"OK","spac","del","lang","clear",
		"6","н","р","т",".","7","г","о","ь","@","8","ш","л","б","*","9","щ","д","ю","-","0","з","ж","х","_"
		),
);	
	
	$temps= count($keyboard[$lang])."\n";
	foreach ($keyboard[$lang] as $item) $temps.= $item."\n";
	file_put_contents( $tmp, $temps );
			echo $tmp;
	 		
}

if(isset($_GET["lang"])) layout($_GET["lang"]);

// считывает историю поиска из ini
function get_history($opt)
{
	$name = array();
	$ini = new TIniFileEx($confs_path.'ua_settings.conf');
	$cnt = $ini->read('search_history','count','0');
	for ($i=0; $i<=$cnt-1; $i++) 
		$name[]=$ini->read('search_history',$i,'');
	unset ($ini);
	if ($opt=="last") return $name[0];
	if ($opt=="list") return $name;
}
//сохраняет историю поиска 
function save_ini($cnt,$name)
{
	$ini = new TIniFileEx($confs_path.'ua_settings.conf');
	$ini->write('search_history','count',$cnt);	
	for ($i=0; $i<=$cnt-1; $i++)
		$ini->write('search_history',$i,$name[$i]);
	$ini->updateFile();
	unset ($ini);
}

// подготавливает историю поиска к сохранению (кол-во итемов для сохранения 10)
function save_history($search)
{
	$name = get_history("list");
	$idx = array_search($search,$name);
	$cnt =  count($name);
	if ($idx===false)
	{
		if ($cnt == 10) 
			{
				unset($name[$cnt-1]);
				$name=array_values($name);
				$cnt--;
			}
		array_unshift($name, $search);
		$cnt++;
		save_ini($cnt,$name);
	}else
	{
		unset($name[$idx]);
		$name=array_values($name);
		array_unshift($name, $search);
		save_ini($cnt,$name);

	}
}

// формат вывода итема для клавиатуры
function prepare_item($item)
{
	$res = iconv_strlen($item,'UTF-8')."\n";
	for ($i=0; $i<=iconv_strlen($item,'UTF-8'); $i++)
	$res.=iconv_substr($item,$i,1,'UTF-8')."\n";
	return $res;
}

// тут сохраняем/читаем историю ввода с клавы
if(isset($_GET["search_history"]))
{
	if ($_GET["search_history"]=='req')
	{
		echo prepare_item(get_history("last"));
	}
	if ($_GET["search_history"]=='send')	
	{
		save_history($_GET["sh_val"]);
		echo $_GET["sh_val"];
	}
	if ($_GET["search_history"]=='popup')
	{
		$name=get_history("list");
		$cnt = count($name);
		echo $cnt."\n";
		for ($i=0; $i<=$cnt-1; $i++) echo $name[$i]."\n";
	}
	if ($_GET["search_history"]=='prepare')
	{
		$names=get_history("list");
		echo prepare_item($names[($_GET["val"])]);
	}
}


?>