<?php

class MediaObject {
	var $id;
	var $type;
	var $title;
	var $params = array();
	var $expireDate = -1;
	var $childObjects = array();
	var $parentObject;
    var $displayParams = array(); # name => size (most likely width)
	protected $sortParam  = null;
	
	function MediaObject($id, $type, $title = "") {
		$this->id    = $id;
		$this->type  = $type;
		$this->title = $title;
        $this->params['id']    = $id;
        $this->params['type']  = $type;
	    $this->params['title'] = $title;
	}
	
	function getExpireDate() {
		return $this->expireDate;
	}
	function setExpireDate($date) {
		$this->expireDate = $date;
	}
	
	//better way to set expireDate
	function setTtl($ttlSeconds) {
		$this->setExpireDate(time() + $ttlSeconds);
	}

	function cloneObj($withChildren = false) {
		$co= new MediaObject($this->getId(), $this->getType(),$this->getTitle());
		$co->params       = $this->getParams();
		$co->expireDate   = $this->getExpireDate();
		$co->parentObject = $this->getParent();
		if ($withChildren) {
			$co->childObjects = $this->getChildren();
		}
		return $co;
	}

	function addChild(MediaObject $child, MediaObject $parent = null ) {
		$this->childObjects[] = $child;
		if(null == $parent ) {
			$parent = $this;
		}
		//set parent obj without children to avoid cycled tree
		$child->setParent($parent->cloneObj());
	}

	function addChildren($children, MediaObject $parent = null) {
		foreach ($children as $child) {
			$this->addChild($child, $parent);
		}
	}

	function getChildCount() {
		return count($this->childObjects);
	}

	function getChildren() {
		return $this->childObjects;
	}
	
	function getId() {
		return $this->id; 	
	}

	function getType() {
		return $this->type; 	
	}

	function getTitle() {
		return $this->title; 	
	}

	function getParams() {
		return $this->params;
	}

	function setParam($name, $value) {
        $name = strtolower($name);
        if ($name == 'id' || $name == 'type' || $name == 'title') {
        	throw new Exception("Cannot set $name field!");
        } else if ($name == 'expireDate') {
            $this->setExpireDate($value);
        }
		$this->params[$name] = $value;
	}

	function getParam($name) {
		$name = strtolower($name);
		if ($name == 'id') {
			return $this->getId();
        } else if ($name == 'type') {
            return $this->getType();
        } else if ($name == 'title') {
            return $this->getTitle();
        } else if ($name == 'expireDate') {
            return $this->getExpireDate();
        }
        if(isset($this->params[$name]))
			return $this->params[$name];
		return null;
	}

	function hasChildren() {
		return count($this->childObjects) > 0 ;
	}

	function getFirstChild() {
        return 0 == count($this->childObjects) ? null : $this->childObjects[0];
    }

    function getLastChild() {
        $length = count($this->childObjects);
        return 0 == $length ? null : $this->childObjects[$length - 1];
    }

    function setParent($parentObject) {
		$this->parentObject = $parentObject;
	}

	function getParent() {
		return $this->parentObject;
	}

	function addDisplayParam($name, $size = 10) {
        $this->displayParams[$name] = $size;
    }

    function getDisplayParams() {
        return $this->displayParams;
    }

    function getTitleSize() {
    	$titleSize = 100;
    	foreach ($this->displayParams as $param => $size) {
    		$titleSize -= $size;
    	}
    	return $titleSize;
    }

	function sortChildren($param = 'title') {
        if (func_num_args() <= 1) {
            $this->sortParam = $param;
            $func = "cmpSingle";
		} else {
            $this->sortParam = func_get_args();
            $func = "cmpArray";
		}
        uasort($this->childObjects, array($this, $func));
	}
	
    function cmpArray($a, $b) {
    	foreach ($this->sortParam as $param) {
            $res = $this->cmpByParam($a, $b, $param);
    		if (0 != $res) {
    			return $res;
    		}
    	}
        return 0;
    }
    
    function cmpSingle($a, $b) {
        return $this->cmpByParam($a, $b, $this->sortParam);
    }
    
    function cmpByParam($a, $b, $param) {
    	$asc = 1;
    	if ('!' == $param[0]) {
    		$asc = -1;
    		$param = substr($param, 1);
    	}
    	$p1 = $a->getParam($param);
    	$p2 = $b->getParam($param);
    	if (is_string($p1)) {
    		$res = strcasecmp($p1, $p2);
    	} else {
    		$res = $p1 == $p2 ? 0 : ($p1 > $p2 ? 1 : -1);
    	}
    	return $asc * $res;    	
    }
}
?>
