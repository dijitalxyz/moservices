<?php
#############################################################################
# Abstract interface to implement any live TV service channels display      #
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/provider.inc';
require_once 'providers/commonProvider.inc';

abstract class LiveTvProvider extends Provider {

	public static $OBJ_GROUP_LIST    = "GROUP_LIST";
	public static $OBJ_CHANNELGROUP  = "CHANNELGROUP";
	public static $OBJ_CHANNEL       = "CHANNEL";
	public static $OBJ_EPG           = "EPG";
	public static $OBJ_PROGRAMM      = "PROGRAM";

	abstract protected function getGroupList($objectId,$params);
	abstract protected function getChannelGroup($objectId,$params);
	abstract protected function getChannel($objectId,$params);
	abstract protected function getChannelEpg($objectId,$params);
	abstract protected function getStreamUrl($objectId,$params);

	public function getObject($objectId, $params) {
        if ($this->isDemoAccount()) {
            $resultObj = CommonProvider::getObject(
                CommonProvider::$ID_AUTH_DEMO_CONFIRM);
            $resultObj->setParam("username", $this->username);
            $resultObj->setParam("password", $this->password);
            return $resultObj;
        } 
                    
        // default response - list of groups
        $objectType = $this->getParam($params, "type", self::$OBJ_GROUP_LIST);

        if ($objectType == self::$OBJ_GROUP_LIST) {
        	$resultObj = $this->getGroupList($objectId,$params);
        } else if($objectType == self::$OBJ_CHANNELGROUP) {
        	$resultObj = $this->getChannelGroup($objectId,$params);
        } else if ($objectType == self::$OBJ_CHANNEL) {
        	$resultObj = $this->getChannel($objectId,$params);
        } else if($objectType == self::$OBJ_EPG) {
        	$resultObj = $this->getChannelEpg($objectId,$params);
        } else if($objectType == self::$OBJ_STREAM_URL) {
        	$resultObj = $this->getStreamUrl($objectId,$params);
        } else {
        	$resultObj = parent::getObject($objectId, $params);
        }
        return $this->postProcess($resultObj);
    }

    public function calculatePreviousDay($date, $epgOffset=0) {
    	#define previous day
		$time = $date -  $epgOffset;
		$time -= 24 * 60 * 60;
		$time = mktime(23, 59, 59, date("n", $time), date("j", $time), date("Y", $time));
		return $time+$epgOffset;
    }

    public function calculateNextDay($date, $epgOffset=0) {
		#define next day
		$time = $date - $epgOffset;
		$time += 24 * 60 * 60;
		$time = mktime(0, 0, 1, date("n", $time), date("j", $time), date("Y", $time));
		return $time + $epgOffset;		
    }
}
