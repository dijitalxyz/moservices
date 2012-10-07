<?php
#############################################################################
# Author: consros 2011                                                      #
#############################################################################

require_once 'templatable.inc';

class Message extends Templatable {

    protected $caption;
    protected $text;
    protected $image;
    protected $forwardUrl;

    public function __construct($caption, $text, $image = null, $fwd = null) {
        $this->caption    = $caption;
        $this->text       = $text;
        $this->image      = $image;
        $this->forwardUrl = $fwd;
        $this->setSupportedTemplates('message');
    }

    public function getText() {
        return $this->text;
    }
    public function setText($text) {
        $this->text = $text;
    }

    public function getCaption() {
        return $this->caption;
    }
    public function setCaption($caption) {
        $this->caption = $caption;
    }

    public function getImage() {
        return $this->image;
    }
    public function setImage($image) {
        $this->image = $image;
    }

    public function getForwardUrl() {
        return $this->forwardUrl;
    }
    public function setForwardUrl($fwd) {
        $this->forwardUrl = $fwd;
    }

    public function __toString() {
        return $this->caption . ": " . $this->text;
    }
}
?>
