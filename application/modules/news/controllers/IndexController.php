<?php
/**
* @file: IndexController.php
* @author: huuthanh3108@gmaill.com
* @date: 15-10-2012
* @company : http://dnict.vn
**/
class News_IndexController extends Core_Controller_Action {
	public function init() {
		parent::init ();
		// 		$this->_helper->viewRenderer->setNoRender();
		// 		$this->_helper->layout ()->disableLayout ();
	}
	public function indexAction(){
		$cat_id = $this->_getParam('cat_id');
		$filter = array();
		//trang thai 
		$filter[] = array('field' => 'nc.state', 'value'=>1);
		if ((int)$cat_id > 0 ) {
			$filter[] = array('field' => 'nc.state', 'value'=>$this->_getParam('state'));
		}
		if ($this->_getParam('search') != null) {
			$filter[] = array('field' => 'nc.title', 'value'=>$this->_getParam('search'),'operator'=>'LIKE');
		}
		$id_cat = $this->_getParam('id_cat');
		if ($id_cat != null) {
			$filter[] = array('field' => 'nc.id_cat', 'value'=>$id_cat);
		}
		//var_dump($filter);
		$page = $this->_getParam('page', 1);
		$limit = $this->_getParam('limit', Core::config('core/page/limit'));
		$start  = $this->_getParam('start', 0);
		$order  = $this->_getParam('filter_order', 'nc.id') . ' ' . $this->_getParam('filter_order_Dir', 'DESC');
		$start = (($page-1)*$limit);
		$mapper = new News_Model_ContentMapper();
		$rows = $mapper->listItems($filter, $order, $limit, $start);
		$paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Null($mapper->getCountItems()));
		$paginator->setDefaultScrollingStyle();
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		$this->view->params = $this->_getAllParams();
		$this->view->categories = array(''=>'-Tất cả-') + News_Model_Collect_Categories::collect(array(array('field' => 'nc.id', 'value'=>1,'operator'=>'NE')));
		$this->view->rows = $rows;
		$this->view->paginator=$paginator;
		$this->view->start = $start;
		$this->view->filter_order = $this->_getParam('filter_order');
		$this->view->filter_order_Dir = $this->_getParam('filter_order_Dir');
	}
	public function readAction(){
		$id = $this->_getParam('id');
		$mapper = new News_Model_ContentMapper();
		$this->view->content = $mapper->read($id);
	}
}