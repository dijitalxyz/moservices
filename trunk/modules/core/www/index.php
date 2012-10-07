<?php

// get mos path

$mos = '/usr/local/etc/mos';
$mos_www = dirname( $_SERVER['SCRIPT_FILENAME'] );
$mos_web = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';

include "modules/core/index.php";

?>