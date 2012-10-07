<?php
/*  xIpla - IplaInterface.php
 *  Copyright (C) 2010 ToM/UD
 *
 *  This Program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2, or (at your option)
 *  any later version.
 *
 *  This Program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with flvstreamer; see the file COPYING.  If not, write to
 *  the Free Software Foundation, 675 Mass Ave, Cambridge, MA 02139, USA.
 *  http://www.gnu.org/copyleft/gpl.html
 *
 */

include_once "config.php";

define("CLIENT_NAME", "ipla");
define("CLIENT_VERSION", "281");
define("USER_ID", "-4572669");

define("DATABASE_NAME", "/tmp/xIpla.cache");

define("CATEGORIES_LIST_URL", "http://getmedia.redefine.pl/r/0_9_10_0/action/2.0/categories/list/");
define("VODS_LIST_URL","http://getmedia.redefine.pl/action/2.0/vod/list/");
define("VODS_RECOMENDATIONS_URL", "http://get.reko.redefine.pl/recommendations/");
define("VODS_SEARCH_URL","http://getmedia.redefine.pl/vods/search/");
define("CHANNELS_LIST_URL", "http://getmedia.redefine.pl/action/2.0/channels/");
define("CHANNELS_ID", "17126");


class IplaInterface
{	
	var $db;
	var $categories;
	var $vods;
	var $channels;

	function IplaInterface()
	{

		if (file_exists(DATABASE_NAME)) $this->db = sqlite_open(DATABASE_NAME);
		else
		{

			$this->db = sqlite_open(DATABASE_NAME);

			$this->CreateDatabase();
						
		}
		
		$this->categories = "";
		$this->vods = "";
		$this->channels = "";

	}

	
	function Close()
	{
		
		sqlite_close($this->db);
		$this->categories = "";
		$this->vods = "";
		$this->channels = "";
		
	}

