<?
$radio_top50_fc = '/usr/local/etc/mos/www/modules/radio_top50/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}

function radio_top50_sets_head()
{
?>

<style>
table.set_list
{
	padding:0;
	margin:0;
	white-space:nowrap;
}
.set_list th
{
	background-color: #d0d0d0;
	font-weight: bold;
	text-align:left;
}

.set_list td.set_delim
{
	border-top:1px solid #ccc;
}

div.set_card
{
	position:relative;
	display:block;
	float:left;
	padding:0px;
	margin:0px;

	background-color:white;

	border:1px solid #ccc;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
	width: 100%;
}
button.key1
{
	position:static;
	float:left;
	display:block;
	line-height:130%;
	text-decoration:none;
	font-weight:normal;
	cursor:pointer;
	padding:3px 6px 3px 6px; /* Links */
	margin: 2px;

	background-color:#ddd;
	border:#ccc 1px solid;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
}

button.key1:hover, a.key1:hover, a.key1:active
{
	color: black;
	background-color:#fff;
	border:1px solid #aaa;
}

</style>


<?php

}

function radio_top50_sets_actions( $act, $log )
{
global $radio_top50_config;
global $radio_top50_fc;

 if( $act != 'set' ) return;
 
if($act)
{

	 $refresh_time_chag=$_REQUEST['refresh_time_chag'];
	 $screen_time=$_REQUEST['screen_time'];
	 $screensaver_time=$_REQUEST['screensaver_time'];
	$radio_top50_config['refresh_time_chag'] = $refresh_time_chag;
	$radio_top50_config['screen_time'] = $screen_time;
	$radio_top50_config['screensaver_time'] = $screensaver_time;
	
	if (@file_put_contents( $radio_top50_fc, '<?php $radio_top50_config = '.var_export( $radio_top50_config, true ).'; ?>' ))
	{
		null;
	}	else { echo "<h3>Ошибка! Не удалось сохранить конфигурацию!</h3>"; }	

}

$refresh_time_chag = $radio_top50_config['refresh_time_chag'];
$screen_time = $radio_top50_config['screen_time'];
$screensaver_time = $radio_top50_config['screensaver_time'];
}

function radio_top50_sets_body()
{
global $radio_top50_config;
$refresh_time_chag = $radio_top50_config['refresh_time_chag'];
$screen_time = $radio_top50_config['screen_time'];
$screensaver_time = $radio_top50_config['screensaver_time'];

$refresh_time = array(
	"0" => 'Выключено',
	"10" => '10',
	"20" => '20',
	"30" => '30',
);

$screen = array(
	"0" => 'Выключен',
	"30" => '30',
	"60" => '60',
	"90" => '90',
	"120" => '120',
	"180" => '180',
);

$screensaver = array(
	"2" => '2',
	"5" => '5',
	"10" => '10',
	"15" => '15',
	"20" => '20',
);
?>

<div id="container">
<h3>Настройка модуля radio_top50</h3>

<div class="set_card">

<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="?page=radio_top50_sets&act=set" method="post">
    <tr>
        <td>Время обновления панели информации, сек</td>
        <td>
            <select name="refresh_time_chag" size=1 style="width: 100px; background-color: white; color: black;">
<?php
	foreach( $refresh_time as $id => $info)
	{
		if( $id == $refresh_time_chag ) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$id\"$sel>$info</option>\n";
	}
?>				
</SELECT></td></tr>
    <tr>
        <td>Время, через которое будет включен скринсейвер, сек</td>
        <td width="100%">
            <select name="screen_time" size=1 style="width: 100px; background-color: white; color: black;">
<?php
	foreach( $screen as $id => $info)
	{
		if( $id == $screen_time ) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$id\"$sel>$info</option>\n";
	}
?>								
</SELECT></td></tr>
    <tr>
        <td>Время обновления скринсейвера, сек</td>
        <td width="100%">
            <select name="screensaver_time" size=1 style="width: 100px; background-color: white; color: black;">
<?php
	foreach( $screensaver as $id => $info)
	{
		if( $id == $screensaver_time ) $sel = ' selected';
		else $sel = '';
		echo "<option value=\"$id\"$sel>$info</option>\n";
	}
?>								
</SELECT></td></tr> 
 <tr>
         <td colspan="2" class="set_delim">
<button class="key1" type="submit" style="text-align=right"><?= getMsg( 'coreCmSave') ?></button>
		</td>
	</tr>
</form></table>
</div>
</div>
<?php
}
?>
