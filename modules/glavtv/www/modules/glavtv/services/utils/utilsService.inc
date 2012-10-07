<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/service.inc';
require_once 'interfaces/channel.inc';
require_once 'interfaces/item.inc';
require_once 'interfaces/message.inc';
require_once 'interfaces/exceptions.inc';
require_once 'tools/http.inc';
require_once 'tools/sysinfo.inc';

require_once 'history.inc';
require_once 'remote.inc';
require_once 'backgrounds.inc';
require_once 'tools/realtek.inc';

class UtilsService extends Service {

    protected $log;
    protected $http;

    protected $history;
    protected $remote;

    public function init(&$cfg, &$lang) {
        parent::init($cfg, $lang);
        $this->log  = Logger::getLogger(get_class());
        $this->http = new HttpTools();

        $this->history = new History($cfg);
        $this->remote  = new RemoteFunctions($cfg);
    }

    public function getServiceName() {
        return 'GlavTV';
    }

    public function startPage() {
        return $this->welcome();
    }

    public function login() {
        $channel = $this->tools->showLoginDialog();
        $channel->set(Channel::DESCRIPTION, $this->lang->msg(
            'Enter your GlavTV login data'));
        return $channel;
    }

    public function auth() {
        $username = $this->getRequiredParam('username');
        $password = $this->getRequiredParam('password');
        $this->tools->saveAuth($username, $password);
        return $this->startPage();
    }

    public function message() {
        $caption = $this->getOptionalParam('caption', 'Message');
        $text = $this->getOptionalParam('text', 'Press OK to continue');
        $img  = $this->getOptionalParam('img');
        $fwd  = $this->getOptionalParam('fwd');
        return new Message($this->lang->msg($caption), $this->lang->msg($text), $img, $fwd);
    }

    public function information() {
        $caption = $this->getOptionalParam('caption', 'Information');
        $text = $this->getOptionalParam('text');
        $img  = $this->getOptionalParam('img');
        $fwd  = $this->getOptionalParam('fwd', $this->cfg->get('home_url'));
        $filename = $this->cfg->get('infoFile', 'information.txt');
        if (! isset($text) && is_readable($filename)) {
            $text = @file_get_contents($filename);
            @rename($filename, "$filename.old");
        }
        $msg = new Message($this->lang->msg($caption),
            $this->lang->msg($text), $img, $fwd);
        $msg->setSupportedTemplates('information');
        return $msg;
    }

    public function time() {
        $time = $this->getOptionalParam('time');
        $msg = new Message('time', $this->tools->formatTime($time));
        $msg->setSupportedTemplates('plain-text');
        return $msg;
    }

    public function seconds() {
        $msg = new Message('time', time());
        $msg->setSupportedTemplates('plain-text');
        return $msg;
    }

    public function open() {
        return $this->tools->open($this->getRequiredParam('url'),
            $this->getOptionalParam('title', 'Video'));
    }

    public function makeFun() {
        $fwd = $this->getOptionalParam('fwd');
        list($username, $password) = $this->tools->loadAuth();
        list($caption, $text, $image) = $this->remote->makeFun($username);
        $msg = new Message($this->lang->msg($caption),
            $this->lang->msg($text), $image, $fwd);
        $msg->setSupportedTemplates('funny-message');
        return $msg;
    }

    public function services() {
        $channel = $this->arrangeServices();
        $items = array();
        foreach ($channel->getItems() as $item) {
            if ($item->get(Item::CATEGORY) != 'off') {
                $items[] = $item;
            }
        }
        $channel->setItems($items);
        $channel->addHotkey('user1', $this->lang->msg('Search for updates'),
            $this->cfg->get('service_url') . '&req=update', array('for' => Item::ID));
        $channel->addHotkey('user4', $this->lang->msg('User'),
            $this->cfg->get('home_url') . '?req=login', array('srv' => Item::ID));
        $channel->addHotkey('user3', $this->lang->msg('Management'),
            $this->cfg->get('home_url') . '?req=showManagement');
        return $channel;
    }

    public function servicemenu() {
        $channel = $this->arrangeServices();
        $channel->setSupportedTemplates('servicemenu');
        $items = array();
        foreach ($channel->getItems() as $item) {
            if ($item->get(Item::CATEGORY) != 'special') {
                $items[] = $item;
            }
        }
        $channel->setItems($items);
        return $channel;
    }

