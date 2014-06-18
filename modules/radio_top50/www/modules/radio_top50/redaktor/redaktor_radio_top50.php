<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?
#view all

$data_fc =  '../favorites.php';
if( is_file( $data_fc ) )
{
	include( $data_fc );

$a=1;
$favorites_table_temp = array();
foreach( $favorites_table as $sub => $item ) {
	$favorites_table_temp [$a] = $item ;
	$a++;
}
file_put_contents( $data_fc, '<?php $favorites_table = '.var_export( $favorites_table_temp, true ).'; ?>' );

$favorites_table = $favorites_table_temp;	
	
} else { echo "<h3>Ошибка! Не удалось найти файл базы!</h3>"; }
/////////////////////////////////// 
 if ( isset( $_POST['up'] )) {

$action = $_POST['up'];
 if ($action > 1) {
//	echo 'Вверх ';
//	echo $action;
	list ($favorites_table[$action-1], $favorites_table[$action]) = array($favorites_table[$action], $favorites_table[$action-1]);
//	print_r($favorites_table);
	file_put_contents( $data_fc, '<?php $favorites_table = '.var_export( $favorites_table, true ).'; ?>' );
	}
}
//////////////////////////////////////////////////
 if ( isset( $_POST['down'] )) {

$action = $_POST['down'];
 if ($action < count( $favorites_table )) {
//	echo 'Вниз';
//	echo $action;
	list ($favorites_table[$action+1], $favorites_table[$action]) = array($favorites_table[$action], $favorites_table[$action+1]);
//	print_r($favorites_table);
	file_put_contents( $data_fc, '<?php $favorites_table = '.var_export( $favorites_table, true ).'; ?>' );
	}
}
//////////////////////////////////////////////////
 if ( isset( $_POST['del'] )) {

$action = $_POST['del'];
unset ($favorites_table [$action]);
$a=1;
$favorites_table_temp = array();
foreach( $favorites_table as $sub => $item ) {
	$favorites_table_temp [$a] = $item ;
	$a++;
}
file_put_contents( $data_fc, '<?php $favorites_table = '.var_export( $favorites_table_temp, true ).'; ?>' );

$favorites_table = $favorites_table_temp;

}
//////////////////////////////////////////////////

$a_id = (count ($favorites_table));


	foreach( $favorites_table as $sub => $item ) {
	$id=htmlspecialchars($favorites_table[$sub]["id"]);
			$name=htmlspecialchars($favorites_table[$sub]["name"]);

		$logo=null;
		$logo=$favorites_table[$sub]["logo"];		
		$link=htmlspecialchars($favorites_table[$sub]["link"]);

		$bitrate=htmlspecialchars($favorites_table[$sub]["bitrate"]);
			$city=htmlspecialchars($favorites_table[$sub]["city"]);
			$language=htmlspecialchars($favorites_table[$sub]["language"]);
			$genre=htmlspecialchars($favorites_table[$sub]["genre"]);

	$name_alt=$name;
		
//
?>


<html>

<head>
<title>Редактор станций</title>

<link rel="stylesheet" href="/modules/core/css/main.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">

</head>

<body>
<table class="mod_listview" border="0" width="803">
    <tr class="mod_list_last" valign="top">
        <td width="34">&nbsp;<?echo $sub ?></td>
        <td width="606">&nbsp;<?echo $name ?>
<div class="spoil">
<div class="smallfont"><input type="button" value="Информация о станции" class="input-button" onclick="if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = ''; this.innerText = ''; this.value = 'Свернуть'; } else { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerText = ''; this.value = 'Информация о станции'; }"/>
</div>
<div class="alt2">
<div style="display: none;">
<table border="0" width="688">
    <tr>
        <td width="275">&nbsp;Название станции</td>
        <td width="397">&nbsp;<?echo $name ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Идентификатор станции</td>
        <td width="397">&nbsp;<?echo $id ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Логотип станции</td>
        <td width="397">&nbsp;<?echo $logo ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Линк станции</td>
        <td width="397">&nbsp;<?echo $link ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Битрейт потока</td>
        <td width="397">&nbsp;<?echo $bitrate ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Город вещания</td>
        <td width="397">&nbsp;<?echo $city ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Язык вещания</td>
        <td width="397">&nbsp;<?echo $language ?></td>
    </tr>
    <tr>
        <td width="275">&nbsp;Жанр вещания</td>
        <td width="397">&nbsp;<?echo $genre ?></td>
    </tr>	
</table>
</div>
</div>
</div>
		</td>
		
        <td width="43"> 
                <p align="center"><form method="post"action="redaktor_radio_top50.php">
				<input type="image" name="up" src="img/verh.PNG" title="Переместить вверх" alt="Переместить вверх">
				<input type="hidden" name="up" value="<?echo $sub ?>"></form></p>
</td>
        <td width="43">           
                <p align="center"><form method="post"action="redaktor_radio_top50.php">
				<input type="image" name="down" src="img/vniz.PNG" title="Переместить вниз" alt="Переместить вниз">
				<input type="hidden" name="down" value="<?echo $sub ?>"></form></p>

</td>
        <td width="43">            
                <p align="center"><form>
				<input type="image" name="new" src="img/ok.PNG" onClick="javascript:window.showModalDialog('redaktor_stanci_radio_top50.php?key=<?echo $sub ?>', 'okno', 'width=1200,height=650,status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes');" title="Редактировать станцию" alt="Редактировать станцию">
				</form></p>
</td>
         <td width="43">            
                <p align="center"><form method="post"action="redaktor_radio_top50.php">
				<input type="image" name="del" src="img/del.PNG" title="Удалить станцию" alt="Удалить станцию">
				<input type="hidden" name="del" value="<?echo $sub ?>"></form></p>
</td>
           
    </tr>
</table>
<?
    }
?>

<form name="form4">
    <p align="left"><form>
				<input type="image" name="new" src="img/new.PNG" onClick="javascript:window.showModalDialog('redaktor_stanci_radio_top50.php?key=new', 'okno', 'width=1200,height=650,status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes');" title="Добавить станцию" alt="Добавить станцию">
				</form></p>
</form>
</body>

</html>

<?php

function remove_dups($array, $key, $row_element) {
     $new_array[0] = $array[0];
     foreach ($array as $item => $current) {
             if ($current[$key]==$row_element) {
					return $item;
             }

     }
     return false;
 }

?>