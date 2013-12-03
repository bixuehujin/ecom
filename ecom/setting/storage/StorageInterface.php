<?php namespace ecom\setting\storage;
/**
 * The Storage interface file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 */

interface StorageInterface {
	
	public function get($key, $default = null);
	
	public function mget(array $keys);
	
	public function set($key, $value);
	
	public function mset(array $values);
	
	public function del($key);
	
	public function mdel(array $keys);
	
	public function exists($key);
	
	public function deleteAll();
}
