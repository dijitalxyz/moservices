<?php
/*	------------------------------
	Ukraine online services 	
	WEB simple file manager module v1.1
	------------------------------
	Created by Sashunya 2012
	wall9e@gmail.com			
	Some code was used from Kelkos
	------------------------------ */

include("ua_paths.inc.php");
//=============================================================
if (isset($_REQUEST["download_file"]))
{
	$file=$_REQUEST["download_file"];
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Content-Length: ' . filesize($file));
	readfile($file);
	exit;
	
}

//чтение каталога
function ReadFolder($catalog) 
{
	$dirlist=array();
	if ($dir = @opendir($catalog)) 
	{ 
		while (($file = readdir($dir)) !== false) 
			{ 
				if ($file != '..' && $file != '.') $dirlist[]= $file;
			}             
		closedir($dir); 
	}
  return $dirlist;
}

//функция сортирует массив $db
function my_sort ($db)
{
	if (count ($db)>1)
	{
		for ($u=0; $u < count ($db); $u++) 
		for ($i=0; $i < (count ($db)-1); $i++)
		{
			if ($db[$i]>$db[$i+1])
			{
				$cash=$db[$i];
				$db[$i]=$db[$i+1];
				$db[$i+1]=$cash;
			}

		}
	}
return $db;
}
//Функция возвращает массив, выведенный из строки $str с разделитем полей $razdel
function get_datastring ($str, $razdel) 
{
	$f=array();
    if ($str<>'') $f = explode($razdel,$str);
	return $f;
}
function normal_size($size) 
{
//Функция выводин нормальный размер файлов типа 100KB
	$kb = 1024;         // Kilobyte
	$mb = 1024 * $kb;   // Megabyte
	$gb = 1024 * $mb;   // Gigabyte
	$tb = 1024 * $gb;   // Terabyte
	if($size < $kb) 
	{
		return $size." B";
	}
	else if($size < $mb) 
	{
		return round($size/$kb,2)." KB";
	}
	else if($size < $gb) 
	{
		return round($size/$mb,2)." MB";
	}
	else if($size < $tb) 
	{
		return round($size/$gb,2)." GB";
	}
	else 
	{
		return round($size/$tb,2)." TB";
	}
}

// удалить папку
function delete_dir($file) 
{
	umask (000);
	@chmod($file,0777);
	if (is_dir($file)) 
	{
		$handle = opendir($file); 
		while($filename = readdir($handle)) 
		{
			if ($filename != "." && $filename != "..") 
			{
				delete_dir ($file."/".$filename);
			}
		}
		closedir($handle);
		rmdir($file);
	} else 
	{
		unlink($file);
	}
}

//функция возвращает тип файла по его расширению
function get_file_type($filename) 
{
	$filename=strtolower ($filename);
	ereg( ".*\.([a-zA-z0-9]{0,5})$", $filename, $regs );
	$f_ext = $regs[1];
	$types['image'] = array ('jpg', 'gif','png', 'swf', 'bmp');
	$types['script'] = array ('html', 'htm', 'php', 'php3');
	$types['document'] = array ('txt', 'doc');
	$types['music'] = array ('mp3', 'mpeg3');
	$types['archives'] = array ('zip', 'rar', 'arj');
	$types['programm'] = array ('com', 'exe');
	foreach ($types as $k => $v) 
	{
		if (in_array($f_ext, $v)) 
			{
			return $k;
			}
	}
	return 'unknown';
}

$_REQUEST['add_path']=ereg_replace ("[..]", '', $_REQUEST['add_path']);
if ($_REQUEST['add_path']<>'') $_REQUEST['add_path'].='/';
$full_path=$DOCUMENT_ROOT.$main_offset.'/'.$_REQUEST['add_path'];


$_REQUEST['add_path']=ereg_replace ("//", '/', $_REQUEST['add_path']);

