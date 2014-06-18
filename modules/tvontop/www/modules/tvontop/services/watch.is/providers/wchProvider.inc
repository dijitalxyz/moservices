<?php
require_once 'interfaces/vodProvider.inc';
require_once 'tools/parser.inc';
require_once 'tools/httpSession.inc';

class WchProvider extends VodProvider {
	public static $OBJ_GENRE_LIST = "GENRE_LIST";
	public static $SEARCH_TTL 		= 3600;	//1 hour
	public static $MOVIE_TTL 		= 3600;	//1 hour
	public static $GENRE_LIST_TTL 	= 86400;//24 Hours
	public static $GENRE_TTL 		= 86400;//24 Hours
	
	public static $SESSION_TIMEOUT  = 86400;//244 Hours
	
	protected static $AUTHORIZATION_ERROR_TOKEN = "<error>Access Denied</error>";
	protected $sessionVar = "PHPSESSID";
	
	protected $session = null;

	public function WchProvider() {
	}

	public function init(Configuration &$cfg) {
		parent::init($cfg);
		$this->baseUrl = "http://watch.is/api/";
	  	$this->sectionName = $this->config->get("name", "Service");
		$this->cookieName = "WatchIs_Cookie";
 		$this->http = new HttpTools(
			"User-Agent: Mozilla/5.0\r\n" .
			"Connection: Close\r\n");

 		$this->session = new HttpSession();
 		$this->http->cookie = $this->session->exists($this->cookieName) ?
							$this->session->get($this->cookieName) : "";
		$this->log  = Logger::getLogger(get_class());
	}

	public function getObject($objectId, $params) {
		$objectType = $this->getParam($params, "type", null);
		if ($objectType == self::$OBJ_GENRE_LIST) {
			return $this->getGenreList($objectId, $params);
		} 
		return parent::getObject($objectId, $params);
	}

	protected function splitId($id) {
		// id = "[page]_[genreId]"
		$default = array(1, 0);
		if (! isset($id) || $id === "") {
			return $default;
		}
		$idParts = explode("_", $id, 2);
		$page  = isset($idParts[0]) ? (int)$idParts[0] : $default[0];
		$genre = isset($idParts[1]) ? (int)$idParts[1] : $default[1];
		return array($page, $genre);
	}

