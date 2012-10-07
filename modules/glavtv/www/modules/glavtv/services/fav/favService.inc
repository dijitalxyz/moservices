<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/service.inc';
require_once 'interfaces/channel.inc';
require_once 'interfaces/item.inc';
require_once 'interfaces/exceptions.inc';
require_once 'interfaces/message.inc';
require_once 'tools/config.inc';

require_once 'services/utils/utilsService.inc';

class FavService extends Service {

    const MAX_FAVORITES_ENTRIES = 100;

    public function getServiceName() {
        return 'Favorites';
    }

    public function startPage() {
        return $this->show();
    }

    public function login() {
        $channel = new Channel($this->lang->msg('Login'));
        $channel->set(Channel::LINK,
            $this->cfg->get('home_url') . '?srv=utils&req=login');
        $channel->setSupportedTemplates('redirect');
        return $channel;
    }

    public function show() {
        $channel = new Channel($this->lang->msg('Favorites'));
        $channel->set(Channel::THUMB_RATIO, 0);
        $channel->setSupportedTemplates('detailed-list', 'icon-table');
        $channel->addHotkey('enter', null,
            $this->cfg->get('home_url') . '?srv=fav&req=count',
            array('title' => Item::TITLE, 'url' => Item::LINK));
        $channel->addHotkey('user1', $this->lang->msg('Delete'),
            $this->cfg->get('home_url') . '?srv=fav&req=remove',
            array('title' => Item::TITLE, 'url' => Item::LINK));

        $cfgFav = new ConfigFile($this->cfg->get('cfgPath') . '/favorites.txt');
        foreach ($cfgFav->getSectionNames() as $sectionName) {
            $section = $cfgFav->getSection($sectionName);
            foreach ($section as $title => $info) {
                list($count, $time, $url, $descr) = explode('|', $info);
                $item  = new Item($title, $descr);
                $item->set(Item::LINK, $url);
                $thumb = $this->cfg->get('home_url') . "services/$sectionName/logo.png";
                $item->set(Item::THUMBNAIL, $thumb);
                $item->set('count', (int) $count);
                $item->set(Item::TIME, $time);
                $channel->addItem($item);
            }
        }
        $channel->sortItems('!' . Item::TIME, '!count', Item::TITLE);
        foreach ($channel->getItems() as $item) {
            $item->set(Item::TIME, $this->tools->formatTime(
                $item->get(Item::TIME)));
        }
        return $channel;
    }

    public function add() {
        list($title, $url, $sectionName, $descr) = $this->getAddRemoveParams();

        $cfgFav = new ConfigFile($this->cfg->get('cfgPath') . '/favorites.txt', $sectionName);
        if (null != $cfgFav->get($title)) {
            return new Message($this->lang->msg('Error'),
                $this->lang->msg('Not added to favorites') . '. ' .
                $this->lang->msg('Name already exists') . ': "' . $title .'"');
        }

        $time = date('Y-m-d H:i:s');
        $info = "0|$time|$url|$descr";
        $section = $cfgFav->getSection($sectionName);
        $section = array($title => $info) + $section;
        $section = array_slice($section, 0, self::MAX_FAVORITES_ENTRIES);
        $cfgFav->setSection($sectionName, $section);
        $cfgFav->saveFile();

        return new Message($this->lang->msg('Message'),
            $this->lang->msg('Added to favorites') . ': "' . $title .'"');
    }

    public function remove() {
        list($title, $url, $sectionName) = $this->getAddRemoveParams();

        $cfgFav = new ConfigFile($this->cfg->get('cfgPath') . '/favorites.txt', $sectionName);
        if (null == $cfgFav->get($title)) {
            return new Message($this->lang->msg('Error'),
                $this->lang->msg('Not removed from favorites') . '. ' .
                $this->lang->msg('No such name') . ': "' . $title .'"');
        }

        $section = $cfgFav->getSection($sectionName);
        unset($section[$title]);
        $cfgFav->setSection($sectionName, $section);
        $cfgFav->saveFile();

        return new Message($this->lang->msg('Message'),
            $this->lang->msg('Removed from favorites') . ': "' . $title .'"');
    }

    public function count() {
        list($title, $url, $sectionName) = $this->getAddRemoveParams();

        $cfgFav = new ConfigFile($this->cfg->get('cfgPath') . '/favorites.txt');
        $section = $cfgFav->getSection($sectionName);

        $count = 0;
        if (isset($section[$title])) {
            list($count, $time, $url, $descr) = explode('|', $section[$title]);
            $count++;
            $time = date('Y-m-d H:i:s');
            $info = "$count|$time|$url|$descr";
            $section = array($title => $info) + $section;
            $cfgFav->setSection($sectionName, $section);
            $cfgFav->saveFile();
        }
        $channel = new Channel($this->lang->msg('Counter'),
            $this->lang->msg('Counter increased') . ": $title, $count");
        $channel->set(Channel::LINK, $this->getFullUrl($url));
        $channel->setSupportedTemplates('redirect');
        return $channel;
    }

    protected function getAddRemoveParams() {
        $title = $this->getRequiredParam('title');
        $url   = $this->getSmallUrl($this->getRequiredParam('url'));
        $descr = $this->getOptionalParam('descr', '');

        $pos = strpos($url, '?');
        $paramStr = false === $pos ? $url : substr($url, $pos + 1);
        parse_str($paramStr, $params);
        if (! isset($params['srv'])) {
            throw new BadRequestException(
                $this->lang->msg('Cannot parse data') . ": URL, SRV");
        }

        $descr = str_replace(array("\r", "\n", '\"', '"'), array(' ', ' ', '″', '″'), $descr);
        $descr = preg_replace(array('@\s+@', '@&\w{1,6};@i'), array(' ', ''), $descr);
        $descr = ParserTools::removeHtmlTags($descr);
        $title = str_replace(array("\r", "\n", '\"', '"', '=', '!'), array(' ', ' ', '″', '″', ' ', '.'), $title);
        $title = preg_replace(array('@\s+@', '@&\w{1,6};@i'), array(' ', ''), $title);
        $title = ParserTools::removeHtmlTags($title);
        return array($title, $url, $params['srv'], $descr);
    }

    protected function getFullUrl($url) {
        return 0 === strpos($url, 'http:') ? $url : ($this->cfg->get('home_url') . "?$url");
    }

    protected function getSmallUrl($url) {
        $prefix = $this->cfg->get('home_url') . '?';
        return 0 === strpos($url, $prefix) ? substr($url, strlen($prefix)) : $url;
    }
}
?>
