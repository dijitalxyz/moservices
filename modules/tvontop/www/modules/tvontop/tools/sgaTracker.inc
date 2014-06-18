<?php
class SgaTracker {

	const VERSION = '5.2.5'; 

	protected $endPointHost = 'www.google-analytics.com';
	protected $endPointPath = '/__utm.gif';
	//account	
	protected $accountId = null;
	protected $domain = null;
	//visitor
	protected $visitor = array();
	//session
	protected $sessionId;
	protected $trackCount;
	protected $startTime;
	//page
	protected $path;
	protected $title;
	//custom variables
	protected $customVariables = array();

	const CV_SCOPE_VISITOR = 1;
	const CV_SCOPE_SESSION = 2;
	const CV_SCOPE_PAGE    = 3;
	
	const TYPE_CUSTOMVARIABLE = 'var';
	const X10_CUSTOMVAR_NAME_PROJECT_ID  = 8;
	const X10_CUSTOMVAR_VALUE_PROJECT_ID = 9;
	const X10_CUSTOMVAR_SCOPE_PROJECT_ID = 11;	
	
	public function __construct($gaId, $gaDomain) {
		$this->accountId = $gaId;
		$this->domain = $gaDomain;
	}

	function generate32bitRandom() {
		return round((rand() / getrandmax()) * 0x7fffffff);
	}

	public static function encodeUriComponent($value) {
		return self::convertToUriComponentEncoding(rawurlencode($value));
	}

	public static function convertToUriComponentEncoding($encodedValue) {
		return str_replace(array('%21', '%2A', '%27', '%28', '%29'), array('!', '*', "'", '(', ')'), $encodedValue);
	}

	public static function generateHash($string) {
		$string = (string)$string;
		$hash = 1;

		if($string !== null && $string !== '') {
			$hash = 0;

			$length = strlen($string);
			for($pos = $length - 1; $pos >= 0; $pos--) {
				$current   = ord($string[$pos]);
				$hash      = (($hash << 6) & 0xfffffff) + $current + ($current << 14);
				$leftMost7 = $hash & 0xfe00000;
				if($leftMost7 != 0) {
					$hash ^= $leftMost7 >> 21;
				}
			}
		}
		return $hash;
	}

	public function setVisitor($remoteAddr, $userAgent, $resolution,$locale=null) {
		$this->visitor['REMOTE_ADDR'] 		= $remoteAddr;
		$this->visitor['HTTP_USER_AGENT'] 	= $userAgent;
		$this->visitor['SCREEN_RESOLUTION'] = $resolution; 
		$this->visitor['LOCALE'] 			= $locale;
	}

	public function initSession($sessionId=null, $trackCount=null, $startTime=null) {
		if(isset($sessionId)) {
			$this->sessionId = $sessionId;
		} else {
			$this->sessionId = $this->generate32bitRandom();
		}
		if(isset($trackCount)) {
			$this->trackCount = $trackCount;
		} else {
			$this->trackCount = 0;	
		}
		if(isset($startTime)) {
			$this->startTime = $startTime;
		} else {
			$this->startTime = new DateTime();	
		}
	}

	public function setCustomVariable($index, $name, $value, $scope) {
		$this->customVariables[$index] = array();
		$this->customVariables[$index]['name'] = $name;
		$this->customVariables[$index]['value'] = $value;
		$this->customVariables[$index]['scope'] = $scope;
	}

	public function setPageInfo($path, $title) {
		$this->path = $path;
		$this->title = $title;
	}

