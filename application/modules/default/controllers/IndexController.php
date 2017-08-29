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
        $file_name='';
        $files = scandir(UPLOAD . "/public/guide/", 0);
        foreach ($files as $file){
            if ($file != '.' || $file != '..') {
                $file_name=$file;
            }
        }      
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' .$file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(UPLOAD . '/public/guide/'.$file_name));
        readfile(UPLOAD . '/public/guide/'.$file_name);
        exit;
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
