<?php
require_once 'interfaces/provider.inc';
require_once 'tools/logger.inc';
require_once 'tools/httpSession.inc';

class HistoryProvider extends Provider {
	protected $session = null;
	function HistoryProvider() {
        $this->log = Logger::getLogger(get_class());
        $this->session = new HttpSession(); 
	}

	function getObject($objectId, $params) {

		if($this->getSubprovider() == null ) {
			$this->log->error(get_class() + " can not be the last provider in service.");
			return null;
		}
		$key = $params["pl"];
		$value = "";
		$this->session->set($key, $value, NULL);
		$mediaObj = $this->getSubprovider()->getObject($objectId, $params);
		try {

		} catch (Exception $e) {
			$this->log->error($e->getMessage());
		}
		return $mediaObj;
	}
}
?>