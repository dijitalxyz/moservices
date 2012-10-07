<?php
#############################################################################
# Library with all functions needed to connect ruhd.tv using REST API.      #
# These functions allow to log on to ruhd.tv, get information about         #
# available series and to get the stream URLs themselves.               	#
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'services/ruhd.tv/providers/rhdProvider.inc';

class RsdProvider extends RhdProvider {
	
	public function init(Configuration &$cfg) {
		parent::init($cfg);
		$this->baseUrl = "http://rusd.tv/";
      	$this->sectionName = $this->config->get("name", "Service");
        $this->cookieName = "RsdCookie";
        $this->http->cookie = isset($_SESSION[$this->cookieName]) ?
            $_SESSION[$this->cookieName] : "";		
    }
    
	public function getIniFilename() {
		return dirname(__FILE__) . "/../config.ini";
	}
    protected function parseEpisodeLink($objectId, $xml) {
		if(!isset($xml->item)) {
			return null;
		}
		$resultObj = new MediaObject($objectId, self::$OBJ_STREAM_URL, (string)$xml->item->title);
		$resultObj->setTtl(0);
		$resultObj->setParam("url", $this->baseUrl.'content/'.(string)$xml->item->vurl);
		return $resultObj;
    }
}

?>