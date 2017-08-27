<?php
/**
* @file: HomeMenu.php
* @author: huuthanh3108@gmaill.com
* @date: 05-12-2012
* @company : http://dnict.vn
**/
class Block_HomeMenu extends Zend_View_Helper_Abstract{
	private $_sourceArr;
	public function homeMenu(){
		$auth = Zend_Auth::getInstance();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if( ($container = Core::cache()->load('main_menu_'.Core::getUserId())) === FALSE ){
			$select = Core::single('Core/Menu')
			->select(array('name','link','id','id_parent','is_system'))
			->order('lft')
			->where('id !=1')
			->where('status = 1')
			->where('menutype = ?','mainmenu','STRING');
			if ($auth->hasIdentity() == false) {
				$select->where('access = 0');
			}
			//echo $select->__toString();
			$this->_sourceArr = $select->fetchAll();
			$container = new Zend_Navigation($this->buildRecursive(1));
			Core::cache()->save($container, 'main_menu_'.Core::getUserId());
		}
		if ($container instanceof Zend_Navigation_Container) {
			$uri = $request->getPathInfo();
			$this->view->navigation()->setContainer($container);
			$activeUri = $this->view->navigation()->findByUri($uri);
			//var_dump($activeUri);
			if ($activeUri !== null) {
				$activeUri->active = true;
			}
				
		}
		//$this->setView($this->view);
		return $this->view->navigation();
		//. $this->view->navigation()->breadcrumbs()->setLinkLast(false)->setMinDepth(0)->setMaxDepth(500)->render()
		;
		// 			Core::cache()->save($this->buildMenuUL_LI($rows), 'admintopmenu');
		// 		}
		// 		return Core::cache()->load('admintopmenu');
	}
	public function buildRecursive($parents = 0){
		$resultArr = array();
		$level = array();
		$resultArr = $this->getCategory($this->_sourceArr,$parents);
		return $resultArr;
	}
	public function getCategory($data, $prentsID= 0) {
		$newArray = array();
		foreach($data as $value) {
			//var_dump($value['link']);
			if ($value['id_parent'] == $prentsID) {
				$arr['label']       = $value['name'];
				if ((int)$value['is_system'] == 1) {
					$temp = $value['link'];
					$temp = explode('/', $value['link']);
					$arr['module']      = $temp[1];
					$arr['controller']    = $temp[2];
					$arr['action']       = $temp[3];
					if (Core::acl()->check(array('module'=>$arr['module'],
							'controller'=>$arr['controller'],
							'action'=>$arr['action'])) == true)
					{
						unset($arr['module']);
						unset($arr['controller']);
						unset($arr['action']);
						$arr['uri'] = $value['link'];
						$arr['pages']       = $this->getCategory($data, $value['id']);
						$newArray[]       = $arr;
					}
				}else{
					$arr['uri'] = $value['link'];
					$arr['pages']       = $this->getCategory($data, $value['id']);
					$newArray[]       = $arr;
				}
			}
		}
		return $newArray;
	}
}