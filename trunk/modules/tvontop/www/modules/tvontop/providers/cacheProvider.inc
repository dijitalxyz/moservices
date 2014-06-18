<?php
require_once 'interfaces/provider.inc';
require_once 'tools/logger.inc';
require_once 'tools/httpSession.inc';

class CacheProvider extends Provider {

	//save path for cache
	protected 	$savePath = "temp/cache/";
	//file for cache catalog
	protected 	$cachedObjectsFilePath = "temp/cache/list.txt";
	// max size of objects to cache in session
	public 		$maxMemcachedSize  	= 1024;
	//max TTL to save in session
	public 		$maxMemCachedTTL 	= 3600;
	//clear cache every X seconds
	public 		$cacheClearIntervall 	= 3600;// 1 hour
	//default TTL for objects. Better is to set TTL from provider
	protected 	$defaultCacheTime = 300;//5 min.  

	protected 	$session = null;

	//Session keys
	public static $CACHE_CATALOG_KEY  	= "CACHE_CATALOG";
	public static $CACHE_KEY  			= "CACHE";
	public static $CACHE_CLEARED_KEY  	= "CACHE_LAST_CLEARED";

	function CacheProvider() {
		$this->log = Logger::getLogger(get_class());
		$this->log->setFilepath("temp/log/cache.log"); 
		$this->log->setDefaultLevel(10);
		$this->session = new HttpSession();
	}

	function init(Configuration &$cfg) {
        parent::init($cfg);
        //read config values
        if(null != $cfg) {
        	$this->defaultCacheTime = $cfg->get("cache_lifetime",null,$this->defaultCacheTime);
        	
        	$this->savePath = $cfg->get("cache_path",null,$this->savePath);
        	
        	$this->cachedObjectsFilePath = $this->savePath . "cache_list.txt";
			
			$this->maxMemcachedSize = $cfg->get("cache_max_session_size",null,$this->maxMemcachedSize);
			
			$this->maxMemCachedTTL = $cfg->get("cache_max_session_ttl",null,$this->maxMemCachedTTL);
			
			$this->cacheClearIntervall = $cfg->get("cache_clear_intervall",null,$this->cacheClearIntervall);
			
			$this->log->setFilepath($cfg->get("log_path",null,"temp/log/")."cache.log");
			 
        	$this->log->setLevel($cfg->get("loglevel_cache",null,0));

        }
        
		if(!is_dir($this->savePath)) {
			mkdir($this->savePath,0666,TRUE);
		} 

        if(!$this->session->exists(self::$CACHE_CATALOG_KEY)) {
			if(file_exists($this->cachedObjectsFilePath)) {
				$this->session->set(self::$CACHE_CATALOG_KEY,parse_ini_file($this->cachedObjectsFilePath,false));
			} else {
				$this->session->set(self::$CACHE_CATALOG_KEY,array());
			}
		}
		if(!$this->session->exists(self::$CACHE_CLEARED_KEY) || 
			time() - $this->session->get(self::$CACHE_CLEARED_KEY) > $this->cacheClearIntervall) {
			$this->clearCache();
			$this->session->set(self::$CACHE_CLEARED_KEY,time());
		} 
		//TODO: check free space (disk_ free_ space)
    }

	function clearCache() {
		$this->log->debug("Clear cache");
		if($this->session->exists(self::$CACHE_CATALOG_KEY)) {
			$timestamp = time();
			foreach ($this->session->get(self::$CACHE_CATALOG_KEY) as $cacheId => $expireDate) {
				if($timestamp >= $expireDate) {
					$this->deletefromCache($cacheId);
				}
			}
			$this->saveCatalog();
		}
	}

    function getCacheId($objectId, $type) {
    	$cacheId  = $this->config->get("currentService","Runtime") . "_";
    	$currentUser = $this->config->get("username",$this->config->get("name","Service"));
    	if(isset($currentUser ) && "" != $currentUser) { 
    		$cacheId .= $currentUser . "_"; 
    	}
    	$cacheId .= $type ."_";
    	$cacheId .= $objectId;
		return $cacheId;
    }

    function getCacheFilename($cacheId) {
    	return $this->savePath.$cacheId.".txt";
    }

