<?php
/**
* @file: CategoriesController.php
* @author: huuthanh3108@gmaill.com
* @date: 11-10-2012
* @company : http://dnict.vn
**/
class News_CategoriesController extends Core_Controller_Action {
	public function init() {
		parent::init ();
// 		$this->_helper->viewRenderer->setNoRender();
// 		$this->_helper->layout ()->disableLayout ();
	}
	public function indexAction(){
		$filter = array();
		if ($this->_getParam('published') != null) {
			$filter[] = array('field' => 'published', 'value'=>$this->_getParam('published'));
		}
		if ($this->_getParam('search') != null) {
			$filter[] = array('field' => 'title', 'value'=>$this->_getParam('search'),'operator'=>'LIKE');
		}
		$menutype = $this->_getParam('id_parent');
		if ($menutype != null) {
			$filter[] = array('field' => 'id_parent', 'value'=>$menutype);
		}
		//var_dump($filter);
		$page = $this->_getParam('page', 1);
		$limit = $this->_getParam('limit', Core::config('core/page/limit'));
		$start  = $this->_getParam('start', 0);
		$order  = $this->_getParam('filter_order', 'id') . ' ' . $this->_getParam('filter_order_Dir', 'DESC');
		$start = (($page-1)*$limit);
		$mapper = new News_Model_CategoriesMapper();		
		$rows = $mapper->listItems($filter, $order, $limit, $start);
		$paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Null($mapper->getCountItems()));
		$paginator->setDefaultScrollingStyle();
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		$this->view->params = $this->_getAllParams();
		$this->view->itemParents = array(''=>'-Tất cả-');
		$this->view->rows = $rows;
		$this->view->paginator=$paginator;
		$this->view->start = $start;
		$this->view->filter_order = $this->_getParam('filter_order');
		$this->view->filter_order_Dir = $this->_getParam('filter_order_Dir');
	}
	public function createAction(){
		$form = new News_Form_Categories();		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$mapper = new News_Model_CategoriesMapper();
				if($mapper->create($formData)){
					Core::message()->addSuccess('Thêm mới thành công');
					$this->_helper->redirector('index');
				}else{
					Core::message()->addError('Lỗi. Xử lý thất bại.');
					$form->populate($formData);
				}
			}else{
				$form->populate($formData);
			}
		}
		$this->view->form = $form;
	}
	public function updateAction(){
		$id = $this->_getParam('id');		
		$mapper = new News_Model_CategoriesMapper();
		$row = $mapper->read($id);
		$form = new News_Form_Categories(array('id'=>$row['id']));
		//var_dump($row);
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {				
				if($mapper->update($formData)){
					Core::message()->addSuccess('Thêm mới thành công');
					$this->_helper->redirector('index');
				}else{
					Core::message()->addError('Lỗi. Xử lý thất bại.');
					$form->populate($formData);
				}
			}else{
				$form->populate($formData);
			}
		}else {
			$form->setDefaults($row);
		}
		$this->view->form = $form;
		$this->render('create');
	}
	public function deleteAction(){
		$mapper = new News_Model_CategoriesMapper ();
		$valid = new Zend_Validate_NotEmpty ();
		$cid = $this->_getParam ( 'cid' );
		if ($valid->isValid ( $cid )) {
			try {
				$mapper->delete ( $cid );
				Core::message ()->addSuccess ( 'Xử lý thành công' );
			} catch ( Exception $e ) {
				Core::message ()->addError ( $e->__toString () );
			}
		}		
		$this->_helper->redirector ( 'index');
	}
}