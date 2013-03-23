<?php
/**
 * AttachedData class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * Attach data to other objects.
 * 
 * @property  boolean $isExpired
 * 
 */
class AttachedData extends CActiveRecord {
	
	const EXPIRE = 2147483647;
	
	/**
	 * @return AttachedData
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return 'attached_data';
	}
	
	public function behaviors() {
		return array(
			'SerializeBehavior' => array(
				'class' => 'ext.common.behaviors.SerializeBehavior',
				'attributes' => array('data'),
			),
			'CTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'attached',
				'updateAttribute' => null,
			),
		);
	}
	
	/**
	 * Attach data to a object with name $name, if the key is already attached, false will be returned.
	 * 
	 * @param string $name
	 * @param integer $attachTo
	 * @param string $key
	 * @param mixed $data
	 * @param integer $expire
	 * @return boolean
	 */
	public function attach($name, $attachTo, $key, $data, $expire = null) {
		$this->isNewRecord = true;
		$this->key = $key;
		$this->data = $data;
		$this->name = $name;
		$this->attach_to = $attachTo;
		$this->expire = $expire == null ? self::EXPIRE : $expire;
		return $this->save(false);
	}
	
	/**
	 * update or attach data.
	 * 
	 * @return boolean
	 */
	public function set($name, $attachTo, $key, $data, $expire = null) {
		$result = $this->findByAttributes(array(
			'attach_to' => $attachTo,
			'key' => $key,
			'name' => $name,
		));
		if ($result) {
			$result->data = $data;
			$result->expire = $expire == null ? self::EXPIRE : $expire;
			return $result->update(array('data', 'expire'));
		}else {
			return $this->attach($name, $attachTo, $key, $data, $expire);
		}
	}
	
	/**
	 * Whether the attached data is expired.
	 * 
	 * @return boolean
	 */
	public function getIsExpired() {
		return time() > $this->expire;
	}
	
	/**
	 * Remove expired data.
	 * 
	 * @param integer $limit
	 * @return integer
	 */
	public function removeExpired($limit = null) {
		$cond = array(
			'condition' => 'expire<' . time(),
		);
		if (is_int($limit)) {
			$cond['limit'] = $limit;
		}
		return $this->deleteAll($cond);
	}
	
	/**
	 * Get all attached data by its name and id.
	 * 
	 * @param string $name
	 * @param integer $attachTo
	 * @param $keys the keys to fetch, null to fetch all.
	 * @return array
	 */
	public function mget($name, $attachTo, $keys = null) {
		$criteria = new CDbCriteria();
		$criteria->condition = 'expire > ' . time();
		if (is_array($keys) && !empty($keys)) {
			$criteria->addInCondition('key', $keys);
		} 
		$results = $this->findAllByAttributes(array(
			'attach_to' => $attachTo,
			'name' => $name,
		), $criteria);
		return Utils::arrayColumns($results, null, 'key');
	}
	
	/**
	 * Get data by $key attached to $name.
	 * 
	 * @param string $name
	 * @param integer $attachTo
	 * @param string $key
	 * @return AttachData
	 */
	public function get($name, $attachTo, $key) {
		$result = $this->findByAttributes(array(
			'attach_to' => $attachTo,
			'key' => $key,
			'name' => $name,
		));
		if ($result && !$result->getIsExpired()) {
			return $result;
		}
		return false;
	}
	
	/**
	 * Remove the data with $key that attached to $name.
	 *
	 * @param string $name
	 * @param integer $attachTo
	 * @param string $key
	 * @return boolean
	 */
	public function remove($name, $attachTo, $key) {
		return (boolean)$this->deleteAllByAttributes(array(
			'attach_to' => $attachTo,
			'key' => $key,
			'name' => $name,
		));
	}
	
	/**
	 * Remove the data attached to $name.
	 *
	 * @param string $name
	 * @param integer $attachTo
	 * @param string $key
	 * @return ingeger
	 */
	public function removeAll($name, $attachTo, $keys = null) {
		$criteria = new CDbCriteria();
		$criteria->addColumnCondition(array(
			'attach_to' => $attachTo,
			'name' => $name,
		));
		if (is_array($keys) && !empty($keys)) {
			$criteria->addInCondition('key', $keys);
		}
		return $this->deleteAll($criteria);
	}
	
	
	/**
	 * Check whether a key is attached to.
	 * 
	 * @param string $name
	 * @param integer $attachTo
	 * @param string $key
	 * @return boolean
	 */
	public function exist($name, $attachTo, $key) {
		$criteria = new CDbCriteria();
		$criteria->addColumnCondition(array(
			'attach_to' => $attachTo,
			'key' => $key,
			'name' => $name,
		));
		return $this->exists($criteria);
	}
	
	
}