	private function cutString($string,$end) 
	{

		if(strlen($string)>$end)
		{
			$string=preg_replace('/\es+?(\eS+)?$/','',substr($string,0,$end+1));
			$string=substr($string,0,$end)."...";
		}
		return $string;
		
	}  	
	
	
	private function CreateDatabase()
	{

		sqlite_single_query($this->db,'CREATE TABLE categories (sqlid INTEGER PRIMARY KEY, id INTEGER, pid INTEGER, addtime INTEGER, title VARCHAR(255), descr BLOB, img VARCHAR(255));');
		sqlite_single_query($this->db,'CREATE INDEX categories_id ON categories (id, pid);');
		
		sqlite_single_query($this->db,'CREATE TABLE vods (sqlid INTEGER PRIMARY KEY, id INTEGER, sort INTEGER, addtime INTEGER, title VARCHAR(255), descr BLOB, plevel FLOAT, vote FLOAT, adult INTEGER, img VARCHAR(255), url VARCHAR(255), timestamp INTEGER, dur INTEGER);');
		sqlite_single_query($this->db,'CREATE INDEX vods_id ON vods (id, sort);');
	
		sqlite_single_query($this->db,'CREATE TABLE vodsrecommendations (sqlid INTEGER PRIMARY KEY, addtime INTEGER, title VARCHAR(255), descr BLOB, plevel FLOAT, vote FLOAT, adult INTEGER, img VARCHAR(255), url VARCHAR(255), timestamp INTEGER, dur INTEGER);');
		
		sqlite_single_query($this->db,'CREATE TABLE epg (sqlid INTEGER PRIMARY KEY, id INTEGER, epgtimestart VARCHAR(5), epgtimeend VARCHAR(5), title VARCHAR(255), descr BLOB, plevel FLOAT, vote FLOAT, adult INTEGER, img VARCHAR(255), url VARCHAR(255), timestamp INTEGER, dur INTEGER);');		
		
	}

	
	private function GetWebData($url)
	{
		
		$url = str_replace(" ","%20",$url);
		
		$pUrl = parse_url($url);
		$host = $pUrl['host'];
	
		$options = array(
			'http' => array(
				'method'=>  'GET',
				'header'=> 	"User-Agent: " . CLIENT_NAME . "/" . CLIENT_VERSION ."\r\n" .
							"Host: $host\r\n" .
							"Content-Type: application/x-www-form-urlencoded\r\n" .
							"X-Client: " . CLIENT_NAME . "\r\n" .
							"X-Client-Build: " . CLIENT_VERSION . "\r\n" .
							"X-Client-Params: InstallerType=1\r\n"
							)
						);		
		
		$context = stream_context_create($options);

		$oldLevel = error_reporting(E_ALL ^ E_WARNING);

		for ($i = 1; $i <= 5; $i++) 
		{
			
			$f = fopen($url, "r", false, $context);
			if ($f) break;
        
			sleep(1); 
			
		}				
		
		error_reporting($oldLevel);

		$content = "";
		
		if ($f)
		{
			
			while($data = fread($f, 1024)) $content .= $data;
		
			fclose($f);
			
		}
		
		return $content;		

	}

	
	private function ParseVodsAndStore($www_data, $cat_id, $sort, $store)
	{
	
		
		$xmlParser = xml_parser_create();
		xml_parse_into_struct($xmlParser, $www_data, $vals, $index);
		xml_parser_free($xmlParser);	

		$open = 0;			
		$starttime = 0;
		
		
		foreach ($vals as $val) 
		{

			if (($val['tag'] == 'VOD') && ($val['type'] == 'open'))
			{
				
				$title = sqlite_escape_string($val['attributes']['TITLE']);
				$descr = sqlite_escape_string($val['attributes']['DESCR']);
				$plevel = $val['attributes']['PLEVEL'];
				$vote  = $val['attributes']['VOTE'];
				$adult = $val['attributes']['ADULT'];
				
				if ((strlen($title) == 0) && (strlen($descr) > 0 ))
				{
					$title = $descr;
					$title = $this->cutString($title, 45);
				}
				
								
				$timestamp = $val['attributes']['TIMESTAMP'];
				$dur = $val['attributes']['DUR'];
				$img = ($val['attributes']['THUMBNAIL_BIG'] == "" ? $val['attributes']['THUMBNAIL'] : $val['attributes']['THUMBNAIL_BIG']);
				
				if ($store == 4)
				{
				
					$epgtimestart = date("H:i", $starttime);
					$starttime += $dur;
					$epgtimeend = date("H:i", $starttime);
					
				}
				
				$quality = -1;
				$format = -1;
				$url = "";
				$open = 1;
				
			}	
			elseif (($val['tag'] == 'VOD') && ($val['type'] == 'close') && ($open == 1))
			{
			
				if((strlen($url) > 0) && (strlen($title) > 0))
				{
					
					switch ($store)
					{

						case 1:
							
							sqlite_single_query($this->db, "INSERT INTO vods VALUES (NULL, '$cat_id', '$sort', '$addtime', '$title', '$descr', '$plevel', '$vote', '$adult', '$img', '$url', '$timestamp', '$dur');");
							break;

						case 2:
						
							sqlite_single_query($this->db, "INSERT INTO vodsrecommendations VALUES (NULL, '$addtime', '$title', '$descr', '$plevel', '$vote', '$adult', '$img', '$url', '$timestamp', '$dur');");
							break;
						
						case 3:
							
							$vod = array("title" => $title, "descr" => $descr, "plevel" => $plevel, "vote" => $vote, "adult" => $adult, "img" => $img, "url" => $url, "timestamp" => $timestamp, "dur" => $dur);
							$this->vods[] = $vod;
							break;
						
						case 4:
						
							sqlite_single_query($this->db, "INSERT INTO epg VALUES (NULL, '$cat_id', '$epgtimestart', '$epgtimeend', '$title', '$descr', '$plevel', '$vote', '$adult', '$img', '$url', '$timestamp', '$dur');");
							break;
						
					}
					
				}
				
				$open = 0;
				
			}
			elseif (($val['tag'] == 'SRCREQ') && ($val['type'] == 'complete') && ($open == 1) && ($val['attributes']['QUALITY'] > $quality) && ($val['attributes']['QUALITY'] <= MAX_VIDEO_QUALITY) && ($val['attributes']['DRMTYPE'] == 0))
			{
			
				$quality = $val['attributes']['QUALITY'];
				$format = $val['attributes']['FORMAT'];
				$url = $val['attributes']['URL'];
				
			}
			elseif (($val['tag'] == 'SRCREQ') && ($val['type'] == 'complete') && ($open == 1) && ($val['attributes']['QUALITY'] == $quality) && ($val['attributes']['QUALITY'] <= MAX_VIDEO_QUALITY) && ($val['attributes']['DRMTYPE'] == 0) && ($val['attributes']['FORMAT'] > $format) && ($val['attributes']['FORMAT'] <= MAX_VIDEO_FORMAT))
			{

				$quality = $val['attributes']['QUALITY'];
				$format = $val['attributes']['FORMAT'];
				$url = $val['attributes']['URL'];
			
			}
			elseif (($val['tag'] == 'SEMILIVE') && ($val['type'] == "open")) $starttime = $val['attributes']['TIME'] - $val['attributes']['OFFSET'];
			
		}
		
	}

	
	function GetEpgArray($id)
	{
		
		$ret = array();
		$count = 0;
		
		$id = sqlite_escape_string($id);
		
		$query = sqlite_query($this->db,"SELECT epgtimestart, title FROM epg WHERE id = '$id'");
		
		while($row = sqlite_fetch_array($query, SQLITE_ASSOC))
		{
		
			$ret[$count] = $row['epgtimestart'] . "  " . $row['title'];
			$count++;
			
		}
		
		return $ret;
		
	}
	