    function cacheObject($obj, $cacheId = null) {
    	$expireDate = $obj->getExpireDate();
    	$timestamp 	= time();
    	if($expireDate == 0 || ($expireDate > 0 && $expireDate <= $timestamp)) {
			// no cache for this object
			return;
		}

		if($expireDate == -1) {
			//default cache time, but it is better to set expire timr by provider 
			$expireDate  = $timestamp + $this->defaultCacheTime;
		}

		if(!$this->session->exists(self::$CACHE_CATALOG_KEY)) {
			$this->session->set(self::$CACHE_CATALOG_KEY, array());
		}

		if(!isset($cacheId)) {
			$cacheId = $this->getCacheId($obj->getId(), $obj->getType());
		}
		$strObj = serialize($obj);
		//size if object
		$size = strlen($strObj);
		//cache to memory if object is not too big and TTL is not to long
		// set   maxMemcachedSize to 0 to turn mem cache off.
		if($size < $this->maxMemcachedSize && ($expireDate - $timestamp) < $this->maxMemCachedTTL) {
			$this->saveToMemory($cacheId, $strObj);
		} else {
			$this->saveToDisk($cacheId, $strObj);
			$this->updateCatalog($cacheId,$expireDate);
		}
		foreach ($obj->getChildren() as $child) {
			$this->cacheObject($child);
		}
    }

	function updateCatalog($cacheId, $expireDate) {
		$catalog = $this->session->get(self::$CACHE_CATALOG_KEY);
		$catalog[$cacheId] = $expireDate;
		//?? do we need this
		$this->session->set(self::$CACHE_CATALOG_KEY,$catalog);
		$this->saveCatalog();
	}

	function saveCatalog() {
		$strCatalog = "";
		$catalog = $this->session->get(self::$CACHE_CATALOG_KEY);
		foreach ($catalog as $key => $expDate) {
			$strCatalog .= $key."=".$expDate."\r\n";
		}
		file_put_contents ($this->cachedObjectsFilePath, $strCatalog);
	}

	function saveToDisk($cacheId, $strObj) {
		$this->log->debug("Cache Object [".$cacheId."] to disk.");
    	$file = $this->getCacheFilename($cacheId);
		file_put_contents($file, $strObj);
		return $file;
    }

    function saveToMemory($cacheId, $strObj) {
    	$this->log->debug("Cache Object [".$cacheId."] to session.");
    	$this->session->set($cacheId,$strObj);
		return "SESSION";
    }

    function getCachedObject($objId, $objectType) {
    	$obj = null;
    	$cacheId = $this->getCacheId($objId, $objectType);
    	$catalog = $this->session->get(self::$CACHE_CATALOG_KEY);
    	if(isset($catalog[$cacheId])) {
    		$expireDate = $catalog[$cacheId];
    	}

    	if(isset($expireDate) && time() < $expireDate) {
    		//try to get obj from session
    		$cache = $this->session->get(self::$CACHE_KEY);
    		if(isset($cache[$cacheId])) {
    			$this->log->debug("Object [".$cacheId."] found in session cache.");
    			$strObj = $cache[$cacheId];
    		}
    		if(!isset($strObj) || "" == $strObj) {
		   		$filename = $this->getCacheFilename($cacheId);
    			if(file_exists($filename)) {
    				$this->log->debug("Object [".$cacheId."] found in file cache.");
		    		$strObj = file_get_contents($filename);
    			}
    		}
    		if(isset($strObj) && "" != $strObj) {
    			$obj = unserialize($strObj);
    		}
    	} else {
    		//delete object from cache
    		$this->deletefromCache($cacheId);
    	}
    	return $obj;	
    }
    
    function deletefromCache($cacheId) {
    	$catalog = $this->session->get(self::$CACHE_CATALOG_KEY);
    	if(isset($catalog[$cacheId])) {
    		unset($catalog[$cacheId]);
    	}
    	$cache = $this->session->get(self::$CACHE_KEY);
    	if(isset($cache[$cacheId])) {
    		$this->log->debug("Delete object [".$cacheId."] from session cache.");
    		unset($cache[$cacheId]);
    		$this->session->set(self::$CACHE_KEY,$cache);
    	}

   		$filename = $this->getCacheFilename($cacheId);
   		if(file_exists($filename)) {
			$this->log->debug("Delete object [".$cacheId."] from file cache.");
   			unlink($filename);
   		}
    }

	function getObject($objectId, $params) {
		$rawObj = null;
		$objectType = $this->getParam($params, "type");
		$cacheId = $this->getCacheId($objectId, $objectType);

   		$this->log->info("getObject(".$cacheId.")");

		$cacheObj = $this->getCachedObject($objectId, $objectType);

		if(null != $cacheObj) {
			if('MediaObject' == get_class($cacheObj) || is_subclass_of($cacheObj, 'MediaObject') ) {
				$this->log->info("Object [".$cacheId."] found in cache.");
				return $cacheObj;
			}
		}
		$this->log->info("Object [".$cacheId."] not found in cache.");
		$subProviderName  = get_class($this->getSubprovider()); 
		$this->log->info($subProviderName."->getObject(".$objectId.",".$objectType.")");
		$newObj = $this->getSubprovider()->getObject($objectId, $params);
		$this->log->info("done");
		if(null != $newObj) {
			$this->cacheObject($newObj,$cacheId);
		}
		return $newObj;
	}
}