<?php
/**
 * FileUploadAction class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since  2012-04-09
 */

/**
 * @property string  $source 
 * @property string  $fileType
 * @property mixed   $allowExtensions
 * @property array   $properties
 */
class FileUploadAction extends CAction {
	
	public $source = 'file';
	/**
	 * The type of uploaded file, file or image, detault to file.
	 * 
	 * @var string
	 */
	public $fileType = 'file';
	/**
	 * The extensions be allowed to upload.
	 * 
	 * @var array|string
	 */
	public $allowExtensions;
	/**
	 * Properties needs to send to client.
	 * 
	 * @var array
	 */
	public $properties;
	
	
	public function run() {
		if ($this->fileType == 'image') {
			$fileManaged = Image::model();
		}else {
			$fileManaged = FileManaged::model();
		}
		if ($this->allowExtensions) {
			$fileManaged->setAllowExtensions($this->allowExtensions);
		}
		
		$file = $fileManaged->upload($this->source, 'item.avatars');
		$ajax = new AjaxReturn();
		if ($file) {
			if ($this->properties) {
				$ajax->setData(Utils::fetchProperties($file, $this->properties));
			}else {
				$ajax->setData($file->getAttributes());
			}
		}else {
			$ajax->setCode(300)->setMsg($fileManaged->getErrors());
		}
		$ajax->send();
	}
	
}
