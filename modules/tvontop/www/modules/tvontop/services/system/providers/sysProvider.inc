<?php
require_once 'interfaces/provider.inc';
require_once 'loader.inc';
require_once "tools/http.inc";

class SysProvider extends Provider {
	
	function init(Configuration &$cfg) {
		parent::init($cfg);
      	$this->sectionName = $this->config->get("name", "Service");
	}

	function getOnscreenKeyboard($objectId, $params) {
		$keybObj = new MediaObject($objectId, self::$OBJ_KEYBOARD,"Keyboard");
		$keybObj->setParam("special_keys", "special_keys.txt");
		
		$localeObj = new MediaObject("ru", self::$OBJ_KEY_LOCALE,"ru");
		$localeObj->setParam("layout", "layout_ru.txt");
		
		$keybObj->addChild($localeObj);
		 
		$localeObj = new MediaObject("de", self::$OBJ_KEY_LOCALE,"de");
		$localeObj->setParam("layout", "layout_de.txt");
		$keybObj->addChild($localeObj);
		return $keybObj;
		
	}
	function getObject($objectId,$params) {
		$objectType = $this->getParam($params,"type",self::$OBJ_KEYBOARD);
		if (self::$OBJ_KEYBOARD == $objectType) {
			$retObj = $this->getOnscreenKeyboard($objectId, $params);
		}
		return $retObj;
	}

	function getIniFilename() {
		return dirname(__FILE__) . "/config.ini";
	}
}
?>