<?php
#############################################################################
# One of main data describing classes: channel representation.              #
# Fields are combination of RRS2 and Media RSS specs.                       #
#                                                                           #
# For more information about fields see this following specs:               #
# - English (RSS2): http://www.rssboard.org/rss-specification               #
# - Russian (RSS2): http://beshenov.ru/rss2.html                            #
# - English (Media RSS): http://www.rssboard.org/media-rss                  #
# - Russian (Media RSS): http://mainsource.ru/info/articles/a-55/           #
#                                                                           #
# RSS2 standard, used fields (channel):                                     #
# - (required) title, link, description                                     #
# - (optional) category, image, language                                    #
#                                                                           #
# RSS2 standard, ignored fields (channel):                                  #
# - textInput, ttl, copyright, managingEditor, webMaster, pubDate,          #
# - lastBuildDate, generator, docs, cloud, skipHours, skipDays, rating      #
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

require_once 'templatable.inc';
require_once 'pager.inc';

class Channel extends Templatable {

    /* required params, according to RSS2 spec. */
    const TITLE       = 'title';
    const LINK        = 'link';
    const DESCRIPTION = 'description';

    /* optional params, according to RSS2 spec. */
    const CATEGORY    = 'category';
    const IMAGE       = 'image';
    const LANGUAGE    = 'language';

    /* most common extra params. */
    const THUMB_RATIO = 'thumbRatio'; // 0 - square, 1 - horizontal, 2 - vertical
    const SCREENSHOTS = 'screenshots';
    const SERVICE     = 'service';


    /* official params, according to RSS2 and Media RSS specs. */
    protected $params = array();

    /* child items, according to RSS2 spec. */
    protected $items = array();

    /* pager if channel is page splitted. */
    protected $pager = null;

    /* array of hotkeys handled by this channel. */
    protected $hotkeys = array();

    /* helper array for sorting method. */
    protected $sortParams = null;

    /* set it when channel provides search support. */
    protected $searchUrl = null;

    public function __construct($title, $description = '') {
        $this->set(self::TITLE, $title);
        $this->set(self::DESCRIPTION, $description);
    }

    public function get($name, $defValue = null) {
        return isset($this->params[$name]) ? $this->params[$name] : $defValue;
    }
    public function set($name, $value) {
        if (null === $value || '' === $value) {
            unset($this->params[$name]);
        } else {
            $this->params[$name] = $value;
        }
    }
    public function getParams() {
        return $this->params;
    }

    public function addItem($item, $addToBeginning = false) {
        if ($addToBeginning) {
            $this->items = array_merge(array($item), $this->items);
        } else {
            $this->items[] = $item;
        }
    }

    public function getItems() {
        return $this->items;
    }

    public function setItems($items) {
        $this->items = $items;
    }

    public function getItemParams() {
        $params = array();
        foreach ($this->items as $item) {
            $params += $item->getParams();
        }
        return array_keys($params);
    }

    public function hasEnclosures() {
        foreach ($this->items as $item) {
            if (0 != count($item->getEnclosures())) {
                return true;
            }
        }
        return false;
    }

    public function getPager() {
        return $this->pager;
    }

    public function setPager($pager) {
        $this->pager = $pager;
    }

    public function getHotkeys() {
        return $this->hotkeys;
    }

    public function addHotkey($keyId, $title, $url, $itemParams = array()) {
        if (! isset($this->hotkeys[$keyId])) {
            $this->hotkeys[$keyId] = array(
                'title' => $title, 'url' => $url, 'itemParams' => $itemParams);
        }
    }

    public function getSearchUrl() {
        return $this->searchUrl;
    }

    public function addSearchSupport($searchUrl = null) {
        if (null == $searchUrl) {
            global $cfg;
            $searchUrl = $cfg->get('service_url') . '&req=search';
        }
        $this->searchUrl = $searchUrl;
    }

    public function __toString() {
        return $this->toString();
    }

    public function toString($spaces = '') {
        $str = $spaces . '<channel>' . PHP_EOL;
        $tab = '    ';
        foreach ($this->params as $name => $value) {
            if (! empty($value) && ! is_array($value)) {
                $str .= $spaces . $tab . "<$name>";
                $str .= is_string($value) ? stripslashes($value) : $value;
                $str .= "</$name>" . PHP_EOL;
            }
        }
        if (isset($this->searchUrl)) {
            global $lang;
            $str .= $spaces . $tab . '<enclosure url="' . $this->searchUrl .
                '" type="input" title="' . $lang->msg('Search') . '"/>' . PHP_EOL;
        }
        foreach ($this->items as $item) {
            if (null != $item) {
                $str .= $item->toString($spaces . $tab);
            }
        }
        $str .= $spaces . '</channel>' . PHP_EOL;
        return $str;
    }

    public function sortItems($param = Item::TITLE) {
        $this->sortParams = 0 == func_num_args() ?
            explode(',', $param) : func_get_args();
        uasort($this->items, array($this, 'cmpItems'));
    }

    public function cmpItems($item1, $item2) {
        foreach ($this->sortParams as $param) {
            $asc = 1;
            if ('!' == $param[0]) {
                $asc = -1;
                $param = substr($param, 1);
            }
            $p1 = $item1->get($param);
            $p2 = $item2->get($param);
            $res = is_string($p1) ? strcasecmp($p1, $p2) :
                ($p1 == $p2 ? 0 : ($p1 > $p2 ? 1 : -1));
            if (0 != $res) {
                return $asc * $res;
            }
        }
        return 0;
    }
}
?>
