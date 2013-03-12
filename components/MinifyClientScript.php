<?php
/**
 * MinifyClientScript class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Minify your client scripts.
 */
class MinifyClientScript extends ClientScript {
	
	public $debug	= false;
	public $mergeCss = true;
	public $mergeJs = true;

	private $cachePath;
	private $cachedScripts = array();
	
	public function setCachePath($path) {
		if ($path[0] == '/') {
			$this->cachePath = $path;
		}else {
			$this->cachePath = realpath(Yii::app()->getBasePath() . '/../assets');
		}
		if (!is_writable($path)) {
			throw new CException(sprintf('Cache path "%s" unwriteable.', $this->cachePath));
		}
	}
	
	public function getCachePath() {
		if ($this->cachePath == null) {
			$this->cachePath = realpath(Yii::getPathOfAlias('application') . '/../assets');
		}
		return $this->cachePath;
	}
	
	public function getCacheUrl() {
		$base = Yii::app()->getBasePath() . '/../';
		return str_replace(realpath($base), '', $this->getCachePath());
	}
	
	/**
	 * @param string $uri
	 * @return string
	 */
	protected function getContentFromUri($uri) {
		$file = realpath(Yii::app()->getBasePath() . '/../') . $uri;
		if (file_exists($file)) {
			return file_get_contents($file);
		}else {
			return '';
		}
	}
	
	/*
	public function registerScriptFile($url, $position = null) {
		if($position===null) {
			$position=$this->defaultScriptFilePosition;
		}
		if ($this->debug) {
			return parent::registerScriptFile($url, $position);
		}
		
		$toUrl = $this->resolveCachedScriptFileUri($position);
		$this->mergeJs($toUrl, $url);
		
		$this->hasScripts=true;
		$this->scriptFiles[$position][$toUrl]=$toUrl;
		
		$params=func_get_args();
		$this->recordCachingAction('clientScript','registerScriptFile',$params);
		return $this;
	}
	*/
	
	
	protected function preRenderHead() {
		if (isset($this->scriptFiles[self::POS_HEAD])) {
			$str = '';
			$scripts = array();
			foreach ($this->scriptFiles[self::POS_HEAD] as $key => $file) {
				if (strpos($file, 'http') !== 0) {
					$str .= $file . '|';
					$scripts[] = $file;
					unset($this->scriptFiles[self::POS_HEAD][$key]);
				}
			}
			$str = rtrim($str, '|');
			$name = md5($str) . '.js';
			$file = $this->getCachePath() . '/' . $name;
			if (!file_exists($file)) {
				$content = '';
				foreach ($scripts as $script) {
					$content .= $this->getContentFromUri($script);
				}
				file_put_contents($file, $content);
			}
			$this->scriptFiles[self::POS_HEAD][] = $this->getCacheUrl() . '/' . $name;
		}
			
		$strs = array();
		$cssFiles = array();
		foreach ($this->cssFiles as $url => $media) {
			if (strpos($url, 'http') !== 0) {
				$str = &$strs[$media];
				$str.= $url . '|';
				$cssFiles[$media][] = $url;
			}
		}
		unset($str);
		$this->cssFiles = array();
		foreach ($strs as $media => $str) {
			$str = rtrim($str, '|');
			$name = md5($str) . '.css';
			$file = $this->getCachePath() . '/' . $name;
			if (!file_exists($file)) {
				$content = '';
				foreach ($cssFiles[$media] as $cssFile) {
					$content .= $this->getContentFromUri($cssFile);
				}
				file_put_contents($file, $content);
			}
			$this->cssFiles[$this->getCacheUrl() . '/' . $name] = $media;
		}
	}
	
	public function renderHead(&$output) {
		if (!$this->debug) {
			$this->preRenderHead();
		}
		parent::renderHead($output);
	}
	
	public function __destruct() {
		/*
		var_dump($this->coreScripts);
		var_dump($this->cssFiles);
		var_dump($this->scriptFiles);
		*/
	}
}
