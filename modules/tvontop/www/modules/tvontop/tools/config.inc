<?php
require_once 'tools/logger.inc';

class Configuration {
	var $files;
	public static $RUNTIME_FILE				= "NO_FILE";
	public static $RUNTIME_SECTION			= "Runtime";
	public static $CUSTOM_FILE				= "custom.ini";
	public static $CUSTOM_FILE_KEY			= "custom_file";
	public static $CUSTOM_SECTION			= "Custom Data";
	public static $KEY_PROCESSING_URL		= "cfg_processing_url";
	protected $log; 
	
	
	function Configuration() {
		$this->log = Logger::getLogger(get_class());
		$this->files = array();
	}

	function findSectionFile($sectionName) {
		foreach ($this->files as $filename =>  $file) {
			foreach ($file as $section => $fields) {
				if (is_array($fields) && $section == $sectionName) {
					return $filename;
				}
			}
		}
		return null;
	}
	function findValue($valueName) {
		foreach ($this->files as $file) {
			foreach ($file as $key => $value) {
				if (!is_array($value) && $key == $valueName) {
					return $value;
				}
			}
		}
		return null;
	}

	function exists($key, $section = null) {
		if(null == $section){
			$val = $this->findValue($key);
			return isset($val);
		}
		$file = $this->findSectionFile($section);
		return isset($file) && isset($this->files[$file][$section][$key]) ;
	}

	function getByFile($filename, $name, $section = null, $defVal = null) {
		$fields = isset($filename) && isset($this->files[$filename]) ? $this->files[$filename] : null;
		if (null == $section) {
			$value = (isset($fields)  && isset($fields[$name])) ? $fields[$name] : $defVal;
		}
		else {
			$value = (isset($fields) && isset($fields[$section]) && isset($fields[$section][$name])) ? $fields[$section][$name] : $defVal;
		}
		return $value; 
	}
	function get($name, $section = null, $defVal = null) {
		$value = null;
		if (null == $section) {
			$value = $this->findValue($name);
		} else {
			$file = $this->findSectionFile($section);
			$fields = isset($file) ? $this->files[$file] : null;
			$value = (isset($fields) && isset($fields[$section]) && isset($fields[$section][$name])) ? $fields[$section][$name] : $defVal;	

		}
		if (isset($value)){
			return $value;
		}
		if (null == $section) {
			$this->log->debug("-------------value without section: ". $name);
		}
		return $defVal;
	}

	function set($name, $value, $section = null, $file  = null) {
		if(!isset($file)) {
			if (isset($section)) {
				$fileName = $this->findSectionFile($section);
			}
			if (!isset($fileName)) {
				$fileName = self::$RUNTIME_FILE;
				if (!isset($this->files[$fileName])) {
					$this->files[$fileName]["iniTitle"] = "Runtime config";
				}
			}
		} else {
			$fileName = $file;
		}
		if (isset($section)) {
			$this->files[$fileName][$section][$name] = $value;
		} else {
			$this->files[$fileName][$name] = $value; 
		}
	}

	function addFile($filename, $has_sections = true) {
		if (null != $filename && "" != $filename) {
			if(file_exists($filename)) {
				$newFields = parse_ini_file($filename, $has_sections);
				if(is_array($newFields)) {
					$this->files[$filename] = $newFields;
				}	
			} else {
				$this->files[$filename] = array();
			}
		}
	}

	function readFile($filename, $has_sections = false) {
		$this->files[$filename] = parse_ini_file($filename, $has_sections);
		return $this->files;
	}

	function saveSection($section, $file=null) {
		if(!isset($file)) {
			if (isset($section)) {
				$fileName = $this->findSectionFile($section);
			}
		} else {
			$fileName = $file;
		}
		if (isset($fileName)) {
			if(!isset($this->files[$fileName])) {
				$this->files[$fileName] = array();
			}
			if(!isset($this->files[$fileName][$section])) {
				$this->files[$fileName][$section] = array();
				$this->files[$fileName][$section] = $this->files[self::$RUNTIME_FILE][$section]; 
				unset($this->files[self::$RUNTIME_FILE][$section]);
			}
			$this->writeFile($this->files[$fileName],$fileName,true);
		}
	}
	//TODO: read and write comments too
    function writeFile($ini_array, $filename, $has_sections = true) {
        $content = "";
        if ($has_sections) {
            foreach ($ini_array as $section => $entries) {
            	if(is_array($entries)) {
	                $content .= "[$section]\n";
	                $content .= $this->arrayToIniString($entries) . "\n";
            	}
            	else {
            		$content .= "$section = \"$entries\"\n";
            	}
            }
        } else {
            $content = $this->arrayToIniString($ini_array);
        }
        
        $fh = fopen($filename, 'w');
        if (false === $fh) {
            return false;
        }
        fwrite($fh, $content);
        fclose($fh);
        return true;
    }
	
    protected function arrayToIniString($array) {
        $content = "";
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $elem) {
                    $content .= $key . "[] = \"$elem\"\n";
                }
            } else if ("" == $key) {
                $content .= "\n";
            } else if (";" == $key) {
                $content .= ";$value\n";
            } else if ("" == $value) {
                $content .= "$key = \n";
            } else {
                $content .= "$key = \"$value\"\n";
            }
        }
        return $content;
    }
}
?>