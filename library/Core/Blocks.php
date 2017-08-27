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
	/**
	 * lay css hoạc javascript cua block
	 * return array
	 */
	static function getAssets(){
		if(self::$_notModule == 1){
			return array();
		}
		$data = array();
		$db = Core::db();
		$rows = $db->fetchAll($db->select()->from(array('cb'=>'core_blocks'),array('class_name'))
				->join(array('b'=>'core_fk_block_action'),'cb.id = b.id_block', array())
				->where('(b.id_action IS NULL) OR (b.id_action IN ('
						.$db->select()->from(array('ca'=>'core_actions'),array('id'))
						->where('ca.action_name = ?',self::$_action,'STRING')
						->where('ca.controller_name = ?',self::$_controller,'STRING')
						->where('ca.module_name = ?',self::$_module,'STRING')
						.'))'
				)
				->where('cb.status = 1')->order('cb.orders','asc'));
		for ($i = 0; $i < count($rows); $i++) {
			if( ($items = Core::cache()->load('block_'.$rows[$i]['class_name'])) === FALSE ){
				$file = APPLICATION_PATH.DS.'blocks'.DS.'block_'.$rows[$i]['class_name'].DS.'config.xml';
				//var_dump($file);
				if (file_exists($file)) {
					$config = new Zend_Config_Xml($file,'assets');
					$items = $config->toArray();
					Core::cache()->save($items, 'block_'.$rows[$i]['class_name']);						
				}				
			}
			if (is_array($items)){				
				foreach ($items as $key => $value) {
					$data[substr($key, 0, 2)][] = $value;
				}
			}	
		
		}
		//var_dump($data);
		return $data;
	}
	static function countModules($position,array $option = null ){
		if(self::$_notModule == 1){
			return 0;
		}
		elseif (in_array($position, Core_Blocks::$_notPosition)){
			return 0;
		}
		$db = Core::db();
		$select = $db->select()->from(array('cb'=>'core_blocks'),'COUNT(*)')
				->join(array('b'=>'core_fk_block_action'),'cb.id = b.id_block', array())
				->where('(b.id_action IS NULL) OR (b.id_action IN ('
						.$db->select()->from(array('ca'=>'core_actions'),array('id'))						
						->where('ca.action_name = ?',self::$_action,'STRING')
						->where('ca.controller_name = ?',self::$_controller,'STRING')
						->where('ca.module_name = ?',self::$_module,'STRING')
						.'))'
						)
				->where('cb.status = 1')
				->where('cb.position=?',$position,'STRING');
		if (! isset($option['access']) &&  $option['access'] != 1) {
			$select->where('cb.access = 0');
		}
		return $db->fetchOne($select);
	}	
	static function countModulesList($position,array $option = null ){
		if(self::$_notModule == 1){
			return array();
		}
		$db = Core::db();
		$select = $db->select()->from(array('cb'=>'core_blocks'),'*')
				->join(array('b'=>'core_fk_block_action'),'cb.id = b.id_block', array())
				->where('(b.id_action IS NULL) OR (b.id_action IN ('
						.$db->select()->from(array('ca'=>'core_actions'),array('id'))						
						->where('ca.action_name = ?',self::$_action,'STRING')
						->where('ca.controller_name = ?',self::$_controller,'STRING')
						->where('ca.module_name = ?',self::$_module,'STRING')
						.'))'
						)
				->where('cb.status = 1')
				->where('cb.position=?',$position,'STRING')->order('cb.orders','asc');
		if (! isset($option['access']) &&  $option['access'] != 1) {
			$select->where('cb.access = 0');
		}
		return $db->fetchAll($select);
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