<?php
require_once 'interfaces/provider.inc';
require_once 'tools/logger.inc';

class TimeSyncProvider extends Provider {

	protected static $MAX_DIFFERENCE = 60;

	function TimeSyncProvider() {
        $this->log = Logger::getLogger(get_class());
	}

	function getObject($objectId, $params) {

		if($this->getSubprovider() == null ) {
			// TODO: probably ntp update ?
			return null;
		}
		$mediaObj = $this->getSubprovider()->getObject($objectId, $params);
		try {
			if($this->config->get("autoset_time",null,true) == true) {

				$serverTime = $mediaObj->getParam("serverTime");
				
				if(isset($serverTime) && $serverTime > 0){
	
					$this->log->debug("Server time: ".$serverTime." Local time: " . time() . " Difference: " . ($serverTime - time()));
					
					if(abs($serverTime - time()) >= TimeSyncProvider::$MAX_DIFFERENCE) {
						// only for Linux/STB
						if (strtoupper(substr(php_uname("s"), 0, 5)) === 'LINUX') { 
							date_default_timezone_set('UTC');
							$cmd =  "date " . date("mdHiY.s",$serverTime); 
							$this->log->debug("Adjust time: ".$cmd);
							#update system Time
							exec($cmd, $res);
							$this->log->debug("Result: ".implode("\r\n", $res ));
							#update hw clock
							exec("hwclock -w");				
							date_default_timezone_set($this->config->get("timezone", null, 'Europe/Berlin'));
						}
					}
				}
			}
		} catch (Exception $e) {
			$this->log->error($e->getMessage());
		}
		return $mediaObj;
	}
}