<?php
#############################################################################
# Library with all functions needed to connect kartina.tv using REST API.   #
# These functions allow to log on to kartina.tv, get information about      #
# available channels and to get the channels URLs themselves.               #
#                                                                           #
# Author: consros 2011                                                      #
# Author: stalker 2011                                                      #
#############################################################################
require_once 'interfaces/liveTvProvider.inc';
require_once 'providers/commonProvider.inc';
require_once "tools/logger.inc";
require_once 'ktvConnection.inc';

class KtvProvider extends LiveTvProvider {
    public static $OBJ_CHANNEL_INFO  = "CHANNEL_INFO";
    
    public static $TTL_CATEGORY  		= 86400;	//24 Hours
    public static $TTL_CATEGORY_LIST  	= 86400;	//24 Hours
    public static $TTL_CHANNEL  		= 300; 	//5Min
    public static $TTL_EPG  			= 1209600;	//14 days;

    protected $ktvConnection;
    protected 		$session;

    public function KtvProvider() {
        $this->sectionName = "Kartina.TV";
		$this->log = Logger::getLogger(get_class());
        $this->ktvConnection = new KtvConnection($this);
        $this->session = new HttpSession();
    }

    protected function getGroupList($objectId,$params) {
		$rawObj = $this->getChannelsList();

		$rootObj = new MediaObject('ROOT', self::$OBJ_GROUP_LIST, 'Картина.ТВ');
		$rootObj->setTtl(self::$TTL_CATEGORY_LIST);
		$category0 = new MediaObject($objectId, self::$OBJ_CHANNELGROUP, $this->locale->msg('All Categories'));
		$category0->params['icon'] = "all";
		$rootObj->addChild($category0);

		$rootObj->addChildren($rawObj);
        return $rootObj; 	
	}
	
	protected function getChannelGroup($objectId,$params) {
		$categoryList = $this->getChannelsList();
		if(null == $objectId) {
			$rootObj = new MediaObject($objectId, self::$OBJ_CHANNELGROUP, $this->locale->msg('All Categories'));
			$rootObj->setTtl(self::$TTL_CATEGORY);
			foreach($categoryList as $category) {
				foreach($category->getChildren() as $channel) {
					$rootObj->addChild($channel,$category);
				}
			}
			return $rootObj;
		} else {
			foreach($categoryList as $category) {
				if($category->getId() == $objectId) {
					return $category;
				}
			}
		}
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
		

		$EPG_START_OFFSET = 3*60*60;
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
		//program id (gmt-timestamp)
		$gmt   = isset($params['pid'])   ? $params['pid']  : null;
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

		$url = "/get_url?cid=$objectId";

		if (isset($gmt)) {
			$url .= "&gmt=$gmt";
		}
		if (isset($pkey)) {
			$url .= "&protect_code=" . $pkey;
		}
		$reply = $this->ktvConnection->getData($url, "URL of stream $objectId");
		$url = false === $reply ? false : (string) $reply->url;
		if($url != false) {
			//handle protected streams
			if("protected" == $url) {
				$so = new MediaObject($objectId, self::$OBJ_PROTECTED_STREAM_URL,"Keyboard");
				//should expire immediatly
				$so->setExpireDate(0);
				$so->setParam("special_keys", "special_keys.txt");
				$localeObj = new MediaObject("de", self::$OBJ_KEY_LOCALE,"de");
				$localeObj->setParam("layout", "layout_de.txt");
				$so->addChild($localeObj);
			} else {
				//save pkey in session
				if (isset($pkey)) {
					$pkey_lifetime = $this->config->get("pkey_lifetime");
					$this->session->set("pkey_ttl", time() + $pkey_lifetime);
					$this->session->set("pkey", $pkey);
				}
				$url = preg_replace('/(rtsp|http)(\/ts|)([^ "]*).*/s', '$1$3', $url);
				$so = new MediaObject($objectId, self::$OBJ_STREAM_URL);
				//url should expire immediatly
				$so->setExpireDate(0);
				$so->setParam("url", $url);
			}
			return $so;
		}
		return false;
	}

	public function postProcess($object) {
		$servertime = $this->ktvConnection->getServerTime(); 
		if (isset($servertime)) {
			$object->setParam("servertime", $servertime);
		} else {
			$object->setParam("servertime", time());
		}
		return $object;
	}
	
	protected function getCategoryIcon($id) {
        $icons = array(
            '0'  => 'all',
            '1'  => 'common',
            '3'  => 'science',
            '5'  => 'news',
            '7'  => 'entertainment',
            '9'  => 'children',
            '11' => 'music',
            '13' => 'comedy',
            '15' => 'sport',
            '17' => 'international',
            '19' => 'movie',
            '21' => 'erotic');
        return isset($icons[$id]) ? $icons[$id] : 'unknown';
    }

