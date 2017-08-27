<?php
/**
* @file: Footermain.php
* @author: huuthanh3108@gmaill.com
* @date: 07-12-2012
* @company : http://dnict.vn
**/
class Block_Footermain extends Zend_View_Helper_Abstract{

	public function footermain($data){		
		include 'html/default.phtml';
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}