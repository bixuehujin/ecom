<?php
/**
 * StateBehavior class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-07-13
 */

/**
 * Provide ability of variable state storage.
 */
class StateBehavior extends CActiveRecordBehavior {
	
	/**
	 * array('type' => 'owner', 'name' => 'data')
	 * array('type' => 'model', 'class' => 'path.to.class')
	 */
	private $_provider = array('type' => 'owner', 'name' => 'data');
	
	private $_states = array();
	
	public $preload = false;
	
	
	public function setProvider($provider) {
		$this->_provider = $provider;
	}
	
	public function init() {
		if ($this->preload) {
			
		}
	}
	
	
	
	public function setState($name, $value) {
		$this->_states[$name] = $value;
	}
	
	public function getState($name, $default = null) {
		if (!empty($this->_states) && key_exists($name, $this->_states)) {
			return $this->_states[$name];
		}else {
			return $default;
		}
	}
	
	public function save() {
		$type = $this->_provider['type'];
		if ($type === 'owner') {
			return $this->getOwner()->save(false, array($this->_provider['name']));
		}else {
			//TODO
		}
	}
	
	public function __get($name) {
		if ($this->canGetProperty($name)) {
			return parent::__get($name);
		}else {
			return isset($this->_states[$name]) ? $this->_states[$name] : null;
		}
	}
	
	public function __set($name, $value) {
		if ($this->canSetProperty($name)) {
			parent::__set($name, $value);
		}else {
			$this->_states[$name] = $value;
		}
	}
	
	public function __unset($name) {
		if (!$this->hasProperty($name)) {
			unset($this->_states[$name]);
		}else {
			parent::__unset($name);
		}
	}
	
	public function __isset($name) {
		return isset($this->_states[$name]) || $this->canGetProperty($name);
	}
	
	public function beforeSave($event) {
		$type = $this->_provider['type'];
		if ($type === 'owner') {
			$this->getOwner()->setAttribute($this->_provider['name'], serialize($this->_states));
		}else if ($type === 'model') {
			//TODO
		}
	}
	
	public function afterFind($event) {
		$type = $this->_provider['type'];
		if ($type === 'owner') {
			$owner = $this->getOwner(); 
			$name = $this->_provider['name'];
			$owner->$name = $this->_states = unserialize($owner->getAttribute($name));
		}else if ($type === 'model') {
			//TODO
		}
	}
	
}
