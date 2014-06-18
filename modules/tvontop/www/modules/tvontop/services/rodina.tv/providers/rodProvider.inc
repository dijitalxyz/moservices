<?php
#############################################################################
# Library with all functions needed to connect rodina.tv using REST API.    #
# These functions allow to log on to rodina.tv, get information about       #
# available channels and to get the channels URLs themselves.               #
#                                                                           #
# Author: stalker 2013                                                      #
#############################################################################
require_once 'interfaces/liveTvProvider.inc';
require_once 'providers/commonProvider.inc';
require_once "tools/logger.inc";
require_once 'rodConnection.inc';

class RodProvider extends LiveTvProvider {
    public static $OBJ_CHANNEL_INFO  = "CHANNEL_INFO";
    
    public static $TTL_CATEGORY  		= 86400;	//24 Hours
    public static $TTL_CATEGORY_LIST  	= 86400;	//24 Hours
    public static $TTL_CHANNEL  		= 300; 	//5Min
    public static $TTL_EPG  			= 1209600;	//14 days;

    protected $rodConnection;
	protected 		$session;
    public function RodProvider() {
        $this->sectionName = "Rodina.TV";
		$this->log = Logger::getLogger(get_class());
        $this->rodConnection = new RodConnection($this);
        $this->session = new HttpSession();
    }

    protected function getGroupList($objectId,$params) {

    	$reply = $this->rodConnection->getData("get_channels", null, "get_channels"); 
        if(false === $reply) {
        	return false;
        }
        $childList = $this->parseCategoriesList($reply);
		$rootObj = new MediaObject('ROOT', self::$OBJ_GROUP_LIST, 'Родина.ТВ');
		//no caching
		$rootObj->setTtl(0);
		$category0 = new MediaObject($objectId, self::$OBJ_CHANNELGROUP, $this->locale->msg('All Categories'));
		$category0->params['icon'] = "all";
		$rootObj->addChild($category0);

		$rootObj->addChildren($childList);
        return $rootObj;
	}
	
	protected function getChannelGroup($objectId,$params) {

		$xml = $this->rodConnection->getData("get_channels", null, "get_channels"); 
        if(false === $xml) {
        	return false;
        }

		$xPath = "//array[@name='categories']/row[item[@name='number']/text()='".$objectId."']";
		$xmlCat = $xml->xpath($xPath);
		$cat_name = $this->rodConnection->getItemValueByName($xmlCat[0], "title");

		$category = new MediaObject($objectId, self::$OBJ_CHANNELGROUP, $cat_name);
		$category->setTtl(self::$TTL_CATEGORY);

	  	$channels = array();
		if(isset($objectId) && $objectId != "") {
			$xPath = "//array[@name='channels']/row[array[@name='categories']/item/text()='".$objectId."']";
		} else {
			$xPath = "//array[@name='channels']/row";
		}

		$result = $xml->xpath($xPath);
		$channelNumbers = array();
		while(list( , $row) = each($result)) {
			
			$id = $this->rodConnection->getItemValueByName($row, "number");
			$channelNumbers[] = $id;

			$title = $this->rodConnection->getItemValueByName($row, "title");

            $channel = new MediaObject($id, self::$OBJ_CHANNEL, $title);
			$channel->setTtl(self::$TTL_CHANNEL);

			$channel->params['channel_id']   = $id;
			$channel->params['has_archive']  = (1 == $this->rodConnection->getItemValueByName($row, "has_record"));

			$icon = $this->rodConnection->getItemValueByName($row, "URL_ICON_100_100");
			if(!isset($icon) || false == $icon) {
				$icon = $this->rodConnection->getItemValueByName($row, "icon_45_45");
			}

			$channel->params['icon'] = $icon;
			$category->addChild($channel);
		}

		//request EPG info for curent programms
		$epgParams = array( 'period' => 10,
							'count'=>1,
							'number'=>implode(",",$channelNumbers));
		$xmlEpg = $this->rodConnection->getData("get_epg", $epgParams, "get_epg"); 
	    if(false === $xmlEpg) {
	    	return false;
	    }

		foreach($category->getChildren() as $channel) {

			$xPath = "//row[item[@name='number']/text()='".$channel->getId() ."']/array/row";
			$xmlProg = $xmlEpg->xpath($xPath);
			if(isset($xmlProg[0])) {
				$row = $xmlProg[0];
				//current programm start
				$val = $this->rodConnection->getItemValueByName($row, "curr_ut_start");
				if(isset($val)) {
					$channel->params['epg_start'] = (int) $val;
				}
				//current program end
				$val = $this->rodConnection->getItemValueByName($row, "curr_ut_stop");
				if(isset($val)) {
					$channel->params['epg_end'] = (int) $val;
					$channel->setExpireDate($channel->params['epg_end']);
				}
				//current program title
				$val = $this->rodConnection->getItemValueByName($row, "title");
				if(isset($val)) {
					$channel->params['epg_title'] = $val;
				}

				$val = $this->rodConnection->getItemValueByName($row, "desc");
				if(isset($val)) {
					$channel->params['epg_info'] = $val;
				}

				$xPath = "array[@name='categories']/item";
				$cats = $row->xpath($xPath);
				$sCat = implode(",", $cats);
				$channel->params['parent_title'] = $sCat;
			}
		}
		return $category;
	}

