<?php
/**
* @file: ModuleController.php
* @author: huuthanh3108@gmaill.com
* @date: 03-12-2012
* @company : http://dnict.vn
**/
class System_AdminModuleController extends Core_Controller_Backend{

public function init() {
		parent::init();
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('create', 'html')
		->addActionContext('update', 'html')
		->initContext();
	}
	public function indexAction() {
		$params = $this->getRequest ()->getParams ();
		$filter = array();
		if ($this->_getParam('search') != null) {
			$filter[] = array('field' => 'name', 'value'=>$this->_getParam('search'),'operator'=>'CN');
		}
		if ($this->_getParam('is_active') != null) {
			$filter[] = array('field' => 'is_active', 'value'=>$this->_getParam('is_active'),'operator'=>'EQ');
		}
		
		$page = $this->_getParam('page', 1);
		$limit = $this->_getParam('limit', Core::config('core/page/limit'));
		$start  = $this->_getParam('start', 0);
		$order  = $this->_getParam('filter_order', 'id') . ' ' . $this->_getParam('filter_order_Dir', 'DESC');
		$start = (($page-1)*$limit);
		
		$mapper = new System_Model_ModuleMapper();
		$rows = $mapper->listItems($filter, $order, $limit, $start);
		$paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Null($mapper->getCountItems()));
		$paginator->setDefaultScrollingStyle();
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->rows = $rows;
		$this->view->paginator=$paginator;
		$this->view->start = $start;
		$this->view->filter_order = $this->_getParam('filter_order');
		$this->view->filter_order_Dir = $this->_getParam('filter_order_Dir');
		$this->view->params = $params;
	}
	public function createAction() {
		$form = new System_Form_Module ();
		$form->setAction ( $this->_helper->url ( 'create' ) );
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getPost ();
			if ($form->isValid ( $formData )) {
				$mapper = new System_Model_ModuleMapper ();
				if ($mapper->create ( $formData )) {
					Core::message ()->addSuccess ( 'Thêm mới thành công' );
					$this->_helper->redirector ( 'index' );
				} else {
					Core::message ()->addError ( 'Lỗi. Xử lý thất bại.' );
					$form->populate ( $formData );
				}
			} else {
				$form->populate ( $formData );
			}
		}
		$this->view->form = $form;
	}
	public function updateAction() {
		$id = $this->_getParam ( 'id' );
		$mapper = new System_Model_ModuleMapper ();
		$form = new Admin_Form_Module ();
		$form->setAction ( $this->_helper->url ( 'update' ) );
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getPost ();
			if ($form->isValid ( $formData )) {
				if ($mapper->update ( $formData )) {
					Core::message ()->addSuccess ( 'Cập nhật thành công' );
					$this->_helper->redirector ( 'index' );
				} else {
					Core::message ()->addError ( 'Lỗi. Xử lý thất bại.' );
					$this->_helper->redirector ( 'update', null, null, array (
							'id' => $id 
					) );
				}
			} else {
				$form->populate ( $formData );
			}
		} else {
			$form->setDefaults ( $mapper->read ( $id ) );
		}
		$this->view->form = $form;
		$this->render ( 'create' );
	}
	public function deleteAction() {
		$mapper = new System_Model_ModuleMapper ();
		$valid = new Zend_Validate_NotEmpty ();
		$cid = $this->_getParam ( 'cid' );
		if ($valid->isValid ( $cid )) {
			try {
				$mapper->delete ( $cid );
				Core::message ()->addSuccess ( 'Xử lý thành công' );
			} catch ( Exception $e ) {
				Core::message ()->addError ( $e->__toString () );
			}
			$this->_helper->redirector ( 'index', 'module', 'admin' );
		}
		$this->_helper->viewRenderer->setNoRender ();
	}
	public function savestatusAction(){
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('is_active',0);
		$mapper = Core::single('Core/Module');
		$result = $mapper->find($id);
		$row = $result->current();
		$row->is_active = $status;
		$row->save();
		Core::message ()->addSuccess ( 'Cập nhật thành công' );
		$this->_helper->redirector ( 'index', 'module', 'admin' );
	}
}