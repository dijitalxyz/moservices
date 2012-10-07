<?php
require_once 'interfaces/service.inc';
require_once 'tools/config.inc';
require_once 'tools/logger.inc';

class Loader {
    public static $OBJ_SERVICES_LIST = "SERVICES_LIST";
    public static $OBJ_SERVICE       = "SERVICE";
    
    protected $log;

	public function Loader() {
		$this->log = Logger::getLogger(get_class());
	}
    public function loadConfig() {
        $cfg = new Configuration();
        $cfg->readFile("config.ini", true);
        $iniPath = $cfg->get("ini_path","");
        $cfg->addFile($iniPath."auth.ini", true);
        return $cfg;
    }

    public function loadView(&$cfg) {
        $viewClass = $this->findView($cfg);
        $view = $this->createView($viewClass);
        $view->init($cfg);
        return $view;
    }

    protected function createView($class, $dir = 'views') {
        $file = strtolower($class[0]) . substr($class, 1) . ".inc";
        foreach (scandir($dir) as $entry) {
            $fullname = "$dir/$entry/$file";
            if (0 !== strpos($entry, '.') && is_readable($fullname)) {
                require_once $fullname;
                return new $class();
            }
        }
        return new $class();
    }


    protected function findView(&$cfg) {
        // default type
        $type = 'default';

        // select view by agent
        $realAgent = $_SERVER['HTTP_USER_AGENT'];

		$log = Logger::getLogger(get_class());

        if(isset($log)) {
        	$log->debug("UserAgent: $realAgent");
        }  
        
        // set user agent type
        // for debug purposes let the user overwrite uat
        if (isset($_GET['uat'])){
            $type = $_GET['uat'];
        } else if (null != $cfg->get('force_agent')) {
            $type = $cfg->get('force_agent');
        } else {
            // define uat by user agent

            // TODO: Read from config!
            $supportedAgents = array('Mozilla', 'RealtekVOD', 'Syabas');
            foreach ($supportedAgents as $agent) {
                if (false !== stripos($realAgent, $agent)) {
                    $type = $agent;
                    break;
                }
            }
        }

        $viewClass = $cfg->get($type,"user_agents");

        $cfg->set('user_agent', $type,Configuration::$RUNTIME_SECTION);
        $cfg->set('selected_view', $viewClass, Configuration::$RUNTIME_SECTION);

        return $viewClass;
    }

    public function loadServices(&$cfg, $dir = 'services') {
        $services = array();
        foreach (scandir($dir) as $entry) {
            $file = "$dir/$entry/service.xml";
            if (0 !== strpos($entry, '.') && is_readable($file)) {
                $descriptor = simplexml_load_file($file);
                $service = new Service($descriptor, "$dir/$entry");
                $services[$service->getShortName()] = $service;
            }
        }
        return $services;
    }

    public function getServicesMediaObject(&$cfg, $services) {
        $result = new MediaObject("0", self::$OBJ_SERVICES_LIST, 'Available Services');
        foreach($services as $pl => $service) {
        	if(!$service->isHidden()) {
	            $obj = new MediaObject($service->getShortName(),
	                self::$OBJ_SERVICE, $service->getName());
	            $obj->setParam("icon",        $service->getLogo());
	            $obj->setParam("version",     $service->getVersion());
	            $obj->setParam("author",      $service->getAuthor());
	            $obj->setParam("description", $service->getDescription());
	            $obj->setParam("displayName", $service->getDisplayName());
	            $obj->setParam("demoService", null == $service->getProviderDescriptor());
	            if(isset($service->keys)) {
		            foreach ($service->keys as $name => $params) {
		            	$keyObj = new MediaObject($name,Provider::$OBJ_KEY, $name);
		            	if(isset($service->keysDescr)) {
		            		$keyObj->setParam("description",$service->keysDescr[$name]);
		            	}
		            	$keyObj->setParam("params", $params);
		            	$obj->addChild($keyObj);
		            }
	            }
	            $result->addChild($obj);
        	}
        }
        return $result;
    }
}
?>
