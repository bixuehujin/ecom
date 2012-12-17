<?php
/**
 * Location class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class Location extends CActiveRecord {
	
	public function tableName() {
		return 'location';
	}
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * get all provinces.
	 * @return array Array keyed with province id and the value is the 
	 * corresponding name.
	 */
	public function getAllProvinces() {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('id', '__', false);
		$provinces = $this->findAll($criteria);
		return $this->toIndexedArray($provinces);
	}
	
	public function getAllCities($proviceId = null) {
		if (!$proviceId) {
			return array();
		}
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('id', $proviceId . '__', false);
		return $this->toIndexedArray($this->findAll($criteria));
	}
	
	/**
	 * returns whether a city has districts.
	 * 
	 * @param integer $cityId
	 * @return boolean
	 */
	public function cityHasDistrict($cityId) {
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('id', $cityId . '__', false);
		
		return $this->find($criteria) ? true : false;
	}
	
	public function getAllDistricts($cityId = null) {
		if (!$cityId) {
			return array();
		}
		$criteria = new CDbCriteria();
		$criteria->addSearchCondition('id', $cityId . '__', false);
		return $this->toIndexedArray($this->findAll($criteria));
	}
	
	private function toIndexedArray($data) {
		$ret = array();
		foreach ($data as $item) {
			$ret[$item->id] = $item->name;
		}
		return $ret;
	}
	
	public function provinceNo($id) {
		$len = strlen($id);
		if ($len == 2) {
			return $id;
		}else if ($len > 2) {
			return substr($id, 0, 2);
		}else {
			return 0;
		}
	}
	
	public function cityNo($id) {
		$len = strlen($id);
		if ($len == 4) {
			return $id;
		}else if($len > 4) {
			return substr($id, 0, 4);
		}else {
			return 0;
		}
	}
	
	public function districtNo($id) {
		if (strlen($id) == 6) {
			return $id;
		}else {
			return 0;
		}
	}
}
