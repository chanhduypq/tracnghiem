<?php
/**
* @file: pageTitle.php
* @author: chanhduypq@gmail.com
* @date: 27-08-2012
* @company : http://dnict.vn
**/
class Core_View_Helper_PageTitle extends Zend_View_Helper_Abstract{
	public function pageTitle(){
		//return $this;
		$pageTitle = '<h3>'.$this->view->pageTitle.'</h3>';
		if($this->view->pageSubTitle != '' || $this->view->pageSubTitle != null){
			$pageTitle.='<span>'.$this->view->pageSubTitle.'</span>';
		}
		return $pageTitle;
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}	
}