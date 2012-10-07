<?php
require_once 'interfaces/provider.inc';

class PerfLogProvider extends Provider {
	protected $filepath;
	protected static $lastMem;
	protected static $lastSeconds;
	protected static $lastMilliSeconds;
	
  public function init(Configuration &$cfg) {
        parent::init($cfg);
        $this->filepath = "perfomance.log";
        $this->writeToLog("\r\n");
    }

   protected function writeMessage($msg){
        
        // append date/time to message
        list($totalSeconds, $extraMilliseconds) = $this->timeAndMilliseconds();
        
        $usedMem = memory_get_usage(); 
        
		$secGone = $totalSeconds - self::$lastSeconds;
		if($extraMilliseconds > self::$lastMilliSeconds) {
			$msGone = $extraMilliseconds -  self::$lastMilliSeconds;
		} 
		else {
			$secGone--;
			$msGone = self::$lastMilliSeconds - $extraMilliseconds;
		}
		$memGone = $usedMem - self::$lastMem; 
        $str = "[" . date("Y-m-d H:i:s",$totalSeconds)." ". sprintf("%03d",$extraMilliseconds) . "] [Delta: $secGone.$msGone] [Used Mem $usedMem, Delta: $memGone] $msg\n";

        self::$lastMem = $usedMem;
		self::$lastSeconds = $totalSeconds;
		self::$lastMilliSeconds = $extraMilliseconds;
		$this->writeToLog($str);
    }
    
    function writeToLog($str) {
    	$fd = fopen($this->filepath, "a");
        if (false === $fd) {
            return false;
        }
        // write string
        fwrite($fd, $str);
        // close file
        fclose($fd);
    }

    function timeAndMilliseconds()	{
	    $m = explode(' ',microtime());
	    return array($m[1], (int)round($m[0]*1000,3));
	}

    protected function perfLog($msg) {
    	$this->writeMessage($msg);
    }

	public function getObject($objectId, $params) {
		$this->perfLog(get_class($this->getSubprovider())."->getObject($objectId, $params) start");
		$newObj = $this->getSubprovider()->getObject($objectId, $params);
		$this->perfLog(get_class($this->getSubprovider())."->getObject($objectId, $params) end");
		return $newObj; 
	}	
}
?>