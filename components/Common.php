<?php

class Common extends CApplicationComponent {
	
	protected $_assetsUrl;
	
	public function init() {
		if(Yii::getPathOfAlias('common') === false) {
			Yii::setPathOfAlias('common', realpath(__DIR__ . '/..'));
		}
		if (Yii::app() instanceof CConsoleApplication) {
			return;
		}
		
	}
	
	public function registerLocationPickerScript() {
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerScriptFile($this->getAssetsUrl() . '/js/location-data.js');
		$cs->registerScriptFile($this->getAssetsUrl() . '/js/location-picker.js');
	}
	
	/**
	 * returns the URL to the published assets folder.
	 * 
	 * @return string
	 */
	protected function getAssetsUrl() {
		if (isset($this->_assetsUrl)) {
			return $this->_assetsUrl;
		}
		$assetsPath = Yii::getPathOfAlias('common.assets');
		$assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, YII_DEBUG);
		return $this->_assetsUrl = $assetsUrl;
	}
	
	
	
}
