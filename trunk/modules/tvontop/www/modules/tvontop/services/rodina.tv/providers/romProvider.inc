<?php
require_once 'interfaces/vodProvider.inc';
require_once 'rodConnection.inc';
require_once 'tools/parser.inc';

class RomProvider extends VodProvider {
    public static $OBJ_GENRE_LIST = "GENRE_LIST";
    public static $SEARCH_TTL 		= 0;	//no cache
    public static $MOVIE_TTL 		= 3600;	//1 hour
    public static $GENRE_LIST_TTL 	= 86400;//24 Hours
    public static $GENRE_TTL 		= 1800;//0,5 Hours

    protected $rodConnection;

    public function RomProvider() {
        // same section name as Kartina.TV, due to same login data
        $this->sectionName = "Rodina.TV";
		$this->log = Logger::getLogger(get_class());
        $this->rodConnection = new RodConnection($this);
	}

    public function getObject($objectId, $params) {
        $objectType = $this->getParam($params, "type", self::$OBJ_GENRE_LIST);
        if ($objectType == self::$OBJ_GENRE_LIST) {
            return $this->getGenreList($objectId, $params);
        }
        return parent::getObject($objectId, $params);
    }

    protected function splitId($id) {
        $default = array(1, 0);
        if (! isset($id) || $id === "") {
            return $default;
        }
        $idParts = explode("_", $id, 2);
        $page  = isset($idParts[0]) ? (int)$idParts[0] : $default[0];
        $genre = isset($idParts[1]) ? (int)$idParts[1] : $default[1];
        return array($page, $genre);
    }

    protected function getMovieList($objectId, $params) {
  		list($page, $genre) = $this->splitId($objectId);
        $nums = $this->getParam($params, "moviesPerPage", 50);

    	$parameters = array('num_genre'  => $genre,
    						'count'  => $nums,
    						'offset'  => ($page-1)*$nums);

		$reply = $this->rodConnection->getData("get_cinema_films", $parameters, "get_cinema_films"); 
        if(false === $reply) {
        	return false;
        }

        return false === $reply ? false : $this->parseMovieList(
            $objectId, $page, $nums, $genre, $reply);
    }

    protected function search($objectId, $params) {
        list($page, $genre) = $this->splitId($objectId);

        $nums = $this->getParam($params, "moviesPerPage", 50);
 		$search = $this->getParam($params, "search", "");
    	$parameters = array('word'   => urlencode($search),
    						'count'  => $nums,
    						'offset' => ($page-1)*$nums);

		$reply = $this->rodConnection->getData("get_cinema_search", $parameters, "get_cinema_films"); 
        if(false === $reply) {
        	return false;
        }

        return false === $reply ? false : $this->parseMovieList(
            $objectId, $page, $nums, $genre, $reply, $search);
    }

    protected function getMovie($objectId, $params) {
    	//get_cinema_desc
    	$parameters = array('fid'  => $objectId);
    	$reply = $this->rodConnection->getData("get_cinema_desc", $parameters, "get_cinema_desc"); 
        if(false === $reply) {
        	return false;
        }
        return false === $reply ? false : $this->parseMovie($reply);
    }

    protected function getGenreList($objectId, $params) {

    	$reply = $this->rodConnection->getData("get_cinema_genres", null, "get_cinema_genres"); 
        if(false === $reply) {
        	return false;
        }
 		$genreList = new MediaObject("", self::$OBJ_GENRE_LIST, $this->locale->msg("Genres"));
		$genreList->setTtl(self::$GENRE_LIST_TTL);

		foreach ($reply->row as $xmlCategory) {
			$id = $this->getItemValueByName($xmlCategory, "number");
			$title = $this->getItemValueByName($xmlCategory, "title");
			
 			$movieList = new MediaObject("1_".$id, self::$OBJ_MOVIE_LIST, Utils::ucfirstUTF8($title));
			$movieList->setExpireDate(0);
            $genreList->addChild($movieList);
		}

        return $genreList;
    }

    protected function getStreamUrl($objectId, $params) {

		$parameters = array('type'  => 'http',
							'fid' => $objectId);

		$reply = $this->rodConnection->getData("get_url", $parameters, "get_url"); 
        if(false === $reply) {
        	return false;
        }

        if(isset($reply->row)) {
        	$streamUrl = $this->getItemValueByName($reply->row, "url");
        	$so = new MediaObject($objectId, self::$OBJ_STREAM_URL);
			//url should expire immediatly
			$so->setExpireDate(0);
			$so->setParam("url", $streamUrl);
			return $so;
        }

        return false;
    }

