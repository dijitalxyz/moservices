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

class HdoProvider extends RhdProvider {
	
	function init(Configuration &$cfg) {
		parent::init($cfg);
		$this->baseUrl = "http://hdout.tv/";
      	$this->sectionName = $this->config->get("name", "Service");
        $this->cookieName = "HdoCookie";
        
		$this->session = new HttpSession();

 		$this->http->cookie = $this->session->exists($this->cookieName) ?
        					$this->session->get($this->cookieName) : "";
        $this->sessionVar = 'SID';
    }
    
	function getIniFilename() {
		return dirname(__FILE__) . "/../config.ini";
	}
    protected function parseEpisodeLink($objectId, $xml) {
		if(!isset($xml->item)) {
			return null;
		}
		$resultObj = new MediaObject($objectId, self::$OBJ_STREAM_URL, (string)$xml->item->title);
		$resultObj->setParam("url", (string)$xml->item->videourl);
		return $resultObj;
    }
    
	protected function getSeriesList($objectId,$params) {
		$url = $this->baseUrl."List/all/XML/";
	 	$reply = $this->getData($url, "series list");
        return false === $reply ? false : $this->parseSeriesList($reply);
	}
    protected function getSerie($objectId,$params) {
		$url = $this->baseUrl."Series/".$objectId."/XML/";
		$reply = $this->getData($url, "Serie $objectId");

        return false === $reply ? false : $this->parseEpisodesList($objectId,null,$reply);
	}
	
	protected function getStreamUrl($objectId,$params) {
		$url = $this->baseUrl."EpisodeLink/".$objectId."/XML/";

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

	protected function getFavoriteSeriesList($objectId,$params) {
    	$url = $this->baseUrl."List/my/XML/";
	 	$reply = $this->getData($url, "favorite series list");
        return false === $reply ? false : $this->parseSeriesList($reply);
    }

    //TODO: change parse function fpr new series
    protected function getNewSeriesList($objectId,$params) {
    	$url = $this->baseUrl."List/new/XML/";
	 	$reply = $this->getData($url, "favorite series list");
        return false === $reply ? false : $this->parseSeriesList($reply);
    }

	protected function parseSeriesList($xml) {
		$resultObj = new MediaObject($this->config->get("name", "Service"), self::$OBJ_SERIES_LIST,$this->config->get("displayName", "Service"));
		if(isset($xml->fp) && isset($xml->fp->serieslist)) {
			foreach ($xml->fp->serieslist->item as $serie) {
				$child = new MediaObject((string)$serie->id_series, self::$OBJ_SERIE, (string)$serie->title);
				$child->setParam("etitle",  	(string)$serie->etitle);
				$child->setParam("descr", 		ParserTools::removeHtmlTags((string)$serie->info));
				$child->setParam("mark", 		(string)$serie->mark);
				
				$child->setParam("thumbnail", 	$this->baseUrl."static/c/hd/s/".(string)$serie->mark.".jpg");
				//set thumbnail as img because of different image sizes on ruhd
				$child->setParam("img", 		$child->getParam("thumbnail"));
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
		$serieObj->setParam("etitle", 	(string)$serie->etitle);
		$serieObj->setParam("descr", 	ParserTools::removeHtmlTags((string)$serie->info));;
		$serieObj->setParam("mark", 	(string)$serie->mark);
		$serieObj->setParam("thumbnail", 	$this->baseUrl."static/c/hd/s/".(string)$serie->mark.".jpg");
		$serieObj->setParam("img", 		$serieObj->getParam("thumbnail"));
		$serieObj->setParam("isclosed", (string)$serie->isclosed);
		$seasonsCnt = 0;
		$seriesCnt = 0;
		foreach ($serie->season as $season) {
			$seasonsCnt++;
			
			$seasonsSeriesCnt = 0;
			$curentSeasonId = (string)$season->attributes()->title;
			//add serie id to season
			$seasonObj = new MediaObject($serieId.self::$ID_DELIMITER.$curentSeasonId, self::$OBJ_SEASON,$this->locale->msg('Season')." ".(string)$season->attributes()->title);
			//copy serie thumbnail to season  - we have no season thumbnail from hdout
			$seasonObj->setParam("thumbnail",$serieObj->getParam('thumbnail'));

			foreach ($season->item as $episode) {
				
				$seasonsSeriesCnt++;
				$title = (string)$episode->title;
				if(!isset($title) || "" == $title) {
					//fallback to english title
					$title  = (string)$episode->etitle;
				}
				if(!isset($title) || "" == $title) {
					//fallback to episode number
					$title  = $this->locale->msg('Episode')." ".(string)$episode->vnum;
				}
				$episodeObj = new MediaObject((string)$episode->id_episodes, self::$OBJ_EPISODE, $title);
				
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
}
?>