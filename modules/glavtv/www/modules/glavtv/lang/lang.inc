<?php
#############################################################################
# Configuration manager based on sets of ini files.                         #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

require_once 'interfaces/exceptions.inc';

class LangTools {

    protected $lang;
    protected $messages;

    public function __construct($lang) {
        $this->lang = $lang;
        $fileName = 'lang/' . $lang . '.ini';
        $this->messages = @parse_ini_file($fileName);
        if (false === $this->messages) {
            throw new ParseException($fileName + " missed");
        }
    }

    public function getLang() {
        return $this->lang;
    }

    public function addLangDir($dir) {
        $fileName = $dir . '/' . $this->lang . '.ini';
        $newMessages = @parse_ini_file($fileName);
        if (false !== $newMessages) {
            $this->messages = $newMessages + $this->messages;
            return true;
        }
        return false;
    }

    public function msg($s) {
        $params = func_get_args();
        if (isset($this->messages[$s])) {
            $params[0] = $this->messages[$s];
        }
        return func_num_args() > 1 ?
            call_user_func_array('sprintf', $params) : $params[0];
    }
}
?>
