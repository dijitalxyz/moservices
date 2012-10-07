<?php
require_once 'interfaces/provider.inc';

class CfgProvider extends Provider {

	public static $OBJ_CONFIG		= "CONFIG";
	public static $OBJ_INI_FILE		= "INI_FILE";
	public static $OBJ_INI_SECTION	= "INI_SECTION";
	public static $OBJ_INI_PARAM	= "INI_PARAM";
    public static $OBJ_SET_INI_SECTION = "SET_INI_SECTION";
	
	public static $INI_FILE_TITLE = "iniTitle";
		
	function getSection($cfg, $sectionName) {
	}

	function getAllConfigs($params) {
		$rootObj = new MediaObject(0, self::$OBJ_CONFIG,"Config List");
		foreach ($this->config->files as $fileName =>$fields) {
			$title = $fileName;
			if(isset($fields[self::$INI_FILE_TITLE])) {
				$title  = $fields[self::$INI_FILE_TITLE]; 
			}
			$fileObj = new MediaObject($fileName, self::$OBJ_INI_FILE,$title);
/*			foreach ($fields as $fieldName =>$value) {
				if(is_array($value)) {
					$fileObj->addChild($this->getSection($cfg, $sectionName))					
				}
			}
			$fileObj
			*/ 
			$rootObj->addChild($fileObj);
		}
		return $rootObj;
	}
	
	function getIniFile($objectId, $params) {
		$iniFile = $this->config->files[$objectId];
		if(isset($iniFile)) {
			$title = $objectId;
			if(isset($iniFile[self::$INI_FILE_TITLE])) {
				$title  = $iniFile[self::$INI_FILE_TITLE]; 
			}
			$rootObj = new MediaObject($objectId,self::$OBJ_INI_FILE,$title);
			foreach ($iniFile as $key =>$val) {
				if(is_array($val)) {
					$sectObj = new MediaObject($objectId . "/" . $key,self::$OBJ_INI_SECTION,$key);
					$rootObj->addChild($sectObj);
				}
				else {
					$rootObj->setParam($key, $val);
				}
			}
		}
		return $rootObj;
	}
	function getIniSection($objectId, $params) {
		$ids = explode("/", $objectId);
		if(isset($this->config->files[$ids[0]]) && isset($this->config->files[$ids[0]][$ids[1]])) {
			$iniSection = $this->config->files[$ids[0]][$ids[1]];
			$rootObj = new MediaObject($objectId,self::$OBJ_INI_SECTION,$ids[1]);
			foreach ($iniSection as $key =>$val) {
				$rootObj->setParam($key, $val);
			}
		}
		return $rootObj;
	}

	function getObject($objectId,$params) {
		$retObj = null;
		if (null == $objectId) {
			$retObj  = $this->getAllConfigs($params);
		} else {
			$objectType = $this->getParam($params,"type");
			if (self::$OBJ_INI_FILE == $objectType) {
				$retObj = $this->getIniFile($objectId, $params);
			} else if( self::$OBJ_INI_SECTION == $objectType) {
				$retObj = $this->getIniSection($objectId, $params);
            } else if (self::$OBJ_SET_INI_SECTION == $objectType) {
            	$this->setObject($objectId, $params);
            	$retObj = null;
			}
		}
		return $retObj;
	}

	function setObject($objectId, $params) {
		$objectType = $this->getParam($params,"type");
		$ids = explode("/", $objectId);
		//HACK!!!
		if($ids[0] == "auth.ini") {
			$ids[0] =  $this->config->get("ini_path","").$ids[0];
		}
		
		if(self::$OBJ_INI_FILE == $objectType) {
			
		} else if(self::$OBJ_INI_SECTION == $objectType) {
			//$retObj  = $this->getIniSection($objectId, $params);
			$exclude = array("pl", "op", "id", "type", "send_sess_end", "original_url");
			if(!isset($this->config->files[$ids[0]])) {
				$this->config->files[$ids[0]] = array();
			}
			if(!isset($this->config->files[$ids[0]][$ids[1]])) {
				$this->config->files[$ids[0]][$ids[1]] = array();
			}
			foreach($params as $key => $val) {
				if(!in_array(strtolower($key), $exclude) && false === strpos(strtolower($key), "debug")) {
					$this->config->files[$ids[0]][$ids[1]][$key] = $val;
				} 
			}
		}
		$this->config->writeFile($this->config->files[$ids[0]], $ids[0], true);
		$retObj  = $this->getObject($objectId,$params);
        //;username=" + userName + "&amp;password=" + passWord;
	}

	function getIniFilename() {
		return dirname(__FILE__) . "/config.ini";
	}
}

?>