<?php
#############################################################################
# Library with all functions needed to connect rodnoe.tv using REST API.   #
# These functions allow to log on to rodnoe.tv, get information about      #
# available channels and to get the channels URLs themselves.               #
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/liveTvProvider.inc';
require_once 'providers/commonProvider.inc';
require_once "tools/logger.inc";
require_once "tools/http.inc";
require_once "tools/parser.inc";
require_once "tools/exceptions.inc";

class RtvProvider extends LiveTvProvider {


    protected $http;
    protected $lastError;
    protected $lastErrorMsg;

    public function RtvProvider() {

        $this->sectionName = "Rodnoe.TV";
        $this->cookieName = "RtvCookie";
        $this->baseUrl = "http://file-teleport.com/iptv/api/xml";

        $this->lastError 	= null;
        $this->lastErrorMsg = "";
		$this->log = Logger::getLogger(get_class());
        
        $this->http = new HttpTools(
            "User-Agent: Mozilla/5.0\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Connection: Close\r\n");
        $this->http->cookie = isset($_SESSION[$this->cookieName]) ?
            $_SESSION[$this->cookieName] : "";
    }
    
	function getGroupList($objectId,$params) {
		$rawObj = $this->getChannelsList();
		$rootObj = new MediaObject('ROOT', self::$OBJ_GROUP_LIST, 'Родное.ТВ');
		
		$category0 = new MediaObject("", self::$OBJ_CHANNELGROUP, $this->locale->msg('All Categories'));
		$category0->params['icon'] = "all";
		$rootObj->addChild($category0);
        
		$rootObj->addChildren($rawObj);
		return $rootObj;
	}

	function getChannelGroup($objectId,$params) {
		$rawObj = $this->getChannelsList();
		if(null == $objectId) {
			$rootObj = new MediaObject('ALL_CATEGORIES', self::$OBJ_CHANNELGROUP, 'Все категории');
			foreach($rawObj as $category) {
				foreach($category->getChildren() as $channel) {
					$rootObj->addChild($channel,$category);
				}
			}
			return $rootObj;
		} else {
			foreach($rawObj as $category) {
				if($category->getId() == $objectId) {
					return $category;
				}
			}
		}
	}
	
	function getChannel($objectId,$params) {
		$rawObj = $this->getChannelsList();
		foreach($rawObj as $category) {
			foreach($category->getChildren() as $channel) {
				if($channel->getId() == $objectId) {
					return $channel;
				}
			}
		}
	}
	
	function getChannelEpg($objectId,$params) {
        # RS:TODO: Juri should add here the same meta id as for ktv.
		$metaId = explode("_",$objectId);
		if(count($metaId) > 1) {
			$arcTime = $metaId[1];
		}
		else {
			$arcTime = time();
		}
		$channelId =  $metaId[0];
		
		$epgObj = new MediaObject($objectId, self::$OBJ_EPG, 'EPG');
		$epgObj->setParam("arcTime", $arcTime);
		$epgObj->setParam("channel_id", $channelId);

		$prevDay = $this->calculatePreviousDay($arcTime);
		$epgObj->setParam("prevDayTime",$prevDay);

		$nextDay = $this->calculateNextDay($arcTime);
		$epgObj->setParam("nextDayTime",$nextDay);

		foreach($this->getEpg($channelId, $arcTime) as $program) {
			$epgObj->addChild($program);
		}
		return $epgObj;
	}
	
