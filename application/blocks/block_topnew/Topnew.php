<?php
/**
* @file: Topnew.php
* @author: huuthanh3108@gmaill.com
* @date: 05-12-2012
* @company : http://dnict.vn
**/
class Block_Topnew extends Zend_View_Helper_Abstract{

	public function topnew($data){		
		include 'html/default.phtml';
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}