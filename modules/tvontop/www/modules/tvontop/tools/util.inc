<?php
require_once 'tools/config.inc';

class Utils {
	protected $config; 

	function init(Configuration &$cfg) {
        $this->config = $cfg;
	}
	public function getTimezoneOffset() {
		$dateTimeZoneUTC = new DateTimeZone("UTC");
		$dateTimeZoneLocal = new DateTimeZone($this->config->get("timezone", null, 'Europe/Berlin'));
		$dateTimeLocal = new DateTime("now", $dateTimeZoneLocal);
		$timeOffset = $dateTimeZoneLocal->getOffset($dateTimeLocal);
		return $timeOffset; 		
	}

	public function getImageSizes($path) {
		//list($width, $height, $type, $attr)= getimagesize($this->baseUrl.(string)$serie->pimg);
		return getimagesize($path);
	}

	public static function ucfirstUTF8($str) {
        $line = iconv("UTF-8", "Windows-1251", $str); // convert to windows-1251
        $line = ucfirst($line);
        $line = iconv("Windows-1251", "UTF-8", $line); // convert back to utf-8
        return $line;
    }
}
?>