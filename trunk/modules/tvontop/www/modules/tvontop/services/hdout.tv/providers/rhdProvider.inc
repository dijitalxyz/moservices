<?php
#############################################################################
# Library with all functions needed to connect ruhd.tv using REST API.      #
# These functions allow to log on to ruhd.tv, get information about         #
# available series and to get the stream URLs themselves.               	#
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/seriesProvider.inc';
require_once "tools/logger.inc";
require_once "tools/http.inc";
require_once "tools/parser.inc";
require_once "tools/exceptions.inc";
require_once "tools/httpSession.inc";

class RhdProvider extends SeriesProvider {
	
	protected static $ID_DELIMITER           		= "_";
	protected static $AUTHORIZATION_ERROR_TOKEN     = '<form id="loginform" method="POST" action="/"';
	protected static $TTL_SERIES_LIST	= 86400;//24 Hours
	protected static $TTL_SERIE			= 604800;//1 Week
	protected static $TTL_SEASON		= 86400;//24 Hours
	protected static $TTL_EPISODE		= 604800;//1 Week

	protected $http;
    protected $username;
    protected $password;
    protected $sessionVar = "PHPSESSID";
	protected $session = null;
	
	public function init(Configuration &$cfg) {
		parent::init($cfg);
		$this->baseUrl = "http://ruhd.tv/";
      	$this->sectionName = $this->config->get("name", "Service");
        $this->cookieName = "RhdCookie";
 		$this->http = new HttpTools(
            "User-Agent: Mozilla/5.0\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Connection: Close\r\n");

		$this->session = new HttpSession();
        $this->http->cookie = $this->session->exists($this->cookieName) ?
            $this->session->get($this->cookieName) : "";

        $this->log  = Logger::getLogger(get_class());
    }

	public function getIniFilename() {
		return dirname(__FILE__) . "/../config.ini";
	}

    protected function loadAuthData() {
        if (null != $this->config) {
            $this->username = $this->config->get("username", $this->sectionName, $this->username);
            $this->password = $this->config->get("password", $this->sectionName, $this->password);
        }
    }

    protected function isDemoAccount() {
    	return false;
    }
    protected function isAuthorized($reply = null) {
    	if(null != $reply) {
    		return strrpos($reply , self::$AUTHORIZATION_ERROR_TOKEN) === FALSE;
    	}
    	return isset($this->http->cookie) && $this->http->cookie != "";
    }
    public function forgetCookie() {
    	$this->session->delete($this->cookieName);
        $this->http->cookie = "";
    }
  	protected function login($username, $password) {
  		$url = $this->baseUrl;
  		$cookies = $this->http->getPageCookies($url);
  		if(isset($cookies[$this->sessionVar])) {
  			$this->http->cookie = "Cookie: $this->sessionVar=".$cookies[$this->sessionVar]."; c_login=$username; c_password=$password";
  		}
  		$authData = "login=$username&password=$password";
  		$url = $this->baseUrl;
        $reply = $this->http->sendPostRequest($url."?".$authData);
        $this->session->set($this->cookieName,$this->http->cookie,86400);
    	return $reply != false;
    }

    protected function sendData($url, $name) {
    	if (! $this->isAuthorized()) {
    		$this->loadAuthData();
            $this->login($this->username, $this->password);
		}
        $reply = $this->http->sendGetRequest($url);
        if (!$this->isAuthorized($reply)) {
			$reply = false;
			$this->forgetCookie();
			throw new AuthException($this->locale->msg('Authorization error'));
        }
        return $reply; 
    }

    protected function getData($url, $name) {

    	if (! $this->isAuthorized()) {
    		$this->loadAuthData();
            $this->login($this->username, $this->password);
		}
		//TODO: Authorization check and error handling
        $reply = $this->http->sendGetRequest($url);
        if (!$this->isAuthorized($reply)) {
			$reply = false;
			$this->forgetCookie();
			throw new AuthException($this->locale->msg('Authorization error'));
        }
        if(false !== $reply) {
        	$reply  = simplexml_load_string($reply);
        }
        return $reply; 
    }

    protected function getFavoriteSeriesList($objectId,$params) {
    	$url = $this->baseUrl."ShowSeries/my/XML/";
	 	$reply = $this->getData($url, "favorite series list");
	 	$result = $this->parseSeriesList($reply);
	 	if(isset($result)) {
	 		$result->setTtl(0);
	 	}
        return $result;
    }

