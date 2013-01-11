<?php
require_once "tools/http.inc";
require_once "tools/logger.inc";
require_once "tools/parser.inc";
require_once "tools/exceptions.inc";
require_once "tools/httpSession.inc";



define('E_UNKNOW_ERROR',            0);
define('E_INCORRECT_REQUEST',       1);
define('E_TOO_ENOUGH_DATA',         2);
define('E_ACCESS_DENIED',           3);
define('E_INCORRECT_LOGIN',         4);
define('E_CONTRACT_INACTIVE',       5);
define('E_CONTRACT_PAUSED',         6);
define('E_NOT_ALLOWED',             7);
define('E_GET_URL_INFO',            8);
define('E_NEED_DAY',                9);
define('E_NEED_CHANNEL_ID',         10);
define('E_ANOTHER_CLIENT_IN',       11);
define('E_AUTH_ERROR',              12);
define('E_PACKET_EXPIRED',          13);
define('E_INCORRECT_METHOD',        14);
define('E_ARCHIVE_NOT_AVAILABLE',   15);
define('E_NEED_PLACE',              16);
define('E_NEED_SETTING_VARIABLE',   17);
define('E_PASS_BAD_CONFIRM',        18);
define('E_CURRENT_PASS_BAD',        19);
define('E_NEW_PASS_BAD',            20);
define('E_NEED_VALUE',              21);
define('E_VALUE_NOT_ALLOWED',       22);


define('M_FAVORITE_SETS',   2);
define('M_PASS_CHANGED',    3);
define('M_COMPLETED',       4);


class KtvConnection {
    protected $baseUrl;
	protected $http;
	protected $log;
	protected $cookieName;
	protected $serverTime;
    protected $lastError;
	protected $errMsg = array();
	protected $username;
	protected $password;

	public function KtvConnection() {
        $this->lastError = null;
		$this->cookieName = "KtvCookie";
		$this->log  = Logger::getLogger(get_class());
        $this->baseUrl = "http://iptv.kartina.tv/api/xml";
		$this->http = new HttpTools(
            "User-Agent: Mozilla/5.0\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Connection: Close\r\n");
	
		$this->session = new HttpSession();

 		$this->http->cookie = $this->session->exists($this->cookieName) ?
        					$this->session->get($this->cookieName) : "";

		//TODO: move it!move it!
		$this->errMsg[E_INCORRECT_REQUEST]      = "Incorrect request";
		$this->errMsg[E_TOO_ENOUGH_DATA]        = "Wrong login or password";
		$this->errMsg[E_ACCESS_DENIED]          = "Access denied";
		$this->errMsg[E_INCORRECT_LOGIN]        = "Login incorrect";
		$this->errMsg[E_CONTRACT_INACTIVE]      = "You contract is inactive";
		$this->errMsg[E_CONTRACT_PAUSED]        = "You contract is paused";
		$this->errMsg[E_NOT_ALLOWED]            = "Channel not found or not allowed";
		$this->errMsg[E_GET_URL_INFO]           = "Error generate URL. Bad parameters";
		$this->errMsg[E_NEED_DAY]               = "Need DAY parameter <DDMMYY>";
		$this->errMsg[E_NEED_CHANNEL_ID]        = "Need ChannelID";
		$this->errMsg[E_ANOTHER_CLIENT_IN]      = "Another client with you login was logged";
		$this->errMsg[E_AUTH_ERROR]             = "Authentification error";
		$this->errMsg[E_PACKET_EXPIRED]         = "You packet was expired";
		$this->errMsg[E_UNKNOW_ERROR]           = "Unknown error";
		$this->errMsg[E_INCORRECT_METHOD]       = "Unknown API function";
		$this->errMsg[E_ARCHIVE_NOT_AVAILABLE]  = "Archive is not available";
		$this->errMsg[E_NEED_PLACE]             = "Need place to set";
		$this->errMsg[E_NEED_SETTING_VARIABLE]  = "Need name of settings variable";
		$this->errMsg[E_PASS_BAD_CONFIRM]       = "Incorrect confirmation code";
		$this->errMsg[E_CURRENT_PASS_BAD]       = "Current code is wrond";
		$this->errMsg[E_NEW_PASS_BAD]           = "New code is wrond";
		$this->errMsg[E_NEED_VALUE]             = "Need value (val) parameter";
		$this->errMsg[E_VALUE_NOT_ALLOWED]      = "This value is not allowed";

		$this->errMsg[M_FAVORITE_SETS]          = "Favorite channel was set";
		$this->errMsg[M_PASS_CHANGED]           = "Password was changed";
		$this->errMsg[M_COMPLETED]              = "Operation completed successfully";
	}

    public function setAuthData($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getData($url, $name, $useBaseUrl = true) {
		$this->log->debug("Getting $name");
		if ($useBaseUrl) {
            $url = $this->baseUrl . $url;
		}
		$reply = $this->http->sendGetRequest($url);
		if (! $this->isAuthorized($reply)) {
			$this->login();
			$this->log->debug("Second try to get $name");
			$reply = $this->http->sendGetRequest($url);
			if (!$this->isAuthorized($reply)) {
				$reply = false;
				throw new AuthException($this->errMsg[$this->lastError], $this->lastError);
			}
		}
		if(false !== $reply) {
			$reply  = simplexml_load_string($reply);
			$this->serverTime = (int)$reply->servertime;
		}
		return $reply;
	}

	public function getServerTime() {
		return $this->serverTime;
	}

	public function isAuthorized($reply = "") {
		if (false === $reply) {
			$reply = "<error><code>1001</code></error>"; # No connection
		}
		$error     = ParserTools::parseParam($reply, '<error>', '</error>', '');
		$errorCode = ParserTools::parseParam($error, '<code>', '</code>', null);

		$ok = isset($this->http->cookie) &&
		0 != strlen($this->http->cookie) &&
		false == strpos($this->http->cookie, "deleted") &&
		null  == $errorCode;
		if (! $ok) {
			$this->log->debug("Authorization missed or lost. Error ". $errorCode . " (".$error.")");
			$this->lastError = $errorCode;
		}
		return $ok;
	}
    
	public function login() {
		$this->log->debug("Login started");

		$this->forgetCookie();

		$url = $this->baseUrl . "/login?login=$this->username&pass=$this->password";
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

		$this->session->set($this->cookieName,$this->http->cookie);

		return true;
	}

	public function logout() {
		$url = $this->baseUrl . "/logout";
		$reply = $this->http->sendGetRequest($url);
		$this->forgetCookie();
	}
	public function forgetCookie() {
		$this->session->set($this->cookieName,"");
		$this->http->cookie = "";
	}
}
?>