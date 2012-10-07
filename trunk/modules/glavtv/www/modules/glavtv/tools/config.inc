<?php
#############################################################################
# Configuration manager based on set of ini files.                          #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################


class ConfigFile {

    const DEFAULT_SECTION = 'Default';
    const ESC_SYMBOLS     = '(,),[,],{,}';
    const UNESC_SYMBOLS   = '^RL^,^RR^,^SL^,^SR^,^FL^,^FR^';

    protected $sections;
    protected $fileName;
    protected $defSection;

    public function __construct($fileName = null, $defSection = self::DEFAULT_SECTION) {
        $this->sections   = array();
        $this->fileName   = $fileName;
        $this->defSection = $defSection;
        $this->mergeFile($fileName);
    }

    public function mergeFile($fileName, $defSection = null) {
        if (null == $defSection) {
            $defSection = $this->defSection;
        }
        if (null != $fileName && '' != $fileName && file_exists($fileName)) {
            $iniArray = $this->readFile($fileName, true);
            foreach ($iniArray as $name => $value) {
                if (is_array($value)) {
                    $this->sections[$name] = isset($this->sections[$name]) ?
                        $value + $this->sections[$name] : $value;
                } else {
                    $this->set($defSection, $name, $value);
                }
            }
        }
    }

    public function get($paramName, $defValue = null) {
        if (func_num_args() > 2) {
            $sectionName = func_get_arg(0);
            $paramName   = func_get_arg(1);
            $defValue    = func_get_arg(2);
        } else {
            $sectionName = $this->defSection;
        }
        return $this->getFromSection($sectionName, $paramName, $defValue);
    }

    public function getFromSection($sectionName, $paramName, $defValue = null) {
        if (! isset($this->sections[$sectionName])) {
            return $defValue;
        }
        return ! isset($this->sections[$sectionName][$paramName]) ? $defValue :
            $this->sections[$sectionName][$paramName];
    }

    public function getSection($sectionName) {
        return isset($this->sections[$sectionName]) ? $this->sections[$sectionName] : array();
    }

    public function getSectionNames() {
        return array_keys($this->sections);
    }

    public function set($paramName, $value) {
        if (func_num_args() > 2) {
            $sectionName = func_get_arg(0);
            $paramName   = func_get_arg(1);
            $value       = func_get_arg(2);
        } else {
            $sectionName = $this->defSection;
        }
        $this->setToSection($sectionName, $paramName, $value);
    }

    public function setToSection($sectionName, $paramName, $value) {
        if (! isset($this->sections[$sectionName])) {
            $this->sections[$sectionName] = array();
        }
        $this->sections[$sectionName][$paramName] = $value;
    }

    public function setSection($sectionName, $section) {
        $this->sections[$sectionName] = $section;
    }

    public function saveFile($fileName = null) {
        if (! isset($filename)) {
            $fileName = $this->fileName;
        }
        if (null != $fileName && '' != $fileName) {
            $this->writeFile($fileName, $this->sections, true);
        }
    }

    protected function escapeKey($key) {
        return str_replace(explode(',', self::ESC_SYMBOLS),
            explode(',', self::UNESC_SYMBOLS), $key);
    }

    protected function unescapeKey($key) {
        return str_replace(explode(',', self::UNESC_SYMBOLS),
            explode(',', self::ESC_SYMBOLS), $key);
    }

    protected function readFile($fileName, $hasSections = true) {
        $iniArray = parse_ini_file($fileName, $hasSections);
        foreach ($iniArray as $sectionName => $entries) {
            if (is_array($entries)) {
                foreach ($entries as $key => $value) {
                    unset($entries[$key]);
                    $entries[$this->unescapeKey($key)] = $value;
                }
                $iniArray[$sectionName] = $entries;
            } else {
                unset($iniArray[$sectionName]);
                $iniArray[$this->unescapeKey($sectionName)] = $entries;
            }
        }
        return $iniArray;
    }

    protected function writeFile($fileName, $iniArray, $hasSections = true) {
        $content = '';
        if ($hasSections) {
            foreach ($iniArray as $sectionName => $entries) {
                if (is_array($entries)) {
                    $content .= "[$sectionName]\n";
                    $content .= $this->arrayToIniString($entries) . "\n";
                } else {
                    $content .= $this->escapeKey($sectionName) . " = \"$entries\"\n";
                }
            }
        } else {
            $content = $this->arrayToIniString($iniArray);
        }

        if (! is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }
        $fh = fopen($fileName, 'w');
        if (false === $fh) {
            return false;
        }
        fwrite($fh, $content);
        fclose($fh);
        return true;
    }

    protected function arrayToIniString($array) {
        $content = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $elem) {
                    $content .= $key . "[] = \"$elem\"\n";
                }
            } else if ('' == $key) {
                $content .= "\n";
            } else if (';' == $key) {
                $content .= ";$value\n";
            } else if ('' == $value) {
                $content .= $this->escapeKey($key) . " = \n";
            } else {
                $content .= $this->escapeKey($key) . " = \"$value\"\n";
            }
        }
        return $content;
    }
}
?>
