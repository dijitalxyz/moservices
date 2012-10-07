<?php
#############################################################################
# Entry point for whole application.                                        #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

$starttime = microtime(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'tools/logger.inc';
require_once 'tools/config.inc';
require_once 'tools/sysinfo.inc';
require_once 'lang/lang.inc';
require_once 'invoker.inc';

session_name('GlavTV');
session_id(md5('GlavTVSid123456789' . SysInfo::getMac()));
session_start();

# merge POST parameters
$_GET += $_POST;

# read required get params
$srv = ! empty($_GET['srv']) ? $_GET['srv'] : 'utils';
$req = ! empty($_GET['req']) ? $_GET['req'] : 'startPage';
$tpl = ! empty($_GET['tpl']) ? $_GET['tpl'] : null;

# special handling for switchable templates
$nexttpl = isset($_GET['nexttpl']) && $_GET['nexttpl'] > 0;
unset($_GET['nexttpl']);

# special handling for forced templates
if (isset($_GET['forcetpl'])) {
    $tpl = $_GET['forcetpl'];
    $forceTpl = "&forcetpl=$tpl";
} else {
    $forceTpl = '';
}

# init configuration files
$cfg = new ConfigFile('platform.ini');
$cfg->mergeFile($cfg->get('cfgPath') . '/config.ini');
$cfg->mergeFile($cfg->get('cfgPath') . '/auth.ini');

# funny messages support
if ($srv != 'utils' && @$_SESSION['fmAllowed'] && rand(1, 100) <= @$_SESSION['fmRate']) {
    $srv = 'utils';
    $req = 'makeFun';
    $_SESSION['fmAllowed'] = false;
}

# information file should be respected
if ($srv == 'utils' && $req == 'startPage' && is_readable($cfg->get('infoFile', 'information.txt'))) {
    $req = 'information';
}

# define platform if not defined
$platform = $cfg->get('platform');
if ('' == $platform) {
    $platform = SysInfo::getPlatform();
    $cfg->set('platform', $platform);
}

# check margins (lower priority, values can be overwritten)
if ($req != 'saveIniParams' && null == $cfg->get('marginX')) {
    $srv = 'utils';
    $req = 'margins';
}

# set the time zone (middle priority, values can be overwritten)
$timezone = $cfg->get('timezone');
if ('' == $timezone) {
    $timezone = 'Europe/Berlin';
    if ($req != 'saveIniParams') {
        $srv = 'utils';
        $req = 'timezones';
    }
}
date_default_timezone_set($timezone);

# init logger
Logger::init($cfg);
$log = Logger::getLogger('index');

# load proper language (highest priority, values cannot be overwritten)
$langId = $cfg->get('lang');
if ('' == $langId) {
    $langId = 'en';
    if ($req != 'saveIniParams') {
        $srv = 'utils';
        $req = 'languages';
    }
}
$lang = new LangTools($langId);

# set common use constants
$host = 'http://' . $_SERVER['HTTP_HOST'];
$cfg->set('home_url',      $host . dirname($_SERVER['PHP_SELF']) . '/');
$cfg->set('home_disk',     dirname(__FILE__) . '/');
$cfg->set('resource_url',  $cfg->get('home_url')  . 'templates/');
$cfg->set('resource_disk', $cfg->get('home_disk') . 'templates/');
$cfg->set('service_url',   $cfg->get('home_url')  . "?srv=$srv" . $forceTpl);
$cfg->set('self_url',      $cfg->get('home_url')  .
    (empty($_GET) ? '' : '?' . http_build_query($_GET)));

$projectName = $lang->msg($cfg->get('projectName', SysInfo::getProjectName()));
$version = SysInfo::isDevEnvironment() ? 'DEV' :
    $cfg->get('version', SysInfo::getVersion());

if ($srv != 'utils' && $srv != 'fav' && $req != 'play' && ! SysInfo::isDevEnvironment()) {
    $_SESSION['fmAllowed'] = true;
} 
$log->info('Got request: ' . $cfg->get('self_url'));
$invoker = new Invoker($cfg, $lang);
$invoker->processRequest($srv, $req, $tpl, $nexttpl);

$endtime = microtime(true);
$processing_time = (int)(($endtime - $starttime) * 1000);
$log->warn('Processed in ' . $processing_time . ' ms ' .
    parse_url($cfg->get('self_url'), PHP_URL_QUERY));

?>
