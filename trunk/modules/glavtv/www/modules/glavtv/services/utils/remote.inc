<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/exceptions.inc';
require_once 'tools/http.inc';
require_once 'tools/sysinfo.inc';
require_once 'tools/parser.inc';

class RemoteFunctions {

    const DEFAULT_USER = 'anonymous';

    protected $cfg;
    protected $http;
    protected $updaterUrl;

    public function __construct(&$cfg) {
        $this->cfg = $cfg;
        $this->http = new HttpTools();
        $this->updaterUrl = $cfg->get('updaterUrl',
            'http://glavnee.net/management/up.php');
    }

    public function welcome($username, $password) {
        $xml = $this->send('welcome', $username, array('pass' => $password));
        $action = ParserTools::parseXmlParam($xml, 'action');
        if (! isset($action)) {
            throw new ParseException('No action');
        }
        $params = ParserTools::parseTokens($xml, '<param name="', '"', '>', '<');
        foreach ($params as $name => &$value) {
            $value = htmlspecialchars_decode($value);
        }
        $_SESSION['fmRate'] = ParserTools::parseXmlParam($xml, 'fmrate', 50);
        $_SESSION['fmTime'] = ParserTools::parseXmlParam($xml, 'fmtime', 10);
        return array($action, $params);
    }

    public function version($username, $password) {
        $xml = $this->send('version', $username, array('pass' => $password));
        $version = ParserTools::parseXmlParam($xml, 'version');
        $linkId  = ParserTools::parseXmlParam($xml, 'linkId');
        if (! isset($version) || ! isset($linkId)) {
            throw new ParseException();
        }
        $downloadUrl  = $this->updaterUrl . '?req=download';
        $downloadUrl .= '&user='   . urlencode($username);
        $downloadUrl .= '&linkId=' . urlencode($linkId);

        return array($version, $linkId, $downloadUrl);
    }

    public function setinfo($username, $linkId) {
        $this->send('setinfo', $username, array('linkId' => $linkId));
        return true;
    }

    public function stats($username) {
        global $srv, $req;
        $xml = $this->send('stats', $username, array('info' => "$srv:$req"));
        return true;
    }

    public function makeFun($username) {
        global $srv, $req, $lang;
        $xml  = $this->send('makefun', $username,
            array('info' => "$srv:$req", 'lang' => $lang->getLang()));
        $caption = ParserTools::parseXmlParam($xml, 'caption');
        $text    = ParserTools::parseXmlParam($xml, 'text');
        $image   = ParserTools::parseXmlParam($xml, 'image');
        return array($caption, $text, $image);
    }

    protected function send($request, $username, $extraParams) {
        if ('' == $username) {
            $username = self::DEFAULT_USER;
        }

        global $version;
        $url  = $this->updaterUrl . "?req=$request&";
        $url .= http_build_query($extraParams + array(
            'user' => $username, 'version' => $version,
            'hardware' => SysInfo::getPlatformExt(),
            'hwid' => SysInfo::getMac()));

        #print "Hi there! $url";
        $xml = $this->http->sendPostRequest($url);
        $error = ParserTools::parseParam($xml, '<error>', '</error>');
        if (null != $error) {
            if (0 === strpos($error, 'AUTH:')) {
                throw new AuthException(substr($error, 5));
            } else {
                throw new GtvRuntimeException($error);
            }
        }
        return $xml;
    }
}
?>