	protected function getData($url, $name) {
		$loginDone = false;
		//check cookie and try to login 
		if (! $this->isAuthorized()) {
			$this->loadAuthData();
			$this->login($this->username, $this->password);
			$loginDone  = true;
		}
		//try to get data
		$reply = $this->http->sendGetRequest($url);

		//check reply for authorization error, relogin if not already done and get data again
		if (!$this->isAuthorized($reply) && !$loginDone) {
			$loginDone  = true;
			$this->loadAuthData();
			$this->login($this->username, $this->password);
			$reply = $this->http->sendGetRequest($url);
		}
		//last check for authorization error and throw exception
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

	protected function loadAuthData() {
		if (null != $this->config) {
			$this->username = $this->config->get("username", $this->sectionName, $this->username);
			$this->password = $this->config->get("password", $this->sectionName, $this->password);
		}
	}

	public function forgetCookie() {
		$this->session->set($this->cookieName,"");
		$this->http->cookie = "";
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

  	protected function login($username, $password) {
  		$url = $this->baseUrl;
 		$cookies = $this->http->getPageCookies($url);
  		if(isset($cookies[$this->sessionVar])) {
  			$this->http->cookie = "Cookie: $this->sessionVar=".$cookies[$this->sessionVar];
  		}

		$authData = "username=$username&password=$password&rnd=".rand(0, 10000);
		$url = $this->baseUrl."?".$authData;
		$reply = $this->http->sendGetRequest($url);
		//save cookie in session for TIMEOUT seconds, after this time (4h) login is necessary
		$this->session->set($this->cookieName,$this->http->cookie, self::$SESSION_TIMEOUT);
		return $reply != false;
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
		$type = $this->getParam($params, "moviesType", null);
		if($type == "best") {
			//no pagind for top fimes, only 50 results 
			$url = $this->baseUrl."top";
			$nums = 50;
		} else {
			$url = $this->baseUrl."?page=".($page-1);
			if ($genre != 0) {
				$url .= "&genre=$genre";
			}
		}
		$reply = $this->getData($url, "movies list");
		if(false === $reply) {
			return false;
		}
		if($type == "best") {
			$genreName = $this->locale->msg("Best");
		} else {
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
		}
		return $this->parseMovieList($objectId, $page, $nums, $genre, $genreName, $reply);
	}

	protected function search($objectId, $params) {
		list($page, $genre) = $this->splitId($objectId);
		$nums   = $this->getParam($params, "moviesPerPage", 50);
		$search = $this->getParam($params, "search", "");
		$url = $this->baseUrl."?search=".$search;
		if ($genre != 0) {
			$url .= "&genre=$genre";
		}
		$reply = $this->getData($url, "search $search");
		return $this->parseMovieList($objectId, $page, $nums, $genre, $reply, $search);
	}

	protected function getMovie($objectId, $params) {
		$url = $this->baseUrl."watch/".$objectId;
		$reply = $this->getData($url, "movie info");
		return false === $reply ? false : $this->parseFimlInfo($reply);
	}

	protected function getGenreList($objectId, $params) {
		$url = $this->baseUrl."genres";
		$reply = $this->getData($url, "genres list");
		return false === $reply ? false : $this->parseGenresList($reply);
	}

	protected function getStreamUrl($objectId, $params) {
		$hd = false;
		if(substr($objectId,0,3) == "HD_") {
			$objectId = substr($objectId, 3);
			$hd = true; 
		}
		
		$url = $this->baseUrl."watch/".$objectId;
		$reply = $this->getData($url, "movie info");
		if(false !== $reply ) {
			$so = new MediaObject($objectId, Provider::$OBJ_STREAM_URL);
			//url should expire immediatly
			$so->setExpireDate(0);
			if($hd && isset($reply->hdvideo)) {
				$streamUrl = (string)$reply->hdvideo;
			} else {
			 	$streamUrl = (string)$reply->video;
			}
			//$so->setParam("url", (string)$reply->video);
			$so->setParam("url", $streamUrl);
			return $so;
		}
		return false;
	}

	protected function parseGenresList($xml) {
		$genreList = new MediaObject("", self::$OBJ_GENRE_LIST, $this->locale->msg("Genres"));
		$genreList->setTtl(self::$GENRE_LIST_TTL);

		$movieList = new MediaObject("", self::$OBJ_MOVIE_LIST, $this->locale->msg("All"));
		$movieList->setExpireDate(0); 
		$genreList->addChild($movieList);
		foreach ($xml->item as $xmlGenre) {
			$movieList = new MediaObject("1_" . (string)$xmlGenre->id,
				self::$OBJ_MOVIE_LIST, Utils::ucfirstUTF8((string)$xmlGenre->title));
			$movieList->setExpireDate(0);
			$genreList->addChild($movieList);
		}
		return $genreList;
	}

	protected function parseMovieList($objectId, $page, $perPage, $genre, $genreName, $xml, $search = null) {
		if (null != $search) {
			$type = self::$OBJ_SEARCH;
			$movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST,
				$this->locale->msg("Search") . ": " . urldecode($search));
			$movieList->setParam("search", $search);
			$movieList->setTtl(self::$SEARCH_TTL);
		} else {
			$type = self::$OBJ_MOVIE_LIST;
			$movieList = new MediaObject($objectId, self::$OBJ_MOVIE_LIST, $genreName);
			$movieList->setTtl(self::$GENRE_TTL);
		}
		if(false == $xml) {
			return $movieList;
		}
		$attributes = $xml->attributes();
		if(null != $attributes->page) {
			$page = (string)$attributes->page+1;
		}
		if(null != $attributes->total) {
			$total = (string)$attributes->total;
		} else {
			$total = (string)$xml->children()->count();
		}
		if(null != $attributes->pageSize) {
			$perPage = (string)$attributes->pageSize;
		}

		$movieList->setParam("total",	$total);
		$movieList->setParam("perPage",	$perPage);
		$movieList->setParam("page",	$page); 
		$pages = ($perPage == 0 ? 1 : floor($total / $perPage));
		if(($total % $perPage ) > 0) {
			$pages ++; 
		}

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

		foreach ($xml->item as $xmlMovie) {
			$movie = new MediaObject((string)$xmlMovie->id, self::$OBJ_MOVIE, (string)$xmlMovie->title);
			//not complete objects here
			$movie->setTtl(0);
			$movie->setParam("name_orig",		(string)$xmlMovie->title);
//			$movie->setParam("descr",			ParserTools::removeHtmlTags((string)$xmlMovie->description));
			$movie->setParam("img",			  (string)$xmlMovie->poster);
			$movie->setParam("year",			 (string)$xmlMovie->year);
			$movie->setParam("thumbnail",		(string)$xmlMovie->poster);
			$imdbRating = number_format((float)(string)$xmlMovie->rating,	  1);
			if ($imdbRating == "0.0") {
				$imdbRating = "-.-";
			}
			$movie->setParam("rating", $imdbRating);
			$movie->setParam("midRating", $imdbRating);
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
		$movie = new MediaObject((string)$xml->id, self::$OBJ_MOVIE, (string)$xml->title);
		$movie->setTtl(self::$MOVIE_TTL);
		$movie->setParam("name_orig", (string)$xml->title);
		$movie->setParam("descr",  ParserTools::removeHtmlTags((string)$xml->about));
		$movie->setParam("thumbnail", (string)$xml->poster);
		$movie->setParam("genre", (string)$xml->genre);
		$movie->setParam("year", (string)$xml->year);
		$movie->setParam("director", (string)$xml->director);
		$movie->setParam("actors", ParserTools::removeHtmlTags((string)$xml->cast));
		$movie->setParam("country", (string)$xml->country);
		$length = (string)$xml->duration;
		if ("" != $length && "0" != $length) {
			$movie->setParam("length", "$length " . $this->locale->msg("min"));
		}
		$video = new MediaObject((string)$xml->id, Provider::$OBJ_STREAM_URL,   $this->locale->msg("Смотреть"));
		//no live for stream url
		$video->setTtl(0);
		$movie->addChild($video);
		if(isset($xml->hdvideo)) {
			$video = new MediaObject("HD_".(string)$xml->id, Provider::$OBJ_STREAM_URL,   $this->locale->msg("Смотреть") . " " . $this->locale->msg("HD"));
			//no live for stream url
			$video->setTtl(0);
			$movie->addChild($video);
		}
		return $movie;
	}

	public function postProcess($object) {
		return $object;
	}

	protected function loadDefaultAuthData() {
		$this->username  = null;
		$this->password  = null;
	}
}
?>