	function buildParameters() {
		$p = array();
		//request
		$p['utmwv'] = self::VERSION;
		$p['utmac'] = $this->accountId;
		$p['utmhn'] = $this->domain;

		$p['utmt'] = null; //page type
		$p['utmn'] = $this->generate32bitRandom();
		$p['utmhid'] = $this->sessionId;
		$p['utms']   = $this->trackCount;

		$p['utmul'] = strtolower(str_replace('_', '-', $this->visitor['LOCALE']));
		$p['utmsr'] = $this->visitor['SCREEN_RESOLUTION'];

		$customVars = $this->customVariables;
		$p['utme'] = '';
		if($customVars) {
			$x10 = new X10();
			$x10->clearKey(self::X10_CUSTOMVAR_NAME_PROJECT_ID);
			$x10->clearKey(self::X10_CUSTOMVAR_VALUE_PROJECT_ID);
			$x10->clearKey(self::X10_CUSTOMVAR_SCOPE_PROJECT_ID);

			foreach($customVars as $index => $customVar) {
				// Name and value get encoded here,
				$name  = self::encodeUriComponent($customVar['name']);
				$value = self::encodeUriComponent($customVar['value']);
				$x10->setKey(self::X10_CUSTOMVAR_NAME_PROJECT_ID, $index, $name);
				$x10->setKey(self::X10_CUSTOMVAR_VALUE_PROJECT_ID, $index, $value);
				if($customVar['scope'] !== null && $customVar['scope'] != self::CV_SCOPE_PAGE) {
					$x10->setKey(self::X10_CUSTOMVAR_SCOPE_PROJECT_ID, $index, $customVar['scope']);
				}
			}
			$p['utme'] .= $x10->renderUrlString();
		}

		//$p = $this->buildCampaignParameters($p);
		$domainHash = 	$this->generateHash($this->domain);
		$p['__utma']  = $domainHash . '.';
		$visitorId = (($this->generate32bitRandom() ^ $this->generateHash($this->domain)) & 0x7fffffff);
		$p['__utma'] .= $visitorId . '.';
		//use now as visit time
		$now = new DateTime();
		$p['__utma'] .= $now->format('U') . '.';
		$p['__utma'] .= $now->format('U') . '.';
		$p['__utma'] .= $now->format('U') . '.';
		//visit count
		$p['__utma'] .= 1;
		$p['__utmb']  = $domainHash . '.';
		$p['__utmb'] .= $this->trackCount . '.';
		$p['__utmb'] .= 10 . '.';
		$p['__utmb'] .= $this->startTime->format('U');
		$p['__utmc'] = $domainHash;

		$cookies = array();
		$cookies[] = '__utma=' . $p['__utma'] . ';';
		if(isset($p['__utmz'])) {
			$cookies[] = '__utmz=' . $p['__utmz'] . ';';
		}
		if(isset($p['__utmv'])) {
			$cookies[] = '__utmv=' . $p['__utmv'] . ';';
		}

		$p['utmcc'] = implode('+', $cookies);

		//page view request 
		$p['utmp']  = $this->path;
		$p['utmdt'] = $this->title;
		return $p;
	}

	protected function buildHttpRequest() {
		$parameters = $this->buildParameters();

		if(defined('PHP_QUERY_RFC3986')) {
			$queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
		} else {
			$queryString = str_replace('+', '%20', http_build_query($parameters, '', '&'));
		}
		$queryString = self::convertToUriComponentEncoding($queryString);
		$usePost = strlen($queryString) > 2036;
		if(!$usePost) {
			$r = 'GET ' . $this->endPointPath . '?' . $queryString . ' HTTP/1.0' . "\r\n";
		} else {
			$r = 'POST /p' . $this->endPointPath . ' HTTP/1.0' . "\r\n";
		}
		$r .= 'Host: ' . $this->endPointHost  . "\r\n";
		
		if($this->visitor['HTTP_USER_AGENT']) {
			$r .= 'User-Agent: ' . str_replace(array("\n", "\r"), '', $this->visitor['HTTP_USER_AGENT'] ) . "\r\n";
		}
		if($usePost) {
			// Don't ask me why "text/plain", but ga.js says so :)
			$r .= 'Content-Type: text/plain' . "\r\n";
			$r .= 'Content-Length: ' . strlen($queryString) . "\r\n";
		}
		$r .= 'Connection: close' . "\r\n";
		$r .= "\r\n\r\n";
		if($usePost) {
			$r .= $queryString;
		}
		return $r;
	}