	function getStreamUrl($objectId,$params) {

		$metaId = explode("_",$objectId);
		$channelId =  $metaId[0];

		$gmt   = isset($params['gmt'])   ? $params['gmt']  : null;
		$pkey   = isset($params['pkey']) ? $params['pkey'] : null;
		$url = $this->baseUrl . "/get_url_tv?cid=$channelId";

		$allowErotic = (null != $this->config) ? $this->config->get("allow_erotic", null, FALSE) : FALSE;

		if (isset($gmt)) {
			$url .= "&lts=$gmt";
		}
		if (isset($pkey)) {
			$url .= "&protect_code=" . $pkey;
		} else if ($allowErotic) {
			$url .= "&protect_code=" . $this->config->get("pkey", $this->sectionName, $this->password);
		}
		$reply = $this->getData($url, "URL of stream $channelId");
		if(false !== $reply ) {
			$url = "URL_PROTECTED" == $this->lastError ? "protected" : (string) $reply->url;
			$url = preg_replace('/(rtsp|http)(\/ts|)([^ "]*).*/s', '$1$3', $url);
			$so = new MediaObject($channelId, self::$OBJ_STREAM_URL);
			//url should expire immediatly
			$so->setExpireDate(0);
			$so->setParam("url", $url);
			return $so;
		}
	}

	public function postProcess($object) {
		if(isset($this->serverTime)) {
			$object->setParam("servertime", $this->serverTime);
		}
		else {
			$object->setParam("servertime", time());
		}
		return $object;
	}
    public function getCategoryIcon($id) {
        $iconStr  = 'all,common,news,entertainment,children,movie';
        $iconStr .= ',science,sport,music,business,culture,erotic';
        $icons = explode(",",$iconStr); 
    	return isset($icons[$id]) ? $icons[$id] : 'unknown';
    }

    public function forgetCookie() {
        $_SESSION[$this->cookieName] = "";
        $this->http->cookie = "";
    }

    protected function loadDefaultAuthData() {
        $this->username = "demo";
        $this->password = "demo";
    }

    public function isAuthorized($reply = "") {
        if (false === $reply) {
            $reply = "<error><code>1001</code></error>"; # No connection
        }
        $error     = ParserTools::parseParam($reply, '<error>', '</error>', '');
        $errorCode = ParserTools::parseParam($error, '<code>', '</code>', null);
        $errorMsg  = ParserTools::parseParam($error, '<message>', '</message>', null);
        $ok = isset($this->http->cookie) &&
            0 != strlen($this->http->cookie) &&
            false == strpos($this->http->cookie, "deleted") &&
            (null  == $errorCode || "URL_PROTECTED" == $errorCode);
		$this->lastError = $errorCode;
        $this->lastErrorMsg = $errorMsg;            
        if (! $ok) {
            $this->log->debug("Authorization missed or lost");
        }
        return $ok;
    }

    public function login() {
        $this->log->debug("Login started");

        $this->forgetCookie();

        $passValue = md5(md5($this->username) . md5($this->password));
        $url = $this->baseUrl . "/login?login=$this->username&pass=$passValue";
        $reply = $this->http->sendPostRequest($url);

        $sid = ParserTools::parseParam($reply, '<sid>', '</sid>', null);
        $sid_name = ParserTools::parseParam($reply, '<sid_name>', '</sid_name>', null);
        if (! isset($sid) || ! isset($sid_name)) {
            $this->lastError = 1; # E_INCORRECT_REQUEST
            return false;
        }

        $this->http->cookie = "Cookie: $sid_name=$sid";
        if (! $this->isAuthorized($reply)) {
            $this->lastError = 12; # E_AUTH_ERROR
            return false;
        }

        $this->log->debug("Login returned: " . $this->http->cookie);
        $_SESSION[$this->cookieName] = $this->http->cookie;
        return true;
    }

    public function logout() {
        $url = $this->baseUrl . "/logout";
        $reply = $this->http->sendPostRequest($url);
        $this->forgetCookie();
    }


    protected function getData($url, $name) {
        $this->log->debug("Getting $name");
        $reply = $this->http->sendPostRequest($url);
        if (! $this->isAuthorized($reply)) {
            $this->login();
            $this->log->debug("Second try to get $name");
            $reply = $this->http->sendPostRequest($url);
            if (! $this->isAuthorized($reply)) {
            	if ($this->lastError != "URL_PROTECTED") {
                	$reply = false;
                	throw new AuthException($this->lastErrorMsg);
            	}
            }
        }

        if(false !== $reply) {
        	$reply = simplexml_load_string($reply);
        	$this->serverTime = (int)$reply->servertime; 
        }
        return $reply;
    }

