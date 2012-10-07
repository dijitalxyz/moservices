<?php
require_once 'interfaces/vodProvider.inc';
require_once 'services/kartina.tv/providers/ktvConnection.inc';
require_once 'tools/parser.inc';

class KlbProvider extends VodProvider {
    public static $OBJ_GENRE_LIST = "GENRE_LIST";
    public static $SEARCH_TTL 		= 3600;	//1 hour
    public static $MOVIE_TTL 		= 3600;	//1 hour
    public static $GENRE_LIST_TTL 	= 86400;//24 Hours
    public static $GENRE_TTL 		= 86400;//24 Hours

    protected $ktvConnection;

    public function KlbProvider() {
        // same section name as Kartina.TV, due to same login data
        $this->sectionName = "Kartina.TV";
		$this->log = Logger::getLogger(get_class());
        $this->ktvConnection = new KtvConnection();
    }

    public function getObject($objectId, $params) {
        $objectType = $this->getParam($params, "type", null);
        if ($objectType == self::$OBJ_GENRE_LIST) {
            return $this->getGenreList($objectId, $params);
        }
        return parent::getObject($objectId, $params);
    }

    protected function splitId($id) {
        // id = "page_genre"
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
        /*
         * type  = best|last|text
         * page  = 1
         * query = text if type==text
         * genre = id
         * nums  = <movies per page>
         */
        list($page, $genre) = $this->splitId($objectId);
        $nums = $this->getParam($params, "moviesPerPage", 50);
        $type = $this->getParam($params, "moviesType", "last");
        $url = "/vod_list?type=$type&page=$page&nums=$nums";
        if ($genre != 0) {
            $url .= "&genre=$genre";
        }
        $reply = $this->ktvConnection->getData($url, "movies list");
        return false === $reply ? false : $this->parseMovieList(
            $objectId, $page, $nums, $genre, $reply);
    }

    protected function search($objectId, $params) {
        list($page, $genre) = $this->splitId($objectId);
        $nums   = $this->getParam($params, "moviesPerPage", 50);
        $search = $this->getParam($params, "search", "");
        $url = "/vod_list?type=text&page=$page&nums=$nums&query=$search";
        if ($genre != 0) {
            $url .= "&genre=$genre";
        }
        $reply = $this->ktvConnection->getData($url, "search $search");
        return false === $reply ? false : $this->parseMovieList(
            $objectId, $page, $nums, $genre, $reply, $search);
    }

    protected function getMovie($objectId, $params) {
        $url = "/vod_info?id=$objectId";
        $reply = $this->ktvConnection->getData($url, "movie info");
        return false === $reply ? false : $this->parseFimlInfo($reply);
    }

    protected function getGenreList($objectId, $params) {
        $url = "/vod_genres";
        $reply = $this->ktvConnection->getData($url, "genres list");
        return false === $reply ? false : $this->parseGenresList($reply);
    }

    protected function getStreamUrl($objectId, $params) {
        $url = "/vod_geturl?fileid=$objectId";
        $reply = $this->ktvConnection->getData($url, "stream url");
        $url = false === $reply ? false : (string) $reply->url;
        if ($url !== false) {
            $url = preg_replace('/(rtsp|http)(\/ts|)([^ "]*).*/s', '$1$3', $url);
            $so = new MediaObject($objectId, Provider::$OBJ_STREAM_URL);
            //url should expire immediatly
            $so->setExpireDate(0);
            $so->setParam("url", $url);
            return $so;
        }
        return false;
    }

    protected function parseGenresList($xml) {
        $genreList = new MediaObject("", self::$OBJ_GENRE_LIST, $this->locale->msg("Genres"));
        $genreList->setParam("servertime", (string)$xml->servertime);
        $genreList->setTtl(self::$GENRE_LIST_TTL);

        $movieList = new MediaObject("", self::$OBJ_MOVIE_LIST, $this->locale->msg("All"));
        $movieList->setExpireDate(0); 
        $genreList->addChild($movieList);
        foreach ($xml->genres->item as $xmlGenre) {
            $movieList = new MediaObject("1_" . (string)$xmlGenre->id,
                self::$OBJ_MOVIE_LIST, Utils::ucfirstUTF8((string)$xmlGenre->name));
			$movieList->setExpireDate(0);
            $genreList->addChild($movieList);
        }
        return $genreList;
    }

