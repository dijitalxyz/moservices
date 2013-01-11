<?php
#############################################################################
# Author: stalker 2011                                                      #
# Author: consros 2011                                                      #
#############################################################################

require_once "logger.inc";

class HttpTools {
    var $log;
    var $defaultHeaders;
    var $cookie;

    function HttpTools($headers = "User-Agent: Mozilla/5.0\r\n") {
		$this->log = Logger::getLogger(get_class());
        $this->defaultHeaders = $headers;
        $this->cookie = "";
    }

    function sendGetRequest($url, $headerExtra = "") {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];

        $header  = "Host: $host\r\n";
        $header .= $this->defaultHeaders;
        $header .= $headerExtra;
        
/*        
$header  = "Accept: text/html,application/xhtml+xml,application/xml\r\n";
$header .= "Accept-Charset: utf-8;\r\n";
//$header .= "Accept-Encoding: gzip, deflate\r\n";

$header .= "Cookie: uid=21533; username=tvontop; password=f91f26d787543ab6b143fd500d68c959; group_id=1; verification=56039253e2b0ee8733e2a07808608e8c; PHPSESSID=oc5dqu11rsgesdttff98b4ipp3\r\n";

$header .= "Accept-Language: en-us,en;q=0.5\r\n";
$header .= "Connection: keep-alive\r\n";
$header .= "Host: watch.is\r\n";
$header .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0.1) Gecko/20100101 Firefox/8.0.1\r\n";
        */
        if (isset($this->cookie) && "" != $this->cookie) {
            $header .= $this->cookie . "\r\n";
        }

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'header'  => $header)));

        $this->log->debug("GET ===>$url===>");
        $this->log->debug("GET ===>$header===>");
        $response = file_get_contents($url, false, $context);
//        $this->log->debug("GET <===$response<===\n");
        return $response;
    }

    function sendSimpleGetRequest($url, $headerExtra = "") {
        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
        		'timeout' => 500,
                'header'  => $header)));
        $response = file_get_contents($url, false, null);

        return $response;
    }
    
    function sendPostRequest($url, $headerExtra = "") {
        $parsedUrl = parse_url($url);
        $host   = $parsedUrl['host'];
        $params = isset($parsedUrl['query']) ? $parsedUrl['query'] : null;

        $header  = "Host: $host\r\n";
        $header .= $this->defaultHeaders;
        $header .= $headerExtra;
        if (isset($this->cookie) && "" != $this->cookie) {
            $header .= $this->cookie . "\r\n";
        }

        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'timeout' => 50,
                'content' => $params,
                'header'  => $header)));

        $url = str_replace("?$params", "", $url);
        $this->log->debug("POST ===>$url, PARAMS: $params===>");
        $response = file_get_contents($url, false, $context);
        $this->log->debug("POST <===$response===\n");        
        return $response;
    }
    
    function getPageCookies($url) {
    	$headers = get_headers($url, 1);
	    if (empty($headers) || !isset($headers["Set-Cookie"])) {
	        return null;
	    }
	    $cookieKvs = explode(";", $headers["Set-Cookie"]);
	    $cookies = array();
	    foreach ($cookieKvs as $cookieKv) {
	    	$kv = explode("=", $cookieKv);
	    	$cookies[$kv[0]] = $kv[1];
	    }
	    return $cookies; 
    }
}
?>
