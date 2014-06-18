<?php
/*	------------------------------
	Ukraine online services 	
	WEB interface module v4.0
	------------------------------
	Created by Sashunya 2014
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */
include("ua_paths.inc.php");

ob_implicit_flush( false );

//вывести сообщение
function message($mes)
{
	?>
			<script type="text/javascript">
				alert("<?=$mes?>");
			</script>
	<?
}
// сохранить закладки в файл
function save_fav($ua_fav)
{
global $fav_name_array;
global $fav_link_array;
global $fav_poster_array;
global $fav_type_array;
global $fav_site_array;
global $ua_path;
global $ua_favorites_filename;
	$tmps="";
	$count=0;
	foreach ($fav_name_array as $key=>$val)
		{
			$name=$val;
			$poster=$fav_poster_array[$key];
			$link=$fav_link_array[$key];
			$type=$fav_type_array[$key];
			$site=$fav_site_array[$key];
			$tmps.=$name."\n".$link."\n".$poster."\n".$type."\n".$site."\n";
			$count++;
		}
	$tmps=$count."\n".$tmps;
	file_put_contents($ua_favorites_filename,$tmps);
}


if ($_SERVER['REQUEST_METHOD']=='POST')
{
// загоняем в массив все что пришло постом, имена, ссылки закладок
	$fav_name_array=array();
	$fav_link_array=array();
	$fav_poster_array=array();
	$fav_type_array=array();
	$fav_site_array=array();
	$sel_array=array();
	$fav_cnt=0;
	foreach ($_REQUEST as $key=>$val)
		{
			if (preg_match("/fav_name/",$key)) { $fav_name_array[]=$val; $fav_cnt++;}
			if (preg_match("/fav_link/",$key)) $fav_link_array[]=$val;
			if (preg_match("/fav_poster/",$key)) $fav_poster_array[]=$val;
			if (preg_match("/fav_type/",$key)) $fav_type_array[]=$val;
			if (preg_match("/fav_site/",$key)) $fav_site_array[]=$val;
			if (preg_match("/chk/",$key)) $sel_array[]=$fav_cnt;
		}
// удалить выбранные закладки
		if ($_REQUEST["oper"]=='del_fav')
		{
			if (count($sel_array)!=0)
			{
				foreach ($sel_array as $val)
				{
					//echo $val;
					unset($fav_name_array[$val]);
					unset($fav_link_array[$val]);
					unset($fav_poster_array[$val]);
					unset($fav_type_array[$val]);
					unset($fav_site_array[$val]);
				}
				save_fav();
			}
		}
		
// добавить пустую запись в начало списка или сохранить
		if ($_REQUEST["oper"]=='add_fav' || $_REQUEST["oper"]=='save')
			{
				if ($_REQUEST["oper"]=='add_fav')
				{
					array_unshift($fav_name_array,"");
					array_unshift($fav_link_array,"");
					array_unshift($fav_poster_array,"./images/ua_web_logo_def.png");
					array_unshift($fav_type_array,"-1");
					array_unshift($fav_site_array,"-1");
				}
				if ($_REQUEST["oper"]=='save')
				{
					message("изменения сохранены");
				}
				save_fav();
			}

	// ОБНОВИТЬ		
	if ($_REQUEST["oper"]=="refresh")
	{
		if (count($sel_array)!=0)
			{
				html_header();
				fav_header("Редактор закладок");
				?>
					<div class="content">
					<div class="enter_label label_restore center">Выполняется обновление закладок, подождите</div>
					<!-- Progress bar holder -->
					<div id="progress" class="progress center"></div>
				<?
				
				// Total processes
				$total = count($sel_array);
				$i=1;
				foreach ($sel_array as $val)
				{
					
					// Calculate the percentation
					$percent = intval($i/$total * 100)."%";
					$i++;
					// Javascript for updating the progress bar and information
					echo '<script language="javascript">
					document.getElementById("progress").innerHTML="<div class=\"progress_fill\" style=\"width:'.$percent.';\">'.$percent.'</div>";
					</script>';

				// This is for the buffer achieve the minimum size in order to flush data
					echo str_repeat(' ',1024*64);
				
				// Send output to browser immediately
					flush();
					
					
						if ($fav_link_array[$val]!="")
						{
							if ($fav_site_array[$val]=='-1')
							{
								$link=$fav_link_array[$val];
								// проверяем, а что за ссылку мы ввели
								$fav_site_array[$val]=check_site($link);
							}
							//EX.UA
							if ($fav_site_array[$val]=='0')
							{
								include_once($ua_path.$exua_parser_filename);
								if(isset($fav_link_array[$val])) 
								{
									$link=$fav_link_array[$val];
									
									$fav_link_array[$val]=check_ex_link($link);
								}
							
								$s=load_page($fav_link_array[$val]);
								$fav_name_array[$val]=favtitle($s);	
								if (analyze_page($s)) $fav_type_array[$val]="list"; else $fav_type_array[$val]="link";
								$poster=get_poster_and_descr($s);
								$fav_poster_array[$val]=$poster["purl"];	
							}
							// brb.to
							if ($fav_site_array[$val]=='2')
							{	
								if(isset($fav_link_array[$val])) 
								{
								
								$link=$fav_link_array[$val];
									
									if (!preg_match("/\?ajax&folder/",$link)) $link.="?ajax&folder";
									$fav_link_array[$val]=$link;
								}
								
								include_once($ua_path.$fsua_parser_filename);
								
								$s=check_prefix($fav_link_array[$val]);
								$main=get_fs_data($fav_link_array[$val],'','',true);
								$fav_type_array[$val]="list";
								$fav_poster_array[$val]=$main["image"];
								$fav_name_array[$val]=$main["title"];
							}
							// uakino.net
							if ($fav_site_array[$val]=='3')
							{
								include_once($ua_path.$uakino_parser_filename);
								if(isset($fav_link_array[$val])) 
								{
									$link=check_uakino_link($fav_link_array[$val]);
									
									$fav_link_array[$val]=$link;
								
								}
								$s=file_get_contents("http://uakino.net/video/".$fav_link_array[$val]);
								if ($s)
								{
									$main=get_data($s,$fav_link_array[$val],true);
								
									$fav_type_array[$val]="link";
									$fav_poster_array[$val]=$main["image"];
									$fav_name_array[$val]=$main["title"];
								}
							}
						}
					
					sleep(1);
				}
				echo "</div>";
				html_footer();
				save_fav();
				message("Обновление закладок выполнено");
				
			}
		exit("<meta http-equiv='refresh' content='0; url= $_SERVER[PHP_SELF]'>");
	}
				
		
// создаем резервную копию
if ($_REQUEST["oper"]=="backup")
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
	
// страница восстановления из резервной копии
if ($_REQUEST["oper"]=="restore")
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
					<a href="<?=$_SERVER["PHP_SELF"]?>" class="button green" title="Вернуться на главную страницу">Назад</a>
				</td>
			</tr>
		</table>
	</div>
<?php	
	html_footer();
	exit;	
}

	
}