    protected function getNewSeriesList($objectId,$params) {
    	$url = $this->baseUrl."ShowSeries/new/XML/";
	 	$reply = $this->getData($url, "favorite series list");
	 	$result = $this->parseSeriesList($reply);
	 	if(isset($result)) {
	 		$result->setTtl(0);
	 	}
        return $result;
    }

	protected function getSeriesList($objectId,$params) {
		$url = $this->baseUrl."ShowSeries/all/XML/";
	 	$reply = $this->getData($url, "series list");
        return false === $reply ? false : $this->parseSeriesList($reply);
	}

	protected function getSerie($objectId,$params) {
		$url = $this->baseUrl."ShowEpisodes/".$objectId."/XML/";
		$reply = $this->getData($url, "Serie $objectId");
		//$seasonId -> null for all seasons 
        return false === $reply ? false : $this->parseEpisodesList($objectId,null,$reply);
	}

	protected function getSeason($objectId,$params) {
		$ids = explode(self::$ID_DELIMITER, $objectId);
		$serieId = $ids[0];
		$seasonId = $ids[1];
		$url = $this->baseUrl."ShowEpisodes/".$serieId."/XML/";
		$reply = $this->getData($url, "Serie $objectId");
		return false === $reply ? false : $this->parseEpisodesList($serieId,$seasonId,$reply);
	}

	protected function getStreamUrl($objectId,$params) {
		$url = $this->baseUrl."GetEpisodeLink/".$objectId."/XML/";
		$tryCnt = $this->config->get("maxStreamStartTries", $this->sectionName, 5);
		while($tryCnt > 0 ) {
			$reply = $this->getData($url, "Episode link $objectId");
			if( false !== $reply ) {
				$linkObj = $this->parseEpisodeLink($objectId,$reply);
				$mediaUrl = $linkObj->getParam("url");
				$headers = get_headers($mediaUrl, 1);
			    if (empty($headers) || strpos($headers[0], "200 OK") === false) {
			    	$this->log->warning("Get stream: response $headers[0]. Waiting 1 sec.");
					sleep(1);
					$tryCnt--;
			    } else {
			    	$this->log->debug("Get stream: response ok: $headers[0].");
			    	$tryCnt = 0;
			    }
			} else {
				return "";
			}
		}
		return $linkObj;
	}

	protected function addToFavorites($objectId,$params) {
		$url = $this->baseUrl."AddToFavorites/".$objectId."/";
		$reply = $this->sendData($url, "Adding to favorites serie $objectId");
		//TODO: Messages template
		if(false === $reply || "" === $reply) {
			$msg = new MediaObject("1", self::$OBJ_ERROR,"Error");
			$msg->setParam("descr", "Favorite not added");	
		} else {
			$msg = new MediaObject("1", self::$OBJ_MESSAGE,"Message");
			$msg->setParam("descr", "Favorite added");
		} 
		return $msg; 		
	}
	protected function removeFromFavorites($objectId,$params) {
		$url = $this->baseUrl."RemoveFromFavorites/".$objectId."/";
		$reply = $this->sendData($url, "Adding to favorites serie $objectId");
		
		if(false === $reply || "" === $reply) {
			$msg = new MediaObject("1", self::$OBJ_ERROR,"Error");
			$msg->setParam("descr", "Favorite not removed");	
		} else {
			$msg = new MediaObject("1", self::$OBJ_MESSAGE,"Message");
			$msg->setParam("descr", "Favorite removed");
		} 
		return $msg; 
	}
	protected function parseSeriesList($xml) {
		$resultObj = new MediaObject($this->config->get("name", "Service"), self::$OBJ_SERIES_LIST,$this->config->get("displayName", "Service"));
		$resultObj->setTtl(self::$TTL_SERIES_LIST);
		if(isset($xml->fp) && isset($xml->fp->serieslist)) {
			foreach ($xml->fp->serieslist->item as $serie) {
				$child = new MediaObject((string)$serie->id_series, self::$OBJ_SERIE, (string)$serie->title);
				$child->setTtl(0);
				$child->setParam("etitle",  	(string)$serie->etitle);
				$child->setParam("descr", 		ParserTools::removeHtmlTags((string)$serie->info));
				$child->setParam("thumbnail", 	$this->baseUrl.(string)$serie->fpimg);
				//set thumbnail as img because of different image sizes on ruhd
				$child->setParam("img", 		$child->getParam("thumbnail"));
				//$child->setParam("img", 		$this->baseUrl.(string)$serie->pimg);
				$child->setParam("mark", 		(string)$serie->mark);
				$child->setParam("isclosed",	(string)$serie->isclosed);
				$resultObj->addChild($child);
				unset($child); 
			}
		}
		return $resultObj;
    }

