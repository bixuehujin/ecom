<?php namespace ecom\setting;
/**
 * Setting class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

use Yii;
use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;
use CApplicationComponent;
use ecom\setting\storage\StorageInterface;
use ecom\setting\storage\DatabaseStorage;

/**
 * Setting Component provide key-value storage to save settings.
 * 
 * @property array $storage The backend storage configuration, default will use DatabaseStorage.
 */
class Setting extends CApplicationComponent implements ArrayAccess {
	
	/**
	 * @var StorageInterface
	 */
	private $storage;
	
	/**
	 * Sets the storage backend of the setting component.
	 * 
	 * @param mixed $config The configration for create storage object.
	 * @throws \CException
	 */
	public function setStorage($config) {
		$object = Yii::createComponent($config);
		if (!$object instanceof StorageInterface) {
			throw new \CException('The storage object must be an instance of "ecom\settings\storage\StorageInterface"');
		}
		$this->storage = $object;
	}
	
	/**
	 * Gets the storage used by 
	 * 
	 * @return StorageInterface
	 */
	public function getStorage() {
		if ($this->storage === null) {
			$this->storage = new DatabaseStorage();
		}
		return $this->storage;
	}
	
	/**
	 * Get settings by key, if the key does not exist, the $default will be return.
	 * 
	 * @param string $key The key will be get.
	 * @param mixed  $default The default value if a key does not exist
	 * @return mixed
	 */
	public function get($key, $default = null) {
		return $this->getStorage()->get($key, $default);
	}
	
	/**
	 * Gets multiple keys at the same time.
	 * 
	 * @param array $keys
	 * @return array The results indexed with the key.
	 */
	public function mget(array $keys) {
		return $this->getStorage()->mget($keys);
	}
 	
	/**
	 * Sets key by value.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return boolean true on success, false otherwise.
	 */
	public function set($key, $value) {
		return $this->getStorage()->set($key, $value);
	}
	
	/**
	 * Sets multiple key-value pairs at the same time.
	 * 
	 * @param array $values array of key-value pairs
	 * @return integer 
	 */
	public function mset(array $values) {
		return  $this->getStorage()->mset($values);
	}
	
	/**
	 * Returns whether a key is existed.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key) {
		return $this->getStorage()->exists($key);
	}
	
	/**
	 * Delete by key.
	 * 
	 * @param string $key
	 * @return integer
	 */
	public function del($key) {
		return $this->getStorage()->del($key);
	}
	
	/**
	 * Delete multiple keys at the same time.
	 * 
	 * @param array $keys
	 * @return integer
	 */
	public function mdel(array $keys) {
		return $this->getStorage()->mdel($keys);
	}
	
	/**
	 * Delete all keys in storage.
	 * 
	 * @return integer
	 */
	public function deleteAll() {
		return $this->getStorage()->deleteAll();
	}
	
	public function offsetExists($offset) {
		return $this->exists($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}
	
	public function offsetGet($offset) {
		return $this->get($offset);
	}
	
	public function offsetUnset($offset) {
		$this->del($offset);
	}
}
