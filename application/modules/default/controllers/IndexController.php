<?php

class IndexController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
        $this->view->headTitle('Trang chủ', true);
    }

    public function indexAction() 
    {
        $mapper = new Admin_Model_HomecontentMapper();
        $item = $mapper->getContent();
        $this->view->lienHe = $item["content"];
    }
    
    public function guideAction() 
    {
        $this->download(UPLOAD . "/public/guide/");
    }

    public function loginAction() 
    {


        $this->_helper->layout()->disableLayout();
        $data = $this->_request->getPost();
        if (count($data) > 0) {
            $username = $this->_request->getParam('username', null);
            $password = $this->_request->getParam('password', null);
            $index = new Admin_Model_IndexMapper();
            if ($index->login($username, $password)) {
                echo '';
            } else {
                echo 'error';
            }
        } else {
            $this->_helper->redirector('index', 'index', 'default');
        }
        return;
    }

    public function logoutAction() 
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_helper->redirector('index', 'index', 'default');
    }

}
