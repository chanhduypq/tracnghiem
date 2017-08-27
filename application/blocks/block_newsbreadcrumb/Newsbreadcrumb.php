<?php
/**
* @file: Newsbreadcrumb.php
* @author: huuthanh3108@gmaill.com
* @date: 06-12-2012
* @company : http://dnict.vn
**/
class Block_Newsbreadcrumb extends Zend_View_Helper_Abstract{
		
	public function newsbreadcrumb($data){		
	
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$id_content = (int)$request->getParam('id');
		$id_category = Core::single('News/Content')->getIdCatById($id_content);
		$table = Core::single('News/Categories');
		$id_category = ((int)$id_category > 0 )?$id_category:1;
		$row = $table->fetchRow('id = '.$id_category);		
		$select = $table->select(array('id','title'))
						->order('lft')
						->where('id !=1')
						->where('published = 1')
						->where('lft >= ?',$row->lft,'INTEGER')
						->where('rgt <= ?',$row->rgt,'INTEGER');
		if (Core::getUserId() == null) {
			$select->where('access = 0');
		}
		//echo $select->__toString();exit;
		$rows = $select->fetchAll();
		unset($select,$row);		
		include 'html/default.phtml';
		
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}	
}