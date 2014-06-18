<?php
class SysInfo {

    public static function getMac() {
        $dump = exec('/sbin/ifconfig | grep thern | grep eth');
        return preg_match('/..:..:..:..:..:../', $dump, $mac) ?
            $mac[0] : '--:--:--:--:--:--';
    }

    public static function getSysinfo() {
        return php_uname();
    }

    public static function isDevEnvironment() {
        return false !== stripos(php_uname(), 'windows');
    }

    public static function isOldPlatform($platform = null) {
        $platform = empty($platform) ? self::getPlatform() : $platform;
        $new = in_array($platform, array('118x', 'iconbit'));
        return ! $new;
    }

    public static function getPlatform() {
        $ext = self::getPlatformExt();
        if (self::isDevEnvironment()) {
            return '118x';
        } else if (false !== stripos($ext, 'prodigy')) {
            return '118x';
        } else if (false !== stripos($ext, 'sidewinder3')) {
            return '118x';
        } else if (false !== stripos($ext, 'xtreamer')) {
            return 'xtreamer';
        } else if (false !== stripos($ext, 'iconbit')) {
            return 'iconbit';
        } else if (false !== stripos($ext, 'inext')) {
            return '118x';
        }
        return 'realtek';
    }

    public static function getPlatformExt() {
        $boardId = @trim(@file_get_contents('/sys/realtek_boards/board_id'));
        if (is_readable('/tmp/model')) {
            return str_replace('XTREAMER', 'Xtreamer ',
                @trim(@file_get_contents('/tmp/model')));
        } else if (is_file('/usr/local/bin/scripts/settings/img/xtreamer_logo.png')) {
            return 'Xtreamer ' . (is_file('/sbin.android') ? 'Prodigy' : 'SW2');
        } else if (is_file('/etc/ICONBIT_COPYRIGHT')) {
            return 'iconBIT ' . @trim(@file_get_contents(
                '/etc/VENDOR_NAME'));
        } else if (is_readable('/usr/local/bin/Resource/model.dat')) {
            return strtolower(@trim(@file_get_contents(
                '/usr/local/bin/Resource/model.dat')));
        } else if (is_dir('/usr/local/etc/mos/www/modules')) {
            return 'mos ' . $boardId;
        } else if (is_dir('/sbin/www')) {
            return 'Xtreamer Unknown ' . $boardId;
        }
        return 'realtek ' . $boardId;
    }

    public static function getInstallPath() {
        $fullPath = dirname(dirname(__FILE__));
        $pos = strrpos($fullPath, '/');
        return false === $pos ? $fullPath : substr($fullPath, 0, $pos + 1);
    }

    public static function getProjectName() {
        $verFile = getcwd() . '/version.xml';
        if (is_readable($verFile)) {
            $xml = @simplexml_load_file($verFile);
            if (false !== $xml && isset($xml->name)) {
                return (string)$xml->name;
            }
        }
        return 'GlavTV';
    }

    public static function getVersion() {
        $verFile = getcwd() . '/version.xml';
        if (is_readable($verFile)) {
            $xml = @simplexml_load_file($verFile);
            if (false !== $xml && isset($xml->version)) {
                return (string)$xml->version;
            }
        }
        return 'Installer';
    }

    public static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function getFullSysInfo() {
        return array(
            'Mac'         => self::getMac(),
            'InstallPath' => self::getInstallPath(),
            'Platform'    => self::getPlatform(),
            'PlatformExt' => self::getPlatformExt(),
            'SysInfo'     => self::getSysinfo(),
            'Version'     => self::getVersion(),
            'UserAgent'   => self::getUserAgent());
    }
}
?>