	function GetCategoriesList($cat_id)
	{
	
		$addtime = time();
		$cachetime = $addtime - (CACHE_TIME_CATEGORIES * 3600);
		
		if (sqlite_single_query($this->db, "SELECT COUNT(*) FROM categories WHERE addtime > '$cachetime'") == 0)
		{
			
			sqlite_single_query($this->db, "DELETE FROM categories");
			
			$url = CATEGORIES_LIST_URL . "?login=common_user";
			$www_data = $this->GetWebData($url);
			
			$xmlParser = xml_parser_create();
			xml_parse_into_struct($xmlParser, $www_data, $vals, $index);
			xml_parser_free($xmlParser);	
		
			foreach ($vals as $val) 
			{
			
				if (($val['tag'] == 'CAT') && $val['type'] == 'open')
				{
			
					$id = $val['attributes']['ID'];
					$title = sqlite_escape_string($val['attributes']['TITLE']);
					$descr = sqlite_escape_string($val['attributes']['DESCR']);
					$img = ($val['attributes']['THUMBNAIL_BIG'] == "" ? $val['attributes']['THUMBNAIL'] : $val['attributes']['THUMBNAIL_BIG']);
					$pid = $val['attributes']['PID'];
				
					sqlite_single_query($this->db, "INSERT INTO categories VALUES (NULL, '$id', '$pid', '$addtime', '$title', '$descr', '$img');");

				}

			}
			
			$CategoriesToDelete = array("FILM", "MINI", "Gry Flash");
						
			foreach ($CategoriesToDelete as $val) 
			{
			
				$id = sqlite_single_query($this->db, "SELECT id FROM categories WHERE title = '$val' LIMIT 1");
				sqlite_single_query($this->db, "DELETE FROM categories WHERE id = '$id' OR pid = '$id'");
				
			}

		}
		
		$cat_id = sqlite_escape_string($cat_id);
		$this->categories = sqlite_array_query($this->db, "SELECT id, pid, title, descr, img FROM categories WHERE pid = '$cat_id'", SQLITE_ASSOC);
		
	}

	
	function GetVodsList($cat_id, $sort)
	{

		$addtime = time();
		$cachetime = $addtime - (CACHE_TIME_VODS * 3600);

		$cat_id = sqlite_escape_string($cat_id);
		$sort = sqlite_escape_string($sort);
		
		if (sqlite_single_query($this->db, "SELECT COUNT(*) FROM vods WHERE addtime <= '$cachetime'") > 0) sqlite_single_query($this->db, "DELETE FROM vods WHERE addtime <= '$cachetime'");
		
		if (sqlite_single_query($this->db, "SELECT COUNT(*) FROM vods WHERE addtime > '$cachetime' AND id = '$cat_id' AND sort = '$sort'") == 0)
		{
			
			$url = VODS_LIST_URL . "?login=common_user&passwdmd5=&ver=" . CLIENT_VERSION . "&cuid=" . USER_ID . "&category=" . $cat_id . "&sortedby=" . $sort . "&page=0";
			$www_data = $this->GetWebData($url);
			
			$this->ParseVodsAndStore($www_data, $cat_id, $sort, 1);
		
		}

		
		$this->vods = sqlite_array_query($this->db, "SELECT title, descr, plevel, vote, adult, img, url, timestamp, dur FROM vods WHERE id = '$cat_id' AND sort = '$sort'", SQLITE_ASSOC); 
		
	}
	
	
	function GetVodsRecommendations()
	{
		$addtime = time();
		$cachetime = $addtime - (CACHE_TIME_VODS_RECOMMENDATIONS * 3600);

		if (sqlite_single_query($this->db, "SELECT COUNT(*) FROM vodsrecommendations WHERE addtime > '$cachetime'") == 0)
		{
			
			sqlite_single_query($this->db, "DELETE FROM vodsrecommendations");
			
			$url = VODS_RECOMENDATIONS_URL;
			$www_data = $this->GetWebData($url);

			$this->ParseVodsAndStore($www_data, 0, 0, 2);
			
		}
		
		$this->vods = sqlite_array_query($this->db, "SELECT title, descr, plevel, vote, adult, img, url, timestamp, dur FROM vodsrecommendations", SQLITE_ASSOC); 
	
	}
	
	
	function GetVodsSearch($keywords)
	{
	
		$url = VODS_SEARCH_URL . "?uid=" . USER_ID . "&vod_limit=500&matchopt=0&ver=" . CLIENT_VERSION. "&keywords=" . $keywords;
		$www_data = $this->getWebData($url);
		
		$this->vods = array();
				
		$this->ParseVodsAndStore($www_data, 0, 0, 3);
		
	}