    public function getChannelsList() {
        $url = $this->baseUrl . "/get_list_tv";
        $reply = $this->getData($url, "channels list");
        return false === $reply ? false : $this->parseChannelsList($reply);
    }

   
    public function getEpg($id = null, $date = null) {
        $params = array();
        if (isset($id)) {
            $params['cid'] = $id;
        }
        if (isset($date)) {
            # date format is yymmdd: 101227 for 27.12.2010
            $params['day'] = date('ymd', $date);
        }

        $url = $this->baseUrl . "/get_epg";
        if (count($params) > 0) {
            $url .= "?" . http_build_query($params);
        }

        $reply = $this->getData($url, "EPG for channel $id");
        return false === $reply ? false : $this->parseEpg($reply);
    }

    protected function parseChannelsList($xml) {
        $iconPath = $xml->icons->default;
        $categories = array();
        foreach ($xml->groups->item as $xmlCategory) {
            $category = new MediaObject((string)$xmlCategory->id, self::$OBJ_CHANNELGROUP, (string)$xmlCategory->name);
            $category->params['color']   = (string) $xmlCategory->color;
            $category->params['icon']    = $this->getCategoryIcon($category->getId());


           // $channels = array();
            foreach ($xmlCategory->channels->item as $xmlChannel) {

                $channel = new MediaObject((string) $xmlChannel->id, self::$OBJ_CHANNEL,(string)$xmlChannel->name);

                $channel->params['channel_id']   = (string)$xmlChannel->id;
                $channel->params['has_archive']  = 1   ==   $xmlChannel->have_archive;
                $channel->params['aspect_ratio'] = (string) $xmlChannel->aspect_ratio;
                $channel->params['languages']    = (string) $xmlChannel->languages;
                $channel->params['icon']         = str_replace("%ICON%",
                        $xmlChannel->icon, $iconPath);

                if (isset($xmlChannel->epg_current_start) && 0 != $xmlChannel->epg_current_start) {
                    $channel->params['epg_start'] = (int) $xmlChannel->epg_current_start;
                }
                if (isset($xmlChannel->epg_current_end) && 0 != $xmlChannel->epg_current_end) {
                    $channel->params['epg_end'] = (int) $xmlChannel->epg_current_end;
                }
                if (isset($xmlChannel->epg_current_title) && "" != $xmlChannel->epg_current_title) {
                    $channel->params['epg_title'] = (string) $xmlChannel->epg_current_title;
                }
                if (isset($xmlChannel->epg_current_info) && "" != $xmlChannel->epg_current_info) {
                    $channel->params['epg_info'] = (string) $xmlChannel->epg_current_info;
                    if ($channel->params['epg_title'] == $channel->params['epg_info']) {
                        unset($channel->params['epg_info']);
                    }
                }

                if (1 == $xmlChannel->is_video) {
                    $category->addChild($channel);
                }
            }

            if ($category->getChildCount() > 0) {
                //$category->params['itemCount'] = count($category->getChildCount());
                $categories[] = $category;
            }
        }
        return $categories;
    }

    protected function parseEpg($xml) {
        $programs = array();
        foreach ($xml->channels->item->epg->item as $xmlProgram) {
            $programs[] = $this->parseEpgProgram($xmlProgram);
        }
        return $programs;
    }

    protected function parseEpgProgram($xml) {
        $program = new MediaObject((int)$xml->begin, self::$OBJ_PROGRAMM,(string) $xml->title);
        $program->setParam('start', (int)$xml->begin);
        $program->setParam('end',   (int)$xml->end);
        $program->setParam('descr', (string) $xml->info);
        if ($program->title == $program->params['descr']) {
            unset($program->params['info']);
        }
        return $program;
    }

}
?>
