<?php
require_once 'interfaces/provider.inc';

class M3uProvider extends Provider {

    public static $OBJ_GROUP_LIST    = "GROUP_LIST";
    public static $OBJ_CHANNELGROUP  = "CHANNELGROUP";
    public static $OBJ_STREAM_LIST   = "STREAM_LIST";
    public static $OBJ_STREAM        = "STREAM";
    
	function getObject($objectId,$params) {

		$objectType = $this->getParam($params,"type");

		$playlistDir = $this->config->get("playlistDir");
		$rootObject = null;

		if(isset($objectId) && null != $objectId && "" != $objectId) {
			$rootObject = new MediaObject($objectId,self::$OBJ_STREAM_LIST,$objectId);

			$parser = new M3uParser();
			$parser->parseFile($playlistDir."/".$objectId.".m3u", $objectId,$rootObject);


		} else if ($handle = opendir($playlistDir)) {

			if($objectType == self::$OBJ_CHANNELGROUP) {
				$parser = new M3uParser();
				$rootObject = new MediaObject("root", self::$OBJ_STREAM_LIST,"Все файлы");
			} else {
				$rootObject = new MediaObject("root", self::$OBJ_GROUP_LIST,"Плэйлисты");
				$category0 = new MediaObject("", self::$OBJ_CHANNELGROUP, "Все файлы");
				$category0->params['icon'] = "all";
				$rootObject->addChild($category0);
			}

			while (false !== ($file = readdir($handle))) {
				if (strlen($file) > 4 && ".m3u" == strtolower(substr($file, -4))) {
					if($objectType == self::$OBJ_CHANNELGROUP) {
						$parser->parseFile($playlistDir."/".$file, substr($file,0,strlen($file)-4),$rootObject);
					} else {
						$playlist = new MediaObject(substr($file,0,strlen($file)-4), self::$OBJ_CHANNELGROUP,substr($file,0,strlen($file)-4));
						$playlist->setParam('url',$file);
						$playlist->setParam('icon', $this->getCategoryIcon($playlist->getId()));
						$rootObject->addChild($playlist);
					}
				}
			}
			closedir($handle);
		}
		return $rootObject ;
	}

	public function getCategoryIcon($id) {
		return strtolower($id);
	}

	function getIniFilename() {
		return dirname(__FILE__) . "/config.ini";
	}
}




class M3uParser {

	var $itemsAmount;
	var $items;
	var $name;
	var $uri;

	function M3uParser() {
		$this->itemsAmount = 0;
		$this->items = array();
	}

	function parseFile($fileUri,$name, $parent) {
			
		//$rootObject = new MediaObject($fileUri,"M3U",$name);
			
		$lines = file($fileUri);

		foreach ($lines as $line_num => $line) {
			//trim UTF8 BOM in first line if any
			if(0 == $line_num) {
				$fi = ord(substr($line,0,1));
				$se = ord(substr($line,1,1));
				$th = ord(substr($line,2,1));
				if(239 == $fi && 187 == $se && 191 == $th) {
					$line = substr($line,3,7);
				}
			}

			$line = trim($line);
			if(0 == strncasecmp($line, "#EXTM3U",7)) {
				//skip
			}
			else if(0 == strncasecmp($line, "#EXTINF:",8)){
				$descr = substr($line,8);
			}
			else if(0 != strncasecmp($line, "#",1)){
				$title = $descr;
				if(null != $descr) {
					$properties = explode(',',$descr);
					$length =  $properties[0];
					$title = $properties[1];
				}
				$item = new MediaObject($this->itemsAmount, M3uProvider::$OBJ_STREAM, $title);
				$item->setParam("url", htmlspecialchars($line));
				$item->setParam("length", $length);
					
				$descr = null;
				$parent->addChild($item);
				$this->itemsAmount++;
					
			}
		}
		//return $rootObject;
	}

	function parse($string) {
		# compact all to a single line
		$content = str_replace("\n","",$string);

		# no spaces between tags
		$content = str_replace('>[[:space:]]+', '>', $content);
		$content = str_replace('[[:space:]]+<', '<', $content);

		# parse out channels
		$channels = explode('><',$content);

		$i=0;
		foreach($channels as $k => $v) {
			$i=$i+1;
			if(strstr ($v,"channelgroup ")) {

				ereg('id="([^"]*)"', $v, $matches_id);
				ereg('title="([^"]*)"', $v, $matches_name);
				ereg('color="([^"]*)"', $v, $matches_color);

				$category = new Category(
				$matches_id[1], $matches_name[1], $matches_color[1]);

				$this->categories[] = $category;
			}
			if(strstr ($v,"channel ")) {
				$matches_id = null;
				$matches_name = null;
				$matches_idx = null;
				$matches_programm = null;
				$matches_sprog = null;
				$matches_eprog =  null;

				ereg('id="([^"]*)"', $v, $matches_id);
				ereg('title="([^"]*)"', $v, $matches_name);
				ereg('idx="([^"]*)"', $v, $matches_idx);
				ereg('programm="([^"]*)"', $v, $matches_programm);
				ereg('sprog="([^"]*)"', $v, $matches_sprog);
				ereg('eprog="([^"]*)"', $v, $matches_eprog);
				$channel = new Channel(
				$matches_idx[1], $matches_id[1], $matches_name[1],
				$matches_programm[1], $matches_sprog[1], $matches_eprog[1]);
				$this->channelsAmount++;

				/*                    if (array_key_exists('SELECTED', $attr)) {
				 $this->selectedChannel = $channel->number;
				 }
				 */
				if (count($this->categories) == 0) {
					$this->categories[] =
					new Category(0, "No name", "black");
				}

				$last = count($this->categories) - 1;
				$this->categories[$last]->addChannel($channel);
			}
		}

		if (CL_CUSTOM_SORTING) {
			$this->categories = Sorter::sortCategories($this->categories);
			$this->channelsAmount = Sorter::getChannelsAmount($this->categories);
		}

	}
}
?>