    protected function arrangeServices() {
        $services = $this->findServices();

        $channel = new Channel($this->lang->msg('Available services'),
            $this->lang->msg('All supported services of GlavTV project'));
        $channel->setSupportedTemplates('icon-table');
        $channel->set(Channel::THUMB_RATIO, 0);

        # preserve order
        $order = $this->cfg->get('serviceOrder');
        if (empty($order)) {
            $channel->setItems(array_values($services));
            $channel->sortItems('!' . Item::CATEGORY, Item::TITLE);
        } else {
            $specials = array();
            foreach ($services as $id => $srv) {
                if ($srv->get(Item::CATEGORY) == 'special') {
                    $specials[] = $id;
                }
            }
            $order = explode(',', $order);
            $order = array_unique(array_merge($specials, $order, array_keys($services)));
            foreach ($order as $id) {
                if (isset($services[$id])) {
                    $channel->addItem($services[$id]);
                }
            }
        }

        # disabled services support
        $disabled = $this->cfg->get('serviceDisabled');
        if (! empty($disabled)) {
            $disabled = explode(',', $disabled);
            foreach ($channel->getItems() as $item) {
                if (in_array($item->get(Item::ID), $disabled)) {
                    $item->set(Item::CATEGORY, 'off');
                }
            }
        }
        return $channel;
    }

    protected function findServices() {
        $services = array();
        $dir = 'services';
        foreach (scandir($dir) as $entry) {
            $file = "$dir/$entry/service.xml";
            if (! is_readable($file)) {
                continue;
            }
            $xml   = simplexml_load_file($file);
            $name  = $this->lang->msg((string)$xml->name);
            $descr = $this->lang->msg((string)$xml->description);
            $item  = new Item($name, $descr);
            $item->set(Item::LINK, $this->cfg->get('home_url') . '?' .
                http_build_query(array('srv' => $entry, 'req' => null) + $_GET));
            $thumb = is_readable("$dir/$entry/logo.png") ?
                $this->cfg->get('home_url') . "$dir/$entry/logo.png" :
                ($this->cfg->get('resource_url') . 'img/unknown-service.png');
            $item->set(Item::THUMBNAIL, $thumb);
            $item->set(Item::ID, $entry);
            $item->set(Item::INFO, $this->lang->msg((string)$xml->type));
            $item->set(Item::CATEGORY, 'fav' == $entry ? 'special' : 'on');
            $item->set('version', (string)$xml->version);
            $services[$entry] = $item;
        }
        return $services;
    }


    public function pager() {
        $pages = $this->getRequiredParam('pages');
        parse_str($this->getRequiredParam('src'), $params);

        $channel = new Channel($this->lang->msg('Pager'));
        $channel->setSupportedTemplates('pager');
        $url = $this->cfg->get('home_url') . '?';
        for ($page = 1; $page <= $pages; $page++) {
            $params = array('page' => $page) + $params;
            $item = new Item($page, $this->lang->msg('To page') . " $page");
            $item->set(Item::LINK, $url . http_build_query($params));
            $item->set(Item::INFO, http_build_query($params));
            $channel->addItem($item);
        }
        return $channel;
    }

    public function welcome() {
        list($username, $password) = $this->tools->loadAuth();
        list($action, $params) = $this->remote->welcome($username, $password);
        $method = array($this, $action);
        if (! is_callable($method)) {
            throw new BadRequestException($this->lang->msg('No such method') .
                ": $req");
        }
        # correct possible platform troubles
        Realtek::fixAllPlatformTroubles();
        $_GET = $params + $_GET;
        return call_user_func($method);
    }

