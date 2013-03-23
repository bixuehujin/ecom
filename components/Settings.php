<?php
/**
 * Settings class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */
require_once __DIR__ . '/../models/Setting.php';

/**
 * Settings Component provide key-value storage to save settings.
 */
class Settings extends CApplicationComponent {
	
	public $tableName;
	private $settings = array();
	/**
	 * @var Setting
	 */
	private $model;
	
	public function init() {
		$this->model = Setting::model();
		$settings = $this->model->findAll();
		foreach ($settings as $setting) {
			$this->settings[$setting->name] = $setting->value;
		}
	}
	
	
	/**
	 * fetch system settings by key. 
	 * @param string $key the key will be get. if NULL , all settings will return.
	 * @return mixed
	 */
	public function get($key = NULL) {
		if($key == NULL) 
			return $this->settings;
		if (isset($this->settings[$key])) {
			return $this->settings[$key];
		}else {
			return NULL;
		}
	}
	
	/**
	 * Get multiple keys at the same time.
	 * 
	 * @param array $keys
	 * @return array Array indexed with the key
	 */
	public function mget(array $keys) {
		$ret = array();
		foreach ($keys as $key) {
			$ret[$key] = $this->get($key);
		}
		return $ret;
	}
 	
	/**
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return boolean true on success, false otherwise.
	 */
	public function set($key, $value) {
		if(!is_string($key)) 
			return false;
		
		if(array_key_exists($key, $this->settings) && $value === $this->settings[$key]) {
			return true;
		}
		
		$model = Setting::model();
		$model->name = $key;
		$model->value = $value;
		if (!array_key_exists($key, $this->settings)) {
			$model->isNewRecord = true;
			$model->save(false);
		}else {
			$model->update();
		}
		
		$this->settings[$key] = $value;
		
		return true;
	}
}
