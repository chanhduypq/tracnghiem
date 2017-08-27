<?php
/**
* @file: Menu.php
* @author: huuthanh3108@gmaill.com
* @date: 01-10-2012
* @company : http://dnict.vn
**/
class Core_Menu{
	protected $_sourceArr;
	public function __construct($sourceArr = null){
		$this->_sourceArr = $sourceArr;
	}
	public function buildRecursive($parents = 1){
		$resultArr = array();		
		$resultArr = $this->getCategory($this->_sourceArr,$parents);
		return $resultArr;
	}
	public function getCategory($data, $prentsID= 1) {
		$newArray = array();
		foreach($data as $value) {
			if ($value['id_parent'] == $prentsID) {
				$arr['label']       = $value['name'];
				if ((int)$value['is_system'] == 1) {
					$temp = $value['link'];
					$temp = explode('/', $value['link']);
					$arr['module']      = $temp[1];
					$arr['controller']    = $temp[2];
					$arr['action']       = $temp[3];
	
				}else{
					$arr['uri']=$value['link'];
				}
				$arr['pages']       = $this->getCategory($data, $value['id']);
				$newArray[]       = $arr;
			}
		}
		return $newArray;
	}
}