<?php
require_once 'tools/util.inc';
require_once 'interfaces/mediaObjects.inc';
require_once 'interfaces/view.inc';

require_once 'Smarty.class.php'; 

class SmartyView extends View {
	protected $log;
	function getIniFilename() {
		return dirname($_SERVER['PHP_SELF'])."/views/smarty/config.ini";
	}
	
	function init(Configuration $config) {
		parent::init($config);
		$this->log = Logger::getLogger(get_class());

		$this->config->set('activeTemplate', $this->config->get("Templates_" . $this->config->get('user_agent',Configuration::$RUNTIME_SECTION),"templates"),Configuration::$RUNTIME_SECTION);
		if($this->config->exists("cfg_resources_home","templates")) {
			$this->config->set('cfg_resources_home', $this->config->get("cfg_resources_home","templates"),Configuration::$RUNTIME_SECTION);
		} else {
			$this->config->set('cfg_resources_home', $this->config->get("cfg_realpath",Configuration::$RUNTIME_SECTION) .$this->config->get("activeTemplate",Configuration::$RUNTIME_SECTION),Configuration::$RUNTIME_SECTION);
		}
		
		//add template ini
        $this->config->addFile($this->config->get("activeTemplate",Configuration::$RUNTIME_SECTION)."config.ini",true);
	}

	// Find best matching template
	// Prio1: config value
	// Prio2: type + mode
	// Prio3: mode
	// Prio4: type
	// Last : default
	function getTemplate(MediaObject $obj, $mode) {
		$tmplType = "tmpl_" . $obj->getType();
		$templatesToCheck = array();
        $cfgTmpl = $this->config->get($tmplType, "templates");
        if (null != $cfgTmpl && "" != $cfgTmpl) {
        	$templatesToCheck[] = str_replace('.xml', '', $cfgTmpl);
        }
        if (null != $mode && "" != $mode) {
            $templatesToCheck[] = $tmplType . "_" . $mode;
            $templatesToCheck[] = "tmpl_" . $mode; // default mode has higher prio
        }
        $templatesToCheck[] = $tmplType;

        $currentTemplatePath = $this->config->get('activeTemplate', 
            Configuration::$RUNTIME_SECTION);
        $path = getcwd() . "/" . $currentTemplatePath;

        foreach ($templatesToCheck as $template) {
        	$filename = $path . $template . ".xml";
        	if (file_exists($filename)) {
        		return $filename;
        	}
        }
		return $path . "tmpl_default.xml";
	}

	function getSubtempl($childType) {
		$subtemplFile = $this->config->get("activeTemplate",Configuration::$RUNTIME_SECTION) . "subtempl_".$childType.".xml";
		if(!file_exists($subtemplFile)) {
			$subtemplFile  = $this->config->get("activeTemplate",Configuration::$RUNTIME_SECTION)."subtempl_default.xml";
		}		
		return $subtemplFile;
	}
	
	function drawObject(MediaObject $mediaObj,$mode) {

		if(isset($mediaObj)) {

			if($mediaObj->getType() == 'EMPTY') {
				return;
			}
			if($mode == "redirect") {
				$this->redirect($mediaObj->getParam("url"));
			}
			else {
				$templateFile = $this->getTemplate($mediaObj,$mode);

				$selfUrl = "http://".$_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . "?" . htmlspecialchars ($_SERVER["QUERY_STRING"]);
		
				//-------------------------------------!!! no variable changes after this point
				$smrt = new Smarty();
				$smrt->debugging = $this->config->get("smarty_debug","templates");
				//$smrt->compile_check = $this->config->get("compile_check","templates");
				
				$tempDir = $this->config->get("temp_dir","", "temp/");
				$smrt->setCompileDir($this->config->get("compile_dir","templates", $tempDir. "templates_c"));
				
				$util = new Utils();
				$util->init($this->config);

				$smrt->assign('view',$this);
				$smrt->assign('mediaObj',$mediaObj);
				$smrt->assign('selfUrl',$selfUrl);
				$smrt->assign('config',$this->config);
				$smrt->assign('locale',$this->locale);
				$smrt->assign('util',$util);

				$smrt->display($templateFile);
			}
		}
	}
}

?>