//----------------обработка action---------
if ($_REQUEST['file_name']<>'') $_REQUEST['file_name']=$_REQUEST['file_name'];
$_REQUEST['file_name']=ereg_replace ("[.]", ',', $_REQUEST['file_name']);
$_REQUEST['file_name']=ereg_replace (",,", '', $_REQUEST['file_name']);
$_REQUEST['file_name']=ereg_replace (",", '.', $_REQUEST['file_name']);
$type=get_file_type($full_path.$_REQUEST['file_name']);
//-----------------------------------------


if ($_REQUEST['operation']=='choose_dir') 
{
		header("Location: ".$ua_path_link2.'ua_web_fav.php?operation=ua_set&set_path='.$_REQUEST['set_path']);
		exit;
}

//удалить выбранные
if ($_REQUEST['operation']=='delete_dir') 
{

	for($i=0; $i<count ($_REQUEST['del']); $i++)
	{
		$_REQUEST['del'][$i]=ereg_replace ("[.]", ',', $_REQUEST['del'][$i]);
		$_REQUEST['del'][$i]=ereg_replace (",,", '', $_REQUEST['del'][$i]);
		$_REQUEST['del'][$i]=ereg_replace (",", '.', $_REQUEST['del'][$i]);
		if ($_REQUEST['del'][$i]<>'') delete_dir ($DOCUMENT_ROOT.$main_offset.'/'.$_REQUEST['add_path'].$_REQUEST['del'][$i]);
	}
		header("Location: ".$_SERVER["PHP_SELF"].'?add_path='.$_REQUEST['add_path']);
		exit;
}

//создать каталог
if ($_REQUEST['operation']=='create_dir') 
{
	$_REQUEST['dir_name']=ereg_replace ("[.]", '', $_REQUEST['dir_name']);
	$_REQUEST['dir_name']=ereg_replace ("/", '', $_REQUEST['dir_name']);
	umask (000);
	mkdir ($DOCUMENT_ROOT.$main_offset.'/'.$_REQUEST['add_path'].$_REQUEST['dir_name'], intval ("0777", 8));
	chmod ($DOCUMENT_ROOT.$main_offset.'/'.$_REQUEST['add_path'].$_REQUEST['dir_name'], intval ("0777", 8));
	header("Location: ".$_SERVER["PHP_SELF"].'?add_path='.$_REQUEST['add_path']);
	exit;
}




$path_db=get_datastring ($_REQUEST['add_path'], "/");
$dir=ReadFolder($full_path);
//разделяем список на файлы и каталоги и сортируем каждый массив отдельно
$dir_db=array ();
$file_db=array ();
for($i=0; $i<count ($dir); $i++) 
{
	$dir_count=count ($dir_db);
	$file_count=count ($file_db);
	if (is_dir($full_path.$dir[$i])) { $dir_db[$dir_count]=$dir[$i]; }
    else { $file_db[$file_count]=$dir[$i]; }
}

//--сортировка--
$dir_db=my_sort ($dir_db);
$file_db=my_sort ($file_db);

$form_name='view_dir';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Simple File Manager</title>
<link rel="shortcut icon" href="<?=$ua_path_link2."images/uaonline2.ico"?>" />
<link rel="stylesheet" type="text/css" href="<?=$ua_path_link2."css/ua_web_fav.css"?>">
</head>
<script language="javascript">
function sel_all(){
 if( !document.<?=$form_name ?>.cheks ) return;
 if( !document.<?=$form_name ?>.cheks.length )
	document.<?=$form_name ?>.cheks.checked = document.<?=$form_name ?>.cheks.checked ? false : true;
 else
	for(var i=0;i<document.<?=$form_name ?>.cheks.length;i++)
		document.<?=$form_name ?>.cheks[i].checked = document.<?=$form_name ?>.cheks[i].checked ? false : true;
}
var alist = new Array ('toplist','bottomlist','MailboxName');
function changeListFolder(nameList) {
 for(var i=0;i<3;i++)
	if( alist[i] != nameList )
		eval( "document.form1."+alist[i]+".selectedIndex = document.form1."+nameList+".selectedIndex" );
}

</script>
<body>