	function GetVodsSearchSQL($keywords, $cat_id, $sort)
	{
		
		$cat_id = sqlite_escape_string($cat_id);
		$sort = sqlite_escape_string($sort);
		$keywords = sqlite_escape_string($keywords);

		$this->vods = sqlite_array_query($this->db, "SELECT title, descr, plevel, vote, adult, img, url, timestamp, dur FROM vods WHERE id = '$cat_id' AND sort = '$sort' AND title LIKE '%$keywords%'", SQLITE_ASSOC); 
	
	}
	
	function GetChannelsList()
	{
	
		sqlite_single_query($this->db, "DELETE FROM epg");
		
		$url = CHANNELS_LIST_URL . "?login=common_user&passwdmd5=&ver=" . CLIENT_VERSION . "&cuid=" . USER_ID . "&category=" . CHANNELS_ID;
		$www_data = $this->getWebData($url);
		
		$this->channels = array();
				
		$xmlParser = xml_parser_create();
		xml_parse_into_struct($xmlParser, $www_data, $vals, $index);
		xml_parser_free($xmlParser);
		
		foreach ($vals as $val) 
		{		

			if (($val['tag'] == 'SCHANNEL') && ($val['type'] == "open"))
			{
				
				$id = $val['attributes']['ID'];
				$img = ($val['attributes']['THUMBNAIL_BIG'] == "" ? $val['attributes']['THUMBNAIL'] : $val['attributes']['THUMBNAIL_BIG']);
				$url = $val['attributes']['URL'];

				$channel = array("id" => $id, "descr" => $val['attributes']['DESCR'], "img" => $img);
				$this->channels[] = $channel;
		
				date_default_timezone_set('Europe/Warsaw');
				$www_data = $this->getWebData($url);
				$this->ParseVodsAndStore($www_data, $id, 0, 4);
			
			}		
		
		}		
	
	}
	
	
	function GetVodsEpg($id)
	{
		$id = sqlite_escape_string($id);
		
		
		$this->vods = sqlite_array_query($this->db, "SELECT epgtimestart, epgtimeend, title, descr, plevel, vote, adult, img, url, timestamp, dur FROM epg WHERE id = '$id'", SQLITE_ASSOC); 
	
	}	
	
	
}
?>