<?php
require_once 'tools/config.inc';
require_once 'interfaces/provider.inc';
require_once 'interfaces/mediaObjects.inc';

class Service {
    protected $name;
    protected $shortName;
    protected $displayName;
    protected $description;
    protected $logo;
    protected $version;
	protected $hidden;
	protected $dir;
	protected $providerDescriptor;
    protected $provider;
	protected $config;
	public 	  $keys;
	public 	  $keysDescr;
	
    public function Service($descriptor, $dir) {
    	$this->dir		   = $dir;
        $this->name        = (string) $descriptor->name;
        //TODO: check for unic shortname
        $this->shortName   = (string) $descriptor->shortName;
        $this->displayName = (string) $descriptor->displayName;
        $this->description = (string) $descriptor->description;
        $this->logo        = (string) $descriptor->logo;
        $this->version     = (string) $descriptor->version;
        $this->author      = (string) $descriptor->author;
        $this->providerDescriptor    = ! isset($descriptor->provider) ? null : $descriptor->provider;  
        $this->hidden	   = ! isset($descriptor->hidden) ? false :  
            				((string) $descriptor->hidden == "true");
        if (! isset($this->logo) || "" == $this->logo) {
            $this->logo = "$dir/logo.png";
        }
        if(isset($descriptor->keys )) {
        	$this->keys			= array();
        	$this->keysDescr	= array();
			foreach ( $descriptor->keys->key as $keyDescr) {
				$this->keys[(string)$keyDescr->attributes()->name] = (string)$keyDescr->attributes()->params;
				$this->keysDescr[(string)$keyDescr->attributes()->name] = (string)$keyDescr;
			}
        }
    }

    public function getName() {
    	return $this->name;
    }
    public function getShortName() {
        return $this->shortName;
    }
    public function getDisplayName() {
        return $this->displayName;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getLogo() {
        return $this->logo;
    }
    public function getVersion() {
        return $this->version;
    }
    public function getAuthor() {
        return $this->author;
    }
    public function getProviderDescriptor() {
        return $this->providerDescriptor;
    }
    public function getProvider() {
        return $this->provider;
    }
    public function getConfig() {
        return $this->config;
    }
	public function isHidden() {
        return $this->hidden;
    }
    
    protected function createInstance($class, $dir = ".", $file = null) {
        if (! isset($file) || "" == $file) {
            $file = strtolower($class[0]) . substr($class, 1) . ".inc";
        }

        # first check service dir then common providers dir
        if (is_readable("$dir/providers/$file")) {
        	$file = "$dir/providers/$file";
        } else if (is_readable("providers/$file")) {
            $file = "providers/$file";
        } else {
            return null;
        }

        require_once $file;
        return new $class();
    }
    
    protected function createProviders($providerDescriptor, $dir) {
        if (! isset($providerDescriptor)) {
        	return null;
        }
    	
    	# accept only the first element if array is supplied
    	if (is_array($providerDescriptor)) {
    		$providerDescriptor = $providerDescriptor[0];
    	}
    	
    	$class = (string) $providerDescriptor->attributes()->class;
        $file  = (string) $providerDescriptor->attributes()->file;

        $provider = $this->createInstance($class, $dir, $file);
        if (isset($providerDescriptor->provider)) {
        	$subProvider = $this->createProviders(
                $providerDescriptor->provider, $dir);
        	$provider->setSubprovider($subProvider);
        }
        return $provider;
    }
    
	public function init(Configuration &$cfg) {
		$this->config = $cfg;
		if (!isset($this->provider)) {
			if (isset($this->providerDescriptor)) {
				$this->provider = $this->createProviders($this->providerDescriptor, $this->dir); 
			}
		}
		// save service config
		$this->config->set("name", 		  $this->getName(), 			"Service");
		$this->config->set("icon",        $this->getLogo(),			 	"Service");
		$this->config->set("version",     $this->getVersion(),		 	"Service");
		$this->config->set("author",      $this->getAuthor(),			"Service" );
		$this->config->set("description", $this->getDescription(),	 	"Service");
		$this->config->set("displayName", $this->getDisplayName(),	 	"Service");
		$this->config->set("demoService", null == $this->getProviderDescriptor(),	"Service");

		if (isset($this->provider)) {
            $this->provider->init($cfg);
		}
	}

	public function draw($view, $op, $mode, $id, $params) {

		$view->setLocale($this->provider->getLocale());
		$resultObj = null;
		try {
			if ($op == "get") {
                $resultObj = $this->provider->getObject($id, $params);
                $resultObj = $this->provider->postProcess($resultObj);
            } else if ($op == "set") {
                $resultObj = $this->provider->setObject($id, $params);
			}            
        } catch (AuthException $e) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_AUTH_ERROR, 
                array("message" => $e->getMessage()));
            $resultObj->setParam("username", $this->username);
            $resultObj->setParam("password", $this->password);
        } catch (NetworkException $e) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_ERROR,
                array("message" => $e->getMessage()));
        } catch (XmlParseException $e) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_ERROR,
                array("message" => $e->getMessage()));
        } catch (Exception $e) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_ERROR,
                array("message" => $e->getMessage()));
        }
        
        if (null != $resultObj) {
        	if (null == $mode || "" == $mode) {
        		$mode = $resultObj->getParam("mode");
        	}
            $view->drawObject($resultObj, $mode);
        }
    }
}
?>
