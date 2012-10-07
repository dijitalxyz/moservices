<?php
class SysInfo {
	
	public static function getMac() {
		ob_start();
		$cmd = "/sbin/ifconfig | grep Ether";
		passthru($cmd);
		$dump = ob_get_contents();
		preg_match('/[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}:[A-F0-9]{2}/i', $dump, $mac);
		if(null != $mac) {
			$mac_address = $mac[0];
		}
		ob_end_clean();	
		return $mac_address ;
	}

	public static function getFoothPrint() {
		return md5(self::getMac());
	}

	public static function getSysinfo() {
		return php_uname();
	}

	public static function getPlatform() {
		$platformExt = self::getPlatformExt();
		
		if(strpos($platformExt,"PRODIGY") !== false ) {
			$platform = "prodigy";
		} else if(is_file("/tmp/model")) {
			$platform = "xtreamer";
		} else if(strpos(strtolower($platformExt),"inext") !== false ){
			$platform = "inext";		
		} else {
			$platform = "realtek";
		}
		return $platform;
	}

	public static function getPlatformExt() {
		//xtreamer
		if(is_file("/tmp/model")) {
			if ($fp = fopen("/tmp/model", 'r')) {
	            $_result .= fgets($fp);
            	fclose($fp);
            	return $_result;
        	}
        	else {
        		return "xtreamer";
        	}
		//iconbit
		} else if(is_file("/etc/ICONBIT_COPYRIGHT")) {
			return "iconBIT";
		//iNext
		} else if(is_file("/usr/local/bin/Resource/model.dat")) {
			if ($fp = fopen("/usr/local/bin/Resource/model.dat", 'r')) {
	            $_result .= fgets($fp);
            	fclose($fp);
            	return $_result;
        	}
        //mos
		} else if(is_dir("/usr/local/etc/mos/www/modules")) {
			return "mos";
		} else {
			return "realtek";
		}
		return "unknown";
	}

	public static function getInstallPath() {
		$fullPath = $_SERVER["SCRIPT_FILENAME"];
		$pos = strrpos($fullPath, "/");
		if(FALSE === $pos) {
			return $fullPath; 
		}
		return substr($fullPath, 0, $pos+1);
	}

	public static function getVersion() {
		$ttVersion =  "1.?.?";
		try {
			$verFile = getcwd() . '/version.xml';
			if (file_exists($verFile)) {
				$verXml  = simplexml_load_file($verFile);
				if(false != $verXml) {
					$ttVersion = (string)$verXml->version;
				} 
			}
		} catch(Exception $e) {
		}
		return $ttVersion;
	}

	public static function getLastVersion() {
		$ttVersion =  "1.?.?";
		try {
			$verFile =  'http://tvontop.com/update/version.php';
			$verXml  = simplexml_load_file($verFile);
			if(false != $verXml) {
				$ttVersion = (string)$verXml->version;
			} 
		} catch(Exception $e) {}
		return $ttVersion;
	}

	public static function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	public static function getFullSysInfo() {
		$info = Array ("Mac" => self::getMac(),
							"FoothPrint" => self::getFoothPrint(),
							"InstallPath" => self::getInstallPath(),
							"Platform" => self::getPlatform(),
							"PlatformExt" => self::getPlatformExt(),
							"SysInfo" => self::getSysinfo(),
							"Version" => self::getVersion(),
							"UserAgent" => self::getUserAgent()
							); 
		
		return $info;
	}
}
?>