<?php
require_once 'mediaObjects.inc';
require_once 'tools/cache.inc';
require_once 'tools/lang.inc';

abstract class Provider {

	public static $OBJ_STREAM_URL   = "STREAM_URL";
	public static $OBJ_CHANGE_AUTH  = "CHANGE_AUTH";
	public static $UNKNOWN_REQUEST  = "UNKNOWN_REQUEST";

	public static $OBJ_ERROR   		= "ERROR";
	public static $OBJ_MESSAGE   	= "MESSAGE";

	/* Add other object types here*/
	//dynamicaly defined key binding
	public static $OBJ_KEY          = "KEY";

    protected $config;
    protected $log;
    protected $sectionName;
    protected $cookieName;
    protected $baseUrl;
    protected $subProvider;
    protected $locale;
    
    protected $username;
    protected $password;
    
    public function setSubprovider(Provider $nl) {
        $this->subProvider = $nl;
    }

    public function getSubprovider() {
        return $this->subProvider;
    }
    //reload this to set preffered locale for you provider
    public function getDefaultLocale() {
    	return "en_EN";
    }
    
    public function getLocale() {
        return $this->locale;
    }
    
    public function init(Configuration &$cfg) {
        $this->config = $cfg;
        $this->config->addFile($this->getIniFilename());
        $localeName = $this->config->getByFile($this->getIniFilename(), "Locale",null,null);
        if (null == $localeName) {
        	$localeName = $this->config->get("Locale",null,$this->getDefaultLocale());
        }
        $lang = new Lang(); 
        $this->locale = $lang->getLocale($localeName);
        // fallback to default locale
        if (null == $this->locale) {
        	$this->locale = $lang->getLocale($this->getDefaultLocale());
        }
        $this->loadAuthData();
        if (null != $this->getSubprovider()) {
        	$this->getSubprovider()->init($cfg);
        }
    }

    /* returns list of mediaobjects*/
    public function getChildren(MediaObject $parentObject) {
    }

    public function getObject($objectId, $params) {
        $objectType = $this->getParam($params, "type", null);
		if ($objectType == self::$OBJ_CHANGE_AUTH) {
			$resultObj = CommonProvider::getObject(CommonProvider::$ID_CHANGE_AUTH);
			$this->loadAuthData();
            $resultObj->setParam("username", $this->username);
            $resultObj->setParam("password", $this->password);
            return $resultObj;
		} else if (null != $this->getSubprovider()) {
		    return $this->getSubprovider()->getObject($objectId, $params);
        }

        return CommonProvider::getObject(CommonProvider::$ID_ERROR,
            array("message" => "Result object is null for type $objectType"));
    }

 	public function setObject($objectId, $params) {
        if (null != $this->getSubprovider()) {
            return $this->getSubprovider()->setObject($objectId, $params);
        }
    }

	public function postProcess($object) {
		return $object;
	}
	/* returns path to provider-specific ini-file */
    public function getIniFilename() {
        return "";
    }
    
    public function getParam($params, $name, $default = null) {
        return isset($params[$name]) ? $params[$name] : $default;
    }
    
    protected function loadAuthData() {
        $this->username = $this->config->get("username", $this->sectionName);
        $this->password = $this->config->get("password", $this->sectionName);
        if (! isset($this->username)) {
            $this->loadDefaultAuthData();
        }
    }

    protected function loadDefaultAuthData() {
    }

    protected function isDemoAccount() {
        $username = $this->config->get("username", $this->sectionName);
        $password = $this->config->get("password", $this->sectionName);

        // password is allowed to be null, it's not demo account case
        return $username == null;
    }
}
?>
