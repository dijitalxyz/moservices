<?php
require_once 'tools/config.inc';

class Lang {
	protected $config;
	function Lang() {
		
	}
	
	function init(Configuration $cfg) {
		$this->config = $cfg;
	}
	function getLocale($locale) {
		$fileName = "lang/".$locale.".inc";
		$className = "Locale_".$locale;
		if(is_readable($fileName)) {
			require_once $fileName;
			return new $className();
		}
	}
	
}
?>