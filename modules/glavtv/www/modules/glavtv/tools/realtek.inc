<?php
#############################################################################
# Author: consros    2011                                                   #
#############################################################################

require_once 'interfaces/exceptions.inc';

class Realtek {

    public static function execCmd($cmd, $silent = true) {
        $last = exec($cmd, $out, $res);
        if (0 != $res && ! $silent) {
            global $lang;
            $error = empty($out) ? $res : implode('<br />', $out);
            throw new GtvRuntimeException($lang->msg('Failed') .
                substr(" $cmd", 0, min(strlen($cmd), 15)), $error);
        }
    }

    public static function fixAllPlatformTroubles() {
        self::resolveVkCom();
        self::resyncTime();
        self::setFilePermissions();
    }

    public static function setFilePermissions() {
        global $cfg;
        $file = $cfg->get('home_disk') . 'tools/rtmpdump';
        self::execCmd("chmod 755 $file");
    }

    public static function resolveVkCom() {
        $host = 'login.vk.com';
        $ip = @gethostbyname($host);
        if (! empty($ip) && $ip != $host) {
            return true;
        }
        $hosts = @file_get_contents('http://glavnee.net/tmp/hosts.txt', false);
        if (false === strpos($hosts, $host)) {
            return false;
        }
        self::execCmd("echo \"$hosts\" >> /etc/hosts");
        $ip = @gethostbyname($host);
        return ! empty($ip) && $ip != $host;
    }

    public static function resyncTime($force = false) {
        $time = @file_get_contents('http://glavnee.net/tmp/time.php', false);
        if (empty($time)) {
            return;
        }
        $zone = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');
        $time = strtotime($time);

        if (! empty($time) && ($force || abs($time - time()) > 60*60*24*3)) {
            self::execCmd('date ' . date('mdHiY.s', $time));
            self::execCmd('hwclock -w');

            global $cfg;
            header('Location: ' . $cfg->get('home_url'));
            die('');
            # throw new GtvRuntimeException(
            #    'System time was properly set. Error is corrected');
        }
        date_default_timezone_set($zone);
    }
}
?>