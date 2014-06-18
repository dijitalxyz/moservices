<?php
require_once "tools/http.inc";
require_once "tools/logger.inc";
require_once "tools/parser.inc";
require_once "tools/exceptions.inc";
require_once "tools/httpSession.inc";

class RodConnection {
    protected $baseUrl;
	protected $http;
	protected $log;
	protected $cookieName;
	protected $serverTime;
    protected $lastError;
	protected $errMsg = array();
	protected $username;
	protected $password;

	public function RodConnection() {
        $this->lastError = null;
		$this->cookieName = "RodCookie";
		$this->log  = Logger::getLogger(get_class());
        $this->baseUrl = "http://api.rodina.tv/";
		$this->http = new HttpTools(
            "User-Agent: Mozilla/5.0\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Connection: Close\r\n");

		$this->session = new HttpSession();
		$this->http->cookie = $this->session->exists($this->cookieName) ?
        					$this->session->get($this->cookieName) : "";
	}

    public function setAuthData($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    protected function getDevice() {
    	return "tvontop";
    }

    protected function getVersion() {
    	return "1.1.6";
    }

    protected function sendQuery($query) {
		$portal = $this->session->get("portal");
		$token  = $this->getToken();
		$url = $portal ."?token=".$token."&query=".$query;
		return $this->getAuthorizedXml($reply = $this->http->sendGetRequest($url));
		
    }

	public function getData($query, $params, $name, $useBaseUrl = true) {
		$loginDone = false;
		//check cookie and try to login 
		if (! $this->isAuthorized()) {
			$this->login($this->username, $this->password);
			$loginDone  = true;
		}
		$keys = "";
		$values="";
		if(isset($params)) {
			$keys = implode("|",array_keys($params));
			$values = implode("|",$params);
		}

		$xml = $this->sendQuery($query."&key=".$keys."&value=".$values);
		//check reply for authorization error, relogin if not already done and get data again
		if ((!isset($xml) || $xml->attributes()->status == "error")&& !$loginDone) {
			$loginDone  = true;
			$this->login($this->username, $this->password);
			$xml = $this->sendQuery($query."&key=".$keys."&value=".$values);
		}

		//last check for authorization error and throw exception
		if (!isset($xml)) {
			$this->forgetCookie();
			throw new AuthException('Authorization error');
		}
		return $xml;
	}

	/**
	 * return xml object or null if autorisation failed
	 * */
	protected function getAuthorizedXml($serverReply) {
		if(false !== $serverReply) {
			$xml = simplexml_load_string($serverReply);
			if(isset($xml)) {
				$attributes = $xml->attributes();
				if(null != $attributes->status && (string)$attributes->status == "error") {
					$errorCode = $this->getItemValueByName($xml->row,"code");
					if($errorCode > 3000 && $errorCode < 3010 ) {
						return null;
					}
				}
				return $xml;
			}
		}
		return null;
	}

	public function getServerTime() {
		return $this->serverTime;
	}

	public function isAuthorized($reply = "") {
		$ttl = $this->session->get("ttl");
		$token_birth = $this->session->get("token_birth");
		if ("" != $this->getToken()) {
			return true;
		}
		return false;		
	}

	public function getItemValueByName($xmlRow, $name) {
		foreach ($xmlRow->item as $item) {
			if($item->attributes()->name == $name) {
				return (string)$item;
			}
		}
		return false;
	}

	public function login() {
		$this->log->debug("Login started");

		$this->forgetCookie();

		$url = $this->baseUrl . "auth.xml";
		$reply = $this->http->sendGetRequest($url);

		if(false !== $reply) {
			$sessionXml = simplexml_load_string($reply);
		}

		if(false !== $sessionXml) {
			$attributes = $sessionXml->attributes();
			if(null != $attributes->status && (string)$attributes->status == "success") {

				$rand = $this->getItemValueByName($sessionXml->row,"rand");
				$sid  = $this->getItemValueByName($sessionXml->row,"sid");
				$md5Passwd = md5($rand ."".md5($this->password));
				$url = $this->baseUrl . "auth.xml?device=".$this->getDevice()."&version=".$this->getVersion()."&sid=". $sid ."&login=".$this->username."&passwd=".$md5Passwd;
				$reply = $this->http->sendGetRequest($url);

				if(false !== $reply) {
					$loginXml = simplexml_load_string($reply);

					if(false !== $loginXml) {
						$this->serverTime = (string)($loginXml->attributes()->timeserver);

						$this->token = $this->getItemValueByName($loginXml->row,"token");
						$this->portal = $this->getItemValueByName($loginXml->row,"portal");
						$this->ttl = $this->getItemValueByName($loginXml->row,"ttl");

						$this->session->set("token",  $this->token);
						$this->session->set("portal", $this->portal);
						$this->session->set("ttl", 	  $this->ttl);
						$this->session->set("token_birth", time());
						$this->log->debug("Login successfull, token returned: " . $this->token);
						return true;
					}
				}
			}
		} else {
			$this->log->error("Login failed, invalid xml:". $reply);
		}
		return flase;
	}

	public function logout() {
		$this->forgetCookie();
	}

	public function forgetCookie() {
		$this->session->set($this->cookieName,"");
		$this->http->cookie = "";
	}

	public function getToken() {
		if(isset($this->session)) {
			$ttl = $this->session->get("ttl");
			$token_birth = $this->session->get("token_birth");		
			if($token_birth + $ttl*1000 > time()) {
				return $this->session->get("token");
			} else {
				$this->login();
				return $this->session->get("token");
			}
		}
		return "";
	}
}
?>