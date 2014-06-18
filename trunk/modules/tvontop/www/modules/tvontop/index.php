<?php
error_reporting ( E_ERROR );
require_once 'dispatcher.inc';

/*migrate from 2.0.1*/
$oldCfg = getcwd() . '/config.ini';
$newCfg = getcwd() . '/config.ini.new';
$bkCfg  = getcwd() . '/config.ini.old';
if(file_exists($newCfg)) {
	rename($oldCfg, $bkCfg);
	rename($newCfg,$oldCfg);
}

//onetime popup function
$anonceFile = getcwd() . '/anonce.rss';
if(file_exists($anonceFile )) {
	readfile($anonceFile);
	unlink($anonceFile);
	exit();
}

session_name("TVonTopSID");
session_id("TVonTopSID123456789");
session_start();

$pl   = isset($_GET['pl'])   ? $_GET['pl']   : null;

$dispatcher = new Dispatcher();
$dispatcher->init();

$cfg = $dispatcher->getConfig();

//set max execution time
$maxExecTime = $cfg->get("max_exec_time", null, '');
if ('' != $maxExecTime) { 
	set_time_limit ($maxExecTime);
} 
// set runtime properties
date_default_timezone_set($cfg->get("timezone", null, 'Europe/Berlin'));
$processingUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$cfg->set('cfg_root_url', $processingUrl ,Configuration::$RUNTIME_SECTION);

if (isset($pl)) {
	$processingUrl .= "?pl=$pl";
}
$cfg->set('cfg_processing_url', $processingUrl ,Configuration::$RUNTIME_SECTION);

$cfg->set('cfg_home', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/", Configuration::$RUNTIME_SECTION);

$cfg->set('cfg_realpath', dirname($_SERVER["SCRIPT_FILENAME"])."/" , Configuration::$RUNTIME_SECTION);

$lastPopupDate = $cfg->get('lastVersionPopup', Configuration::$CUSTOM_SECTION);
$currentDate = date("Y-m-d");

$dispatcher->dispatchRequest($pl, $_GET);

?>
