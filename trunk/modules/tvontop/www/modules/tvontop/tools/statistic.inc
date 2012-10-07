<?php
require_once 'tools/sysinfo.inc';
require_once 'tools/config.inc';
require_once 'tools/http.inc';


class Statistic {
	protected $config;
	protected $http;
	protected $sysInfo;
	protected $baseUrl;

	public function init(Configuration $cfg) {
        $this->config = $cfg;
		$this->http = new HttpTools(
            "User-Agent: Mozilla/5.0\r\n" .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Connection: Close\r\n");
		$this->baseUrl = 'http://spreadsheets.google.com/spreadsheet/formResponse?formkey=dFBoWUcySDdPelFheGtCWkNxUGdzS0E6MQ&theme=0AX42CRMsmRFbUy03NTAzM2Q4My03ODU1LTQ2NzItODI2YS1kZmU5YzdiMzZjOGQ&ifq';
    }

	protected function sendStatistic($params){
		$url = $this->baseUrl;
		$postParams = '&backupCache='; 
		foreach ($params as $id => $param) {
			$postParams  = $postParams . '&entry.'.$id.'.single='.urlencode($param);
		}
		$postParams = $postParams . '&pageNumber=0';
		$postParams = $postParams . '&submit=Submit';
		$this->http->sendPostRequest($url . $postParams);
    }

    public function reportUsage($params) {
    	if(isset($params['nostat'])) {
    		return false;
    	}
    	if(isset($this->config) && $this->config->get("send_stat_data",null,"false") != "true") {
    		return false;
    	}
    	$pl = isset($params['pl'])?$params['pl']:""; 
    	$id = isset($params['id'])?$params['id']:"";
    	$type = isset($params['type'])?$params['type']:"";

    	$statParams = array($pl,
					    	$id,
					    	$type,
					    	SysInfo::getPlatformExt(),
					    	SysInfo::getMac(),
					    	SysInfo::getVersion(),
					    	SysInfo::getUserAgent()
					    	);
		try {
			return $this->sendStatistic($statParams);
		} catch(Exception $e) {
			return false;
		}
    }
}
?>