    public function update() {
        $forceUpdate = 1 == $this->getOptionalParam('force', 0);
        list($username, $password) = $this->tools->loadAuth();
        list($remoteVersion, $linkId, $downloadUrl) =
            $this->remote->version($username, $password);

        if (null == $remoteVersion || '' == $remoteVersion) {
            return new Message($this->lang->msg('Warning'),
                $this->lang->msg('No updates found'));
        }

        global $version;
        if (! $forceUpdate && version_compare($version, $remoteVersion, '>=')) {
            return new Message($this->lang->msg('Message'), $this->lang->msg(
                'You are using the latest version, no update is needed'));
        }

        $actiongName = $forceUpdate ? 'Install' : 'Update to';
        $channel = new Channel($this->lang->msg($actiongName) . " $remoteVersion");
        $channel->setSupportedTemplates('step-dialog');
        $channel->set(Channel::LINK, $this->cfg->get('home_url'));
        $channel->set(Channel::IMAGE,
            $this->cfg->get('resource_url') . 'img/update.png');

        $item = new Item($this->lang->msg('Download and test'),
            $this->lang->msg('Download new version and check its integrity'));
        $item->set(Item::LINK, $this->cfg->get('service_url') .
            '&req=download&url=' . urlencode($downloadUrl));
        $item->set('expResult', 'OK');
        $channel->addItem($item);

        $item = new Item($this->lang->msg('Install'), $this->lang->msg(
            'Unpack downloaded file, clean current version and overwrite with the new one'));
        $item->set(Item::LINK, $this->cfg->get('service_url') . '&req=install' .
            '&linkId=' . urlencode($linkId) . '&user=' . urlencode($username));
        $item->set('expResult', 'OK');
        $channel->addItem($item);

        return $channel;
    }

    protected function execCmd($cmd, $name, $errorMsg) {
        # $this->log->debug('Executing: ' . $cmd);
        $last = exec($cmd, $out, $res);
        if (0 != $res) {
            $msg = new Message($name, $this->lang->msg($errorMsg) .
                (0 == count($out) ? '' : ', ' . implode(' ', $out)));
            $msg->setSupportedTemplates('plain-text');
            return $msg;
        }
        return true;
    }

    public function download() {
        $url = $this->getRequiredParam('url');
        $tmp = $this->cfg->get('tmpDir', '/tmp/');
        $dst = $tmp . 'GlavTV.zip';

        # wget -q -O /tmp/GlavTV.zip http://location/tmp/GlavTV.zip
        $res = $this->execCmd("wget -q -O '$dst' '$url' 2>&1", 'Download',
            'Download failed, try again later');
        if (true !== $res) {
            return $res;
        }

        # unzip -t is not available on asus, let's do it other way
        if (! is_readable($dst) || filesize($dst) == 0) {
            $msg = new Message('Install', $this->lang->msg(
                'Integrity check failed, retry download'));
            $msg->setSupportedTemplates('plain-text');
            return $msg;
        }

        $msg = new Message('Download', 'OK');
        $msg->setSupportedTemplates('plain-text');
        return $msg;
    }

    public function install() {
        $user     = $this->getRequiredParam('user');
        $linkId   = $this->getRequiredParam('linkId');

        $tmp      = $this->cfg->get('tmpDir', '/tmp/');
        $src      = $tmp . 'GlavTV.zip';
        $dst      = $this->cfg->get('home_disk');

        $preinst  = $this->cfg->get('preinstall',  'preinstall.sh');
        $install  = $this->cfg->get('install',     'install.sh');
        $postinst = $this->cfg->get('postinstall', 'postinstall.sh');

        # check whether the file is there
        if (! is_readable($src)) {
            $msg = new Message('Install',
                $this->lang->msg('File not found') . ": $src");
            $msg->setSupportedTemplates('plain-text');
            return $msg;
        }

        # rm -rf /tmp/GlavTV
        $res = $this->execCmd("rm -rf '$tmp/GlavTV' 2>&1", 'Preclean', 'Cannot clean');
        if (true !== $res) {
            $res->setText($res->getText() . " $tmp/GlavTV");
            return $res;
        }

        # cd /tmp/ && unzip -nq GlavTV.zip
        $res = $this->execCmd("cd '$tmp' && unzip -nq '$src' 2>&1", 'Unzip',
            'Unpacking failed');
        if (true !== $res) {
            return $res;
        }

        # execute pre-install script iff provided
        if (is_readable("$tmp/GlavTV/" . $preinst)) {
            $res = $this->execCmd("cd '$tmp/GlavTV/' && sh '$preinst' 2>&1",
                'Preinstall', 'Preinstall failed');
            if (true !== $res) {
                return $res;
            }
        }

        # execute install script iff provided
        if (is_readable("$tmp/GlavTV/" . $install)) {
            $res = $this->execCmd("cd '$tmp/GlavTV/' && sh '$install' '$tmp/GlavTV' '$dst' 2>&1",
                'Install', 'Custom installation failed');
            if (true !== $res) {
                return $res;
            }
        } else if (is_dir($dst)) {
            # usual installation

            # cd /tmp/usbmounts/sda1/scripts/GlavTV/ && cp -R cfg/ /tmp/GlavTV/
            $res = $this->execCmd("cd '$dst' && cp -R cfg/ '$tmp/GlavTV/' 2>&1",
                'Save configs', 'Cannot save configuration files');
            if (true !== $res) {
                return $res;
            }

            # move existing files to some directory on same level (no FS damage)
            $oldDir = preg_replace('|/*$|', '', $dst) . '_' . date('Y-m-d_H-i');
            $res = $this->execCmd("mkdir '$oldDir' && mv '$dst/'* '$oldDir/' 2>&1",
                'Clean move', 'Cannot move clean');

            # rm -rf /tmp/usbmounts/sda1/scripts/GlavTV/*
            $res = $this->execCmd("rm -rf '$dst/'* '$oldDir/' 2>&1; true",
                'Clean', 'Cannot clean');
            if (true !== $res) {
                $res->setText($res->getText() . " $dst");
                return $res;
            }

            # mv "/tmp/GlavTV/"* '/tmp/usbmounts/sda1/scripts/GlavTV/'
            $res = $this->execCmd("mv '$tmp/GlavTV/'* '$dst/' 2>&1",
                'Move', 'Activation failed');
            if (true !== $res) {
                return $res;
            }
        }

        # execute post-install script iff provided
        if (is_readable("$dst/" . $postinst)) {
            $res = $this->execCmd("cd '$dst/' && sh '$postinst' 2>&1",
                'Postinstall', 'Postinstall failed');
            if (true !== $res) {
                return $res;
            }
        }

        # reread informations
        global $version;
        $version = SysInfo::getVersion();

        # update remote info tables
        $this->remote->setinfo($user, $linkId);

        $msg = new Message('Install', 'OK');
        $msg->setSupportedTemplates('plain-text');
        return $msg;
    }

