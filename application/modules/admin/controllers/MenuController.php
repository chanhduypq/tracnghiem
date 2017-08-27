<?php

class Admin_MenuController extends Core_Controller_Action {

    public function init() {
        parent::init();
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('index', 'index', 'admin');
        } else {
            $identity = $auth->getIdentity();
            if (!isset($identity['user']) || $identity['user'] != 'admin') {
                $this->_helper->redirector('index', 'index', 'admin');
            }
        }
    }

    public function indexAction() {
        $mapper = new Admin_Model_MenuMapper();
        if ($this->_request->isPost()) {
            $id_array = $this->_getParam("id");
            $text_array = $this->_getParam("text");
            for ($i = 0, $n = count($id_array); $i < $n; $i++) {
                $data = array(
                    'text' => $text_array[$i]
                );
                $mapper->save($data, "id=" . $id_array[$i]);
            }
            Core::message()->addSuccess('Lưu thành công');
        }
        $this->view->message= $this->getMessage();
        $this->view->data = $mapper->getData();
    }

}