<?php
#############################################################################
# Abstract interface to implement any VoD service with categories/search	#
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/provider.inc';
require_once 'providers/commonProvider.inc';

abstract class VodProvider extends Provider {

    public static $OBJ_MOVIE_LIST = "MOVIE_LIST";
	public static $OBJ_MOVIE      = "MOVIE";
    public static $OBJ_SEARCH     = "SEARCH";
    
	abstract protected function getMovieList($objectId, $params);
    abstract protected function getMovie($objectId, $params);
    abstract protected function search($objectId, $params);
    abstract protected function getStreamUrl($objectId, $params);

    public function getObject($objectId,$params) {
        if ($this->isDemoAccount()) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_AUTH_DEMO_CONFIRM);
            $resultObj->setParam("username", $this->username);
            $resultObj->setParam("password", $this->password);
            return $resultObj;
        } 
                    
        // default response - list of movies
        $objectType = $this->getParam($params, "type", self::$OBJ_MOVIE_LIST);

        if ($objectType == self::$OBJ_MOVIE_LIST) {
            return $this->getMovieList($objectId, $params);
        } else if($objectType == self::$OBJ_MOVIE) {
            return $this->getMovie($objectId, $params);
        } else if($objectType == self::$OBJ_SEARCH) {
            return $this->search($objectId, $params);
        } else if($objectType == self::$OBJ_STREAM_URL){
            return $this->getStreamUrl($objectId,$params);
        }
        return parent::getObject($objectId, $params);
    }
}

?>