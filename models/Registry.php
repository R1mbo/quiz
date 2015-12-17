<?php

class Registry {
 	private static $data = array();
	//Setting the constructor to private will
	//block any attempts on new class instances
	private function __construct() {
	
 	}
	
	public static function setData($key, $value){
		static::$data[$key] = $value;
	}
	
	public static function getData($key){
		return static::$data[$key];
	}
}

