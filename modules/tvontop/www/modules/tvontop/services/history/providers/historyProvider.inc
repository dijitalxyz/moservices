<?php
require_once 'interfaces/provider.inc';

class HistoryProvider extends Provider {

	public static $MAX_ENTRIES = 25;
	public static $SEPARATOR = "\r\n";

	public static $OBJ_HISTORY_ENTRIES = "HISTORY_ENTRIES";
    public static $OBJ_HISTORY_WINDOW  = "HISTORY_WINDOW";
    public static $OBJ_SET_HISTORY_ENTRIES = "SET_HISTORY_ENTRIES";
    
    
	public function getObject($objectId, $params) {
        $objectType = $this->getParam($params, "type", self::$OBJ_HISTORY_ENTRIES);
        if ($objectType == self::$OBJ_HISTORY_ENTRIES) {
            $obj = new MediaObject($objectId, self::$OBJ_HISTORY_ENTRIES, "History slice");
            $obj->setParam("history", $this->getEntries($objectId, $params));
            return $obj;
        } else if ($objectType == self::$OBJ_HISTORY_WINDOW) {
            $obj = new MediaObject($objectId, self::$OBJ_HISTORY_WINDOW, "History slice");
            $obj->setParam("history", $this->getEntries($objectId, $params));
            return $obj;
        } else if ($objectType == self::$OBJ_HISTORY_WINDOW) {
            $this->setEntries($objectId, $params);
        	return null;
        }
        return null;
	}

	protected function getFilename($params) {
		$filename = "history.txt";
		$path = $this->config->get("cfg_resources_home", "Runtime")."keyboard/";
		// $path = "d:/Development/xtreamer/TVonTop/views/smarty/templates/rss-black-fury/keyboard/";
	    return $path . $filename;
	}

	protected function getEntries($objectId, $params) {
        $filename = $this->getFilename($params);
        $lines = @file($filename);
        if (false === $lines) {
        	return array();
        }
        foreach ($lines as &$line) {
        	$line = rtrim($line);
        }
        $limit = $this->getParam($params, "limit", 0);
        return $limit > 0 ? array_slice($lines, 0, $limit) : $lines;
    }

    protected function setEntries($objectId, $params) {
        $str = $this->getParam($params, "str");
        if (! isset($str) || "" == $str) {
        	return;
        }
    	$params["limit"] = self::$MAX_ENTRIES;
    	$lines = $this->getEntries($objectId, $params);
        array_splice($lines, 0, 0, $str);
        $lines = array_unique($lines);
        $filename = $this->getFilename($params);
        file_put_contents($filename, implode(self::$SEPARATOR, $lines));
    }

	public function setObject($objectId, $params) {
        $objectType = $this->getParam($params, "type", self::$OBJ_HISTORY_ENTRIES);
        if ($objectType == self::$OBJ_HISTORY_ENTRIES) {
            $this->setEntries($objectId, $params);
        }
	}
}
?>