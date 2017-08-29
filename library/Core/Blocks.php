<?php
class Core_Blocks {
	static $_notModule = 0;
	static $_notPosition = array();
	static $_action = null;
	static $_controller = null;
	static $_module = null;
	function __construct(){
		
	}
	static function beforProcess($module, $controller, $action){
		self::$_action = $action;
		self::$_controller = $controller;
		self::$_module = $module;
	}
	
		
	
	static function setNotPosition($position){
		$arr = array();
		if(is_string($position)){
			$arr[] = $position;
		}
		elseif(is_array($position)){
			$arr = $position;
		}
		Core_Blocks::$_notPosition = $arr;
	}
	static function render($params){
		return '';
	}
	static function isHomePage(){
		if (self::$_action=='index' 
				&& self::$_controller=='index'
				&& self::$_module=='default') {
			return true;
		}
		return false;
	}
		
}