	function sendTrackRequest($fireAndForget = FALSE) {

		$request = $this->buildHttpRequest();
		$response = null;

		// Do not actually send the request if endpoint host is set to null
		if($this->endPointHost !== null) {
			$timeout = 1;
			$socket = fsockopen($this->endPointHost, 80, $errno, $errstr, $timeout);
			if(!$socket) 
				return false;
			if($fireAndForget) {
				stream_set_blocking($socket, false);
			}
			$timeoutS  = intval($timeout);
			$timeoutUs = ($timeout - $timeoutS) * 100000;
			stream_set_timeout($socket, $timeoutS, $timeoutUs);
			
			$sentData = 0;
			$toBeSentData = strlen($request);
			while($sentData < $toBeSentData) {
				$sentData += fwrite($socket, $request);
			}
			if(!$fireAndForget) {
				while(!feof($socket)) {
					$response .= fgets($socket, 512);
				}
			}
			fclose($socket);
		}
		return $response;		
	}
}

/**
 * This is nearly a 1:1 PHP port of the gaforflash X10 class code.
 * 
 * @link http://code.google.com/p/gaforflash/source/browse/trunk/src/com/google/analytics/data/X10.as
 */
class X10 {
	
	/**
	 * @var array
	 */
	protected $projectData = array();
	
	
	/**
	 * @var string
	 */
	protected $KEY = 'k';
	
	/**
	 * @var string
	 */
	protected $VALUE = 'v';
	
	/**
	 * @var array
	 */
	protected $SET = array('k', 'v');
	
	/**
	 * Opening delimiter for wrapping a set of values belonging to the same type.
	 * @var string
	 */
	protected $DELIM_BEGIN = '(';
	
	/**
	 * Closing delimiter for wrapping a set of values belonging to the same type.
	 * @var string
	 */
	protected $DELIM_END   = ')';
	
	/**
	 * Delimiter between two consecutive num/value pairs.
	 * @var string
	 */
	protected $DELIM_SET = '*';
	
	/**
	 * Delimiter between a num and its corresponding value.
	 * @var string
	 */
	protected $DELIM_NUM_VALUE = '!';
	
	/**
	 * Mapping of escapable characters to their escaped forms.
	 * 
	 * @var array
	 */
	protected $ESCAPE_CHAR_MAP = array(
		"'" => "'0",
		')' => "'1",
		'*' => "'2",
		'!' => "'3",
	);
	
	/**
	 * @var int
	 */
	protected $MINIMUM = 1;
	
	
	/**
	 * @const int
	 */
	const OBJECT_KEY_NUM  = 1;
	/**
	 * @const int
	 */
	const TYPE_KEY_NUM    = 2;
	/**
	 * @const int
	 */
	const LABEL_KEY_NUM   = 3;
	/**
	 * @const int
	 */
	const VALUE_VALUE_NUM = 1;
	
	
	/**
	 * @param int $projectId
	 * @return bool
	 */
	protected function hasProject($projectId) {
		return isset($this->projectData[$projectId]);
	}
	
	/**
	 * @param int $projectId
	 * @param int $num
	 * @param mixed $value
	 */
	public function setKey($projectId, $num, $value) {
		$this->setInternal($projectId, $this->KEY, $num, $value);
	}
	
	/**
	 * @param int $projectId
	 * @param int $num
	 * @return mixed
	 */
	public function getKey($projectId, $num) {
		return $this->getInternal($projectId, $this->KEY, $num);
	}
	
	/**
	 * @param int $projectId
	 */
	public function clearKey($projectId) {
		$this->clearInternal($projectId, $this->KEY);
	}
	
	/**
	 * @param int $projectId
	 * @param int $num
	 * @param mixed $value
	 */
	public function setValue($projectId, $num, $value) {
		$this->setInternal($projectId, $this->VALUE, $num, $value);
	}
	
	/**
	 * @param int $projectId
	 * @param int $num
	 * @return mixed
	 */
	public function getValue($projectId, $num) {
		return $this->getInternal($projectId, $this->VALUE, $num);
	}
	
