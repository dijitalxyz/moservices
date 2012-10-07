<?php
#############################################################################
# Author: consros    2012                                                   #
#############################################################################

require_once 'tools/http.inc';
require_once 'tools/parser.inc';

class Video {

    public static function getVideo($url, $http = null) {
        $http = null != $http ? $http : new HttpTools();
        if (false != strpos($url, 'vk.com') ||
            false != strpos($url, 'vkontakte.ru'))
        {
            return array('vk.com', self::getVideoVkontakte($url, $http));
        } else if (false != strpos($url, 'youtube.com')) {
            return array('youtube.com', self::getVideoYoutube($url, $http));
        }
        return array('Unknown', array());
    }

    public static function getVideoYoutube($url, $http) {
        if (preg_match('|embed/([A-Za-z0-9_-]*)|', $url, $m) ||
            preg_match('|v=([A-Za-z0-9_-]*)|',     $url, $m) ||
            preg_match('|/v/([A-Za-z0-9_-]*)|',    $url, $m))
        {
            $url = 'http://www.youtube.com/watch?v=' . $m[1];
        }
        $html    = $http->sendGetRequest($url);
        $config  = ParserTools::parseParam($html, 'playerConfig = {', '};');
        if (empty($config)) {
            return array();
        }
        $config  = json_decode('{' . $config . '}', true);
        $streams = $config['args']['url_encoded_fmt_stream_map'];
        $streams = explode(',', $streams);

        $videos = array();
        foreach ($streams as $url) {
            $url = urldecode($url);
            if (preg_match('|url=(.*)&itag=(.*)|', $url, $matches)) {
                $url = preg_replace('|;.*|', '', $matches[1]);
                if (preg_match('|quality=(.*?)&.*type=video/(.*)|', $url, $matches)) {
                    $format = preg_replace('|x-|', '', $matches[2]);
                    if ($format == 'mp4' || $format == 'flv') {
                        $res = str_ireplace(array('hd1080', 'hd720', 'large', 'medium', 'small'),
                            array('1080p', '720p', '480p', '360p', '240p'), $matches[1]);
                        $videos[strtoupper($format) . " $res"] = $url;
                    }
                } else {
                    $videos[] = $url;
                }
            }
        }
        return $videos;
    }

    public static function getVideoVkontakte($url, $http) {
        $url = str_replace('vkontakte.ru', 'vk.com', $url);
    	$html  = $http->sendGetRequest($url);

        $host  = ParserTools::parseParam($html, "var video_host = '",   "'");
        $uid   = ParserTools::parseParam($html, "var video_uid = '",    "'", 0);
        $vtag  = ParserTools::parseParam($html, "var video_vtag = '",   "'");
        $hd    = ParserTools::parseParam($html, "var video_max_hd = '", "'", 0);
        $noflv = ParserTools::parseParam($html, "var video_no_flv = ", ";", '');
        $noflv = false !== strpos($noflv, '1');

        # removed videos
        if (null == $host) {
            return array();
        }

        # flv support
        if ($uid <= 0) {
            if ($noflv) {
                return array();
            }
            $vkid = ParserTools::parseParam($html, 'vkid=', '&');
            return array('FLV' => "http://$host/assets/videos/$vtag$vkid.vk.flv");
        }

        static $hds = array('240', '360', '480', '720');

        # mp4 link by default
        $videos = array();
        for ($i = 0; $i <= $hd; $i++) {
            $res = $hds[$i];
            $videos["MP4 $res"] = $host . "u$uid/video/$vtag.$res.mp4";
        }
        return array_reverse($videos, true);
    }

    public static function playVideo($title, $video, $http = null) {
        global $lang;
        list($provider, $videos) = Video::getVideo($video, $http);
        if (0 == count($videos)) {
            return new Message($lang->msg('Message'), $lang->msg(
                'Video is not available. Probably because of license restrictions or because it was removed'));
        }
        $channel = new Channel($lang->msg('Quality selection') . " - $provider");
        $channel->setSupportedTemplates(1 == count($videos) ?
            'auto-open' : 'context-menu');
        foreach ($videos as $quality => $url) {
            $item = new Item($title, $quality);
            $item->addEnclosure($url, 'video/mp4');
            $channel->addItem($item);
        }
        return $channel;
    }
}
?>