	protected function parseEpisodesList($serieId, $seasonId, $xml) {
		if(!isset($xml->series)) {
			return null;
		}
		$serie = $xml->series;

		$serieObj = new MediaObject($serieId, self::$OBJ_SERIE,(string)$serie->title);
		$serieObj->setTtl(self::$TTL_SERIE);
		$serieObj->setParam("etitle", 	(string)$serie->etitle);
		$serieObj->setParam("descr", 	ParserTools::removeHtmlTags((string)$serie->info));;
		$serieObj->setParam("thumbnail",$this->baseUrl.(string)$serie->fpimg);
		$serieObj->setParam("img", 		$this->baseUrl.(string)$serie->pimg);
		$serieObj->setParam("mark", 	(string)$serie->mark);
		$serieObj->setParam("isclosed", (string)$serie->isclosed);
		$seasonsCnt = 0;
		$seriesCnt = 0;
		foreach ($serie->season as $season) {
			$seasonsCnt++;
			
			$seasonsSeriesCnt = 0;
			$curentSeasonId = (string)$season->attributes()->title;
			//add serie id to season
			$seasonObj = new MediaObject($serieId.self::$ID_DELIMITER.$curentSeasonId, self::$OBJ_SEASON,$this->locale->msg('Season')." ".(string)$season->attributes()->title);
			$seasonObj->setTtl(self::$TTL_SEASON);
			//copy serie thumbnail to season  - we have no season thumbnail from ruhd
			$seasonObj->setParam("thumbnail",$serieObj->getParam('thumbnail'));

			foreach ($season->item as $episode) {
				
				$seasonsSeriesCnt++;
				$episodeObj = new MediaObject((string)$episode->id_episodes, self::$OBJ_EPISODE, (string)$episode->title);
				$episodeObj->setTtl(self::$TTL_EPISODE);
				$episodeObj->setParam("etitle", (string)$episode->etitle);
				$episodeObj->setParam("snum", 	(int)$episode->snum);
				$episodeObj->setParam("enum", 	(int)$episode->enum);
				$episodeObj->setParam("vnum", 	(string)$episode->vnum);
				$episodeObj->setParam("tmark", 	(string)$episode->tmark);
				$episodeObj->setParam("defsnd", (string)$episode->defsnd);
				$episodeObj->setParam("addsnd", (string)$episode->addsnd);
				$episodeObj->setParam("sub1", 	(string)$episode->sub1);
				$episodeObj->setParam("sub2", 	(string)$episode->sub2);
				$seasonObj->addChild($episodeObj); 
			}
			$seasonObj->setParam("series", $seasonsSeriesCnt);
			$seriesCnt += $seasonsSeriesCnt;
			if($curentSeasonId == $seasonId) {
				//set parent properties only
				$seasonObj->setParent($serieObj->cloneObj());
				return $seasonObj;
			} else {
				$serieObj->addChild($seasonObj);
			}
		}
		$serieObj->setParam("seasons", $seasonsCnt);
		$serieObj->setParam("series", $seriesCnt);
		return $serieObj;
    }

	protected function parseEpisodeLink($objectId, $xml) {
		
		if(!isset($xml->item)) {
			return null;
		}
		$resultObj = new MediaObject($objectId, self::$OBJ_STREAM_URL, (string)$xml->item->title);
		$resultObj->setExpireDate(0);
		//Take ip, not the hostname for stream
		$parsedUrl = parse_url($this->baseUrl);
		$ip = gethostbyname($parsedUrl['host']);
		$resultObj->setParam("url", 'http://'.$ip.'/content/'.(string)$xml->item->vurl);
		return $resultObj;
    }

    //TODO: implement or delete
	protected function getSoundtrack($objectId,$params) {
		return new MediaObject($objectId, self::$OBJ_SOUNDTRACK);
	}
	protected function getSubtitle($objectId,$params) {
		return new MediaObject($objectId, self::$OBJ_SUBTITLE);
	}
}

?>