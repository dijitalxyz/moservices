<?php
#############################################################################
# Library with cache functions to improve perfomance						#
#                                                                           #
# Author: stalker 2011                                                      #
#############################################################################

define("TIMESTAMP_PREFIX", "ot_");

class CacheManager {
	
	protected $updateInterval = 300;
	protected $savePath = "/tmp/usbmounts/sda1/tmp/";
	protected $saveToDisk = true;
	
	function setUpdateInterval($interval ) {
		$this->updateInterval = $interval; 
	}
	function getObjectKey($objectId, $objectType) {
		return $objectType.'_'.$objectId;
	}

	function getObject($objectId, $objectType) {
		if($this->saveToDisk) {
			return $this->getObjectFromDisk($objectId, $objectType, $obj);
		} else {
			return $this->getObjectFromMemory($objectId, $objectType, $obj);
		}
	}
	
	function getObjectSaveTime($objectId, $objectType) {
		if(isset($_SESSION[TIMESTAMP_PREFIX.$objectType.'_'.$objectId])) {
			return $_SESSION[TIMESTAMP_PREFIX.$objectType.'_'.$objectId];
		}
	}
	
	function saveObject($objectId, $objectType, $obj) {
		if($this->saveToDisk) {
			$this->saveObjectToDisk($objectId, $objectType, $obj);
		} else {
			$this->saveObjectToMemory($objectId, $objectType, $obj);
		}
		foreach ($obj->getChildren() as $child) {
			$this->saveObject($child->getId(), $child->getType(), $child);
		}
		
	}
	
	function getObjectFromMemory($objectId, $objectType) {
		$rawObj = null;
		$objSaveTime = $this->getObjectSaveTime($objectId, $objectType);
		if (isset($_SESSION[$objectType."_".$objectId]) && 
			$objSaveTime > 0 &&
    		time() - $objSaveTime < $this->updateInterval) {
	    	$rawObj= unserialize($_SESSION[$objectType."_".$objectId]);
		}
		return $rawObj;
	}

	function getObjectfromDisk($objectId, $objectType) {
		$rawObj = null;
/*		$objSaveTime = $this->getObjectSaveTime($objectId, $objectType);
		if (isset($_SESSION[$objectType."_".$objectId]) && 
			$objSaveTime > 0 &&
    		time() - $objSaveTime < $this->updateInterval) {
	    	$rawObj= unserialize($_SESSION[$objectType."_".$objectId]);
		}
*/
		return $rawObj;
	}
	function saveObjectToDisk($objectId, $objectType, $obj) {
		$key 		= $this->getObjectKey($objectId, $objectType);
		$strObj 	= serialize($obj);
		$timestamp 	= time();
		$exd = $obj->getExpireDate();
		
		$file = $this->savePath.$key.".txt";

		$content = $timestamp."\r\n".$strObj;
		file_put_contents($file, $content);
		
/*		$_SESSION[$objectType."_".$objectId] = serialize($obj);
		$_SESSION[TIMESTAMP_PREFIX.$objectType.'_'.$objectId] = time();
		
		$o = $_SESSION[$objectType."_".$objectId];
*/
	}
	
	function saveObjectToMemory($objectId, $objectType, $obj) {
		$_SESSION[$objectType."_".$objectId] = serialize($obj);
		$_SESSION[TIMESTAMP_PREFIX.$objectType.'_'.$objectId] = time();
		
		$o = $_SESSION[$objectType."_".$objectId] ;
	}
}
?>