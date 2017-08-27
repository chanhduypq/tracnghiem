<?php
/**
 * @author: huuthanh3108
* @date: Aug 29, 2011
* @company : http://dnict.vn
**/
class Block_Xhtml extends Zend_View_Helper_Abstract{

	public function xhtml($data){
		echo ($data['content']);
		//include 'html/xhtml.phtml';
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}