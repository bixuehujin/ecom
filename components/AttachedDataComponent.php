<?php
/**
 * AttachedDataComponent class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * AttachedDataComponent used to attach variable data to object, such as user.
 */
class AttachedDataComponent extends CApplicationComponent {
	
	public $name = 'user';
	/**
	 * @var CWebUser
	 */
	private $_user;
	/**
	 * @var AttachedData
	 */
	private $_model;
	
	public function init() {
		$this->_model = AttachedData::model();
	}
	
	/**
	 * see AttachedData::attach()
	 */
	public function attach($key, $data, $id, $expire = null) {
		return $this->_model->attach($this->name, $id, $key, $data, $expire);
	}
	
	/**
	 * see AttachedData::set()
	 */
	public function set($key, $data, $id, $expire = null) {
		return $this->_model->set($this->name, $id, $key, $data, $expire);
	}
	
	/**
	 * see AttachedData::exist()
	 */
	public function exist($key, $id) {
		return $this->_model->exist($this->name, $id, $key);
	}
	
	/**
	 * see AttachedData::get()
	 */
	public function get($key, $id) {
		return $this->_model->get($this->name, $id, $key);
	}
	
	/**
	 * see AttachedData::mget()
	 */
	public function mget($keys, $id) {
		return $this->_model->mget($this->name, $id, $keys);
	}
	
	/**
	 * see AttachedData::remove()
	 */
	public function remove($key, $id) {
		return $this->_model->remove($this->name, $id, $key);
	}
	
	/**
	 * see AttachedData::removeExpired()
	 */
	public function removeExpired($limit = 100) {
		return $this->_model->removeExpired($limit);
	}
	
	/**
	 * see AttachedData::removeAll()
	 */
	public function removeAll($keys, $id) {
		$this->_model->removeAll($this->name, $id, $keys);
	}
}
