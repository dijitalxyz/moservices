<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'tools/config.inc';

class History {

    const MAX_HISTORY_ENTRIES = 25;

    protected $cfg;

    public function __construct(&$cfg) {
        $this->cfg = $cfg;
    }

    public function get($offset = 0, $length = -1, $sectionName = ConfigFile::DEFAULT_SECTION) {
        $cfgHst  = new ConfigFile($this->cfg->get('cfgPath') . '/history.txt', $sectionName);
        $section = $cfgHst->getSection($sectionName);
        $length  = -1 != $length ? $length : count($section);

        $history = array();
        foreach ($section as $time => $str) {
            if ($offset-- > 0) {
                continue;
            }
            if ($length-- < 1) {
                break;
            }
            $history[$time] = $str;
        }
        return $history;
    }

    public function add($str, $sectionName = ConfigFile::DEFAULT_SECTION) {
        $time = date('Y-m-d H:i:s');
        $cfgHst = new ConfigFile($this->cfg->get('cfgPath') . '/history.txt', $sectionName);
        $section = $cfgHst->getSection($sectionName);
        $section = array($time => $str) + $section;
        $section = array_unique($section);
        $section = array_slice($section, 0, self::MAX_HISTORY_ENTRIES);
        $cfgHst->setSection($sectionName, $section);
        $cfgHst->saveFile();
        return true;
    }
}
?>
