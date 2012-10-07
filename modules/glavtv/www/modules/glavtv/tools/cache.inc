<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/channel.inc';
require_once 'interfaces/item.inc';
require_once 'tools/config.inc';
require_once 'tools/sysinfo.inc';
require_once 'tools/logger.inc';

class Cache {
    protected $srv;
    protected $home;
    protected $disk;
    protected $path;
    protected $useCache;
    protected $useLocalPath;
    protected $trickIntCache;
    protected $log;

    public function __construct($cfg, $srv = null) {
        $this->srv  = $srv;
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        $this->home = $cfg->get('home_url',  $host . dirname($_SERVER['PHP_SELF']) . '/');
        $this->disk = $cfg->get('home_disk', dirname(dirname(__FILE__)) . '/');
        $this->path = $cfg->get('cachePath', $this->disk . 'cache');

        $this->useCache = 1 == $cfg->get('useCache', 0);
        $this->useLocalPath = ! SysInfo::isDevEnvironment() &&
            1 == $cfg->get('useLocalPath', 0);
        $this->trickIntCache = 1 == $cfg->get('trickInternalCache', 0);

        if (strrpos($this->path, '/') !== 0) {
            $this->path = $this->disk . $this->path;
        }
        if (strrpos($this->path, '/') !== strlen($this->path)) {
            $this->path .= '/';
        }
        if (null != $srv && '' != $srv) {
            $this->path .= $srv . '/';
        }
        $this->log = Logger::getLogger(get_class());
        $this->log->debug("Created cache: " . print_r($this, true));
    }

    public static function mapToFilename($url) {
        $ext = strrchr($url, '.');
        $ext = false === $ext ? '.jpg' : basename($ext);
        return md5($url) . $ext;
    }

    public function getFilename($url) {
        return $this->path . self::mapToFilename($url);
    }

    public function cacheImages(&$channel) {
        static $channelParams = array(Channel::IMAGE);
        static $itemParams = array(Item::THUMBNAIL, Item::ICON);
        foreach ($channelParams as $param) {
            $url = $channel->get($param);
            $channel->set($param, $this->getCachedUrl($url));
        }
        foreach ($channel->getItems() as $item) {
            foreach ($itemParams as $param) {
                $url = $item->get($param);
                $item->set($param, $this->getCachedUrl($url));
            }
        }
    }

    public function getCachedUrl($url) {
        if (0 !== stripos($url, 'http:')) {
            return $url;
        }
        $filename = $this->getFilename($url);
        if (0 === strpos($url, $this->home)) {
            if ($this->useLocalPath) {
                $url = str_replace($this->home, $this->disk, $url);
            }
        } else if (! $this->useCache) {
            if ($this->trickIntCache) {
                $url .= false === strpos($url, '?') ?  '?' : '&';
                $url .= 'fake123=' . rand();
            }
        } else if ($this->useLocalPath && is_readable($filename)) {
            $url = $filename;
        } else {
            $url = $this->home . 'img.php?url=' . urlencode($url);
            if (null != $this->srv) {
                $url .= '&srv=' . urlencode($this->srv);
            }
        }
        return $url;
    }

    public function getContent($url) {
        $filename = $this->getFilename($url);
        if (0 === strpos($url, $this->home)) {
            return @file_get_contents($url);
        } else if (is_readable($filename)) {
            return @file_get_contents($filename);
        }

        $content = @file_get_contents($url);
        if (null != $content && '' != $content) {
            $this->log->debug("Caching $url to $filename");
            if (! is_dir($this->path)) {
                mkdir($this->path, 0755, true);
            }
            file_put_contents($filename, $content);
        }
        return $content;
    }
}
?>