    protected function parseMovieList($objectId, $page, $perPage, $genre, $xml, $search = null) {
        if (null != $search) {
            $type = self::$OBJ_SEARCH;
            $movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST,
                $this->locale->msg("Search") . ": " . urldecode($search));
            $movieList->setParam("search", $search);
            $movieList->setTtl(self::$SEARCH_TTL);
        } else {
            $type = self::$OBJ_MOVIE_LIST;
            $genreName = $this->locale->msg("All");
            if (0 != $genre) {
                // request genres list from server to find genre name
                $genresObj = $this->getGenreList($objectId, $params);
                foreach ($genresObj->getChildren() as $child) {
                    if ($genre == substr($child->getId(), 2)) {
                        $genreName = $child->getTitle();
                        break;
                    }
                }
            }
            $movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST, $genreName);
            $movieList->setTtl(self::$GENRE_TTL);
        }
        $movieList->setParam("list_type",  (string)$xml->type);
        $movieList->setParam("total",      (string)$xml->total);
        $movieList->setParam("perPage",    $perPage);
        $movieList->setParam("page",       (string)$xml->page);
        $movieList->setParam("servertime", (string)$xml->servertime);

        $total   = $movieList->getParam("total");
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
                $prevPage->setParam("search", $search);
            }
            $movieList->addChild($prevPage);
        }

        foreach ($xml->rows->item as $xmlMovie) {
            $movie = new MediaObject((string)$xmlMovie->id, self::$OBJ_MOVIE, (string)$xmlMovie->name);
            //not complete objects here
            $movie->setTtl(0);
            $movie->setParam("name_orig",        (string)$xmlMovie->name_orig);
            $movie->setParam("descr",            ParserTools::removeHtmlTags((string)$xmlMovie->description));
            $movie->setParam("img",              "http://iptv.kartina.tv/".(string)$xmlMovie->poster);
            $movie->setParam("year",             (string)$xmlMovie->year);
            $movie->setParam("thumbnail",        "http://iptv.kartina.tv/".(string)$xmlMovie->poster);

            $movie->setParam("rate_imdb",        (string)$xmlMovie->rate_imdb);
            $movie->setParam("rate_kinopoisk",   (string)$xmlMovie->rate_kinopoisk);
            $movie->setParam("rate_mpaa",        (string)$xmlMovie->rate_mpaa);
            $movie->setParam("country",          (string)$xmlMovie->country);
            $movie->setParam("vis",              (string)$xmlMovie->vis);
            $movie->setParam("genre",            (string)$xmlMovie->genre_str);
            $movie->setParam("modified",         (string)$xmlMovie->dt_modify);

            $imdbRating = number_format((float)(string)$xmlMovie->rate_imdb,      1);
            $kpRating   = number_format((float)(string)$xmlMovie->rate_kinopoisk, 1);
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
                $nextPage->setParam("search", $search);
            }
            $movieList->addChild($nextPage);
        }
        return $movieList;
    }

    protected function parseFimlInfo($xml) {
        $xml = $xml->film;
        $movie = new MediaObject((string)$xml->id, self::$OBJ_MOVIE, (string)$xml->name);
        $movie->setTtl(self::$MOVIE_TTL);
        $movie->setParam("name_orig", (string)$xml->name_orig);
        $movie->setParam("descr",  ParserTools::removeHtmlTags((string)$xml->description));
        $movie->setParam("thumbnail", "http://iptv.kartina.tv/".(string)$xml->poster);
        $movie->setParam("genre", (string)$xml->genre_str);
        $movie->setParam("year", (string)$xml->year);
        $movie->setParam("director", (string)$xml->director);
        $movie->setParam("scenario", (string)$xml->scenario);
        $movie->setParam("actors", ParserTools::removeHtmlTags((string)$xml->actors));
        $movie->setParam("rate_imdb", (string)$xml->rate_imdb);
        $movie->setParam("rate_kinopoisk", (string)$xml->rate_kinopoisk);
        $movie->setParam("rate_mpaa", (string)$xml->rate_mpaa);
        $movie->setParam("country", (string)$xml->country);
        $movie->setParam("studio", (string)$xml->studio);
        $movie->setParam("awards", (string)$xml->awards);
        $length = (string)$xml->lenght;
        if ("" != $length && "0" != $length) {
            $movie->setParam("length", "$length " . $this->locale->msg("min"));
        }
        $budget = number_format((float)(string)$xml->budget, 0, '.' , ' ');
        if ("0" != $budget) {
            $movie->setParam("budget", $budget . " $");
        }
        $movie->setParam("images", (string)$xml->images);
        $movie->setParam("servertime", (string)$xml->servertime);

        $imdbRating = number_format((float)(string)$xml->rate_imdb,      1);
        $kpRating   = number_format((float)(string)$xml->rate_kinopoisk, 1);
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

        foreach ($xml->videos->item as $xmlVideo) {
            $title = (string)$xmlVideo->title;
            $title = isset($title) && $title != "" ? $title : $movie->getTitle();
            $video = new MediaObject((string)$xmlVideo->id, Provider::$OBJ_STREAM_URL, $title);
            //no live for stream url
            $video->setTtl(0);
            $descr = "";
            if ("" != (string)$xmlVideo->format) {
                $descr .= " " . (string)$xmlVideo->format;
            }
            if ("" != (string)$xmlVideo->codec) {
                $descr .= " " . (string)$xmlVideo->codec;
            }
            $length = (string)$xmlVideo->lenght;
            if ("" != $length && "0" != $length) {
                $descr .= " $length " . $this->locale->msg("min");
            }
            if ("" != (string)$xmlVideo->track1_lang) {
                $descr .= " " . (string)$xmlVideo->track1_lang;
            }
            if ("" != (string)$xmlVideo->track2_lang) {
                $descr .= "," . (string)$xmlVideo->track2_lang;
            }
            if (strlen($descr) > 1) {
                $video->setParam("descr", substr($descr, 1));
            }

            $movie->addChild($video);
        }
        return $movie;
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

    protected function loadAuthData() {
        parent::loadAuthData();
        $this->ktvConnection->setAuthData($this->username, $this->password);
    }

    protected function loadDefaultAuthData() {
        $this->username  = null;
        $this->password  = null;
    }
}
?>
