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
	
	/**
	 * @return FileManaged|Image
	 */
	public static function model() {
		return parent::model(get_called_class());
	}
	
	public function tableName() {
		return 'file_managed';
	}
	
	public function rules() {
		return array(
			array('name', 'extensionValidator', 'extensions' => $this->getAllowExtensions()),
		);
	}
	
	/**
	 * Validate file extensions.
	 * 
	 * @param string $attribute
	 * @param array $params
	 */
	public function extensionValidator($attribute, $params) {
		$extensions = isset($params['extensions']) ? $params['extensions'] : $this->getAllowExtensions();
		if ($extensions === null) {
			return true;
		}
		$value = $this->getAttribute($attribute);
		$valueExt = pathinfo($value, PATHINFO_EXTENSION);
		if (!in_array($valueExt, $extensions)) {
			$this->addError($attribute, Yii::t('Common.main', 'The specified file {name} could not be uploaded.', array('{name}' => $this->name)));
			return false;
		}
		return true;
	}
	
	/**
	 * Upload a file and save to database.
	 * 
	 * @param string $name
	 * @param string $domain 
	 * @param integer $status
	 * @return mixed
	 */
	public function upload($source, $domain, $status = self::STATUS_TEMPORARY) {
		
		if (!isset($_FILES['files']['name'][$source])) {
			return false;
		}
		if ($_FILES['files']['error'][$source] != 0) {
			return false;
		}
		
		$fileManaged = static::model();
		$fileManaged->setIsNewRecord(true);
		$name = $_FILES['files']['name'][$source];
		$fileManaged->name = $name;
		$fileManaged->mime = $_FILES['files']['type'][$source];
		$fileManaged->size = $_FILES['files']['size'][$source];
		$fileManaged->status = $status;
		$fileManaged->domain = $domain;
		$fileManaged->uid = Yii::app()->getUser()->getId();
		if (!$r = $fileManaged->validate(array('name'))) {
			return false;
		}
		$fileManaged->name = $fileManaged->uid . '-' . time()
			. $this->resolveFileExtension($name);
		
		if ($fileManaged->isFileExist()) {
			return true;
		}
		if ($fileManaged->save(false)) {
			$path = Yii::app()->fileManager->getPathOfDomain($domain);
			if (!file_exists($path)) {
				mkdir($path, 744, true);
			}
			if (is_writable($path) && 
					move_uploaded_file($_FILES['files']['tmp_name'][$source], $path . '/' . $fileManaged->name)) {
				
				return $fileManaged->setAllowExtensions($this->getAllowExtensions());
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
	 * @param integer $file
	 * @return boolean
	 */
	public static function remove($file) {
		$fid = $file instanceof self ? $file->fid : $file; 
		$model = self::model()->findByPk($fid);
		if ($model) {
			$path = Yii::app()->fileManager->getPathOfDomain($model->domain) . '/' . $model->name;
			if (unlink($path)) {
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
	 * Attach file to an entity.
	 * 
	 * @param integer $entityId
	 * @param string $entityType
	 * @param integer $count
	 * @return boolean
	 */
	public function attachTo($entityId, $entityType, $count = 1) {
		$usage = new FileUsage();
		$usage->entity_id = $entityId;
		$usage->entity_type = $entityType;
		$usage->count = $count;
		$usage->fid = $this->fid;
		return $usage->save(false);
	}
	
	/**
	 * Get web accessable URL of the file.
	 * 
	 * @return string
	 */
	public function getAccessURL() {
		$url = Yii::app()->fileManager->getUrlOfDomain($this->domain);
		return $url . '/' . $this->name;
	}
	
	/**
	 * Get the saving path of the file.
	 * 
	 * @return string
	 */
	public function getFilePath() {
		$path = Yii::app()->fileManager->getPathOfDomain($this->domain);
		return $path . '/' . $this->name;
	}
	
	/**
	 * Load a file record file database.
	 *
	 * @param string $name
	 * @return FileManaged|Image
	 */
	public static function loadByName($name) {
		return static::model()->findByAttributes(array(
				'name' => $name,
		));
	}
	
	/**
	 * Load a file from database by fid.
	 * 
	 * @param string $fid
	 * @return FileManaged|Image
	 */
	public static function load($fid) {
		return static::model()->findByPk($fid);
	}
	
}
