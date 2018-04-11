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
//        $client->setUri('http://tracnghiem.local/demo/apiserver');
        $client->setUri('https://api-sandbox.amazon.co.jp/user/profile?access_token=Atza%7CIwEBIE0lLjnop77ydUocgzjsNAnHL_AuzfgK12K5pyufQIMgIBJsCMFUPds4-H8IBqvwbcfy4TKNIfXrMT_o1aThmVmpfXnPMphJXgZZuKs1eWkgMwoXzq3uBQ7LqVMkIfSk8niam0_U1ribtsiK_2lKuAJSxxJoNlHyMNU42dGe8RPLiqcdjr8ZaE8lPLoUkgdAtaP_fjG9VFC7UmYHxBhXoQrSajVLDZJlIaI8zCrqdsei9DSaOWMY2xUW9TyDE67YfWFyV5C2gNZF4lPDeFw81FQkmXZ85iZFIAQ3kvMqnBABhVS4Il51Z8z3T0D6j_X_iylOz7G7ZyVyu8Xb61KP4dVEetNFD68kw0V_wOLDWmHfvtPfqPlm7f4Gjumb7tu8u_yvf9csn0ONtHwrVuhPMzBy1fUyqQPrhLKDHbyTtmImchMX7vCrRUvqPsElN2NRiLeoYFmcE9b5jj3UdNjZdyDUZWFv1goGXfflwfnyHxRau2iSxJN8DrEmrkx9rOGDb08J2RiE2AgBjOws47k_ACM9aWfHFOBTbkHua-04g3bwmZ_BzZJy0qMFIa7wLGzOuD71ovO8F5vmRZbvf2zT1DPMFHtj8S6JKwZx-Qssi_e15g');
        
//        $client->setParameterGet(array(
//            'id' => 1000
//        ));        
        $client->setMethod(Zend_Http_Client::GET);
        var_dump($client->request()->getBody());
        echo '<br>';
        exit;
        
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
