<?php

class ApiclientController extends Core_Controller_Action {

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction() {
        $api=new Zend_Rest_Client('http://tracnghiem.local/apiserver');
        $post_data = array(
                'method' => 'post',
            'id'  => '1000',

        );
        $json=$api->restPost('http://tracnghiem.local/apiserver',array('id'=>1000));
        var_dump($json);
        $json=$api->restGet('http://tracnghiem.local/apiserver',array('id'=>1000));
        var_dump($json);
        
        
    }
    
    

    

}
