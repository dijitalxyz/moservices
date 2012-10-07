<?php
#############################################################################
# Custom logger with output formatting and named loggers support.           #
# Can be used either to write to file or to stdout.                         #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

class Logger {
    public static $LEVELS = array('OFF', 'FATAL', 'ERROR', 'WARN', 'INFO', 'DEBUG');

    protected static $loggers         = array();
    protected static $defaultLevel    = 0;
    protected static $defaultFilepath = null;

    protected $id;
    protected $level;
    protected $filepath;

    public static function init(&$cfg) {
        $value = $cfg->get('logLevel');
        if (isset($value)) {
            self::setDefaultLevel($value);
        }
        $value = $cfg->get('logFile');
        if (isset($value)) {
            self::setDefaultFilepath($value);
        } else {
            $value = $cfg->get('cfgPath');
            if (isset($value)) {
                self::setDefaultFilepath($value . '/log.txt');
            }
        }
        foreach (self::$loggers as $id => $logger) {
            $value = $cfg->getFromSection($id, 'level');
            if (isset($value)) {
                $logger->setLevel($value);
            }
            $value = $cfg->getFromSection($id, 'filepath');
            if (isset($value)) {
                $logger->setFilepath($value);
            }
        }
    }

    public static function getLogger($id = 'ROOT') {
        if (! isset(self::$loggers[$id])) {
            self::$loggers[$id] = new Logger($id);
        }
        return self::$loggers[$id];
    }

    public static function getDefaultLevel() {
        return self::$defaultLevel;
    }
    public static function setDefaultLevel($level) {
        if ($level < 0) {
            $level = 0;
        } else if ($level >= count(self::$LEVELS)) {
            $level = count(self::$LEVELS) - 1;
        }
        self::$defaultLevel = $level;
    }
    public static function getDefaultFilepath() {
        return self::$defaultFilepath;
    }
    public static function setDefaultFilepath($filepath) {
        self::$defaultFilepath = $filepath;
    }

    private function __construct($id) {
        $this->id       = $id;
        $this->level    = self::$defaultLevel;
        $this->filepath = self::$defaultFilepath;
    }

    public function getLevel() {
        return $this->level;
    }
    public function setLevel($level) {
        if ($level < 0) {
            $level = 0;
        } else if ($level >= count(self::$LEVELS)) {
            $level = count(self::$LEVELS) - 1;
        }
        $this->level = $level;
    }
    public function getFilepath() {
        return $this->filepath;
    }
    public function setFilepath($filepath) {
        $this->filepath = $filepath;
    }

    public function debug($message) {
        $this->writeMessage($message, 5);
    }
    public function info($message) {
        $this->writeMessage($message, 4);
    }
    public function warn($message) {
        $this->writeMessage($message, 3);
    }
    public function error($message) {
        $this->writeMessage($message, 2);
    }
    public function fatal($message) {
        $this->writeMessage($message, 1);
    }

    protected function formatMessage($msg, $level) {
        $mtime = explode(' ', microtime());
        return sprintf('[%s.%03d %-5s] %s: %s', date('Y-m-d H:i:s', $mtime[1]),
            (int) ($mtime[0] * 1000), self::$LEVELS[$level], $this->id, $msg);
    }

    protected function writeMessage($msg, $level = 4){
        if ($level < 1 || $level > $this->level) {
            return;
        }

        // first format the message
        $str = $this->formatMessage($msg, $level) . PHP_EOL;

        // print to stdout if no file specified
        if (! isset($this->filepath) || '' == $this->filepath) {
            print $str;
        } else {
            // open and write the file
            if (! is_dir(dirname($this->filepath))) {
                mkdir(dirname($this->filepath), 0755, true);
            }
            $fd = fopen($this->filepath, 'a');
            if (false !== $fd) {
                fwrite($fd, $str);
                fclose($fd);
            }
        }
    }
}
?>
