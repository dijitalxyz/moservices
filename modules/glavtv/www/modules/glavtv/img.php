<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'tools/config.inc';
require_once 'tools/cache.inc';
require_once 'tools/logger.inc';

// url parameter is required
if (! isset($_GET['url'])) {
    die ("No URL parameter supplied!");
}

$url = $_GET['url'];
$srv = isset($_GET['srv']) ? $_GET['srv'] : null;
$cfg = new ConfigFile('platform.ini');
$cfg->mergeFile($cfg->get('cfgPath') . '/config.ini');

# init logger
Logger::init($cfg);

$cache   = new Cache($cfg, $srv);
$content = $cache->getContent($url);

header("Content-Type: image/jpeg");
print $content;
flush();
?>
