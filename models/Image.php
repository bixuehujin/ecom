<?php
/**
 * Image class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

Yii::setPathOfAlias('PHPThumb', Yii::app()->getBasePath() . '/vendors/PHPThumb');
use PHPThumb\GD;

/**
 * Sub class of FileManaged, provides some extra functions to work with images.
 * 
 * 
 */
class Image extends FileManaged {
	/**
	 * @var PHPThumb\GD
	 */
	private $_gd;
	
	/**
	 * @return \PHPThumb\GD
	 */
	protected function getGd() {
		if ($this->_gd === null) {
			$this->_gd = new GD($this->getFilePath());
		}
		return $this->_gd;
	}
	
	/**
	 * @return array
	 */
	public function getCurrentDimensions() {
		return $this->getGd()->getCurrentDimensions();
	}
	
	public function cropFormCenter($width, $height = null) {
		$this->getGd()->cropFromCenter($width, $height);
		return $this;
	}
	
	public function crop($startX, $startY, $cropWidth, $cropHeight) {
		$this->getGd()->crop($startX, $startY, $cropWidth, $cropHeight);
		return $this;
	}
	
	public function resize($maxWidth = 0, $maxHeight = 0) {
		$this->getGd()->resize($maxWidth, $maxHeight);
		return $this;
	}
	
	/**
	 * Show the image.
	 * 
	 * @param boolean $rawData
	 * @return Image
	 */
	public function show($rawData = false) {
		$this->getGd()->show($rawData);
		return $this;
	}
	
	/**
	 * Save the thumb to a file.
	 * 
	 * @param string $fileName
	 * @param string $format
	 * @return Image
	 */
	public function saveThumbFile($fileName, $format = null) {
		$this->getGd()->save($fileName, $format);
		return $this;
	}

	/**
	 * Get the web accessable url of a image.
	 * 
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	public function getThumbURL($width = null, $height = null) {
		return Yii::app()->fileManager->getThumbBaseUrl() . '/' . $this->createThumbName($width, $height); 
	}
	
	/**
	 * Get the saving path of thumbnail.
	 * 
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	public function getThumbPath($width, $height = null) {
		return Yii::app()->fileManager->getThumbBasePath() . '/' . $this->createThumbName($width, $height);
	}
	
	protected function createThumbName($width = null, $height = null) {
		if ($width === null) {
			$width = '{width}';
			$height = '{height}';
		}else {
			$height = $height === null ? $width : $height;
		}
		return pathinfo($this->name, PATHINFO_FILENAME)
		. "_._${width}x${height}."
		. pathinfo($this->name, PATHINFO_EXTENSION);
	}
}
