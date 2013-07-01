<?php
/**
 * Utils class file.
 *
 * @author Jin Hu <bixuehujin@gmail.com>
 */

/**
 * utils helper functions.
 */
class Utils {

	/**
	 * extract column values into a new array.
	 *
	 * @param array|ArrayAccess $array
	 * @param string $name index of the column. if null is given, the whole value will used.
	 * @param stting|array $indexedWith the index used in the new array, defaults to integer
	 * @return array
	 */
	public static function arrayColumns($array, $name = null, $indexedWith = null) {
		$ret = array();
		if (empty($indexedWith)) {
			$indexedWith = null;
		}
		foreach ($array as  $value) {
			$tv = $name === null ? $value : $value[$name];
			if (is_null($indexedWith)) {
				$ret[] = $tv;
			}else if (is_array($indexedWith)) {
				$t = array();
				foreach ($indexedWith as $kv) {
					$t[] = $value[$kv];
				}
				$ret[implode('-', $t)] = $tv;
			}else {
				$ret[$value[$indexedWith]] = $tv;
			}
		}
		return $ret;
	}

	/**
	 * sort a array with $key in the order of $values.
	 *
	 * @param array $array a array contains arrays or ArrayAccess.
	 * @param array $values
	 * @param string $key
	 * @return array the sorted array
	 */
	public static function sortByValues(array $array, array $values, $key) {
		$ret = array();
		foreach ($values as $value) {
			if($t = self::sortByValuesHelper($array, $value, $key)) {
				$ret[] = $t;
			}
		}
		return $ret;
	}

	/**
	 * @param array $array
	 * @param mixed $value
	 * @param string $key
	 * @return array|boolean
	 */
	protected static function sortByValuesHelper($array, $value, $key) {
		foreach ($array as $item) {
			if ($item[$key] == $value) {
				return $item;
			}
		}
		return false;
	}

	/**
	 * whether $str is started with $substring
	 *
	 * @param string $str
	 * @param string $substring
	 * @return boolean
	 */
	public static function startWith($str, $substring) {
		if (strncmp($str, $substring, strlen($substring)) == 0) {
			return true;
		}else {
			return false;
		}
	}

	/**
	 * whether $str is ended with $substring
	 *
	 * @param string $str
	 * @param string $substring
	 * @return boolean
	 */
	public static function endWith($str, $substring) {
		return (boolean)preg_match("/$substring$/", $str);
	}

	/**
	 * Swapper of realpath, throw exception when file does not exist.
	 *
	 * @param string $path
	 * @throws CException
	 * @return string
	 */
	public static function realpath($path) {
		if (($realPath = realpath($path)) === false) {
			throw new CException(sprintf("File '%s' do not exist!", $path));
		}
		return $realPath;
	}
	
	protected static function fetchPropertiesHelper($confStr) {
		$conf = explode(':', $confStr);
		if (!isset($conf[1])) {
			$conf[1] = $conf[0];
		}
		return $conf;
	}
	
	/**
	 * Fetch a set of properties from a CMoel object.
	 * 
	 * @param CModel  $model
	 * @param array   $names
	 *   array(
	 *     'attributeName[:attributeAlise]',
	 *     'attributeName[:attributeAlise]' => 'subObjectName',
	 *     'attributeName[:attributeAlise]' => array(... sub object properties...)
	 *   )
	 * @return array
	 */
	public static function fetchProperties($model, array $names) {
		$properties = array();
		foreach ($names as $key => $value) {
			if (is_array($value)) {
				list($attrName, $attrAlias) = self::fetchPropertiesHelper($key);
				if (is_object($model->$attrName)) {
					$properties[$attrAlias] = self::fetchProperties($model->$attrName, $value);
				}
			}else if (is_string($key) && is_string($value)) {
				list($attrName, $attrAlias) = self::fetchPropertiesHelper($key);
				$properties[$attrAlias] = is_object($model->$value) ? $model->$value->$attrName : null;
			}else {
				list($attrName, $attrAlias) = self::fetchPropertiesHelper($value);
				$properties[$attrAlias] = $model->$attrName;
			}
		}
		return $properties;
	}
}
