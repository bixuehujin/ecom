<?php
/**
 * FileManaged class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class FileManaged extends CActiveRecord {
	
	const STATUS_TEMPORARY = 0;
	const STATUS_PERSISTENT = 1;
	
	private $_allowExtensions;
	private $_fileSavePath;
	
	/**
	 * @return FileManaged
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'file_managed';
	}
	
	public function rules() {
		return array(
			array('name', 'extensionValidator', 'extensions' => $this->_allowExtensions),
		);
	}
	
	/**
	 * Validate file extensions.
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function extensionValidator($attribute, $params) {
		$extensions = isset($params['extensions']) ? $params['extensions'] : null;
		if ($extensions === null) {
			return;
		}
		$value = $this->getAttribute($attribute);
		$valueExt = $this->resolveFileExtension($value);
		if (!in_array($valueExt, $extensions)) {
			$this->addError($attribute, Yii::t('common', 'File cann\'t be upload, extension not allowed.'));
		}
	}
	
	/**
	 * Upload a file and save to database.
	 * 
	 * @param string $name
	 * @param integer $status
	 * @return mixed
	 */
	public function upload($source, $status = self::STATUS_TEMPORARY) {
		if (!isset($_FILES['files']['name'][$source])) {
			return false;
		}
		if ($_FILES['files']['error'][$source] != 0) {
			return false;
		}
		
		$fileManaged = new FileManaged();
		$name = $_FILES['files']['name'][$source];
		$fileManaged->name = $name;
		$fileManaged->mime = $_FILES['files']['type'][$source];
		$fileManaged->size = $_FILES['files']['size'][$source];
		$fileManaged->status = $status;
		$fileManaged->uid = Yii::app()->getUser()->getId();
		if (!$fileManaged->validate(array('name'))) {
			return false;
		}
		$fileManaged->name = $fileManaged->uid . '-' . time()
			. $this->resolveFileExtension($name);
		
		if ($fileManaged->isFileExist()) {
			return true;
		}
		if ($fileManaged->save(false)) {
			$path = $this->getFileSavePath();
			if (!file_exists($path)) {
				mkdir($path, 744, true);
			}
			if (is_writable($path) && 
					move_uploaded_file($_FILES['files']['tmp_name'][$source], $path . '/' . $fileManaged->name)) {
				
				return $fileManaged->setFileSavePath($this->getFileSavePath())
					->setAllowExtensions($this->getAllowExtensions());
			}
			$fileManaged->delete();
			return false;
		}
		return false;
	}
	
	
	protected function resolveFileExtension($name) {
		$ext = '';
		if (($offset = strrpos($name, '.')) !== false) {
			$ext = substr($name, $offset);
		}
		return $ext;
	}
	
	/**
	 * Delete database record and remove file.
	 * 
	 * @param integer $fid
	 * @return boolean
	 */
	public function remove($fid) {
		$model = $this->findByPk($fid);
		if ($model) {
			if (unlink($this->getFileSavePath() . '/' . $model->name)) {
				$model->delete();
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Convert $_FILES to associte array.
	 * 
	 * @param array $_files The $_FILES variable.
	 * @return array
	 */
	public static function arrayToMulti($_files, $top = TRUE) {
		$files = array();
		foreach($_files as $name=>$file){
			if($top) {
				$sub_name = $file['name'];
			}else{
				$sub_name = $name;
			}
		
			if(is_array($sub_name)){
				foreach(array_keys($sub_name) as $key){
					$files[$name][$key] = array(
						'name'     => $file['name'][$key],
						'type'     => $file['type'][$key],
						'tmp_name' => $file['tmp_name'][$key],
						'error'    => $file['error'][$key],
						'size'     => $file['size'][$key],
					);
					$files[$name] = self::arrayToMulti($files[$name], FALSE);
				}
			}else{
				$files[$name] = $file;
			}
		}
		return $files;
	}
	
	public function beforeSave() {
		if ($this->getIsNewRecord()) {
			$this->timestamp = time();
		}
		return parent::beforeSave();
	}
	
	/**
	 * Returns whether the file is existed according filename.
	 * 
	 * @return boolean
	 */
	public function isFileExist() {
		return (bool)$this->findByAttributes(array('name' => $this->getAttribute('name')));
	}
	
	/**
	 * Set file extensions allow to upload.
	 * 
	 * @param mixed $extensions array contains allowed extensions or string separeted by '|'.
	 * @return FileManaged
	 */
	public function setAllowExtensions($extensions) {
		if (is_array($extensions)) {
			$this->_allowExtensions = $extensions;
		}else {
			$this->_allowExtensions = explode('|', $extensions);
		}
		return $this;
	}
	
	public function getAllowExtensions() {
		return $this->_allowExtensions;
	}
	
	/**
	 * Set base path to save files.
	 * 
	 * @param string $path
	 * @return FileManaged
	 */
	public function setFileSavePath($path) {
		if ($path[0] == '/') {
			$this->_fileSavePath = realpath($path);
		}else {
			$this->_fileSavePath = realpath(Yii::app()->getBasePath() . '/../' . $path);
		}
		return $this;
	}
	
	public function getFileSavePath() {
		if (null === $this->_fileSavePath) {
			throw new CException('Property {fileSavePath} is unset.');
		}
		return $this->_fileSavePath;
	}
}