    public function keyboard() {
        $msg = new Message($this->lang->msg('Keyboard'),
            $this->lang->msg('Not available'));
        $msg->setSupportedTemplates('keyboard/keyboard');
        return $msg;
    }

    public function history() {
        $offset  = $this->getOptionalParam('offset', 0);
        $length  = $this->getOptionalParam('length', -1);
        $section = $this->getOptionalParam('id', ConfigFile::DEFAULT_SECTION);
        $history = $this->history->get($offset, $length, $section);
        $channel = new Channel('History',
            'List of strings entered via international keyboard');
        $channel->setSupportedTemplates('keyboard/rss-array');
        foreach ($history as $time => $str) {
            $channel->addItem(new Item($str, $this->tools->formatTime($time)));
        }
        return $channel;
    }

    public function showhistory() {
        $channel = $this->history();
        $channel->setSupportedTemplates('keyboard/history');
        return $channel;
    }

    public function addhistory() {
        $str     = $this->getRequiredParam('str');
        $section = $this->getOptionalParam('id', ConfigFile::DEFAULT_SECTION);
        $this->history->add($str, $section);
        return new Message($this->lang->msg('Message'),
            $this->lang->msg('Added to history') . ": $str");
    }


    public function saveIniParams() {
        $params  = $this->getRequiredParam('params');
        $file    = $this->getOptionalParam('file', $this->cfg->get('cfgPath') . '/config.ini');
        $section = $this->getOptionalParam('section', ConfigFile::DEFAULT_SECTION);
        $fwd     = $this->getOptionalParam('fwd', $this->cfg->get('home_url'));
        $msg     = 0 != $this->getOptionalParam('msg', 0);

        $cfg = new ConfigFile($file);
        foreach ($params as $name => $value) {
            $cfg->setToSection($section, $name, $value);
        }
        $cfg->saveFile();

        # direct redirect or show a message first?
        if ($msg) {
            $fwd = $this->cfg->get('home_url') . '?req=message&text=' .
                urlencode($this->lang->msg('Changes are successfully saved')) .
                '&fwd=' . urlencode($fwd);
        }

        $channel = new Channel('Redirect');
        $channel->setSupportedTemplates('redirect');
        $channel->set(Channel::LINK, $fwd);
        return $channel;
    }

    public function backgrounds() {
        $bg = new Backgrounds();
        $channel = new Channel('Backgrounds');
        $channel->setSupportedTemplates('rss');
        foreach ($bg->getBackgrounds() as $img) {
            $channel->addItem(new Item($img));
        }
        return $channel;
    }