// это читает постеры
/*
if ($_REQUEST["get_poster"])
{
	$url=$_REQUEST["get_poster"];
	header("Content-Type: image/jpeg");
	readfile($url);	
	exit;
}
*/
if ($_REQUEST["oper"]=="cancel")
{
	header("Location: ".$_SERVER["PHP_SELF"]);
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

if ($_REQUEST["oper"]=="fileman")
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
if ($_REQUEST["oper"]=="about")
{
?>
	<h3 align="center">КРАТКАЯ СПРАВКА ПО ИСПОЛЬЗОВАНИЮ РЕДАКТОРА ЗАКЛАДОК</h3>
	<h3 align="center">UAOnline 2</h3>
	<p>
		<b>СОХРАНИТЬ</b> - сохраняет внесенные изменения: изменение имени, ссылки, позиции закладки<br>
		<b>ДОБАВИТЬ</b> - Добавляет новую закладку в начале списка избранного. После этого 
			нужно ввести ссылку закладки, выделить галочкой и нажать на <b>ОБНОВИТЬ</b><br>
		<b>ОБНОВИТЬ</b> - обновляет данные закладки с онлайн сервиса. Обновляет только выделенные галочками<br>
		<b>УДАЛИТЬ</b> - Удаляет выделенные закладки.<br>
		<b>СОЗДАТЬ РЕЗЕРВНУЮ КОПИЮ</b> - Создает на ПК резервную копию закладок<br>
		<b>ВОССТАНОВИТЬ ИЗ РЕЗЕРВНОЙ КОПИИ</b> - Восстанавливает закладки в модуле из файла, 
		сохраненного на ПК<br>
	</p>
	<p>
		Каждую закладку можно перемещать по списку. Достачно подвести курсор мыши справа на нужную закладку. Появится символ <img src='./images/ua_web_updown2.png' width="15" height="20"> после этого, удерживая левую кнопку мыши, можно перемещать закладку вверх или вниз.
	</p>
	<p>
		Каждую закладку можно выделить галочкой слева от списка. Либо можно отметить все закладки нажав на галочку в самом верху таблицы.
	</p>
	<p>
		Также при нажатии <b>НАСТРОЙКИ</b> можно настроить работу приложения <b>UAOnline2</b>
	</p>
<?php
	exit;
}


// Страница настроек ---------------------------------------------------------
if ($_REQUEST["oper"]=="ua_set")
{
	
// сохраняем настройки	
	if ($_REQUEST["setup"]=="save_settings")
	{
		$player_style=$_REQUEST["alt_player"];
		$built_in_keyb=$_REQUEST["built_in_keyb"];
		$position=$_REQUEST["position"];
		$screensaver=$_REQUEST["screensaver"];
		$hdpr1=$_REQUEST["hdp_r1"];
		$exua_quality=$_REQUEST["ex_quality"];
		$exua_region=$_REQUEST["ex_region"];
		$exua_lang=$_REQUEST["ex_language"];
		$exua_posters=$_REQUEST["exua_posters"];
		$ua_sort=$_REQUEST["uakino_sort"];
		$uakino_decode=$_REQUEST["uakino_decode"];
		$fsua_sort=$_REQUEST["fs_sort"];
		$history_length=$_REQUEST["history_length"];
		$d_path=$_REQUEST["download_path"];
		$auto_path=$_REQUEST["auto_path"];
		$ua_wget_path=$_REQUEST["wget_path"];
		write_conf();
		html_header();
		?>
		<form method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
		<input type="hidden" name="oper" value="ua_set">
		<script type="text/javascript">
			alert("настройки сохранены");
			document.forms[0].submit();
		</script>
		</form>
		<?php
		html_footer();
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
		// сохранять позицию
		if ($position=='1') 
		{
			$position_on="checked"; 
			$position_off="";
		}
			else 
		{
			$position_on=""; 
			$position_off="checked";
		}	
		// фоновая заставка
		if ($screensaver=='1') 
		{
			$screensaver_on="checked"; 
			$screensaver_off="";
		}
			else 
		{
			$screensaver_on=""; 
			$screensaver_off="checked";
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
	// ex.ua постеры
		if ($exua_posters=='1') 
		{
			$exua_posters_on="checked"; 
			$exua_posters_off="";
		}
			else 
		{
			$exua_posters_on=""; 
			$exua_posters_off="checked";
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
		<form method="POST" action="<?=$_SERVER["PHP_SELF"]?>" class="enter_form">
			<input type="hidden" name="oper" value="">
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
							Сохранять позицию просмотра
						</td>
						<td class="setup_td">
							<p><input name="position" type="radio" value="1" <?=$position_on?>> вкл.</p>
							<p><input name="position" type="radio" value="0" <?=$position_off?>> выкл. </p>
						</td>
					</tr>
					<tr>
						<td class="setup_td">
							Фоновая заставка
						</td>
						<td class="setup_td">
							<p><input name="screensaver" type="radio" value="1" <?=$screensaver_on?>> вкл.</p>
							<p><input name="screensaver" type="radio" value="0" <?=$screensaver_off?>> выкл. </p>
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
												<a class="button gray" href="#" onclick="openFileman('?oper=fileman&type=download&d_path=','download','fileman_list','fileman_container')">Обзор...</a>
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
												<a class="button gray" href="#" onclick="openFileman('?oper=fileman&type=wget&d_path=','wget','wget_fileman_list','wget_fileman_container')">Обзор...</a>
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
						<td class="setup_td">
							Показывать постеры
						</td>
						<td class="setup_td">
							<p><input name="exua_posters" type="radio" value="0" <?=$exua_posters_off?>> выкл. </p>
							<p><input name="exua_posters" type="radio" value="1" <?=$exua_posters_on?>> вкл. </p>
						</td>
					</tr>
					<tr>
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
							<p><input name="uakino_decode" type="radio" value="0" <?=$uakino_decode_on?>> вкл. </p>
							<p><input name="uakino_decode" type="radio" value="1" <?=$uakino_decode_off?>> выкл. </p>
						</td>
					</tr>
					<tr>
						<td class="setup_head_td" colspan=2>
							Настройки brb.to
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
							<button class="button green" onclick="with (this) {form.oper.value = 'ua_set'; form.setup.value = 'save_settings'; form.submit ()}" title="Сохранить параметры">Сохранить</button>
						</td>
						<td>
							<button class="button green" onclick="with (this) {form.oper.value = 'cancel'; form.submit ()}" title="Вернуться на главную страница">Назад</button>
						</td>
					</tr>
				</table>
			</form>
	</div>
<?	
	
	html_footer();
	exit;	
}

// функция проверки названия сайта для новых закладок
function check_site($link)
{
	if(preg_match("/(fex.net|ex.ua)/", $link)) $type=0;
	if(preg_match("/brb.to|fs.to/", $link)) $type=2;
	if(preg_match("/uakino.net/", $link)) $type=3;
	return $type;
}
// выводит заголовок шапки html
function html_header()
{
global $ua_path_link2;
global $xtreamer;
global $ua_update_standalone;
 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>UAOnline 2 - Редактор закладок</title>
<link rel="shortcut icon" href="<?=$ua_path_link2."images/uaonline2.ico"?>" />
<script type="text/javascript" src="js/navbar.js" charset="utf-8"></script>
<script type="text/javascript" src="js/about.js" charset="utf-8"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/sel_all.js"></script>

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
<link rel="stylesheet" type="text/css" href="css/index.css">
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
					<a class="button blue" id="about_button" href="#" onclick="openAbout('?oper=about')">Справка</a>
				</td>
			</tr>
		</table>
	</div>
<?php
}

function left_side()
{
global $p_u;
?>
<div class="left_side">
			<table border="0">
					<tr>
						<td>
							<button class="button medium green" onclick="with (this) { form.oper.value = 'save'; form.submit ()}" title="Сохранить внесенные изменения">Сохранить</button>
						</td>
					</tr>
					<tr>
						<td>
							<button class="button medium green" onclick="with (this) { form.oper.value = 'add_fav';  form.submit ()}" title="Добавить пустую закладку">Добавить</button>
						</td>
					</tr>
					<tr>
						<td>
							<button class="button medium green" onclick="with (this) { form.oper.value = 'refresh'; form.submit ()}" title="Обновить данные выбранных закладок с онлайн сервиса">Обновить</button>
						</td>
					<tr>
					<tr>
						<td>
							<button class="button medium green" onclick="with (this) { isConfirmed = confirm('Удалить выбранные закладки?'); if(isConfirmed) {form.oper.value = 'del_fav';} else {form.oper.value = 'reset';} form.submit ()} " title="Удалить выбранные закладки">Удалить</button>														
						</td>
					</tr>
				</table>
	</div>
<?
}
// выводит нижнюю часть редактора 
function fav_footer()
{
?>
	<div class="footer">
				<table border="0">
					<tr>
					<td>
							<button class="button medium green" onclick="with (this) {form.oper.value = 'backup'; form.submit ()}" title="Создать резервную копию избранного на ПК">Создать резервную копию</button>
						</td>
						<td>
							<button class="button medium green" onclick="with (this) {form.oper.value = 'restore'; form.submit ()}" title="Восстановить из резервной копии избранного">Восстановить из резевной копии</button>
						</td>
						<td>
							<button class="button medium blue" onclick="with (this) {form.oper.value = 'ua_set'; form.submit ()}" title="Настройки UAOnline">Настройки</button>
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
// Пункт меню стандартный
function fav_template($poster, $name, $link,  $site_num, $site,  $url_site, $type, $num, $new=false, $fav_lnk=false)
{
?>
	<tr class="table_hover" id="<?=$num?>_row">
				<input type="hidden" name="<?=$num?>_fav_site" id="<?=$num?>_fav_site" value="<?=$site_num?>">
				<input type="hidden" name="<?=$num?>_fav_type" id="<?=$num?>_fav_type" value="<?=$type?>">
				<td class="main checkbox">
				<div align="center">
					<input  type='checkbox'  class="example_check" name='<?=$num?>_chk' id='<?=$num?>_chk' value='1' hidden/><label for="<?=$num?>_chk"></label>
				</div>
				</td>
				<td class="main nomerpp">
					<div align="center">
						<span class="number"><?=$num+1?></span>
					</div>
				</td>
				<td class="main poster">
					<?$post=check_poster($poster);?>
					<input type="hidden" name="<?=$num?>_fav_poster" id="<?=$num?>_fav_poster" value="<?=$post?>">
					<img src="<?=$post?>" class="poster" alt="постер">
					
				</td>
				<td class="main tdsite">
							<a href="<?=$url_site?>" target="_blank"><img class="site" src="<?=$site?>" border="0"></a>
				</td>
				<td class="main favorites">
					<span class="enter_label">Название</span><br>
					<input class="chan_id col_view favorites" type="text" name="<?=$num?>_fav_name" id="<?=$num?>_fav_name" value="<?=$name?>" placeholder="введите название закладки"/> 
					<br>
					<span class="enter_label">Ссылка</span><br>
					<input class="chan_playlist_link col_view favorites" type="text" name="<?=$num?>_fav_link" id="<?=$num?>_fav_link" value="<?=$link?>" placeholder="введите ссылку на закладку"/> 
				</td>
				<td class="main" align=center>
					<span class="arrow_up_down" title="переместить"></span>
				</td>

		</tr>
<?php
}


// пункт меню "СПИСОК ПУСТОЙ"
function fav_template_empty()
{
?>
			<tr class="table_hover" id="<?=$num?>_row">
				
				<td class="main checkbox">
				</td>
				<td class="main nomerpp">
				</td>
				<td class="main poster">
					<img src="./images/ua_web_logo_def.png" class="poster" alt="постер">
				</td>
				<td class="main tdsite">
					
				</td>
				<td class="main favorites">
					<span class="enter_label">Список пуст</span><br>
				</td>
				<td class="main" align=center>
				</td>

		</tr>
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
		<form method="POST" action="<?=$_SERVER["PHP_SELF"]?>" name="favorites">
		<input type="hidden" name="oper" value="">
		
		<div class='block'>
		<div class="scroll">
		<div class="head fixed"></div>
		<table class="table"  id="sort"  width="100%">
			<tr class='head'>
				<td ><div class='st'><input type="checkbox" id="example_maincb"><label for="example_maincb"></label></div><div class='or'><input id="example_maincb" type="checkbox"><label for="example_maincb"></label></div></td>  <!-- должны быть одинаковые значениея для авто подгонки столбцов по заголовкам -->
				<td><div class='st'>№</div><div class='or'>№</div></td>  <!-- должны быть одинаковые значениея для авто подгонки столбцов по заголовкам -->
				<td><div class='st'>Закладка</div><div class='or'>Закладка</div></td>  <!-- должны быть одинаковые значениея для авто подгонки столбцов по заголовкам -->
				<td><div class='st'></div><div class='or'></div></td>
			</tr>
		<tbody>
		
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
					$site_num=$val2;
					if ($val2 == "0") $url_site.="/".$link;
					if ($val2 == "2") 
					{
						$parse =  parse_url($link);
						$url_site="http://".$parse["host"].$parse["path"];
					}
					if ($val2 == "3") 
					{
						$url_site.="/video/".$link;
					}					
					unset($logo_arr);
					
				}
				if ($id2=="type") $type=$val2;
			}

			$fav_lnk=true;
			if ($name=="") $new=true; else $new=false;
			fav_template($poster, $name, $link, $site_num,$site,$url_site, $type, $count,$new,$fav_lnk);		
			$count++;
		}
	}
	
?>
		</tbody>
		</table>
		<script type="text/javascript">
				var fixHelper = function(e, ui) {
					ui.children().each(function() {
					$(this).width($(this).width());
					});
				return ui;
				};
 
				$("#sort tbody").sortable({
				helper: fixHelper
				});
		</script>
		</div>
		</div>
	</div>
<?php
}	
html_header();
fav_header("Редактор закладок");
fav_content();
left_side();
fav_footer();
html_footer();
?>