	protected function getChannel($objectId,$params) {
		$rawObj = $this->getChannelsList();
		foreach($rawObj as $category) {
			foreach($category->getChildren() as $channel) {
				if($channel->getId() == $objectId) {
					return $channel;
				}
			}
		}
	}

	protected function getChannelEpg($objectId,$params) {
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

		$epgDay = time() - $arcTime; 
		if($epgDay  < 60*60*24) {
			$epgObj->setTtl(self::$TTL_CHANNEL);
		} else {
			$epgObj->setExpireDate($arcTime + self::$TTL_EPG);
		}

		$EPG_START_OFFSET = 0;
		$prevDay = $this->calculatePreviousDay($arcTime, $EPG_START_OFFSET);
		$epgObj->setParam("prevDayTime",$prevDay);

		$nextDay = $this->calculateNextDay($arcTime, $EPG_START_OFFSET);
		$epgObj->setParam("nextDayTime",$nextDay);

		foreach($this->getEpg($channelId, $arcTime) as $program) {
			$epgObj->addChild($program);
		}
		return $epgObj;
	}

	protected function getStreamUrl($objectId,$params) {

		//program id for archive 
		$pid   = isset($params['pid'])   ? $params['pid']  : null;

		//handle protected password
		$pkey   = isset($params['pkey']) ? $params['pkey'] : null;
		if(null == $pkey) {
			if($this->config->get("allow_erotic") == true) {
				$pkey = $this->config->get("pkey");
			} else {
				$pkey_ttl = $this->session->get("pkey_ttl");
				if(isset($pkey_ttl) && $pkey_ttl > time()) {
					$pkey = $this->session->get("pkey");	
				}
			}
		}

		$parameters = array('type'  => 'http');
		if (isset($pkey)) {
			$token = $this->rodConnection->getToken();
			$passwd = md5($token."".md5($pkey));
			$parameters['passwd'] = $passwd;
		}

		if (isset($pid)) {
			$parameters['pid'] = $pid; 
		} else {
			$parameters['number'] = $objectId;
		}
		$reply = $this->rodConnection->getData("get_url", $parameters, "get_url"); 
        if(false === $reply) {
        	return false;
        }

        if(isset($reply->row)) {
        	if($this->rodConnection->getItemValueByName($reply->row, "code") == "4008"){
				$so = new MediaObject($objectId, self::$OBJ_PROTECTED_STREAM_URL,$this->locale->msg('Protected channel password:'));
				//should expire immediatly
				$so->setExpireDate(0);
				$so->setParam("special_keys", "special_keys.txt");
				$localeObj = new MediaObject("de", self::$OBJ_KEY_LOCALE,"de");
				$localeObj->setParam("layout", "layout_de.txt");
				$so->addChild($localeObj);
        	} else if($this->rodConnection->getItemValueByName($reply->row, "code") == "4004"){
				$so = new MediaObject("0", self::$OBJ_ERROR, "Error");
				$so->setParam("descr", $this->rodConnection->getItemValueByName($reply->row, "message"));
        	} else {
        		//save pkey in session
				if (isset($pkey)) {
					$pkey_lifetime = $this->config->get("pkey_lifetime");
					$this->session->set("pkey_ttl", time() + $pkey_lifetime);
					$this->session->set("pkey", $pkey);
				}
        		$streamUrl = $this->rodConnection->getItemValueByName($reply->row, "url");
	        	$so = new MediaObject($objectId, self::$OBJ_STREAM_URL);
				//url should expire immediatly
				$so->setExpireDate(0);
				$so->setParam("url", $streamUrl);
        	}
			return $so;
        }
		return false;		
	}

