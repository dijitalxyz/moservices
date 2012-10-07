<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/service.inc';
require_once 'interfaces/channel.inc';
require_once 'interfaces/item.inc';
require_once 'interfaces/pager.inc';
require_once 'interfaces/message.inc';
require_once 'tools/logger.inc';
require_once 'tools/config.inc';

class ServiceTools {
    protected $service;
    protected $cfg;
    protected $lang;
    protected $weekDays;
    protected $monthNames;

    public function __construct($service) {
        $this->service    = $service;
        $this->cfg        = $service->getCfg();
        $this->lang       = $service->getLang();
        $this->weekDays   = explode(',', $this->lang->msg('WeekDays'));
        $this->monthNames = explode(',', $this->lang->msg('MonthNames'));
    }

    public function showLoginDialog($name = null) {
        $details = $this->service->getOptionalParam('details',
            $this->lang->msg('Enter your account data'));
        $name = null != $name ? $name : $this->service->getServiceName();
        $channel = new Channel("$name - " . $this->lang->msg('Login'), $details);
        $channel->setSupportedTemplates('login');
        $channel->set('username', $this->cfg->getFromSection($name, 'username'));
        $channel->set('password', $this->cfg->getFromSection($name, 'password'));
        return $channel;
    }

    public function loadAuth($name = null) {
        $name = null != $name ? $name : $this->service->getServiceName();
        return array($this->cfg->getFromSection($name, 'username'),
            $this->cfg->getFromSection($name, 'password'));
    }

    public function saveAuth($username, $password, $name = null) {
        $name = null != $name ? $name : $this->service->getServiceName();
        $authcfg = new ConfigFile($this->cfg->get('cfgPath') . '/auth.ini');
        $authcfg->set($name, 'username', $username);
        $authcfg->set($name, 'password', $password);
        $authcfg->saveFile();
        $this->cfg->set($name, 'username', $username);
        $this->cfg->set($name, 'password', $password);
    }

    public function open($url, $title) {
        $channel = new Channel($this->lang->msg($title));
        $channel->setSupportedTemplates('auto-open');
        $item = new Item($title);
        $item->addEnclosure($url, 'video/mp4');
        $channel->addItem($item);
        return $channel;
    }

    public function addPager(&$channel, $pager) {
        if ($pager->getPages() < 2) {
            $channel->setPager(null);
            return;
        }
        $channel->setPager($pager);
        $destPage = $pager->getCurrent() - 1;
        $addThumbs = in_array(Item::THUMBNAIL, $channel->getItemParams());
        if ($destPage > 0) {
            $item = $this->getToPageLink($pager, $destPage);
            if ($addThumbs) {
                $item->set(Item::THUMBNAIL, $this->cfg->get('resource_url') . 'img/left.png');
            }
            $channel->addItem($item, true);
        }
        $destPage = $pager->getCurrent() + 1;
        if ($destPage <= $pager->getPages()) {
            $item = $this->getToPageLink($pager, $destPage);
            if ($addThumbs) {
                $item->set(Item::THUMBNAIL, $this->cfg->get('resource_url') . 'img/right.png');
            }
            $channel->addItem($item);
        }
        $title = $channel->get(Channel::TITLE) . " $pager";
        $channel->set(Channel::TITLE, $title);
    }

    protected function getToPageLink($pager, $destPage) {
        $params  = array('page' => $destPage) + $_GET;
        $perPage = $pager->getPerPage();
        $total   = $pager->getTotal();
        $start   = ($destPage - 1) * $perPage + 1;
        $stop    = min($total, $start + $perPage - 1);

        $title   = $this->lang->msg('To page') . " $destPage";
        $descr   = $this->lang->msg('Jump to page', $destPage,
            $pager->getPages(), $start, $stop, $total);
        $link = $this->cfg->get('home_url') . '?' . http_build_query($params);

        $item = new Item($title, $descr);
        $item->set(Item::LINK, $link);
        return $item;
    }

    public function removePager(&$channel) {
        $pager = $channel->getPager();
        if (null != $pager) {
            $title = $channel->get(Channel::TITLE);
            $channel->set(Channel::TITLE, str_replace(" $pager", '', $title));

            $items = $channel->getItems();
            if (! $pager->isFirstPage()) {
                array_shift($items);
            }
            if (! $pager->isLastPage()) {
                array_pop($items);
            }
            $channel->setItems($items);
            $channel->setPager(null);
        }
        return $pager;
    }


    public function formatTime($time = null, $format = 'D d.m H:i', $translate = true) {
        $time = null == $time ? time() : (is_string($time) ? strtotime($time) : $time);
        if (! $translate) {
            return date($format, $time);
        }
        $format = str_replace('M', '\M', $format);
        $format = str_replace('D', '\D', $format);
        $month  = date('m', $time) - 1;
        $day    = date('w', $time);
        $str    = date($format, $time);
        $str    = str_replace('M', $this->monthNames[$month], $str);
        $str    = str_replace('D', $this->weekDays[$day], $str);
        return $str;
    }


    public function getMultiplePages($func, $currentPage, $pagesNumber) {
        $firstChannel = null;
        $lastPager    = null;
        $items        = array();
        $localOffset  = ($currentPage - 1) * $pagesNumber + 1;

        for ($i = 0; $i < $pagesNumber; $i++) {
            # change GET params in order to simulate page call
            $destPage = $localOffset + $i;
            $_GET = array('page' => $destPage) + $_GET;

            # call the channel fetching method
            $channel = call_user_func($func, $destPage);

            # result should be a valid channel object
            if (false == $channel || ! ($channel instanceof Channel)) {
                $_GET = array('page' => $currentPage) + $_GET;
                return $channel;
            }

            # very first channel will be used for the end result
            if (null == $firstChannel) {
                $firstChannel = $channel;
            }

            # final pager will be added at the end
            $pager = $this->removePager($channel);

            # non of children should be lost
            $items = array_merge($items, $channel->getItems());

            # empty pager means there is no need to continue
            if (null == $pager) {
                break;
            }

            # very last pager can contain more complete information
            $lastPager = $pager;
            if ($pager->isLastPage()) {
                break;
            }
        }
        # restore original GET params state
        $_GET = array('page' => $currentPage) + $_GET;

        # if result is incomplete do nothing
        if (null == $firstChannel || null == $lastPager) {
            return $firstChannel;
        }

        # include in result all collected children
        $firstChannel->setItems($items);

        # convert pager to new measurements
        $perPage = $lastPager->getPerPage() * $pagesNumber;
        $pager = new Pager($currentPage, $perPage, null, $lastPager->getTotal());
        $this->addPager($firstChannel, $pager);
        return $firstChannel;
    }
}
?>
