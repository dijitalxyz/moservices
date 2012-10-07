<?php
#require_once 'tools/logger.inc';
class Localization {
    var $messages;
    var $images;
    var $lang;

//    var $image_base_path = '/usr/local/www/images';
//    var $image_base_url = '/images';

    function msg_orig($s) {
        if (isset($this->messages[$s])) {
            return $this->messages[$s];
        } else {
            //error_log("l10n error:LANG:" . "$this->lang,message:'$s'");
            return $s;
        }
    }
    function msg2() {
    	$params2 = func_get_args();	
    }
    
    function msg($s) {
		$params = func_get_args();
    	if (isset($this->messages[$params[0]])) {
            $params[0]  = $this->messages[$params[0]];
        } else {
            //error_log("l10n error:LANG:" . "$this->lang,message:'$s'");
        }    	
        if (func_num_args() >= 1) {
       	 	return call_user_func_array('sprintf', $params);
		} 
		return $params[0];
    }
         
    /*            
    function img($f) {
        if (is_readable("$this->image_base_path/" . 
            "$this->lang/$f")) {
            print "$this->image_base_url/$this->lang/$f";
        } elseif (is_readable("$this->image_base_path/" . "global/$f")) {
            print "$this->image_base_url/global/$f";
        } else {
            //error_log("l10n error:LANG:" . "$this->lang,image:'$f'");
        }
    }*/
}
?>