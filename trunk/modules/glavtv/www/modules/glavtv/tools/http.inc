<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'logger.inc';
require_once 'interfaces/exceptions.inc';

class HttpTools {
    const DEFAULT_HEADER = "User-Agent: Mozilla/5.0 GlavTV plugin Macintosh\r\nConnection: Close\r\n";

    protected $log;
    protected $defaultHeaders;
    protected $cookie;

    public function __construct($headers = self::DEFAULT_HEADER) {
        $this->log = Logger::getLogger(get_class());
        $this->defaultHeaders = $headers;
        $this->cookie = '';
    }

    public function getCookie() {
        return $this->cookie;
    }
    public function setCookie($cookie) {
        $this->cookie = null == $cookie || '' == $cookie ? $cookie :
            'Cookie: ' . (is_array($cookie) ?
                implode("\r\nCookie: ", $cookie) :
                str_replace('Cookie: ', '', $cookie)) . "\r\n";
    }

    public function sendGetRequest($url, $headerExtra = '', $returnHeaders = false) {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        $url  = $this->resolveNames($url, $host);

        $header  = "Host: $host\r\n";
        $header .= $this->defaultHeaders;
        $header .= $headerExtra;
        if (isset($this->cookie) && '' != $this->cookie) {
            $header .= $this->cookie;
        }

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'header'  => $header)));


        $this->log->debug("GET ===>$url===>");
        $this->log->debug("GET ===>$header===>");
        $response = @file_get_contents($url, false, $context);
        if (false === $response || '' === $response) {
            if (! isset($http_response_header)) {
                throw new NetworkException($host);
            }
            $response = '<error>' . $this->getHttpCode($http_response_header) . '</error>';
            $this->log->debug("GET <===" . print_r($http_response_header, true) . "<===\n");
        }
        $this->log->debug("GET <===$response<===\n");
        return ! $returnHeaders ? $response :
            array($http_response_header, $response);
    }

    public function sendPostRequest($url, $headerExtra = '', $returnHeaders = false) {
        $parsedUrl = parse_url($url);
        $host   = $parsedUrl['host'];
        $params = isset($parsedUrl['query']) ? $parsedUrl['query'] : null;
        $url    = $this->resolveNames($url, $host);

        $header  = "Host: $host\r\n";
        $header .= $this->defaultHeaders;
        $header .= $headerExtra;
        if (isset($this->cookie) && '' != $this->cookie) {
            $header .= $this->cookie;
        }

        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $context = stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'method'  => 'POST',
                'timeout' => 50,
                'content' => $params,
                'header'  => $header)));

        $url = str_replace("?$params", '', $url);
        $this->log->debug("POST ===>$url, PARAMS: $params===>");
        $this->log->debug("POST ===>$header===>");
        $response = @file_get_contents($url, false, $context);
        if (false === $response || '' === $response) {
            if (! isset($http_response_header)) {
                throw new NetworkException($host);
            }
            $response = '<error>' . $this->getHttpCode($http_response_header) . '</error>';
            $this->log->debug("POST <===" . print_r($http_response_header, true) . "<===\n");
        }
        $this->log->debug("POST <===$response===\n");
        return ! $returnHeaders ? $response :
            array($http_response_header, $response);
    }

    public function sendMixedPostRequest($url, $params, $headerExtra = '', $returnHeaders = false) {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];

        $header  = "Host: $host\r\n";
        $header .= $this->defaultHeaders;
        $header .= $headerExtra;
        if (isset($this->cookie) && '' != $this->cookie) {
            $header .= $this->cookie;
        }

        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $context = stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'method'  => 'POST',
                'timeout' => 50,
                'content' => $params,
                'header'  => $header)));

        $this->log->debug("POST ===>$url, PARAMS: $params===>");
        $this->log->debug("POST ===>$header===>");
        $response = @file_get_contents($url, false, $context);
        if (false === $response || '' === $response) {
            $response = '<error>' . $this->getHttpCode($http_response_header) . '</error>';
            $this->log->debug("POST <===" . print_r($http_response_header, true) . "<===\n");
        }
        $this->log->debug("POST <===$response===\n");
        return ! $returnHeaders ? $response :
            array($http_response_header, $response);
    }

    protected function getHttpCode($headers, $takeLast = true) {
        if ($takeLast) {
            $headers = array_reverse($headers);
        }
        foreach ($headers as $header) {
            if (0 === strpos($header, 'HTTP/')) {
                return $header;
            }
        }
        return null;
    }

    # temporary solution for vk.com problem
    protected function resolveNames($url, $host) {
        if (false !== strpos($host, 'vk.com')) {
            $ip = @gethostbyname($host);
            if (empty($ip) || $ip == $host) {
                $url = str_replace('login.vk.com', '93.186.224.244', $url);
                $url = str_replace('vk.com', '87.240.131.97', $url);
            }
        }
        return $url;
    }

    public function getPageCookies($headers) {
        $cookies = array();
        foreach ($headers as $header) {
            if (0 === strpos($header, 'Set-Cookie: ')) {
                $cookie = substr($header, strlen('Set-Cookie: '));
                if (false !== strpos($cookie, ';')) {
                    $cookie = substr($cookie, 0, strpos($cookie, ';'));
                }
                list($name, $value) = explode('=', $cookie);
                if (isset($cookies[$name])) {
                    for ($i = 1; isset($cookies[$name . $i]); $i++);
                    $name .= $i;
                }
                $cookies[$name] = $value;
            }
        }
        return $cookies;
    }

    public function formatHeadersArray($headers) {
        $newHeaders = array();
        foreach ($headers as $header) {
            if (false === strpos($header, ': ')) {
                $newHeaders[] = $header;
            } else {
                list($key, $value) = explode(': ', $header, 2);
                $newHeaders[$key] = $value;
            }
        }
        return $newHeaders;
    }

    public static function checkUrl($url) {
        $headers = get_headers($url);
        return ! empty($headers) && false !== strpos($headers[0], "200 OK");
    }
}
?>