    protected function parseMovieList($objectId, $page, $perPage, $genre, $xml, $search = null) {

     	$type = self::$OBJ_MOVIE_LIST;
        $genreTitle = $this->locale->msg("All");
        $total = 0;
		if (0 != $genre) {
			$type = self::$OBJ_MOVIE_LIST;
			// request genres info from server
    		$parameters = array('number'  => $genre);

			$reply = $this->rodConnection->getData("get_cinema_genre_info", $parameters, "get_cinema_genre_info"); 
	        if(false === $reply) {
	        	return false;
	        }
			$xPath = "//array[@name='genres']/row";
			$xmlCat = $reply->xpath($xPath);

	        if(is_array($xmlCat) && isset($xmlCat[0])) {
	        	$total = $this->getItemValueByName($xmlCat[0], "count");
	        	$genreTitle = $this->getItemValueByName($xmlCat[0], "title");
	        }
		}
		if(isset($search) && $search != "") {
			// request genres info from server
    		$parameters = array('word'  => urlencode($search));

			$reply = $this->rodConnection->getData("get_cinema_search_info", $parameters, "get_cinema_search_info"); 
	        if(false === $reply) {
	        	return false;
	        }

	        if(isset($reply->row)) {
	        	$total = $this->getItemValueByName($reply->row, "total_count");
	        }

	        $type = self::$OBJ_SEARCH;
            $movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST,
                $this->locale->msg("Search") . ": " . $search);
            $movieList->setParam("search", $search);
            $movieList->setTtl(self::$SEARCH_TTL);
		} else {
	        $movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST, Utils::ucfirstUTF8($genreTitle));
			$movieList->setTtl(self::$GENRE_TTL);
		}

        $movieList->setParam("total",      $total);
        $movieList->setParam("perPage",    $perPage);
        $movieList->setParam("page",       $page);
        $movieList->setParam("servertime", (string)$xml->timeserver);

        $pages   = ($perPage == 0 ? 0 : $total / $perPage) + 1;

    	if ($page > 1) {
            $destPage = $page - 1;
            $prevPage = new MediaObject($destPage."_".$genre, $type,
                $this->locale->msg("To page") . " $destPage");
			$prevPage->setTtl(0);
            $path  = $this->config->get("cfg_realpath", Configuration::$RUNTIME_SECTION);
            $path .= $this->config->get("activeTemplate", Configuration::$RUNTIME_SECTION);
            $prevPage->setParam("thumbnail", "$path/img/prev.png");
            $prevPage->setParam("descr", $this->locale->msg("Jump to page",
                $destPage, $pages, ($destPage - 1) * $perPage + 1,
                $destPage * $perPage, $total));
            if (null != $search) {
                $prevPage->setParam("search", urlencode($search));
            }

            $movieList->addChild($prevPage);
        }

		foreach ($xml->row as $xmlMovie) {
			$id = $this->getItemValueByName($xmlMovie, "fid");
			$title = htmlspecialchars($this->getItemValueByName($xmlMovie, "title"));
			$descr = $this->getItemValueByName($xmlMovie, "small_desc");
			$imdb_rate = $this->getItemValueByName($xmlMovie, "imdb_rate");
			$kp_rate = $this->getItemValueByName($xmlMovie, "kp_rate");
			$year = $this->getItemValueByName($xmlMovie, "year");
			if("0000" == $year) {
				$year = "";
			}

 			$movie = new MediaObject($id, self::$OBJ_MOVIE, $title);
            //not complete objects here
            $movie->setTtl(0);
            $movie->setParam("descr",            $descr);
            $movie->setParam("img",              $this->getItemValueByName($xmlMovie, "small_cover"));
            $movie->setParam("year",             $year);
            $movie->setParam("thumbnail",        $this->getItemValueByName($xmlMovie, "small_cover"));
            $movie->setParam("rate_imdb",        $imdb_rate);
            $movie->setParam("rate_kinopoisk",   $kp_rate);

            $imdbRating = number_format((float)$imdb_rate,      1);
            $kpRating   = number_format((float)$kp_rate, 1);
            $midRating  = $imdbRating + $kpRating;
            $ratingNum  = 2;
            if ($imdbRating == "0.0") {
                $ratingNum--;
                $imdbRating = "-.-";
            }
            if ($kpRating == "0.0") {
                $ratingNum--;
                $kpRating = "-.-";
            }
            $movie->setParam("rating", "IMDB $imdbRating RU $kpRating");
            $movie->setParam("midRating", 0 == $ratingNum ? 0 : ($midRating / $ratingNum));
            $movieList->addChild($movie);			
		}
     	if ($page * $perPage < $total) {
            $destPage = $page + 1;
            $nextPage = new MediaObject($destPage."_".$genre, $type,
                $this->locale->msg("To page") . " $destPage");
			$nextPage->setTtl(0);
            $path  = $this->config->get("cfg_realpath", Configuration::$RUNTIME_SECTION);
            $path .= $this->config->get("activeTemplate", Configuration::$RUNTIME_SECTION);
            $nextPage->setParam("thumbnail", "$path/img/next.png");
            $nextPage->setParam("descr", $this->locale->msg("Jump to page",
                $destPage, $pages, $page * $perPage + 1,
                min($destPage * $perPage, $total), $total));
            if (null != $search) {
                $nextPage->setParam("search", urlencode($search));
            }
            $movieList->addChild($nextPage);
        }
        return $movieList;
    }

    protected function parseMovie($xml) {
        $xmlMovie = $xml->row;

		$id 		= $this->getItemValueByName($xmlMovie, "fid");
		$title		= $this->getItemValueByName($xmlMovie, "title");
		$orig_title	= $this->getItemValueByName($xmlMovie, "orig_title");
		if(!isset($orig_title) || "" == $orig_title) {
			$orig_title = $title;
		}
		$descr 		= $this->getItemValueByName($xmlMovie, "full_desc");
		$imdb_rate 	= $this->getItemValueByName($xmlMovie, "rate_imdb");
		$kp_rate 	= $this->getItemValueByName($xmlMovie, "rate_kp");
		$year = $this->getItemValueByName($xmlMovie, "prod_date");
		if("0000-00-00" == $year) {
			$year = "";
		}
        $movie = new MediaObject($id, self::$OBJ_MOVIE, $title);
        $movie->setTtl(self::$MOVIE_TTL);
        $movie->setParam("name_orig", $orig_title);
        $movie->setParam("descr",  $descr);
        $movie->setParam("thumbnail", $this->getItemValueByName($xmlMovie, "big_cover"));
        $movie->setParam("year", $year);
        $movie->setParam("rate_imdb", $imdb_rate);
        $movie->setParam("rate_kinopoisk", $kp_rate);
        $movie->setParam("country", $this->getItemValueByName($xmlMovie, "country"));
        $movie->setParam("genre",$this->getListValue($xmlMovie,"genres"));
		$movie->setParam("director",$this->getListValue($xmlMovie,"producers"));
		$movie->setParam("actors",$this->getListValue($xmlMovie,"actors"));
        $movie->setParam("servertime", (string)$xml->servertime);

        $imdbRating = number_format((float)$imdb_rate,      1);
        $kpRating   = number_format((float)$kp_rate, 1);
        $midRating  = $imdbRating + $kpRating;
        $ratingNum  = 2;
        if ($imdbRating == "0.0") {
            $ratingNum--;
            $imdbRating = "-.-";
        }
        if ($kpRating == "0.0") {
            $ratingNum--;
            $kpRating = "-.-";
        }
        $movie->setParam("rating", "IMDB $imdbRating RU $kpRating");
        $movie->setParam("midRating", 0 == $ratingNum ? 0 : ($midRating / $ratingNum));

        $video = new MediaObject($id, Provider::$OBJ_STREAM_URL, $this->locale->msg("Watch"));
        $video->setTtl(0);
        $movie->addChild($video);

        return $movie;
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

    protected function loadAuthData() {
        parent::loadAuthData();
        $this->rodConnection->setAuthData($this->username, $this->password);
    }

    protected function loadDefaultAuthData() {
        $this->username  = null;
        $this->password  = null;
    }

    public function getItemValueByName($xmlRow, $name) {
    	return 	$this->rodConnection->getItemValueByName($xmlRow, $name);
    }

    protected function getListValue($xml, $name) {
    	$xPath = "//array[@name='".$name."']/row";
		$result = $xml->xpath($xPath);
		$values = array();
		while(list( , $row) = each($result)) {
			$values[] = $this->getItemValueByName($row, "title");
		}
		return implode(", ",$values);
    }
}
?>
