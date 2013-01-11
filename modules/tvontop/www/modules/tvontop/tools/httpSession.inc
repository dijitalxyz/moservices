<?php
require_once 'interfaces/session.inc';

class HttpSession extends Session{
	
	public function exists($key) {
		//if exists and ttl is valid or not set
		if(isset($_SESSION[$key]) && 
		  (!isset($_SESSION[$key . "_TTL"]) || ($_SESSION[$key . "_TTL"] > time()))) {
			return true;	
		} else {
			$this->delete($key);
		}
		return false;
	}

	public function get($key) {
		if($this->exists($key)) {
			return $_SESSION[$key];	
		}
		return null;
	}

	public function set($key, $value, $ttl=NULL) {
		$_SESSION[$key] = $value;
		if(isset($ttl)) {
			$_SESSION[$key . "_TTL"] = time()  + $ttl;
		} else {
			unset($_SESSION[$key . "_TTL"]);
		}
	}

	public function delete($key) {
		unset($_SESSION[$key]);
		unset($_SESSION[$key . "_TTL"]);
	}

	public function clear() {
		;//not implemented
	}
}
?>