    public function showPossibilities() {
        $title = $this->getRequiredParam('title');
        $url   = $this->getRequiredParam('url');
        $descr = $this->getOptionalParam('descr');

        $channel = new Channel($this->lang->msg('Possibilities'));
        $channel->setSupportedTemplates('context-menu');

        $item = new Item($this->lang->msg('Go to service list'));
        $item->set(Item::LINK, $this->cfg->get('home_url') . '?req=services');
        $channel->addItem($item);

        $item = new Item($this->lang->msg('Go to favorites'));
        $item->set(Item::LINK, $this->cfg->get('home_url') . '?srv=fav');
        $channel->addItem($item);

        $item = new Item($this->lang->msg('Add this page to favorites'));
        $item->set(Item::LINK, $this->cfg->get('home_url') . '?' .
            http_build_query(array('srv'   => 'fav', 'req' => 'add',
                'title' => $title, 'url'   => $url, 'descr' => $descr)));
        $channel->addItem($item);

        return $channel;
    }

    public function showManagement() {
        $options = array(
            'servicemenu'    => 'Service management',
            'languages'      => 'Language selection',
            'timezones'      => 'Time zone selection',
            'margins'        => 'Margins adjustment', 
            'update&force=1' => 'Force update');

        $channel = new Channel($this->lang->msg('Management'));
        $channel->setSupportedTemplates('context-menu');

        $url = $this->cfg->get('home_url') . '?req=';
        foreach ($options as $req => $title) {
            $item = new Item($this->lang->msg($title));
            $item->set(Item::LINK, $url . $req);
            $channel->addItem($item);
        }
        return $channel;
    }

    public function languages() {
        $languages = array(
            'ru'  => 'Русский',
            'en'  => 'English',
            'de'  => 'Deutsch');

        $channel = new Channel($this->lang->msg('Language selection'));
        $channel->setSupportedTemplates('context-menu');

        $url = $this->cfg->get('service_url') . '&req=saveIniParams&';
        foreach ($languages as $id => $title) {
            $item = new Item($title);
            $item->set(Item::ID, $id);
            $item->set(Item::LINK, $url . http_build_query(
                array('params' => array('lang' => $id))));
            $channel->addItem($item);
        }
        return $channel;
    }

    public function timezones() {
        $timezones = array(
            '-12'  => 'Pacific/Kwajalein',
            '-11'  => 'Pacific/Samoa',
            '-10'  => 'Pacific/Honolulu',
            '-9'   => 'America/Juneau',
            '-8'   => 'America/Los_Angeles',
            '-7'   => 'America/Denver',
            '-6'   => 'America/Mexico_City',
            '-5'   => 'America/New_York',
            '-4'   => 'America/Caracas',
            '-3.5' => 'America/St_Johns',
            '-3'   => 'America/Argentina/Buenos_Aires',
            '-2'   => 'Atlantic/Azores', // no cities here so I don't give a shit and lie
            '-1'   => 'Atlantic/Azores',
            '0'    => 'Europe/London',
            '1'    => 'Europe/Berlin',
            '2'    => 'Europe/Helsinki',
            '3'    => 'Europe/Minsk',
            '3.5'  => 'Asia/Tehran',
            '4'    => 'Europe/Moscow',
            '4.5'  => 'Asia/Kabul',
            '5'    => 'Asia/Karachi',
            '5.5'  => 'Asia/Calcutta',
            '6'    => 'Asia/Colombo',
            '7'    => 'Asia/Bangkok',
            '8'    => 'Asia/Singapore',
            '9'    => 'Asia/Tokyo',
            '9.5'  => 'Australia/Darwin',
            '10'   => 'Pacific/Guam',
            '11'   => 'Asia/Magadan',
            '12'   => 'Asia/Kamchatka');

        $channel = new Channel($this->lang->msg('Time zone selection'));
        $channel->setSupportedTemplates('context-menu');

        date_default_timezone_set('Europe/London');
        $time = time();

        $url = $this->cfg->get('service_url') . '&req=saveIniParams&';
        foreach ($timezones as $diff => $name) {
            $title  = 'GMT';
            $title .= $diff > 0 ? "+$diff" : $diff;
            $title .= " - $name";
            $date = $this->tools->formatTime($time + 3600 * $diff);
            $item = new Item($title, $date);
            $item->set(Item::LINK, $url . http_build_query(
                array('params' => array('timezone' => $name))));
            $channel->addItem($item);
        }
        return $channel;
    }

    public function margins() {
        $channel = new Channel('Margins');
        $channel->setSupportedTemplates('margins');
        return $channel;
    }
}
?>
