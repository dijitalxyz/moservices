<?php
#############################################################################
# Author: consros    2011                                                   #
# Author: agent_wowa 2011                                                   #
#############################################################################

require_once 'tools/http.inc';
require_once 'tools/parser.inc';

class Backgrounds {

    public function getBackgrounds() {
        return array_merge(
            $this->getDeviantArtBackgrounds(),
            $this->getDeviantArtBackgrounds(),
            $this->getDeviantArtBackgrounds());
    }

    public function getDeviantArtBackgrounds() {
        $http = new HttpTools();
        $images = array();
        $url  = 'http://browse.deviantart.com/photography/?order=9&offset=';
        $url .= rand(1, 2000);
        $html = $http->sendGetRequest($url);
        $html = ParserTools::parseParam($html, '<div class="tt-a', 'feedbutton');
        return ParserTools::parseTokens($html, 'super_img="', '"');
    }
}
?>
