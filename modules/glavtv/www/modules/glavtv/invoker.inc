<?php
require_once 'interfaces/exceptions.inc';
require_once 'interfaces/message.inc';
require_once 'interfaces/channel.inc';
require_once 'interfaces/item.inc';
require_once 'tools/cache.inc';
require_once 'tools/sysinfo.inc';
require_once 'tools/parser.inc';

require_once 'services/utils/remote.inc';

class Invoker {
    protected $log;
    protected $cfg;
    protected $lang;

    public function __construct(&$cfg, &$lang) {
        $this->log  = Logger::getLogger(get_class());
        $this->cfg  = &$cfg;
        $this->lang = &$lang;
    }

    public function processRequest($srv, $req, $tpl, $nexttpl) {
        $res = false;
        set_error_handler(array($this, 'handleError'));
        try {
            if ($req == 'welcome' || ($srv != 'utils' && $srv != 'fav')) {
                $remote = new RemoteFunctions($this->cfg);
                $remote->stats($this->cfg->getFromSection('GlavTV', 'username'));
            }
            $res = $this->performInvokationSteps($srv, $req, $tpl, $nexttpl);
        } catch (GtvException $e) {
            $this->handleException($e);
        }
        restore_error_handler();
        return $res;
    }

    public function performInvokationSteps($srv, $req, $tpl, $nexttpl) {
        // step 1: init service
        $service = $this->loadService($srv);
        $srvName = $service->getServiceName();

        // step 2: fetch the object
        try {
            $object = $this->runService($service, $req);
        } catch (AuthException $e) {
            $_GET = array('details' => $e->getDetails()) + $_GET;
            $object = $this->runService($service, 'login');
        }
        $this->log->debug("Got following object: " . PHP_EOL. $object);

        // step 3: check the object
        if (null == $object || ! ($object instanceof Templatable)) {
            throw new GtvRuntimeException(
                'Service returns no data to display', "$srv:$req");
        }
        if ($req == 'search' && ($object instanceof Channel) && 0 == count($object->getItems())) {
            $object = new Message($this->lang->msg('Message'),
                $this->lang->msg('No matches found') . ': ' . $_GET['input']);
        }

        // step 4: find matching template from allowed
        if (null == $tpl) {
            $name = 'LAST_TPL_' . $srv . ':' . $req;
            $lasttpl = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
            $tpl = $this->findTemplate($object, $lasttpl, $nexttpl);

            // remember last used not forced template
            $_SESSION[$name] = $tpl;
        }

        // step 5: special handling for some templates
        if ('redirect' == $tpl) {
            $this->log->info('Location: ' . $object->get(Channel::LINK));
            header('Location: ' . $object->get(Channel::LINK));
            return true;
        }

        // step 6: do object post processing, e.g. image caching
        $object = $this->postProcess($object, $srv, $req, $srvName, $tpl);

        // step 7: apply found template
        $this->applyTemplate($object, $tpl);
        return true;
    }

    public function loadService($srv) {
        $include = 'services/' . $srv . '/' . $srv . 'Service.inc';
        if (! @file_exists($include)) {
            throw new BadRequestException(
                $this->lang->msg('File not found') . ": $include");
        }

        require_once $include;
        $class = ucfirst($srv) . 'Service';
        if (! class_exists($class)) {
            throw new BadRequestException(
                $this->lang->msg('No such class') . ": $class");
        }
        return new $class();
    }

    public function runService($service, $req) {
        $method = array($service, $req);
        if (! is_callable($method)) {
            throw new BadRequestException($this->lang->msg('No such method') .
                ": " . get_class($service) . ":$req");
        }

        $service->init($this->cfg, $this->lang);
        return call_user_func($method);
    }

    public function findTemplate($object, $lasttpl, $nexttpl) {
        $templates = $object->getSupportedTemplates();

        // check cases when there is no other choice
        if (null == $templates || 0 == count($templates)) {
            return null;
        } else if (1 == count($templates)) {
            return $templates[0];
        }

        // restore current template
        $index = null == $lasttpl ? 0 : array_search($lasttpl, $templates);
        if (false === $index) {
            $index = 0;
        }

        // switchable templates support
        $index += $nexttpl ? 1 : 0;
        return $templates[$index % count($templates)];
    }

    public function postProcess($object, $srv, $req, $srvName, $tpl) {
        if (! ($object instanceof Channel)) {
            return $object;
        }
        $cache = new Cache($this->cfg, $srv);
        $cache->cacheImages($object);

        if ($srv != 'utils') {
            $object->set(Channel::SERVICE, $srvName);
        }
        if (null == $object->get(Channel::LINK)) {
            $object->set(Channel::LINK, $this->cfg->get('self_url'));
        }
        if (null == $object->get(Channel::TITLE)) {
            $object->set(Channel::TITLE, "Вова, $srv:$req!");
        }
        if (0 == count($object->getItems()) && in_array($tpl, array(
            'auto-open', 'context-menu', 'detailed-list', 'epg-single',
            'icon-line', 'icon-table', 'media-details', 'plain-list',
            'season-details', 'tv-channels'))) 
        {
            throw new GtvRuntimeException(
                'Service returns no data to display', "$srv:$req");
        }
        return $object;
    }

    public function applyTemplate($object, $tpl) {
        if (null == $tpl) {
            $tpl = 'default';
        }
        $fileName = 'templates/' . $tpl . '.tpl';
        if (! is_readable($fileName)) {
            $fileName = 'templates/common/' . $tpl . '.tpl';
        }
        if (! is_readable($fileName)) {
            $tpl = 'default';
            $fileName = 'templates/' . $tpl . '.tpl';
        }
        require_once $fileName;
    }

    public function handleError($no, $str, $file, $line) {
        if (0 == error_reporting()) {
            return false;
        }
        static $levels = array(
            E_WARNING           => 'WARNING',
            E_NOTICE            => 'NOTICE',
            E_USER_ERROR        => 'USER_ERROR',
            E_USER_WARNING      => 'USER_WARNING',
            E_USER_NOTICE       => 'USER_NOTICE',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_ALL               => 'ALL');
        $level = isset($levels[$no]) ? $levels[$no] : $no;
        $str = ParserTools::removeHtmlTags($str);
        $str = preg_replace('|\[.*?\]|', '', $str);
        throw new GtvRuntimeException(
            $level . ':' . basename($file) . ':' . $line, $str);
    }

    protected function handleException($e) {
        $message = new Message($this->lang->msg('Error'), $e);
        $this->log->error($message);
        $this->applyTemplate($message, 'message');
    }
}
?>
