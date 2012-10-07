<?php
#############################################################################
# Author: consros 2012                                                      #
# Author: agent_wowa 2012                                                   #
#############################################################################

abstract class GtvException extends Exception {
    protected $details;
    public function __construct($message, $details = null, $code = null) {
        global $lang;
        parent::__construct($lang->msg($message), $code);
        $this->details = $lang->msg($details);
    }
    public function getDetails() {
        return $this->details;
    }
    public function __toString() {
        $det = $this->getDetails();
        return $this->getMessage() . (null == $det || '' == $det ? '' : " (" . $det . ")");
    }
}

class BadRequestException extends GtvException {
    public function __construct($details = null) {
        parent::__construct("Bad request", $details);
    }
}

class AuthException extends GtvException {
    public function __construct($details = null) {
        parent::__construct("Wrong authentication data", $details);
    }
}

class NetworkException extends GtvException {
    public function __construct($details = null) {
        parent::__construct("Connection error", $details);
    }
}

class ParseException extends GtvException {
    public function __construct($details = null) {
        parent::__construct("Cannot parse data", $details);
    }
}

class RenderException extends GtvException {
    public function __construct($details = null) {
        parent::__construct("Cannot render template", $details);
    }
}

class GtvRuntimeException extends GtvException {
    public function __construct($msg, $details = null) {
        parent::__construct($msg, $details);
    }
}
?>