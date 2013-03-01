<?php
/**
 * FileManager class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class FileManager extends CApplicationComponent {
	
	private $_basePath;
	private $_thumbBasePath;
	private $_domains;
	
	public function setBasePath($path) {
		if ($path[0] == '/') {
			$this->_basePath = $path;
		}else {
			$this->_basePath = realpath(Yii::app()->getBasePath() . '/../' . $path);
		}
		return $this;
	}
	
	public function getBasePath() {
		if ($this->_basePath === null) {
			$this->_basePath = realpath(Yii::app()->getBasePath() . '/../' . 'uploads');
		}
		return $this->_basePath;
	}
	
	public function setDomains($domains) {
		$this->_domains = $domains;
		return $this;
	}
	
	public function getDomains() {
		return $this->_domains;
	}
	
	/**
	 * Get the path of a domain.
	 * 
	 * @param string $domain
	 * @return string
	 */
	public function getPathOfDomain($domain) {
		$path = $this->getBasePath();
		$domains = $this->getDomains();
		if (!isset($domains[$domain])) {
			throw new CException("Domain {{$domain}} is unset, please define it before using.");
		}
		$path .= '/' . $domains[$domain];
		return $path;
	}
	
	public function getUrlOfDomain($domain) {
		$path = $this->getPathOfDomain($domain);
		$base = Yii::app()->getBasePath() . '/../';
		return str_replace(realpath($base), '', $path);
	}
	
	/**
	 * Set the base path of thumbnail images to save.
	 * 
	 * @param string $path
	 * 
	 */
	public function setThumbBasePath($path) {
		if ($path[0] == '/') {
			$this->_thumbBasePath = realpath($path);
		}else {
			$this->_thumbBasePath = realpath(Yii::app()->getBasePath() . '/../' . $path);
		}
		return $this;
	}
	
	/**
	 * Get the thumbnail base path, defaults to iamges.
	 * 
	 * @return string
	 */
	public function getThumbBasePath() {
		if ($this->_thumbBasePath === null) {
			$this->_thumbBasePath = realpath(Yii::app()->getBasePath() . '/../images');
		}
		return $this->_thumbBasePath;
	}
	
	public function getThumbBaseUrl() {
		$path = $this->getThumbBasePath();
		$base = Yii::app()->getBasePath() . '/../';
		return str_replace(realpath($base), '', $path);
	}
	
}
