<?php namespace ecom\image\model;
/**
 * ImageManaged class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ecom\file\model\FileManaged;
use ecom\image\ImageManagedInterface;
use PHPThumb\GD;

/**
 * 
 */
class ImageManaged extends FileManaged implements ImageManagedInterface {

	private $_thumb;
	
	private $_map = array(
		'c' => 'crop',
		'r' => 'resize',
		'ar' => 'adaptiveResize',
	);
	
	/**
	 * Get a PHPThumb instance of the current image.
	 * 
	 * @return \PHPThumb\GD
	 */
	public function getThumb() {
		if ($this->_thumb === null) {
			$this->_thumb = new GD($this->getRealPath());
		}
		return $this->_thumb;
	}
	
	protected function resolveThumbPath($options) {
		$url = substr($this->hash, 0, 2) . '/'
			. substr($this->hash, 2, 2) . '/'
			. substr($this->hash, 4);
		
		if (is_array($options)) {
			$options = implode('|', $options);
		}
		$url .= '|' . $options;
		
		if ($ext = $this->getExtension()) {
			$url .= '.' . $ext;
		}
		return $url;
	}
	
	/**
	 * Get the saving path of thumb iamges.
	 * 
	 * @param mixed $options
	 */
	public function getThumbPath($options) {
		return Yii::app()->fileManager->getThumbPathOfDomain($this->domain) . '/' . $this->resolveThumbPath($options);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ecom\image\ImageManagedInterface::getThumbUrl()
	 */
	public function getThumbUrl($options) {
		return Yii::app()->fileManager->getThumbUrlOfDomain($this->domain) . '/' . $this->resolveThumbPath($options);
	}
	
	/**
	 * Get a absolute thumb url from specified options for current image.
	 * 
	 * @param string|array $options
	 * @return string
	 */
	public function getAbsoluteThumbUrl($options) {
		return Yii::app()->request->getHostInfo() . $this->getThumbUrl($options);
	}
	
	/**
	 * Render a thumb image from giving options.
	 * 
	 * @param array $options
	 * @param boolean $save
	 * @param boolean $show
	 */
	public function renderThumb($options, $save = true, $show = false) {
		$thumb = $this->getThumb();
		foreach ($options as $option) {
			$arguments = explode('-', $option);
			call_user_func_array(array($thumb, $this->_map[$arguments[0]]), array_slice($arguments, 1));
		}
		
		if ($save) {
			$path = $this->getThumbPath($options);
			
			$dir = dirname($path);
			if (!file_exists($dir)) {
				mkdir($dir, 0777, true);
			}
			
			$this->getThumb()->save($path);
		}
		
		if ($show) {
			$this->getThumb()->show();
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ecom\image\ImageManagedInterface::getWidth()
	 */
	public function getWidth() {
		$dimensions = $this->getThumb()->getCurrentDimensions();
		return $dimensions['width'];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ecom\image\ImageManagedInterface::getHeight()
	 */
	public function getHeight() {
		$dimensions = $this->getThumb()->getCurrentDimensions();
		return $dimensions['height'];
	}
	
	/**
	 * Parse options from thumb request url.
	 * 
	 * @param string $url
	 * @throws \CException
	 * @return array 
	 */
	public static function resolveThumbOptions($url) {
		$components = explode('/', ltrim(parse_url($url, PHP_URL_PATH), '/'));
		
		if (count($components) < 4) {
			throw new \CException('Invalid thumb request url: ' . $url);
		}
		
		$components = array_slice($components, -4, 4);
		list($domain, $path1, $path2, $name) = $components;
		
		if (!Yii::app()->fileManager->hasDomain($domain)) {
			throw new \CException("Invalid thumb url '$url', the domain '$domain' is not defined");
		}
		
		
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		if ($ext) {
			$ext = '.' . $ext;
		}

		$basename = basename($name, $ext);
		$basename = urldecode($basename);
		$options = explode('|', $basename);
		
		$hash = $path1 . $path2 . $options[0];
		
		array_shift($options);
		
		return array($hash, $options);
	}
}
