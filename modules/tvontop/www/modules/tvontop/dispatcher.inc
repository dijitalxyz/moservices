<?php
require_once 'interfaces/view.inc';
require_once 'interfaces/service.inc';

require_once 'tools/config.inc';
require_once 'loader.inc';
require_once 'tools/logger.inc';
require_once 'tools/lang.inc';
require_once 'tools/sysinfo.inc';
require_once 'tools/statistic.inc';

class Dispatcher {
	
	protected $cfg;
	protected $loader;
	public function getCurrentVersion() {
		$ttVersion =  "1.?.?";
		try {
			$ttVersion = SysInfo::getVersion();
		} catch(Exception $e) {

		}
		return $ttVersion;
	}

	public function getLastVersion() {
		$lastTtVersion = "1.?.?";
		try {
			$lastTtVersion = SysInfo::getLastVersion();
		} catch(Exception $e) {
		}
		return $lastTtVersion;
	}

	public function init() {
		$this->loader   = new Loader();
		$this->cfg      = $this->loader->loadConfig();
	}
	
	public function getConfig() {
		return $this->cfg;
	}
	
	public function dispatchRequest($serviceId, $params) {
		
		$op   = isset($params['op'])   ? $params['op']   : "get";
		$mode = isset($params['mode']) ? $params['mode'] : "";
		$id   = isset($params['id'])   ? $params['id']   : null;
		

		$currentVersion = $this->getCurrentVersion();
		$this->cfg->set("version",$currentVersion,  Configuration::$RUNTIME_SECTION);

		$lastTtVersion = $this->getLastVersion();
		$newVersionFound  = version_compare($currentVersion, $lastTtVersion , '<');

		//platform for debug
		$platform = isset($params['platform'])   ? $params['platform']   : null;
		if(!isset($platform)) {
			//read platform from ini
			$platform = $this->cfg->get("platform",null,null);
		}
		//define platform if not defined
		if(!isset($platform)) {
			$platform = SysInfo::getPlatform();
		}

		$this->cfg->set("platform",$platform);
		
		$log = Logger::getLogger("Root");
		
		$log->setDefaultLevel($this->cfg->get("logLevel",null,0));
		
		$this->cfg->set('cfg_realpath', dirname($_SERVER["SCRIPT_FILENAME"])."/" , Configuration::$RUNTIME_SECTION);
		
		$customFileName = $this->cfg->get("ini_path","").$this->cfg->get(Configuration::$CUSTOM_FILE_KEY, null, Configuration::$CUSTOM_FILE);

		$this->cfg->addFile($customFileName, true);

		$view     = $this->loader->loadView($this->cfg);
		$services = $this->loader->loadServices($this->cfg);

		$localeName = $this->cfg->get("Locale",null,"en_EN");
		$lang = new Lang();
		$locale = $lang->getLocale($localeName);
		    
		/*check for new version.*/
		//TODO: full update service  
		$resultObj = null;
		$lastPopupDate = $this->cfg->get('lastVersionPopup', Configuration::$CUSTOM_SECTION);
		$currentDate = date("Y-m-d");
		if($newVersionFound && $currentDate != $lastPopupDate) {
			$resultObj = new MediaObject("1", Provider::$OBJ_MESSAGE,$locale->msg('new_version_title'));
			$resultObj->setParam("descr", $locale->msg('new_version_msg',$lastTtVersion));
		 	// save last popup date  
			$this->cfg->set('lastVersionPopup', $currentDate, Configuration::$CUSTOM_SECTION);
			$this->cfg->saveSection(Configuration::$CUSTOM_SECTION, $customFileName);
		} else {
			if (null == $serviceId || ! isset($services[$serviceId])) {
				//draw service list by default
				$resultObj = $this->loader->getServicesMediaObject($cfg, $services);
				$resultObj->sortChildren('demoService', 'title');
				$resultObj->setParam("selectedService", $this->cfg->get('lastService', Configuration::$CUSTOM_SECTION));
			} else {
				$statistic = new Statistic();
				$statistic->init($this->cfg);
				$statistic->reportUsage($params);
				$this->cfg->set('currentService', $serviceId, Configuration::$RUNTIME_SECTION);
				// save last selected service
				$this->cfg->set('lastService', $serviceId, Configuration::$CUSTOM_SECTION);
				$this->cfg->saveSection(Configuration::$CUSTOM_SECTION, $customFileName);
		
				$services[$serviceId]->init($this->cfg);
				$resultObj = $services[$serviceId]->processRequest($op, $id, $params);
				if (null != $resultObj) {
		        	if (null == $mode || "" == $mode) {
		        		$mode = $resultObj->getParam("mode");
		        	}
		        }
			}
			if(null != $resultObj) {
				//$localeName = $this->cfg->get("Locale",null,"en_EN");
			    //$lang = new Lang(); 
			    //$view->setLocale($lang->getLocale($localeName));
			    $view->setLocale($locale);
				return $view->drawObject($resultObj, $mode);
			}
		}
		return;
	}
}
?>