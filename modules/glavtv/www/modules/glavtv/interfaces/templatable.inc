<?php
#############################################################################
# Definition of structure able to be displayed using template.              #
# Any class of object returned by a service should be inherited             #
# from this class.                                                          #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

abstract class Templatable {

    /* RSS templates supported by this object. */
    protected $supportedTemplates = array();

    public function getSupportedTemplates() {
        return $this->supportedTemplates;
    }

    public function setSupportedTemplates($templates) {
        $this->supportedTemplates = is_array($templates) ? $templates :
            func_get_args();
    }
}
?>
