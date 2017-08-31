<?php

class Admin_IndexController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            if (isset($identity['user']) && $identity['user'] == 'admin') {
                $this->_helper->redirector('index', 'nganhnghe', 'admin');
            }
        }

        $loginResult = $this->_request->getParam('loginResult');
        if ($loginResult == '0') {
            $this->view->loginResult = "Thông tin bạn vừa nhập không đúng.";
        }
    }

    public function loginAction() 
    {
        $username = $this->_request->getParam('username', null);
        $password = $this->_request->getParam('password', null);
        if ($username == null || $password == NULL) {
            $this->_helper->redirector('index', 'index', 'admin');
        } else {
            $index = new Admin_Model_IndexMapper();
            if ($index->loginAdmin($username, $password)) {
                $session = new Zend_Session_Namespace('url');
                $controller = $session->controller;
                $session->unsetAll();
                $this->_helper->redirector('index', $controller, 'admin');
            } else {
                $this->_helper->redirector('index', 'index', 'admin', array('loginResult' => '0'));
            }
        }
    }

    public function logoutAction() 
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_helper->redirector('index', 'index', 'admin');
    }

    public function changepasswordAction() 
    {
        $this->_helper->layout()->disableLayout();
    }

    public function ajaxchangepasswordAction() 
    {
        $this->_helper->layout()->disableLayout();
        $oldPassword = $this->_request->getParam('oldPassword');
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        if ($identity['password'] != sha1($oldPassword)) {
            echo 'error';
            return;
        }
        $newPassword = $this->_request->getParam('newPassword');
        $index = new Admin_Model_IndexMapper();
        $index->changePassword($identity['email'], $newPassword, 'user');
        echo "";
    }

}
