<?php
/**
* @file: CkeditorController.php
* @author: huuthanh3108@gmaill.com
* @date: Mar 13, 2012
* @company : http://dnict.vn
**/
class News_CkeditorController extends Core_Controller_Action{

	public function init(){
		parent::init();
	}
	public function ckfinderAction(){
		$this->_helper->layout()->disableLayout();
	}
}