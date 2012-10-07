<?php
#############################################################################
# One of main data describing classes: channel item representation.         #
# Fields are combination of RRS2 and Media RSS specs.                       #
#                                                                           #
# For more information about fields see this following specs:               #
# - English (RSS2): http://www.rssboard.org/rss-specification               #
# - Russian (RSS2): http://beshenov.ru/rss2.html                            #
# - English (Media RSS): http://www.rssboard.org/media-rss                  #
# - Russian (Media RSS): http://mainsource.ru/info/articles/a-55/           #
#                                                                           #
# RSS2 standard, used fields (item):                                        #
# - (required) title, link, description                                     #
# - (optional) category, enclosure                                          #
#                                                                           #
# RSS2 standard, ignored fields (item):                                     #
# - author, comments, guid, pubDate, source                                 #
#                                                                           #
# Media RSS standard, used fields:                                          #
# - (optional) thumbnail, rating                                            #
#                                                                           #
# Besides of these params there are also Extra Params.                      #
# They are not a part of any spec and will be shown in resulting RSS        #
# only if corresponding template will specially take care of them.          #
#                                                                           #
# Normally all the templates are optimized to show only the params          #
# listed above, which are part of RSS2 or Media RSS specs.                  #
#                                                                           #
#                                                                           #
# Author: consros 2011                                                      #
#############################################################################

class Item {
    /* required params, according to RSS2 spec. */
    const TITLE       = 'title';
    const LINK        = 'link';
    const DESCRIPTION = 'description';

    /* optional params, according to RSS2 spec. */
    const CATEGORY    = 'category';
    const ENCLOSURE   = 'enclosure';

    /* optional params, according to Media RSS spec. */
    const THUMBNAIL   = 'media:thumbnail'; // simple thumbnale is not allowed
    const ICON        = 'icon'; // simple icon, prefer the THUMBNALE field!
    const RATING      = 'rating';

    /* specially handled video types. */
    const REDIRECT    = 'redirect';
    const VIDEOSCRIPT = 'videoScript';

    /* most common extra params. */
    const ID          = 'id';
    const INFO        = 'info';
    const GENRES      = 'genres';
    const YEAR        = 'year';
    const TIME        = 'time';
    const START_TIME  = 'startTime';
    const STOP_TIME   = 'stopTime';
    const LENGTH      = 'timelength'; // simple length is not allowed
    const QUALITY     = 'quality';
    const COUNTRY     = 'country';

    /* array of subitems. */
    const SUBITEMS    = 'subitems';


    /* official params, according to RSS2 and Media RSS specs. */
    protected $params = array();

    /* official enclosures, according to RSS2 and Media RSS specs. */
    protected $enclosures = array();

    /* set it when item provides search support. */
    protected $searchUrl = null;

    public function __construct($title, $description = '') {
        $this->set(self::TITLE, $title);
        $this->set(self::DESCRIPTION, $description);
    }

    public function get($name, $defValue = null) {
        return isset($this->params[$name]) ? $this->params[$name] : $defValue;
    }
    public function set($name, $value) {
        if (null == $value || '' === $value) {
            unset($this->params[$name]);
        } else {
            $this->params[$name] = $value;
        }
    }
    public function getParams() {
        return $this->params;
    }

    public function addEnclosure($url, $type) {
        $this->enclosures[$url] = $type;
    }

    public function getEnclosures() {
        return $this->enclosures;
    }

    public function getSearchUrl() {
        return $this->searchUrl;
    }

    public function addSearchSupport($searchUrl) {
        $this->searchUrl = $searchUrl;
    }

    public function __toString() {
        return $this->toString();
    }

    public function toString($spaces = '') {
    	$str = $spaces . '<item>' . PHP_EOL;
        $tab = '    ';
        foreach ($this->params as $name => $value) {
            if ($name == self::SUBITEMS) {
                foreach ($value as $item) {
                    if (null != $item) {
                        $str .= $item->toString($spaces . $tab);
                    }
                }
            } else if (! empty($value) && ! is_array($value)) {
                $value = is_string($value) ? stripslashes($value) : $value;
                $str .= $spaces . $tab . ((false !== strpos($name, 'media:') || 'icon' == $name) ?
                    "<$name url=\"$value\" />" : "<$name>" . stripslashes($value) . "</$name>") .
                    PHP_EOL;
            }
        }
        if (isset($this->searchUrl)) {
            global $lang;
            $str .= $spaces . $tab . '<enclosure url="' . $this->searchUrl .
                '" type="input" title="' . $lang->msg('Search') . '"/>' . PHP_EOL;
        }
        foreach ($this->enclosures as $url => $type) {
            $str .= $spaces . $tab .
                "<enclosure url=\"$url\" type=\"$type\" />" . PHP_EOL;
        }
        $str .= $spaces . '</item>' . PHP_EOL;
        return $str;
    }
}
?>
