<?php

require_once __DIR__ . '/../models/Location.php';

class LocationPicker extends CWidget {
	/**
	 * location code
	 * @var integer
	 */
	public $location;
	
	public $attributeName = 'location';
	
	public $selector = '.location-picker';
	
	public $model;
	
	public $itemOptions = array('class' => 'input-small');
	
	public $preItem = array(0 => '--请选择--');
	/**
	 * options to construct js LocationPicker object.
	 * 
	 * @var array
	 */
	public $clientOptions = array();
	
	private $province;
	private $city;
	private $district;
	/**
	 * @var Location
	 */
	private $_location;
	
	public function init() {
		$this->_location = Location::model();
		
		$this->province = $this->_location->provinceNo($this->location);
		$this->city = $this->_location->cityNo($this->location);
		$this->district = $this->_location->districtNo($this->location);
		
		Yii::app()->common->registerLocationPickerScript();
		$options = $this->getClientOptions();
		Yii::app()->clientScript->registerScript('hujin', "jQuery('{$this->selector}').LocationPicker($options)");
	}
	
	public function run() {
		
		echo CHtml::hiddenField($this->getLocationFieldName(), $this->location);
		echo CHtml::dropDownList('province', $this->province, 
				$this->preItem + $this->_location->getAllProvinces(), $this->itemOptions);
		
		echo CHtml::dropDownList('city', $this->city, 
				$this->preItem + $this->_location->getAllCities($this->province), $this->itemOptions);
		
		$itemOptions = $this->itemOptions;
		if (!$this->_location->cityHasDistrict($this->city)) {
			$class = &$itemOptions['class'];
			if (is_string($class)) {
				$class .= ' hide';
			}else if (is_array($class)) {
				$class[] = 'hide';
			}
		}
		
		echo CHtml::dropDownList('district', $this->district, 
				$this->preItem + $this->_location->getAllDistricts($this->city), $itemOptions);
	}
	
	/**
	 * @return string json encode string
	 */
	protected function getClientOptions() {
		$options = $this->clientOptions + array(
			'inputId' => isset($this->model) ? get_class($this->model) . '_' . $this->attributeName : 
				$this->attributeName,
		);
		return json_encode((object)$options);
	}
	
	private function getLocationFieldName() {
		if ($this->model == null) {
			return $this->attributeName;
		}else {
			return get_class($this->model) . '[' . $this->attributeName . ']';
		}
	}
}
