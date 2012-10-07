<?php
require_once 'interfaces/provider.inc';
require_once 'tools/logger.inc';

class ImgCacheProvider extends Provider {

	protected $cachePath;
	protected $imgAttributes;
	protected $maxCachedImagesAtOnce;
	protected $cacheLimitCnt;
	protected $setFileSizes;
	protected $cacheImages;
	protected $internalUrls;

	function ImgCacheProvider() {
        $this->log = Logger::getLogger(get_class());
	}
    public function init(Configuration &$cfg) {
        parent::init($cfg);
        if(null != $cfg) {
        	$tempDir = $this->config->get("temp_dir","", "temp/");
        	$this->cachePath = $cfg->get("img_cache_path",null,$tempDir."img/");
        	$this->maxCachedImagesAtOnce = $cfg->get("maxCachedImagesAtOnce",null,10);
        	//TODO: change icon parameter of KTV
        	$attrList = $cfg->get("img_attributes",null,"thumbnail,img");
        	$this->imgAttributes = explode(",", $attrList);
        	$this->imgAttributes = explode(",", $attrList);
        	$this->setFileSizes = $cfg->get("setImageSizes",null,true);
        	$this->cacheImages = $cfg->get("cacheImages",null,false);
        	$this->internalUrls = $cfg->get("internalUrls",null,true);
        } else {
        	$this->cachePath = "temp/img/";
        	$this->imgAttributes = array('thumbnail','img');
        	$this->maxCachedImagesAtOnce = 50;
        	$this->setFileSizes = true;
        	$this->cacheImages = false;
        	$this->internalUrls = true;
        }

		$service = $this->config->get("currentService","Runtime");
		if(isset($service) && "" != $service) {
			$this->cachePath .= $service . "/";
		}

        if(!is_dir($this->cachePath)) {
        	mkdir($this->cachePath,0666, true);
        }
    }

	public function getObject($objectId, $params) {
		if("image" == $objectId && isset($params['img_cache'])) {
			$obj = new MediaObject("", "EMPTY");
			$url = urldecode($params['img_cache']);
			$fileName  = $this->getCacheFileName($url);

			// do not reread from server if image is already cached
			if (is_readable($filename) && 0 != filesize($fileName)) {
			    $image_content = file_get_contents($fileName);
			} else {
			    //read image from server
			    $image_content = file_get_contents($url);
			    //save image  to cache
			    file_put_contents($fileName, $image_content);
			}

			$extension = strtolower(substr($url, strripos($url, ".")+1));
			if($extension == "jpg") {
				$extension  = "jpeg";
			} else if($extension == "tif") {
				$extension  = "tiff";
			}

			//write image to response
			header('Content-type: image/'.$extension);
			echo $image_content;
   			flush();
			return $obj;
		} else {
			$newObj = $this->getSubprovider()->getObject($objectId, $params);
			$this->cacheLimitCnt = $this->maxCachedImagesAtOnce;
			//return $this->cacheImages($newObj );
			return $this->cacheImagesOnTheFly($newObj);
		}
	}

	protected function getCacheFileName($url) {
		$md5Hash = md5($url);
		$ext = substr($url, strripos($url, ".")); 
		return $this->cachePath.$md5Hash.$ext;
	}

	public function cacheImagesOnTheFly($obj) {
		if (! $this->cacheImages) {
			return $obj;
		}
		foreach($this->imgAttributes as $attr) {
			$url = $obj->getParam($attr);
			if(isset($url)) {
				$fileName  = $this->getCacheFileName($url);
				if(!file_exists($fileName) || filesize($fileName) == 0 || ! $this->internalUrls) {
					//replace url with our cache-url
					$obj->setParam($attr,$this->formatFetchUrl($url));
				} else {
					// replace url with direct link
					$obj->setParam($attr,$fileName);
				}
			}
		}
		foreach($obj->getChildren() as $child) {
			$this->cacheImagesOnTheFly($child);
		}
		return $obj;
	}

	protected function formatFetchUrl($origUrl) {
		$url = $this->config->get(Configuration::$KEY_PROCESSING_URL, Configuration::$RUNTIME_SECTION);
		$url .= "&amp;id=image&amp;img_cache=".urlencode($origUrl);
		return $url;
	} 
}