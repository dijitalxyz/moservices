<?php
#############################################################################
# Skeleton for all connection based services.                               #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/exceptions.inc';
require_once 'tools/http.inc';
require_once 'tools/logger.inc';
require_once 'tools/parser.inc';

abstract class Connection {
    protected $baseUrl;
    protected $http;
    protected $log;
    protected $cookieVar;
    protected $username;
    protected $password;

    public function __construct($baseUrl) {
        $this->baseUrl   = $baseUrl;
        $this->cookieVar = get_class($this) . 'Cookie';
        $this->log       = Logger::getLogger(get_class($this));
        $this->http      = new HttpTools();
        $this->http->setCookie(isset($_SESSION[$this->cookieVar]) ?
            $_SESSION[$this->cookieVar] : '');
    }

    public abstract function getLoginCookie();

    public function setAuthData($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getData($url, $name, $useBaseUrl = true) {
        $this->log->debug("Getting $name");
        if ($useBaseUrl) {
            $url = $this->baseUrl . $url;
        }
        $reply = $this->http->sendGetRequest($url);
        if (! $this->isAuthorized($reply)) {
            $this->login();
            if (! $this->isAuthorized()) {
                throw new AuthException();
            }
            $this->log->debug("Second try to get $name");
            $reply = $this->http->sendGetRequest($url);
            if (! $this->isAuthorized($reply)) {
                $this->checkForWrongResponse($reply);
                $error = ParserTools::parseParam($reply, '<error>', '</error>', $reply);
                $error = str_replace('<', '&lt;', $error);
                $error = ParserTools::removeHtmlTags($error);
                throw new BadRequestException($error);
            }
        }
        return $reply;
    }

    public function isAuthorized($reply = '') {
        if (false === $reply) {
            $reply = '<error>Unknown error</error>'; # No connection
        }
        $error = ParserTools::parseParam($reply, '<error>', '</error>', '');

        $cookie = $this->http->getCookie();
        $ok = '' == $error && null != $cookie && '' != $cookie &&
            false === strpos($cookie, 'deleted');
        if (! $ok) {
            $this->log->info("Authorization missed or lost: $error");
        }
        return $ok;
    }

    public function checkForWrongResponse($reply) {
        if (false !== stripos($reply, '302 Found') ||
            false !== stripos($reply, '404 Not Found')) {
            throw new BadRequestException();
        } else if (false !== stripos($reply, '401 Unauthorized') ||
            false !== stripos($reply, '403 Forbidden')) {
            // Exception here breaks everything
            // Should be investigated
            // throw new AuthException();
        }
    }

    public function login() {
        $this->log->debug('Login started');
        $this->forgetCookie();

        $cookie = $this->getLoginCookie();
        $this->log->debug('Login returned: ' . $cookie);
        $this->http->setCookie($cookie);
        $_SESSION[$this->cookieVar] = $cookie;
        return true;
    }

    public function forgetCookie() {
        $_SESSION[$this->cookieVar] = '';
        $this->http->setCookie('');
    }
}
?>
