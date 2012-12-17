<?php
/**
 * SerializeBehavior class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * SerializeBehavior will automatically serialize and unserialize specfic attributes.
 */
class SerializeBehavior extends CActiveRecordBehavior {
	
	/**
	 * attributes to serialize and unserialize.
	 * @var array
	 */
	public $attributes = array();
	
	/**
	 * callable used to serialize
	 * @var callable
	 */
	public $serializeFunc;

	/**
	 * callable used to unserialize
	 * @var callable
	 */
	public $unserializeFunc;
	
	
	public function beforeSave($event) {
		$owner = $this->getOwner();
		foreach ($this->attributes as $attribute) {
			$owner->$attribute = is_callable($this->serializeFunc) 
				? call_user_func($this->serializeFunc, $owner->$attribute) : serialize($owner->$attribute);
		}
	}
	
	
	public function afterFind($event) {
		$owner = $this->getOwner();
		foreach ($this->attributes as $attribute) {
			$owner->$attribute = is_callable($this->unserializeFunc)
				? call_user_func($this->unserializeFunc, $owner->$attribute) : unserialize($owner->$attribute);
		}
	}
}
