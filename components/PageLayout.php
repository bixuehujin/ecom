<?php
/**
 * PageLayout class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

class PageLayout extends CApplicationComponent {
	/**
	 * The name of layout file.
	 * @var string
	 */
	private $_layout;
	
	private $_header;
	
	private $_defaultHeader;
	
	private $_footer;
	
	private $_defaultFooter;
	
	private $_columnItems = array();
	
	private $_heroUnits = array();
	
	/**
	 * Set the view the render a page header.
	 * 
	 * @param string $view The view file.
	 * @param array  $data The data assign to the view.
	 * @return Layout
	 */
	public function setHeader($view, $data = array()) {
		$this->_header = array($view, $data);
		return $this;
	}
	
	/**
	 * Returns whether the current page has a header.
	 * 
	 * @return boolean
	 */
	public function hasHeader() {
		return (boolean)$this->_header || (boolean)$this->_defaultHeader;
	}
	
	/**
	 * Render the page header.
	 */
	public function renderHeader() {
		if ($this->_header != null) {
			$header = $this->_header;
		}else if ($this->_defaultHeader != null) {
			$header = $this->_defaultHeader;
		}else {
			throw new CException('No header to render!');
		}
		list($view, $data) = $header;
		Yii::app()->getController()
			->renderPartial($view, $data);
	}
	
	/**
	 * Set the view the render a page footer.
	 *
	 * @param string $view The view file.
	 * @param array  $data The data assign to the view.
	 * @return Layout
	 */
	public function setFooter($view, $data = array()) {
		$this->_footer = array($view, $data);
		return $this;
	}
	
	/**
	 * Returns whether the current page has a footer.
	 * 
	 * @return boolean
	 */
	public function hasFooter() {
		return (boolean)$this->_footer || (boolean)$this->_defaultFooter;
	}
	
	/**
	 * Render the page footer.
	 */
	public function renderFooter() {
		if ($this->_footer != null) {
			$footer = $this->_footer;
		}else if ($this->_defaultFooter != null) {
			$footer = $this->_defaultFooter;
		}else {
			throw new CException('No footer to render!');
		}
		list($view, $data) = $footer;
		Yii::app()->getController()
			->renderPartial($view, $data);
	}
	
	/**
	 * Add a view item to the column named $coumnName.
	 * 
	 * @param string  $columnName
	 * @param string  $view
	 * @param array   $data
	 * @param integer $weight
	 * @return PageLayout
	 */
	public function addColumnItem($columnName, $view, $data = array(), $weight = 0) {
		$column = &$this->_columnItems[$columnName];
		$column[] = array($view, $data, $weight);
		return $this;
	}

	/**
	 * Returns whether has items belongs to the specifed $columnItem.
	 * 
	 * @param string $columnName
	 * @return boolean
	 */
	public function hasColumnItems($columnName) {
		return isset($this->_columnItems[$columnName]);
	}
	
	/**
	 * Render a column.
	 * 
	 * @param array  $options
	 * @param string $columnName
	 */
	public function renderColumn($columnName, $options = array()) {
		$options += array(
			'prefix' => '', 
			'suffix' => '',
		);
		$items = &$this->_columnItems[$columnName];
		if (!$items) return;
		$controller = Yii::app()->getController();
		foreach ($items as $item) {
			list($view, $data) = $item;
			echo $options['prefix'];
			$controller->renderPartial($view, $data);
			echo $options['suffix'];
		}
	}
	
	
	/**
	 * Add a view to herounit area.
	 * 
	 * @param string  $view   The view file.
	 * @param array   $data   The data assign to view.
	 * @param integer $weight The weight of the view.
	 * @return PageLayout 
	 */
	public function addHeroUnit($view, $data = array(), $weight = 0) {
		$this->_heroUnits[] = array($view, $data, $weight);
		return $this;
	}
	
	/**
	 * Returns whether has one or more views in the herounit area.
	 * 
	 * @return boolean
	 */
	public function hasHeroUnits() {
		return !empty($this->_heroUnits);
	}
	
	/**
	 * Render all herounit views.
	 * 
	 * @param array $options  The options used to render.
	 *  + prefix: 
	 *  + suffix:
	 */
	public function renderHeroUnits($options = array()) {
		$herounits = $this->_heroUnits;
		$prefix = isset($options['prefix']) ? $options['prefix'] : '';
		$suffix = isset($options['suffix']) ? $options['suffix'] : '';
		
		$controller = Yii::app()->getController();
		foreach ($herounits as $unit) {
			list($view, $data) = $unit;
			echo $prefix;
			$controller->renderPartial($view, $data);
			echo $suffix;
		}
	}
	
	
	public function setBreadcrumb() {
		
	}
	
	public function renderBreadcrumb() {
		
	}
	
	/**
	 * Set the name of used layout file.
	 * 
	 * @param string $name
	 * @return Layout
	 */
	public function setLayout($name) {
		$this->_layout = $name;
		Yii::app()->getController()->layout = $name;
		return $this;
	}
	
	public function getLayout() {
		return $this->_layout;
	}
	
	public function setDefaultHeader($header) {
		$this->_defaultHeader = $header;
		return $this;
	}
	
	public function setDefaultFooter($footer) {
		$this->_defaultFooter = $footer;
		return $this;
	}
}
