<?php

class Demo_ApiclientController extends Core_Controller_Action {

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction() {
//        $api=new Zend_Rest_Client('http://tracnghiem.local/apiserver');
//        $post_data = array(
//                'method' => 'post',
//            'id'  => '1000',
//
//        );
//        $json=$api->restPost('http://tracnghiem.local/apiserver',array('id'=>1000));
//        var_dump($json);
//        $json=$api->restGet('http://tracnghiem.local/apiserver',array('id'=>1000));
//        var_dump($json);

        $client = new Zend_Http_Client();        
        $client->setUri('http://tracnghiem.local/demo/apiserver');
        
        $client->setParameterGet(array(
            'id' => 1000
        ));        
        $client->setMethod(Zend_Http_Client::GET);
        var_dump($client->request()->getBody());
        echo '<br>';
        
        $client->setParameterPost(array(
            'id' => 1000
        ));  
        $client->setMethod(Zend_Http_Client::POST);
        var_dump($client->request()->getBody());
        echo '<br>';
        
        /**
         * đối với method delete hoặc put
         * thi setParameterPost cũng dc, setParameterGet cũng dc
         */
        $client->setParameterPost(array(
            'id' => 1000
        ));  
//        hoặc 
//        $client->setParameterGet(array(
//            'id' => 1000
//        ));  
        $client->setMethod(Zend_Http_Client::DELETE);
        var_dump($client->request()->getBody());
        echo '<br>';
        
        $client->setParameterPost(array(
            'id' => 1000
        ));  
//        hoặc 
//        $client->setParameterGet(array(
//            'id' => 1000
//        ));
        $client->setMethod(Zend_Http_Client::PUT);
        var_dump($client->request()->getBody());
        
        
    }

}