	/**
	 * @param int $projectId
	 */
	public function clearValue($projectId) {
		$this->clearInternal($projectId, $this->VALUE);
	}
	
	/**
	 * Shared internal implementation for setting an X10 data type.
	 * 
	 * @param int $projectId
	 * @param string $type
	 * @param int $num
	 * @param mixed $value
	 */
	protected function setInternal($projectId, $type, $num, $value) {
		if(!isset($this->projectData[$projectId])) {
			$this->projectData[$projectId] = array();
		}
		if(!isset($this->projectData[$projectId][$type])) {
			$this->projectData[$projectId][$type] = array();
		}
		
		$this->projectData[$projectId][$type][$num] = $value;
	}
	
	/**
	 * Shared internal implementation for getting an X10 data type.
	 * 
	 * @param int $projectId
	 * @param string $type
	 * @param int $num
	 * @return mixed
	 */
	protected function getInternal($projectId, $type, $num) {
		if(isset($this->projectData[$projectId][$type][$num])) {
			return $this->projectData[$projectId][$type][$num];
		} else {
			return null;
		}
	}
	
	/**
	 * Shared internal implementation for clearing all X10 data of a type from a
	 * certain project.
	 * 
	 * @param int $projectId
	 * @param string $type
	 */
	protected function clearInternal($projectId, $type) {
		if(isset($this->projectData[$projectId]) && isset($this->projectData[$projectId][$type])) {
			unset($this->projectData[$projectId][$type]);
		}
	}
	
	/**
	 * Escape X10 string values to remove ambiguity for special characters.
	 *
	 * @see X10::$escapeCharMap
	 * @param string $value
	 * @return string
	 */
	protected function escapeExtensibleValue($value) {
		$result = '';
		
		$value = (string)$value;
		$length = strlen($value);
		for($i = 0; $i < $length; $i++) {
			$char = $value[$i];
			
			if(isset($this->ESCAPE_CHAR_MAP[$char])) {
				$result .= $this->ESCAPE_CHAR_MAP[$char];
			} else {
				$result .= $char;
			}
		}
		
		return $result;
	}
	
	/**
	 * Given a data array for a certain type, render its string encoding.
	 * 
	 * @param array $data
	 * @return string
	 */
	protected function renderDataType(array $data) {
		$result = array();
		
		$lastI = 0;
		ksort($data, SORT_NUMERIC);
		foreach($data as $i => $entry) {
			if(isset($entry)) {
				$str = '';
				
				// Check if we need to append the number. If the last number was
				// outputted, or if this is the assumed minimum, then we don't.
				if($i != $this->MINIMUM && $i - 1 != $lastI) {
					$str .= $i;
					$str .= $this->DELIM_NUM_VALUE;
				}
	
				$str .= $this->escapeExtensibleValue($entry);
				$result[] = $str;
			}
			
			$lastI = $i;
		}
		
		return $this->DELIM_BEGIN . implode($this->DELIM_SET, $result) . $this->DELIM_END;
	}
	
	/**
	 * Given a project array, render its string encoding.
	 * 
	 * @param array $project
	 * @return string
	 */
	protected function renderProject(array $project) {
		$result = '';
	
		// Do we need to output the type string? As an optimization we do not
		// output the type string if it's the first type, or if the previous
		// type was present.
		$needTypeQualifier = false;
		
		$length = count($this->SET);
		for($i = 0; $i < $length; $i++) {
			if(isset($project[$this->SET[$i]])) {
				$data = $project[$this->SET[$i]];
				
				if($needTypeQualifier) {
					$result .= $this->SET[$i];
				}
				$result .= $this->renderDataType($data);
				$needTypeQualifier = false;
			} else {
				$needTypeQualifier = true;
			}
		}
		
		return $result;
	}
	
	/**
	 * Generates the URL parameter string for the current internal extensible data state.
	 * 
	 * @return string
	 */
	public function renderUrlString() {
		$result = '';
		
		foreach($this->projectData as $projectId => $project) {
			$result .= $projectId . $this->renderProject($project);
		}
		
		return $result;
	}
}
?>
