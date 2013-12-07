<?php namespace ecom\image;
/**
 * ImageManager class file.
 * 
 * @link https://github.com/bixuehujin/ecom
 * @copyright MIT
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\FileManager;

/**
 * ImageManager application component, the enhancement of FileManager, provides more options for handling images.
 * 
 * 
 */
class ImageManager extends FileManager {
	
	private $_thumbBasePath;
	private $_thumbBaseUrl;
	
	public $managedClass = 'ecom\image\model\ImageManaged';
	
	/**
	 * Set the base path of thumbnail images to save.
	 *
	 * @param string $path
	 *
	 */
	public function setThumbBasePath($path) {
		if (($rpath = realpath($path)) !== false && is_dir($rpath) && is_writable($rpath)) {
			$this->_thumbBasePath = $rpath;
		}else {
			throw new \CException("The path '$path' is invalid, make sure it is a valid directory and writable by your web process.");
		}
	}
	
	/**
	 * Get the thumbnail base path, defaults to iamges.
	 *
	 * @return string
	 */
	public function getThumbBasePath() {
		if ($this->_thumbBasePath === null) {
			$path = Yii::app()->getBasePath() . '/../images';
			
			if (($rpath = realpath($path)) !== false && is_dir($rpath) && is_writable($rpath)) {
				$this->_thumbBasePath = $rpath;
			}else {
				throw new \CException("The path '$path' is invalid, make sure it is a valid directory and writable by your web process.");
			}
		}
		return $this->_thumbBasePath;
	}
	
	public function getThumbPathOfDomain($domain) {
		$this->checkDomain($domain);
		
		return $this->getThumbBasePath() . '/' . $domain;
	}
	
	/**
	 * Returns the base url of gereneted thumbnail images.
	 * 
	 * @return string
	 */
	public function getThumbBaseUrl() {
		if ($this->_thumbBaseUrl === null) {
			$path = $this->getThumbBasePath();
			$base = Yii::app()->getBasePath() . '/../';
			$this->_thumbBaseUrl = str_replace(realpath($base), '', $path);
		}
		return $this->_thumbBaseUrl;
	}
	
	public function getThumbUrlOfDomain($domain) {
		$this->checkDomain($domain);
		
		return $this->getThumbBaseUrl() . '/' . $domain;
	}
}
