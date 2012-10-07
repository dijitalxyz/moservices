<?php
require_once 'interfaces/mediaObjects.inc';

abstract class View {
	protected $config;
	protected $locale;
	 
	function redirect($url) {
		header("Location: $url");
	}
	
	function init(Configuration &$cfg) {
		$this->config = $cfg;
		$this->config->addFile($this->getIniFilename());
	}

	function getIniFilename() {
		return "";
	}

	function setLocale(Localization $locale) {
    	$this->locale = $locale;
    }
    
	abstract function drawObject(MediaObject $mediaObj,$mode);
}
?>