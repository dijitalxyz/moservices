<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'tools/servicetools.inc';
require_once 'tools/config.inc';
require_once 'lang/lang.inc';

abstract class Service {
    protected $tools;
    protected $cfg;
    protected $lang;

    public function init(&$cfg, &$lang) {
        $this->cfg   = &$cfg;
        $this->lang  = &$lang;
        $this->tools = new ServiceTools($this);

        # add custom service resources
        $shortName = strtolower(str_replace('Service', '', get_class($this)));
        $this->lang->addLangDir("services/$shortName/lang");
        $localCfg = $cfg->get('localCfgFile', "services/$shortName/local.ini");
        $this->cfg->mergeFile($localCfg, $this->getServiceName());
    }

    public abstract function getServiceName();

    public abstract function startPage();

    public function login() {
        return new Message($this->lang->msg('Message'),
            $this->lang->msg('No authorization is needed for this service') .
            ' (' . $this->getServiceName() . ')');
    }

    public function getCfg() {
        return $this->cfg;
    }

    public function getLang() {
        return $this->lang;
    }

    public function getCfgSection() {
        return $this->cfgSection;
    }

    public function getRequiredParam($name) {
        if (! isset($_GET[$name])) {
            throw new BadRequestException(
                $this->lang->msg('Required parameter missed') . ": $name");
        }
        return $_GET[$name];
    }

    public function getOptionalParam($name, $defValue = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $defValue;
    }
}
?>
