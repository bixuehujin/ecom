<?php
/**
 * AdvLinker factory class.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

namespace AdvLinker;

class AdvLinker {
	
	private static $_instances;
	
	private function __construct() {
	}
	
	/**
	 * @param string $id
	 * @param array $options
	 * @return IAdvLinker
	 */
	public static function instance($id, $options = array()) {
		$object = &self::$_instances[$id];
		if (!isset($object)) {
			$class = __NAMESPACE__ . '\\adapter\\' . ucfirst($id) . 'Linker';
			$object = new $class();
		}
		$object->setOptions($options);
		return $object;
	}
	
	private function __clone() {
	}
}
