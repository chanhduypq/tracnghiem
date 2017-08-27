<?php
/*
* @author: huuthanh3108
* @date: May 10, 2011
* @company : http://dnict.vn
*/
class Core_View_Helper_StatusImage extends Zend_View_Helper_Abstract
{
		public function statusImage($status=0,$option = null)
		{
			$imageName = 'publish_x.png';
			if($status != 0)$imageName='tick.png';
			return '<img src="'.$this->view->baseUrl().'/images/'.$imageName.'">';
		}
		public function setView(Zend_View_Interface $view)
		{
			$this->view = $view;
		}
}