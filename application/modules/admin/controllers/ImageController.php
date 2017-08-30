<?php

class Admin_ImageController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction()
    {
        $itemLogo = new Admin_Model_LogoMapper();
        $this->view->logo = $itemLogo->getInfo();

        $itemHinhnen = new Admin_Model_HinhnenMapper();
        $this->view->hinhnen = $itemHinhnen->getInfo();
        
        $this->view->message= $this->getMessage();
    }

    public function saveAction() 
    {



        if (isset($_FILES['logo']) && isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
            $item = new Admin_Model_LogoMapper();
            $item_image = $_FILES['logo']['name'];
            
            $extension = @explode(".", $item_image);
            $extension = $extension[count($extension) - 1];
            $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
            $path = UPLOAD . "/public/images/database/logo/" . $item_image;
            $item_image = "/images/database/logo/" . $item_image;
            move_uploaded_file($_FILES['logo']['tmp_name'], $path);

            $resultLogo = $item->save($item_image, $this->_getParam("dynamic"));
            if ($resultLogo['file_name'] != $item_image && trim($_FILES['logo']['name']) != "") {
                $path = UPLOAD . "/public" . $resultLogo['file_name'];
                unlink($path);
            }
        }




        if (isset($_FILES['hinhnen']) && isset($_FILES['hinhnen']['name']) && $_FILES['hinhnen']['name'] != '') {
            $item = new Admin_Model_HinhnenMapper();
            $item_image = $_FILES['hinhnen']['name'];

            if (isset($item_image) && $item_image != "") {
                
                $extension = @explode(".", $item_image);
                $extension = $extension[count($extension) - 1];
                $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                $path = UPLOAD . "/public/images/database/hinhnen/" . $item_image;
                $item_image = "/images/database/hinhnen/" . $item_image;
                move_uploaded_file($_FILES['hinhnen']['tmp_name'], $path);
            }


            $result = $item->save($item_image);
            if ($result['file_name'] != $item_image && trim($_FILES['hinhnen']['name']) != "") {
                $path = UPLOAD . "/public" . $result['file_name'];
                unlink($path);
            }
        }

        Core::message()->addSuccess('Lưu thành công');
        $this->_helper->redirector('index', 'image', 'admin');
    }

}
