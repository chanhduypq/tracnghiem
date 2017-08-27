<?php

class Admin_GuideController extends Core_Controller_Action {

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
        $file_name='';
        $files = scandir(UPLOAD . "/public/guide/", 0);
        foreach ($files as $file){
            if ($file != '.' || $file != '..') {
                $file_name=$file;
            }
        }       

        $this->view->item = $file_name;
        $this->view->message= $this->getMessage();
    }

    public function downloadAction() {
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

    public function saveAction() {
        if (isset($_FILES['hinhnen']) && isset($_FILES['hinhnen']['name']) && $_FILES['hinhnen']['name'] != '') {
            $files = scandir(UPLOAD . "/public/guide/", 0);
            foreach ($files as $file){
                if ($file != '.' || $file != '..') {
                    @unlink(UPLOAD . "/public/guide/".$file);
                }
            }      

            
            $item_image = $_FILES['hinhnen']['name'];
            if (isset($item_image) && $item_image != "") {
                Zend_Loader::loadFile('./../library/Core/Common/File.php', null, true);
                $extension = @explode(".", $item_image);
                $extension = $extension[count($extension) - 1];
//                $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                $item_image='huong_dan_su_dung.'.$extension;
                $path = UPLOAD . "/public/guide/" . $item_image;
                move_uploaded_file($_FILES['hinhnen']['tmp_name'], $path);
            }
            Core::message()->addSuccess('Lưu thành công');
        }

        $this->_helper->redirector('index', 'guide', 'admin');
    }

}
