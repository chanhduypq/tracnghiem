<?php
class Core_Themes {
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
	static function countModules($position){
		if(self::$_notModule == 1){
			return 0;
		}
		elseif (in_array($position, Core_Themes::$_notPosition)){
			return 0;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
// 		SELECT "a".* FROM "dvc_themes" AS "a"
// 		INNER JOIN "dvc_theme_privilege" AS "b" ON a.id = b.id_theme
// 		WHERE ((b.id_privilege = 0) OR b.id_privilege IN (SELECT id FROM dvc_privileges WHERE module='default' AND controller='index' AND action='index'))
		
// 		AND (a.position='left') AND (a.published=1)
		$select = $db->select()->from(array('a'=>_TABLE_PREFIX.'themes'),'COUNT(*)')
				->join(array('b'=>_TABLE_PREFIX.'theme_privilege'),'a.id = b.id_theme', array())
				->where('(b.id_privilege = 0) OR (b.id_privilege IN ('
						.$db->select()->from(_TABLE_PREFIX.'privileges',array('id'))						
						->where('action = ?',self::$_action,'STRING')
						->where('controller = ?',self::$_controller,'STRING')
						->where('module = ?',self::$_module,'STRING')
						.'))'
						)
				->where('a.position=?',$position,'STRING')
				->where('a.published=1');
		//echo $select;exit;
		$auth = Zend_Auth::getInstance();
		if ($auth->getIdentity() == false) {
			$select->where('(access = 0) OR (access IS NULL)');
		}
		return $db->fetchOne($select);		
	}	
	static function countModulesList($position){
		if(self::$_notModule == 1){
			return array();
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array('a'=>_TABLE_PREFIX.'themes'),array('*'))
				->join(array('b'=>_TABLE_PREFIX.'theme_privilege'),'a.id = b.id_theme', array())
				->where('(b.id_privilege = 0) OR b.id_privilege IN ('
						.$db->select()->from(_TABLE_PREFIX.'privileges',array('id'))						
						->where('action = ?',self::$_action,'STRING')
						->where('controller = ?',self::$_controller,'STRING')
						->where('module = ?',self::$_module,'STRING')
						.')'
						)
				->where('a.position=?',$position,'STRING')
				->where('a.published=1')
				->order('a.ordering','asc');
		//echo $select;exit;
		$auth = Zend_Auth::getInstance();
		if ($auth->getIdentity() == false) {
			$select->where('(access = 0) OR (access IS NULL)');
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
		Core_Themes::$_notPosition = $arr;
	}
	static function render($params){
		return '';
	}
		
}