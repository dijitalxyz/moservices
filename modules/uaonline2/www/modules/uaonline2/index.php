<?php
/*	------------------------------
	Ukraine online services 	
	WEB interface module v2.6
	------------------------------
	Created by Sashunya 2012
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include("ua_paths.inc.php");
ob_implicit_flush( false );
// это читает постеры
if ($_REQUEST["get_poster"])
{
	$url=$_REQUEST["get_poster"];
	header("Content-Type: image/jpeg");
	readfile($url);	
	exit;
}
// исправляет линк для постера, если он локальный
function check_poster($poster)
{
global $ua_path_link3;
$pattern="/http:\/\/127.0.0.1\//";
return preg_replace($pattern,$ua_path_link3,$poster);
}
// прочитать закладки из файла
function get_fav($name_fav)
{
	if (file_exists($name_fav))
	{
		$ua_fav=file($name_fav,FILE_IGNORE_NEW_LINES);
		$fav_count=$ua_fav[0];
		$count=0;
		$c=1;
		$fav_arr=array();
		while ($count!=$fav_count)
		{
			$name=$ua_fav[$c]; $c++;
			$link=$ua_fav[$c]; $c++;
			$poster=$ua_fav[$c]; $c++;
			$type=$ua_fav[$c]; $c++;
			$site=$ua_fav[$c]; $c++;
			$fav_arr[]=array("name"=>$name,"link"=>$link,"poster"=>$poster,"type"=>$type,"site"=>$site);
			$count++;
		}
	} else $fav_arr=array();
return $fav_arr;
}

// сохранить закладки в файл
function save_fav($ua_fav)
{
global $ua_path;
global $ua_favorites_filename;
	$tmps="";
	$count=0;
	foreach ($ua_fav as $id=>$val)
	{
		foreach ($val as $id2=>$val2)
		{
			if ($id2=="name") $name=$val2;
			if ($id2=="poster") $poster=$val2;
			if ($id2=="link") $link=$val2;
			if ($id2=="type") $type=$val2;
			if ($id2=="site") $site=$val2;
			
		}
		$tmps.=$name."\n".$link."\n".$poster."\n".$type."\n".$site."\n";
		$count++;
	}
	$tmps=$count."\n".$tmps;
	file_put_contents($ua_favorites_filename,$tmps);
}

if ($_REQUEST["operation"]=="fileman")
{
	if ($_REQUEST["type"]=="download")
	{
		if (!file_exists($d_path)) $fileman_path="/tmp/ramfs/volumes"; 
		else $fileman_path=$_REQUEST["d_path"];
	}
	if ($_REQUEST["type"]=="wget")
	{
		if (!file_exists($d_path)) $fileman_path="/";
		else $fileman_path=$_REQUEST["d_path"];
	
	}
 


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
	<iframe id="fileman_frame" class="cont_frame" src="<?=$ua_path_link2."ua_web_fm.php?add_path=".$fileman_path?>" width="100%" height="100%" scrolling="auto" frameborder="0">
	</iframe>
</body>
</html>

<?
exit;
}

// текст для справки
if ($_REQUEST["operation"]=="about")
{
?>
	<h3 align="center">КРАТКАЯ СПРАВКА ПО ИСПОЛЬЗОВАНИЮ РЕДАКТОРА ЗАКЛАДОК</h3>
	<h3 align="center">UAOnline 2</h3>
	<p>
		<b>ДОБАВИТЬ ЗАКЛАДКУ</b> - Добавляет новую закладку в начале списка избранного. После этого 
			нужно ввести ссылку закладки и нажать на <b>ОБНОВИТЬ</b><br>
		<b>СОЗДАТЬ РЕЗЕРВНУЮ КОПИЮ</b> - Создает на ПК резервную копию закладок<br>
		<b>ВОССТАНОВИТЬ ИЗ РЕЗЕРВНОЙ КОПИИ</b> - Восстанавливает закладки в модуле из файла, 
		сохраненного на ПК<br>
	</p>
	<p>
		В каждой закладке есть знак <img src='./images/ua_web_keyb.png' width="30" height="30"> при нажатии
		на который появляется меню с операциями над закладками
	</p>
	<p>
		<b>ОБНОВИТЬ</b> - обновляет данные закладки с онлайн сервиса<br>
		<b>ПЕРЕИМЕНОВАТЬ</b> - Переименовывает закладку.<br>
		<b>УДАЛИТЬ</b> - Удаляет текущую закладку.<br>
		<b>ВВЕРХ</b> - Перемещает закладку вверх по списку.<br>
		<b>ВНИЗ</b> - Перемещает закладку вниз по списку.<br>
	</p>
		
<?php
	exit;
}
// создаем резервную копию
if ($_REQUEST["operation"]=="backup")
{
	if (file_exists($ua_favorites_filename)) 
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($ua_favorites_filename));
		header('Content-Length: ' . filesize($ua_favorites_filename));
		readfile($ua_favorites_filename);
		exit;
	}
}

// загружает резервную копию на сервер
if(isset( $_FILES['backup']['tmp_name'] ))
{
	exec( "cp ".$_FILES['backup']['tmp_name']." ".$ua_favorites_filename);
}

// Страница настроек ---------------------------------------------------------
if ($_REQUEST["operation"]=="ua_set")
{
	
// сохраняем настройки	
	if ($_REQUEST["setup"]=="save_settings")
	{
		$player_style=$_REQUEST["alt_player"];
		$built_in_keyb=$_REQUEST["built_in_keyb"];
		$hdpr1=$_REQUEST["hdp_r1"];
		$exua_quality=$_REQUEST["ex_quality"];
		$exua_region=$_REQUEST["ex_region"];
		$exua_lang=$_REQUEST["ex_language"];
		$ua_sort=$_REQUEST["uakino_sort"];
		$uakino_decode=$_REQUEST["uakino_decode"];
		$fsua_sort=$_REQUEST["fs_sort"];
		$search_history=$_REQUEST["search_text"];
		$history_length=$_REQUEST["history_length"];
		$d_path=$_REQUEST["download_path"];
		$auto_path=$_REQUEST["auto_path"];
		$ua_wget_path=$_REQUEST["wget_path"];
		write_conf();
		html_header();
		?>
		<script type="text/javascript">
			alert("настройки сохранены");
			window.location.href='<?=$_SERVER["PHP_SELF"]."?operation=ua_set"?>';
		</script>
		<?php
		html_footer();
		//header("Location: ".$_SERVER["PHP_SELF"]."?operation=ua_set");
		exit;
	}
	
		
	// тут пошли переменные для установок
	// автопуть для закачек
		if ($auto_path=='1') 
		{
			$auto_path_on="checked"; 
			$auto_path_off="";
		}
			else 
		{
			$auto_path_on=""; 
			$auto_path_off="checked";
		}
	
	// встроенная клавиатура
		if ($built_in_keyb=='1') 
		{
			$built_in_keyb_on="checked"; 
			$built_in_keyb_off="";
		}
			else 
		{
			$built_in_keyb_on=""; 
			$built_in_keyb_off="checked";
		}
		
	// альтернативный плеер
	if ($player_style=='1') 
		{
			$alt_pl_on="checked"; 
			$alt_pl_off="";
		}
			else 
		{
			$alt_pl_on=""; 
			$alt_pl_off="checked";
		}
	// кнопки hdpr1
	if ($hdpr1=='1') 
		{
			$hdpr1_on="checked"; 
			$hdpr1_off="";
		}
			else 
		{
			$hdpr1_on=""; 
			$hdpr1_off="checked";
		}
	// ex.ua качество
	if ($exua_quality=='1') 
		{
			$exua_quality_on="checked"; 
			$exua_quality_off="";
		}
			else 
		{
			$exua_quality_on=""; 
			$exua_quality_off="checked";
		}
	// ex.ua регион
	if ($exua_region=='1') 
		{
			$exua_region_on="checked"; 
			$exua_region_off="";
		}
			else 
		{
			$exua_region_on=""; 
			$exua_region_off="checked";
		}
	// ex.ua язык
	
	if ($exua_lang=='0') 
		{
			$exua_lang_0="checked"; 
			$exua_lang_1=""; 
			$exua_lang_2=""; 
		}
	if ($exua_lang=='1') 		
		{
			$exua_lang_0=""; 
			$exua_lang_1="checked"; 
			$exua_lang_2=""; 
		}
	if ($exua_lang=='2') 		
		{
			$exua_lang_0=""; 
			$exua_lang_1=""; 
			$exua_lang_2="checked"; 
		}
	// uakino сортировка
	if ($ua_sort=='date') 
		{
			$ua_sort_date="checked"; 
			$ua_sort_rating=""; 
			$ua_sort_views=""; 
		}
	if ($ua_sort=='rating') 
		{
			$ua_sort_date=""; 
			$ua_sort_rating="checked"; 
			$ua_sort_views=""; 
		}
	if ($ua_sort=='views') 
		{
			$ua_sort_date=""; 
			$ua_sort_rating=""; 
			$ua_sort_views="checked"; 
		}
		
		if ($uakino_decode=='0') 
		{
			$uakino_decode_on="checked"; 
			$uakino_decode_off="";
		}
			else 
		{
			$uakino_decode_on=""; 
			$uakino_decode_off="checked";
		}	
	// fs.ua сортировка
	if ($fsua_sort=='new') 
		{
			$fsua_sort_new="checked"; 
			$fsua_sort_rating=""; 
			$fsua_sort_year=""; 
		}
	if ($fsua_sort=='rating') 
		{
			$fsua_sort_new=""; 
			$fsua_sort_rating="checked"; 
			$fsua_sort_year=""; 
		}
	if ($fsua_sort=='year') 
		{
			$fsua_sort_new=""; 
			$fsua_sort_rating=""; 
			$fsua_sort_year="checked"; 
		}
	html_header();
	fav_header("Настройки");
?>	
	<div class="content">
		<form method="get" action="<?=$_SERVER["PHP_SELF"]?>" class="enter_form">
			<input type="hidden" name="operation" value="">
			<input type="hidden" name="setup" value="">
				<table class="setup_table" border=0>
					<tr>
						<td class="setup_head_td" colspan=2>
							Общие
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Альтернативный плеер
						</td>
						<td class="setup_td">
							<p><input name="alt_player" type="radio" value="1" <?=$alt_pl_on?>> вкл. </p>
							<p><input name="alt_player" type="radio" value="0" <?=$alt_pl_off?>> выкл. </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Кнопки для плееров с прошивками HDP R1/R3
						</td>
						<td class="setup_td">
							<p><input name="hdp_r1" type="radio" value="1" <?=$hdpr1_on?>> вкл.</p>
							<p><input name="hdp_r1" type="radio" value="0" <?=$hdpr1_off?>> выкл. </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Встроенная клавиатура (из прошивки)
						</td>
						<td class="setup_td">
							<p><input name="built_in_keyb" type="radio" value="1" <?=$built_in_keyb_on?>> вкл.</p>
							<p><input name="built_in_keyb" type="radio" value="0" <?=$built_in_keyb_off?>> выкл. </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Кол-во фильмов в истории просмотров
						</td>
						<td class="setup_td">
							<p><input onkeyup="this.value = this.value.replace (/\D/, '')" name="history_length" class="setup_enter_text" maxlength="2" size="4" value="<?=$history_length?>"></p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							История поиска
						</td>
						<td class="setup_td">
							<p><input name="search_text" class="setup_enter_text" maxlength="90" size="50" value="<?=$search_history?>"></p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Авто выбор папки для закачек
						</td>
						<td class="setup_td">
							<p><input name="auto_path" type="radio" value="1" <?=$auto_path_on?>> вкл. </p>
							<p><input name="auto_path" type="radio" value="0" <?=$auto_path_off?>> выкл. </p>
<?php
if ($auto_path=='1') 
{
	if (!$no_device)
		{
?>							
							<p>Найдено устройство - <span class="device_path"><?=$download_path?></span> размером <span class="device_path"><?=format_fsize(down_path("size"))?></span></p>
<?php
		} else
		{
		?>
							<p><span class="no_device_path">Устройств не найдено</span></p>
		<?php
		}
}
?>
						</td>
					</tr>
					<tr <?php if ($auto_path=='1') {?>style="display:none;"<?php }?>>
						<td class="setup_td">
							Папка для закачек
						</td>
						<td class="setup_td">
							<div id="fileman_container">
								<a href="#" onclick="closeFileman('download','fileman_container',false)"><div id="fileman_topic">Файловый менеджер</div></a>
								<div id="fileman_list">
								</div>
								<table>
									<tr>
										<td class="fm_ok">
											<a href="#" class ="button green small" onclick="closeFileman('download','fileman_container',true)">Выбрать</a>
										</td>
										<td class="fm_ok">
											<a href="#" class ="button green small" onclick="closeFileman('download','fileman_container',false)">Отмена</a>
										</td>
									</tr>
								</table>
							</div>
							<table border=0>
								<tr>
									<td>
										<p>
											<input id="download" name="download_path" class="setup_enter_text" maxlength="90" size="50" value="<?=$d_path?>">
										</p>	
									</td>
									<td>
											<div class="browse_div_button">
												<a class="button gray" href="#" onclick="openFileman('?operation=fileman&type=download&d_path=','download','fileman_list','fileman_container')">Обзор...</a>
											</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Путь к WGET
						</td>
						<td class="setup_td">
						<div id="wget_fileman_container">
								<a href="#" onclick="closeFileman('wget','wget_fileman_container',false)"><div id="wget_fileman_topic">Файловый менеджер</div></a>
								<div id="wget_fileman_list">
								</div>
								<table>
									<tr>
										<td class="fm_ok">
											<a href="#" class ="button green small" onclick="closeFileman('wget','wget_fileman_container',true)">Выбрать</a>
										</td>
										<td class="fm_ok">
											<a href="#" class ="button green small" onclick="closeFileman('wget','wget_fileman_container',false)">Отмена</a>
										</td>
									</tr>
								</table>
								
								
							</div>
							<table border=0>
								<tr>
									<td>
										<p>
											<input id="wget" name="wget_path" class="setup_enter_text" maxlength="90" size="50" value="<?=$ua_wget_path?>" title="для использования встроенного wget оставьте поле пустым">
										</p>	
									</td>
									<td>
											<div class="browse_div_button">
												<a class="button gray" href="#" onclick="openFileman('?operation=fileman&type=wget&d_path=','wget','wget_fileman_list','wget_fileman_container')">Обзор...</a>
											</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="setup_head_td" colspan=2>
							Настройки ex.ua
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Качество видео
						</td>
						<td class="setup_td">
							<p><input name="ex_quality" type="radio" value="1" <?=$exua_quality_on?>> Высокое </p>
							<p><input name="ex_quality" type="radio" value="0" <?=$exua_quality_off?>> Низкое </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Регион
						</td>
						<td class="setup_td">
							<p><input name="ex_region" type="radio" value="0" <?=$exua_region_off?>> ex.ua </p>
							<p><input name="ex_region" type="radio" value="1" <?=$exua_region_on?>> fex.net </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Язык
						</td>
						<td class="setup_td">
							<p><input name="ex_language" type="radio" value="0" <?=$exua_lang_0?>> Русский </p>
							<p><input name="ex_language" type="radio" value="1" <?=$exua_lang_1?>> Украинский </p>
							<p><input name="ex_language" type="radio" value="2" <?=$exua_lang_2?>> Английский </p>
						</td>
					</tr>
					<tr>
						<td class="setup_head_td" colspan=2>
							Настройки uakino.net
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Сортировка
						</td>
						<td class="setup_td">
							<p><input name="uakino_sort" type="radio" value="date" <?=$ua_sort_date?>> дата </p>
							<p><input name="uakino_sort" type="radio" value="rating" <?=$ua_sort_rating?>> рейтинг </p>
							<p><input name="uakino_sort" type="radio" value="views" <?=$ua_sort_views?>> просмотр </p>
						</td>
					</tr>
						<tr>
						<td class="setup_td">
							Декодирование строк
						</td>
						<td class="setup_td">
							<p><input name="uakino_decode" type="radio" value="0" <?=$uakino_decode_on?>> включено </p>
							<p><input name="uakino_decode" type="radio" value="1" <?=$uakino_decode_off?>> выключено </p>
						</td>
					</tr>
					<tr>
						<td class="setup_head_td" colspan=2>
							Настройки fs.ua
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Сортировка
						</td>
						<td class="setup_td">
							<p><input name="fs_sort" type="radio" value="new" <?=$fsua_sort_new?>> дата </p>
							<p><input name="fs_sort" type="radio" value="rating" <?=$fsua_sort_rating?>> рейтинг </p>
							<p><input name="fs_sort" type="radio" value="year" <?=$fsua_sort_year?>> год </p>
						</td>
					</tr>
				</table>
			
	</div>
	<div class="footer">
		
			
				<table border="0">
					<tr>
						<td>
							<button class="button green" onclick="with (this) {form.operation.value = 'ua_set'; form.setup.value = 'save_settings'; form.submit ()}" title="Сохранить параметры">Сохранить</button>
						</td>
						<td>
							<button class="button green" onclick="with (this) {form.operation.value = 'cancel'; form.submit ()}" title="Вернуться на главную страница">Назад</button>
						</td>
					</tr>
				</table>
			</form>
	</div>
<?	
	
	html_footer();
	exit;	
}



// страница восстановления из резервной копии
if ($_REQUEST["operation"]=="restore")
{
	html_header();
	fav_header("Редактор закладок");
?>	
	<div class="content">
		<table border="0">
			<tr>
				<td colspan="2">
					<span class="enter_label label_restore">Укажите путь резервной копии закладок</span>
				</td>
			</tr>	
			<tr>
				<td>
					<form action="<?=$_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
					<input type="file" name="backup"><br />
				</td>
				<td>
					<button class="button green" type="submit">Загрузить</button>
				</td>
					</form>
				<td>
					<a href="<?=$_SERVER["PHP_SELF"]."?operation=cancel"?>" class="button green" title="Вернуться на главную страница">Назад</a>
				</td>
			</tr>
		</table>
	</div>
<?php	
	html_footer();
	exit;	
}

// функция проверки названия сайта для новых закладок
function check_site($link)
{
	if(preg_match("/(fex.net|ex.ua)/", $link)) $type=0;
	if(preg_match("/fs.ua/", $link)) $type=2;
	if(preg_match("/uakino.net/", $link)) $type=3;
	return $type;
}

// обрабатываем перемещение закладки вверх, вниз, добавить, удалить и т.п.
$oper=$_REQUEST["operation"];
if ($oper=="up"||$oper=="down"||$oper=="add_fav" 
	||$oper=="delete" ||$oper=="rename" ||$oper=="refresh" ||$oper=="confirm_del" )
{
	$fav_arr=get_fav($ua_favorites_filename);
	if(isset($_REQUEST["num"])) $num=$_REQUEST["num"]; else $num=0;
	$count=count($fav_arr)-1;

// подтвердить удаление

	if ($oper=="confirm_del")
	{
	html_header();
	?>
	<script type="text/javascript">
		isConfirmed = confirm('Удалить закладку?');
		if(isConfirmed)
		{
			window.location.href = '?operation=delete&num=<?=$num?>';
		}
		else
		{
			window.location.href='<?=$_SERVER["PHP_SELF"]?>';
		}
	</script>
	<?php
	html_footer();
	}

// переименовать
	if ($_REQUEST["operation"]=="rename")
	{
		$fav_arr[$num]["name"]=urldecode($_REQUEST["fav_name"]);
		save_fav($fav_arr);
	}

// ОБНОВИТЬ		
	if ($_REQUEST["operation"]=="refresh")
	{
		//$fav_arr[$num]['site']='0';
		if(!$_REQUEST["fav_link"]=="")
		{
		// пустая закладка
			if ($fav_arr[$num]['site']=='-1')
			{
				$link=$_REQUEST["fav_link"];
				// проверяем, а что за ссылку мы ввели
				$fav_arr[$num]['site']=check_site($link);
			}
		// EX.ua
			if ($fav_arr[$num]['site']=='0')
			{
				include($ua_.$exua_parser_filename);
				if(isset($_REQUEST["fav_link"])) 
				{
					$link=$_REQUEST["fav_link"];
					
					$fav_arr[$num]["link"]=check_ex_link($link);
				}
				
				//$fav_arr[$num]["name"]=$name;
				
				$s=load_page($fav_arr[$num]['link']);
				$fav_arr[$num]["name"]=favtitle($s);	
				if (analyze_page($s)) $fav_arr[$num]["type"]="list"; else $fav_arr[$num]["type"]="link";
				$poster=get_poster_and_descr($s);
				$fav_arr[$num]["poster"]=$poster["image"];	
			}
			// fs.ua
			if ($fav_arr[$num]['site']=='2')
			{
				include($ua_path.$fsua_parser_filename);
				if(isset($_REQUEST["fav_link"])) 
				{
					$link=$_REQUEST["fav_link"];
					if (!preg_match("/\?folder/",$link)) $link.="?folder=0";
					preg_match("/(.*?)#/", $link,$out);
					if ($out) $fav_arr[$num]["link"]=$out[1]; else $fav_arr[$num]["link"]=$link;
				}
				$s=file_get_contents(check_prefix($fav_arr[$num]['link']));
				$main=get_data($s,$fav_arr[$num]['link'],"0",true);
				if ($main["fav_folder"]) $fav_arr[$num]["type"]="list"; else $fav_arr[$num]["type"]="link";
				$fav_arr[$num]["poster"]=$main["image"];
				$fav_arr[$num]["name"]=$main["title"].$main["name1"];
				
			}
			//uakino.net
			
			if ($fav_arr[$num]['site']=='3')
			{
				include($ua_path.$uakino_parser_filename);
				if(isset($_REQUEST["fav_link"])) 
				{
					$link=check_uakino_link($_REQUEST["fav_link"]);
					
					$fav_arr[$num]["link"]=$link;
				}
				
				//$fav_arr[$num]["name"]=$name;
				$s=get_page("uakino.net","/video/".$fav_arr[$num]['link']);
				if ($s)
				{
				$main=get_data($s,$fav_arr[$num]['link'],true);
				$fav_arr[$num]["type"]="link";
				$fav_arr[$num]["poster"]=$main["image"];
				$fav_arr[$num]["name"]=$main["title"];
				}
			}
				
			save_fav($fav_arr);
			
		}
	}
// ВВЕРХ
	if ($_REQUEST["operation"]=="up")
	{
		foreach ($fav_arr[$num] as $id2=>$val2)
		{
			if ($id2=="poster") $poster=$val2;
			if ($id2=="link") $link=$val2;
			if ($id2=="name") $name=$val2;
			if ($id2=="type") $type=$val2;
			if ($id2=="site") $site=$val2;
		}
		
		if ($num>0) 
		{
			$num-=1;
			foreach ($fav_arr[$num] as $id2=>$val2)
			{
				if ($id2=="poster") {$fav_arr[$num+1]["poster"]=$val2; $fav_arr[$num]["poster"]=$poster;}
				if ($id2=="link") {$fav_arr[$num+1]["link"]=$val2; $fav_arr[$num]["link"]=$link;}
				if ($id2=="name") {$fav_arr[$num+1]["name"]=$val2; $fav_arr[$num]["name"]=$name;}
				if ($id2=="type") {$fav_arr[$num+1]["type"]=$val2; $fav_arr[$num]["type"]=$type;}
				if ($id2=="site") {$fav_arr[$num+1]["site"]=$val2; $fav_arr[$num]["site"]=$site;}
			}
			
			save_fav($fav_arr);
		}
	}
// ВНИЗ
	if ($_REQUEST["operation"]=="down")
	{
		foreach ($fav_arr[$num] as $id2=>$val2)
		{
			if ($id2=="poster") $poster=$val2;
			if ($id2=="link") $link=$val2;
			if ($id2=="name") $name=$val2;
			if ($id2=="type") $type=$val2;
			if ($id2=="site") $site=$val2;
		}
		
		if ($num<$count) 
		{
			$num+=1;
			foreach ($fav_arr[$num] as $id2=>$val2)
			{
				if ($id2=="poster") {$fav_arr[$num-1]["poster"]=$val2; $fav_arr[$num]["poster"]=$poster;}
				if ($id2=="link") {$fav_arr[$num-1]["link"]=$val2; $fav_arr[$num]["link"]=$link;}
				if ($id2=="name") {$fav_arr[$num-1]["name"]=$val2; $fav_arr[$num]["name"]=$name;}
				if ($id2=="type") {$fav_arr[$num-1]["type"]=$val2; $fav_arr[$num]["type"]=$type;}
				if ($id2=="site") {$fav_arr[$num-1]["site"]=$val2; $fav_arr[$num]["site"]=$site;}
			}
			
			save_fav($fav_arr);
		}
	}
// ДОБАВИТЬ
	if ($_REQUEST["operation"]=="add_fav")
	{
		array_unshift($fav_arr,array("name"=>"","link"=>"","poster"=>"http://".$_SERVER["HTTP_HOST"].$ua_path_link2.$ua_images_foldername."ua_web_logo_def.png","type"=>"-1","site"=>"-1"));
		save_fav($fav_arr);
	}
// УДАЛИТЬ	
	if ($_REQUEST["operation"]=="delete")
	{
		unset($fav_arr[$num]);
		save_fav($fav_arr);
	}
header("Location: ".$_SERVER["PHP_SELF"]."#fav_".$num);
//header("Location: ".$_SERVER["PHP_SELF"]."?fav=".$num);
//header("Location: ".$_SERVER["PHP_SELF"]);
exit;	
}

if ($_REQUEST["operation"]=="cancel")
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}

	
// выводит заголовок шапки html
function html_header()
{
global $ua_path_link2;
global $xtreamer;
global $ua_update_standalone;
if ($ua_update_standalone)
{ 
	$navbar=$ua_path_link2."js/navbar.js";
	$about=$ua_path_link2."js/about.js";
} 
else
{
	$navbar="/modules/core/js/navbar.js";
	$about="/modules/core/js/about.js";
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>UAOnline 2 - Редактор закладок</title>
<link rel="shortcut icon" href="<?=$ua_path_link2."images/uaonline2.ico"?>" />
<script src="<?=$navbar?>" type="text/javascript" charset="utf-8"></script>
<script src="<?=$about?>" type="text/javascript" charset="utf-8"></script>
<script language="javascript">
function openFileman(url,id,id_list,id_container)
{
	d_path=document.getElementById(id).value;
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
        }
	else
	{// code for IE6, IE5
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlHttp.open("GET", url+d_path, false);
	xmlHttp.send();

	df = document.getElementById(id_list);
	df.innerHTML = xmlHttp.responseText;

	dd = document.getElementById(id_container);
	dd.style.visibility = 'visible';
}

function closeFileman(id,id_container,ok)
{
	
	dd = document.getElementById(id_container);
	dd.style.visibility = 'hidden';
	if (ok) {document.getElementById(id).value=window.frames[0].tmp_folder;}
	iframe = document.getElementById('fileman_frame');
	iframe.parentNode.removeChild(iframe);	
}
</script>
<link rel="stylesheet" type="text/css" href="<?=$ua_path_link2."css/ua_web_fav.css"?>">
</head>
<body>
<?php
}
// выводит заголовок (верхнюю часть) редактора
function fav_header($head)
{
?>

<div id="about_container">
<a href="#" onclick="closeAbout()"><div id="about_topic">Справка</div></a>
<div id="about_list">
</div></div>

	<div class="header">
		<table id="head_table">
			<tr>
				<td>
					<img src="./images/ua_web_logo.png">	
				</td>
				<td>
					<span id="header_ver">Rev.<?=file_get_contents($tmpPath."ua_version")?></span>
				</td>
				<td id="name_header">
					<span id="header_text"><?=$head?></span>
				</td>
				<td>
					<a class="button blue" id="about_button" href="#" onclick="openAbout('?operation=about')">Справка</a>
				</td>
			</tr>
		</table>
	</div>
<?php
}

// выводит нижнюю часть редактора 
function fav_footer()
{
?>
	<div class="footer">
		<form method="get" action="<?=$_SERVER["PHP_SELF"]?>" class="enter_form">
			<input type="hidden" name="operation" value="">
				<table border="0">
					<tr>
						<td>
							<button class="button green" onclick="with (this) {form.operation.value = 'add_fav'; form.submit ()}" title="Добавить новую закладку. Новая закладка будет первой в списке. После этого нужно ввести ссылку и нажать ОБНОВИТЬ">Добавить закладку</button>
						</td>
						<td>
							<button class="button green" onclick="with (this) {form.operation.value = 'backup'; form.submit ()}" title="Создать резервную копию избранного на ПК">Создать резервную копию</button>
						</td>
						<td>
							<button class="button green" onclick="with (this) {form.operation.value = 'restore'; form.submit ()}" title="Восстановить из резервной копии избранного">Восстановить из резевной копии</button>
						</td>
						<td>
							<button class="button blue" onclick="with (this) {form.operation.value = 'ua_set'; form.submit ()}" title="Настройки UAOnline">Настройки</button>
						</td>

					</tr>
				</table>
			</form>
	</div>
<?php
}

// закрывает html
function html_footer()
{
?>
</body>
</html>
<?php
}

// пункт меню "СПИСОК ПУСТОЙ"
function fav_template_empty()
{
?>
			<li class="items">
				<img src="./images/ua_web_logo_def.png" class="poster">		
				<span class="empty_list_label">СПИСОК ПУСТ</span>
								
			</li>
<?php
}

// Пункт меню стандартный
function fav_template($poster, $name, $link, $site,  $url_site, $type, $num, $new=false, $fav_lnk=false)
{
?>
	<li class="items" id="fav_<?=$num?>">
					<table  class="item_table" border="0">
					<tr>
						<td class="poster" rowspan=2>
							<img src="<?=check_poster($poster)?>" class="poster" alt="постер">
						</td>					
						<td colspan=2>
							<a href="<?=$url_site?>" target="_blank"><img class="site" src="<?=$site?>" border="0"></a>
						</td>
					</tr>
					<tr>
					<td>
					<form method="get" action="<?=$_SERVER["PHP_SELF"]?>">
							<input type="hidden" name="operation" value="">
							<input type="hidden" name="num" value="0">
							
							<ul class="enter_list">
								<li><span class="enter_label">Название</span> </li>
								<li><input type="text" name="fav_name" class="enter_text"  value="<?=$name?>" placeholder="введите название закладки"/></li>
<?php
								if ($fav_lnk)
								{
?>								<li><span class="enter_label">Ссылка</span> </li>
								<li><input type="text" name="fav_link" class="enter_text"  value="<?=$link?>" placeholder="введите ссылку на закладку"/></li>
<?php							}
?>							</ul>
							</td>
							<td class="table_menu">		
							<div class="navbarmenu">
													
								<a class="img" onclick="mopen('opt_menu_<?=$num?>')" onmouseout="mclosetime()" title="операции"></a>
									<div class="menu" id="opt_menu_<?=$num?>" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
										<input class="button gray small" type="button" onclick="with (this) {form.operation.value = 'refresh'; form.num.value = <?=$num?>; form.fav_name.value=''; form.submit ()}" value="ОБНОВИТЬ" title="Обновить данные закладки с онлайн сервиса"><br>																			
<?php
									if (!$new)
										{
?>
										<input class="button gray small" type="button" onclick="with (this) {form.operation.value = 'rename'; form.num.value = <?=$num?>; form.fav_link.value=''; form.submit ()}" value="ПЕРЕИМЕНОВАТЬ" title="Сохранить измененное имя закладки"><br>
<?php
										}
?>
										<input class="button gray small" type="button" onclick="with (this) {form.operation.value = 'confirm_del'; form.num.value = <?=$num?>; <?if ($fav_lnk) echo "form.fav_link.value='';"?> form.fav_name.value=''; form.submit ()}" value="УДАЛИТЬ" title="Удалить закладку"><br>
										<input class="button gray small" type="button" onclick="with (this) {form.operation.value = 'up'; form.num.value = <?=$num?>; <?if ($fav_lnk) echo "form.fav_link.value='';"?> form.fav_name.value='';  form.submit ()}" value="ВВЕРХ" title="Переместить закладку вверх"><br>
										<input class="button gray small" type="button" onclick="with (this) {form.operation.value = 'down'; form.num.value = <?=$num?>; <?if ($fav_lnk) echo "form.fav_link.value='';"?> form.fav_name.value=''; form.submit ()}" value="ВНИЗ" title="Переместить закладку вниз">
									</div>
							</div>
					</form>
					</td>
					</tr>
					</table>
					
			</li>
<?php
}



// выводит среднюю часть (список закладок)
function fav_content()
{
global $ua_path;
global $ua_favorites_filename;
global $ua_path_link;
global $ua_path_link2;
global $ua_images_foldername;

?>
	<div class="content">
		<ul id="list">
<?php
	
	$ua_fav=get_fav($ua_favorites_filename);
	$count=0;
	if (count($ua_fav)==0) fav_template_empty(); else
	{
		foreach ($ua_fav as $id=>$val)
		{
			foreach ($val as $id2=>$val2)
			{
				if ($id2=="poster") $poster=$val2;
				if ($id2=="link") $link=$val2;
				if ($id2=="name") $name=$val2;
				if ($id2=="site")
				{
					
					$logo_arr=get_site_logo($val2);
					$site=$logo_arr["image"];
					$url_site=$logo_arr["site_url"];
					unset($logo_arr);
					$type=$val2;
				}
			}
			//if ($link=="") $fav_lnk=true; else $fav_lnk=false;
			$fav_lnk=true;
			if ($name=="") $new=true; else $new=false;
			fav_template($poster, $name, $link, $site,$url_site, $type, $count,$new,$fav_lnk);		
			$count++;
		}
	}
	
?>
		</ul>
	</div>
											
<?php
}	
html_header();
fav_header("Редактор закладок");
fav_content();
fav_footer();
html_footer();
?>