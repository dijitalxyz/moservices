<?php
require_once 'interfaces/view.inc';
require_once 'interfaces/service.inc';

require_once 'tools/config.inc';
require_once 'loader.inc';
require_once 'tools/logger.inc';
require_once 'tools/lang.inc';
require_once 'tools/sysinfo.inc';
require_once 'tools/statistic.inc';

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

$op   = isset($_GET['op'])   ? $_GET['op']   : "get";
$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
$id   = isset($_GET['id'])   ? $_GET['id']   : null;
$pl   = isset($_GET['pl'])   ? $_GET['pl']   : null;

$loader   = new Loader();
$cfg      = $loader->loadConfig();

$ttVersion =  "1.?.?";
$newVersionFound = false;
try {
	$ttVersion = SysInfo::getVersion();
	$lastTtVersion = SysInfo::getLastVersion();	
    if(version_compare($ttVersion, $lastTtVersion , '<')) {
    	$newVersionFound = true;
    } 
} catch(Exception $e) {
}
$cfg->set("version",$ttVersion,  Configuration::$RUNTIME_SECTION);

$platform = $cfg->get("platform",null,null);
//define platform if not defined
if(!isset($platform)) {
	$platform = SysInfo::getPlatform();
}
$cfg->set("platform",$platform);

$log = Logger::getLogger("Root");

$log->setDefaultLevel($cfg->get("logLevel",null,0));

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

$customFileName = $cfg->get("ini_path","").$cfg->get(Configuration::$CUSTOM_FILE_KEY, null, Configuration::$CUSTOM_FILE);

$cfg->addFile($customFileName, true);

$view     = $loader->loadView($cfg);
$services = $loader->loadServices($cfg);

$lastPopupDate = $cfg->get('lastVersionPopup', Configuration::$CUSTOM_SECTION);
$currentDate = date("Y-m-d");


/*check for new version.*/
//TODO: full update service  
if($newVersionFound && $currentDate != $lastPopupDate) {

	$localeName = $cfg->get("Locale",null,"en_EN");
    $lang = new Lang();
    $locale = $lang->getLocale($localeName);
	$msg = new MediaObject("1", Provider::$OBJ_MESSAGE,$locale->msg('new_version_title'));
	$msg->setParam("descr", $locale->msg('new_version_msg',$lastTtVersion));

    $view->setLocale($locale);
	$view->drawObject($msg, $mode);

	// save last popup date  
	$cfg->set('lastVersionPopup', $currentDate, Configuration::$CUSTOM_SECTION);
	$cfg->saveSection(Configuration::$CUSTOM_SECTION, $customFileName);
} else {
	if (null == $pl || ! isset($services[$pl])) {
		$servicesObj = $loader->getServicesMediaObject($cfg, $services);
		$servicesObj->sortChildren('demoService', 'title');
		$servicesObj->setParam("selectedService", $cfg->get('lastService', Configuration::$CUSTOM_SECTION));

		$localeName = $cfg->get("Locale",null,"en_EN");
	    $lang = new Lang(); 
	    $view->setLocale($lang->getLocale($localeName));

		$view->drawObject($servicesObj, $mode);
	} else {
		$statistic = new Statistic();
		$statistic->init($cfg);
		$statistic->reportUsage($_GET);

		$cfg->set('currentService', $pl, Configuration::$RUNTIME_SECTION);
		// save last selected service
		$cfg->set('lastService', $pl, Configuration::$CUSTOM_SECTION);
		$cfg->saveSection(Configuration::$CUSTOM_SECTION, $customFileName);

		$services[$pl]->init($cfg);
		$services[$pl]->draw($view, $op, $mode, $id, $_GET);
	}
}
?>