	public function postProcess($object) {
		$servertime = $this->rodConnection->getServerTime(); 
		if (isset($servertime)) {
			$object->setParam("servertime", $servertime);
		} else {
			$object->setParam("servertime", time());
		}
		return $object;
	}

	protected function getCategoryIcon($id) {
        $icons = array(
            '0'   => 'all',
            '100' => 'common',
            '105' => 'science',
            '107' => 'news',
            '101' => 'entertainment',
            '106' => 'children',
            '108' => 'music',
            '104' => 'sport',
            '102' => 'movie',
            '103' => 'erotic');
        return isset($icons[$id]) ? $icons[$id] : 'unknown';
    }

    protected function getEpg($id, $date = null) {
        # date format is ddmmyy: 271210 for 27.12.2010
        # NOTE: at 03:00 starts another EPG day
        $date = isset($date) ? $date : time();
        $date = date('Ymd', $date);
    	$parameters = array(
            'number'  => $id,
            'date'  => $date,
            'period'  => 60*60*24);
		$reply = $this->rodConnection->getData("get_epg", $parameters, "get_epg"); 
        if(false === $reply) {
        	return false;
        }

        return false === $reply ? false : $this->parseEpg($reply);
    }

    protected function parseChannelsList($xml, $categoryId) {
        $channels = array();
		if(isset($categoryId) && $categoryId != "") {
			$xPath = "//array[@name='channels']/row[array[@name='categories']/item/text()='".$categoryId."']";
		} else {
			$xPath = "//array[@name='channels']/row";
		}

		$category = new MediaObject($categoryId, self::$OBJ_CHANNELGROUP, $categoryId);
		$category->setTtl(self::$TTL_CATEGORY);

		$result = $xml->xpath($xPath);
		while(list( , $row) = each($result)) {

			$id = $this->rodConnection->getItemValueByName($row, "number");
			$title = $this->rodConnection->getItemValueByName($row, "title");

            $channel = new MediaObject($id, self::$OBJ_CHANNEL, $title);
			$channel->setTtl(self::$TTL_CHANNEL);

			$channel->params['channel_id']   = $id;
			$channel->params['has_archive']  = (1 == $this->rodConnection->getItemValueByName($row, "has_record"));

			$icon = $this->rodConnection->getItemValueByName($row, "URL_ICON_100_100");
			if(!isset($icon)) {
				$icon = $this->rodConnection->getItemValueByName($row, "icon_45_45");
			}

			$channel->params['icon'] = $icon;
			$category->addChild($channel);
		}
        return $category;
    }

	protected function parseCategoriesList($xml) {
        $categories = array();
		$result = $xml->xpath("//array[@name='categories']/row");

		while(list( , $node) = each($result)) {
			$id = $this->rodConnection->getItemValueByName($node, "number");
			$title = $this->rodConnection->getItemValueByName($node, "title");

            $category = new MediaObject($id, self::$OBJ_CHANNELGROUP, $title);
			$category->setTtl(self::$TTL_CATEGORY);
			$category->params['icon']    = $this->getCategoryIcon($category->getId());
            $categories[] = $category;
        }
        return $categories;
    }

    protected function parseEpg($xml) {
        $programs = array();
		$result = $xml->xpath("//array[@name='programmes']/row");

        while(list( , $program) = each($result)) {
        	$title 		= $this->rodConnection->getItemValueByName($program, "title");
        	$id			= $this->rodConnection->getItemValueByName($program, "pid");
        	$start		= $this->rodConnection->getItemValueByName($program, "ut_start");
        	$desc		= $this->rodConnection->getItemValueByName($program, "desc");
        	$has_record	= $this->rodConnection->getItemValueByName($program, "has_record");

        	$program = new MediaObject($id, self::$OBJ_PROGRAMM,$title);
			$program->setParam('start',$start);
			$program->setParam('pid',$id);
	        $program->setParam('descr',$desc);
            $programs[] = $program;
        }
        return $programs;
    }

    protected function loadAuthData() {
    	parent::loadAuthData();
    	$this->rodConnection->setAuthData($this->username, $this->password);
    }

    protected function loadDefaultAuthData() {
        $this->username  = null;
        $this->password  = null;
    }
}
?>