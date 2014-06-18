<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?
#view all

$data_fc =  '../favorites.php';
if( is_file( $data_fc ) )
{
	include( $data_fc );
} else { echo "<h3>Ошибка! Не удалось найти файл базы!</h3>"; }

 if ( isset( $_REQUEST['key'] )) {
 $sub = $_REQUEST['key'];
 }

// print_r ($_REQUEST['save']);
 
 if ( isset( $_REQUEST['save'] )) {
 $key = $_REQUEST['save'];
 	$name=$_REQUEST['name']; 
	$id=$_REQUEST['id']; 
	$logo=$_REQUEST['logo']; 
	$link=$_REQUEST['link']; 
	$bitrate=$_REQUEST['bitrate']; 
	$city=$_REQUEST['city']; 
	$language=$_REQUEST['language']; 
	$genre=$_REQUEST['genre'];

	if ($key === 'new') {
	$id = md5($name);
$favorites_table []= array(
    'id' => $id,
    'name' => $name,
    'logo' => $logo,
    'link' => $link,
    'bitrate' => $bitrate,
    'city' => $city,
    'language' => $language,
    'genre' => $genre,
	);	
	} else {
$favorites_table [$key]= array(
    'id' => $id,
    'name' => $name,
    'logo' => $logo,
    'link' => $link,
    'bitrate' => $bitrate,
    'city' => $city,
    'language' => $language,
    'genre' => $genre,
	);
	}

	if (@file_put_contents( $data_fc, '<?php $favorites_table = '.var_export( $favorites_table, true ).'; ?>' ))
	{
		echo "<h3>База успешно обновлена</h3>";
	}
	else { echo "<h3>Ошибка! Не удалось обновить базу!</h3>"; }
	return;
 }
 
	$id=htmlspecialchars($favorites_table[$sub]["id"]);
	$name=htmlspecialchars($favorites_table[$sub]["name"]);

	$logo=null;
	$logo=$favorites_table[$sub]["logo"];		
	$link=htmlspecialchars($favorites_table[$sub]["link"]);

	$bitrate=htmlspecialchars($favorites_table[$sub]["bitrate"]);
	$city=htmlspecialchars($favorites_table[$sub]["city"]);
	$language=htmlspecialchars($favorites_table[$sub]["language"]);
	$genre=htmlspecialchars($favorites_table[$sub]["genre"]);

		
//
?>


<html>

<head>
<title>Редактирование станции</title>
</head>

<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red">
<table border="1" width="803">
    <tr>
        <td width="606">
            <table border="1" width="688">
            <form method="get"action="redaktor_stanci_radio_top50.php">    
				<tr>
                    <td width="275">&nbsp;Название станции</td>
                    <td width="397">&nbsp;<input name="name" id="name" value='<? echo $name; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Идентификатор станции</td>
                    <td width="397">&nbsp;<input name="id" id="id" value='<? echo $id; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Логотип станции</td>
                    <td width="397">&nbsp;<input name="logo" id="logo" value='<? echo $logo; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Линк станции</td>
                    <td width="397">&nbsp;<input name="link" id="link" value='<? echo $link; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Битрейт потока</td>
                    <td width="397">&nbsp;<input name="bitrate" id="bitrate" value='<? echo $bitrate; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Город вещания</td>
                    <td width="397">&nbsp;<input name="city" id="city" value='<? echo $city; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Язык вещания</td>
                    <td width="397">&nbsp;<input name="language" id="language" value='<? echo $language; ?>' class="form" size="100%"></td>
                </tr>
                <tr>
                    <td width="275">&nbsp;Жанр вещания</td>
                    <td width="397">&nbsp;<input name="genre" id="genre" value='<? echo $genre; ?>' class="form" size="100%"></td>
                </tr>
            </table>
        </td>
	        <td width="43">            
                <p align="center">
				<input type="image" name="save" src="img/ok.PNG" title="Сохранить изменения" alt="Сохранить изменения">
				<input type="hidden" name="save" value="<?echo $sub ?>"></form></p>
</td>
            
            
    </tr>
</table>
</body>

</html>