<form enctype="multipart/form-data" action="<?=$PHP_SELF ?>" method="GET" name=<?=$form_name ?>>
	<input name="add_path" type="hidden" value="<?=$_REQUEST['add_path'] ?>">
	<input type="hidden" name="operation" value="">	
	<div class="header">
	<table class="setup_table" border=0 width="57%">
			<tr>
				<td class="fileman_head_td"><center><a href="javascript:sel_all()" title="Выделить всё/снять выделение"><b><font size="3" color="#000000">*</font></a></td>
				<td width="100%" colspan="3"  class="fileman_head_td fileman_head_td_main_col">&nbsp;<?
				//выводим путь
				$offs='';
				echo '<a class="fileman_head_a" href="'.$_SERVER["PHP_SELF"].'?add_path="> .. </a>/';
				for($i=0; $i<count($path_db); $i++) if ($path_db[$i]<>'')
				{
					echo '<a class="fileman_head_a" href="'.$_SERVER["PHP_SELF"].'?add_path='.$offs.$path_db[$i].'">'.$path_db[$i].'</a>/';
					if ($i==count($path_db)-2) $back=$offs;
					$offs.=$path_db[$i].'/';
				}
				?>
				<script type="text/javascript"> tmp_folder='<?="/".$offs?>'; </script> 
				<a class="fileman_head_back" href="<?=$_SERVER["PHP_SELF"]."?add_path=".$back?>"><img class="fileman_head_back_img" src="<?=$ua_path_link2.'images/ua_web_back.png'?>" title="назад"></a>
			 </td>
	
			</tr>
	</table>
	</div>
	<div class="content">	
	<table class="setup_table" border=0 width="57%">
			
<?
for($i=0; $i<count ($dir_db); $i++) 
{
//выводим каталоги
?>
	<tr class="fileman_dir_td" onmouseover="this.className='fileman_dir_td_hl';" onmouseout="this.className='fileman_dir_td';">
		<td><input type="checkbox"  id="cheks" name="del[]" value="<?=$dir_db[$i] ?>"></td>
		<td width="77%"><a class="fileman_dir_a" href="<?=$_SERVER["PHP_SELF"]?>?add_path=<?=$_REQUEST['add_path'].$dir_db[$i] ?>"><?=$dir_db[$i] ?></a></td>
		<td width="23%">Папка</td>
	</tr>
<?
}
//выводим файлы 
/*
for($i=0; $i<count ($file_db); $i++) 
{
?>
	<tr class="fileman_file_td" onmouseover="this.className='fileman_dir_td_hl';" onmouseout="this.className='fileman_file_td';">
		<td><input type="checkbox"  id="cheks" name="del[]" value="<?=$file_db[$i] ?>"></td>  
		<td width="77%"><a class="fileman_file_a" href="<?=$_SERVER["PHP_SELF"].'?download_file=/'.$main_offset.$_REQUEST['add_path'].$file_db[$i] ?>"><?=$file_db[$i] ?></a></td>
		<td width="23%">&nbsp;<? 
			echo normal_size (filesize ($full_path.$file_db[$i])); 
	?>	</td>
	</tr>
<?
}
*/
?> 
</table>
</div>
<div class="footer">
<table border="0">
	<tr>
	<td>
		<button class="button green small" onclick="with (this) {form.operation.value = 'create_dir'; form.submit ()}" title="Создать новую папку">Создать папку</button>
	</td>
	<td>
			Имя папки: <input type="text" name="dir_name" size="30" value="<? echo $_REQUEST['dir_name']; ?>">
	</td>
	<td>
		<button class="button green small" onclick="isConfirmed = confirm('Удалить выбранные папки?'); if(isConfirmed) {{ with (this) {form.operation.value = 'delete_dir'; form.submit ()}}} else {window.location.href='<?=$_SERVER["PHP_SELF"].'?add_path='.$_REQUEST['add_path']?>';}" title="Удалить папку">Удалить папку</button>		
	</td>
	</tr>
</table>
</div>


</form>
</body>
</html>