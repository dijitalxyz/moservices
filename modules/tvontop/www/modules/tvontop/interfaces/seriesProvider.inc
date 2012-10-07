<?php
#############################################################################
# Abstract interface to implement any service with series					#
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/provider.inc';
require_once 'providers/commonProvider.inc';

abstract class SeriesProvider extends Provider {
	
	public static $OBJ_GROUP_LIST   = "GROUP_LIST";
	public static $OBJ_SERIES_LIST	= "SERIES_LIST";
	public static $OBJ_SERIE		= "SERIE";
	public static $OBJ_SEASON		= "SEASON";
	public static $OBJ_EPISODE		= "EPISODE";
	public static $OBJ_SOUNDTRACK	= "SOUNDTRACK";
	public static $OBJ_SUBTITLE		= "SUBTITLE";

	public static $SELECT_ALL		= "all";
	public static $SELECT_FAVORITES	= "favorites";
	public static $SELECT_NEW		= "new";

	abstract protected function getSeriesList($objectId,$params);
	abstract protected function getFavoriteSeriesList($objectId,$params);
	abstract protected function getNewSeriesList($objectId,$params);
	abstract protected function getSerie($objectId,$params);
	abstract protected function getSeason($objectId,$params);
	abstract protected function getSoundtrack($objectId,$params);
	abstract protected function getSubtitle($objectId,$params);
	abstract protected function getStreamUrl($objectId,$params);
    abstract protected function addToFavorites($objectId,$params);
	abstract protected function removeFromFavorites($objectId,$params);    
    
    public function getObject($objectId, $params) {
        if ($this->isDemoAccount()) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_AUTH_DEMO_CONFIRM);
            $resultObj->setParam("username", $this->username);
            $resultObj->setParam("password", $this->password);
            return $resultObj;
        } 
                    
        // default response - list of series
        $objectType = $this->getParam($params, "type", self::$OBJ_SERIES_LIST);
            	
        if ($objectType == self::$OBJ_SERIES_LIST) {
            $selectType = $this->getParam($params, "select");
        	if ($selectType == self::$SELECT_FAVORITES) {
        		$resultObj = $this->getFavoriteSeriesList($objectId, $params);
        	} else if ($selectType == self::$SELECT_NEW) {
        		$resultObj = $this->getNewSeriesList($objectId, $params);
        	} else {
        		// default - select all series
        		$resultObj = $this->getSeriesList($objectId,$params);
        	}
        	if (false !== $resultObj && is_object($resultObj)) {
        		$resultObj->setParam("select", $selectType);
        		return $resultObj; 
        	}
        } else if ($objectType == self::$OBJ_SERIE) {
        	return $this->getSerie($objectId,$params);
        } else if ($objectType == self::$OBJ_SEASON) {
        	return $this->getSeason($objectId,$params);
        } else if ($objectType == self::$OBJ_SOUNDTRACK) {
        	return $this->getSoundtrack($objectId,$params);
        } else if ($objectType == self::$OBJ_SUBTITLE) {
        	return $this->getSubtitle($objectId,$params);
        } else if ($objectType == self::$OBJ_STREAM_URL) {
        	return $this->getStreamUrl($objectId,$params);
/*        } else if ($objectType == self::$OBJ_FAVORITES_ADD) {
            return $this->addToFavorites($objectId, $params);
        } else if ($objectType == self::$OBJ_FAVORITES_REMOVE) {
            return $this->removeFromFavorites($objectId, $params);
*/      }

        return parent::getObject($objectId, $params);
    }

	function setObject($objectId,$params) {
		$update = $this->getParam($params, "update");
		if ($update == "favorites_add") {
			return $this->addToFavorites($objectId, $params);
		} else if ($update == "favorites_remove") {
			return $this->removeFromFavorites($objectId, $params);
		}
    }
}
?>