  	protected function getChannelsList() {
        $reply = $this->ktvConnection->getData("/channel_list", "channels list");
        return false === $reply ? false : $this->parseChannelsList($reply);
    }

    protected function getEpg($id, $date = null) {
        # date format is ddmmyy: 271210 for 27.12.2010
        # NOTE: at 03:00 starts another EPG day
        $date = isset($date) ? $date : time();
        $date = date('dmy', $date - 3 * 60 * 60);
        $url = "/epg?cid=$id&day=$date";
        $reply = $this->ktvConnection->getData($url, "EPG for channel $id");
        return false === $reply ? false : $this->parseEpg($reply);
    }

    protected function getEpg3($date = null, $period = 3) {
        $date = isset($date) ? $date : time();
        $url = "/epg3?dtime=$date&period=$period";
        $reply = $this->ktvConnection->getData($url, "EPG3 for period $period");
        return false === $reply ? false : $this->parseEpg3($reply);
    }

    protected function parseChannelsList($xml) {
        $categories = array();
        foreach ($xml->groups->item as $xmlCategory) {
            $category = new MediaObject((string)$xmlCategory->id, self::$OBJ_CHANNELGROUP, (string)$xmlCategory->name);
			$category->setTtl(self::$TTL_CATEGORY);
            $category->params['color']   = (string) $xmlCategory->color;
            $category->params['icon']    = $this->getCategoryIcon($category->getId());

            foreach ($xmlCategory->channels->item as $xmlChannel) {

            	$name = preg_replace('/ -\d+$/', '', (string)$xmlChannel->name);
                $channel = new MediaObject((string) $xmlChannel->id, self::$OBJ_CHANNEL,$name);
				$channel->setTtl(self::$TTL_CHANNEL);

                $channel->params['channel_id']   = (string)$xmlChannel->id;
                $channel->params['has_archive']  = 1   ==   $xmlChannel->have_archive;
                $channel->params['icon']         = "http://iptv.kartina.tv" .
                    (string) $xmlChannel->icon;
                if (isset($xmlChannel->epg_start)) {
                    $channel->params['epg_start'] = (int) $xmlChannel->epg_start;
                }
                if (isset($xmlChannel->epg_end)) {
                    $channel->params['epg_end'] = (int) $xmlChannel->epg_end;
                    $channel->setExpireDate($channel->params['epg_end']);
                }
                if (isset($xmlChannel->epg_progname)) {
                    $split = $this->splitProgramName(
                        (string) $xmlChannel->epg_progname);
                    $channel->params['epg_title']    = $split[0];
                    $channel->params['epg_info']     = $split[1];
                }

                if (1 == $xmlChannel->is_video) {
                    $category->addChild($channel);
                }
            }

            if ($category->getChildCount() > 0) {
                $categories[] = $category;
            }
        }
        return $categories;
    }

    protected function parseEpg($xml) {
        $programs = array();
        foreach ($xml->epg->item as $xmlProgram) {
            $programs[] = $this->parseEpgProgram($xmlProgram);
        }
        return $programs;
    }

    protected function parseEpg3($xml) {
        $channelInfos = array();
        foreach ($xml->epg3->item as $xmlChannelInfo) {
            $channelInfo  = new MediaObject((string) $xmlChannelInfo->id, self::$OBJ_CHANNEL_INFO,(string) $xmlChannelInfo->name);
            foreach ($xmlChannelInfo->epg->item as $xmlProgram) {
                $channelInfo->addChild($this->parseEpgProgram($xmlProgram));
            }
            $channelInfos[] = $channelInfo;
        }
        return $channelInfos;
    }

    protected function parseEpgProgram($xml) {
        $split = $this->splitProgramName((string) $xml->progname);

        $program = new MediaObject((int) $xml->ut_start, self::$OBJ_PROGRAMM,$split[0]);
        $program->setParam('start',(int) $xml->ut_start);
        $program->setParam('pid',(int) $xml->ut_start);
        $program->setParam('descr',$split[1]);
        return $program;
    }

    protected function splitProgramName($name) {
        $pos = strpos($name, "\n");
        if (false !== $pos) {
            return array(substr($name, 0, $pos), substr($name, $pos + 1));
        }
        return array($name, null);
    }

    protected function loadAuthData() {
    	parent::loadAuthData();
    	$this->ktvConnection->setAuthData($this->username, $this->password);
    }

    protected function loadDefaultAuthData() {
        $this->username  = "147";
        $this->password  = "741";
    }
}
?>