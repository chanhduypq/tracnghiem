<?php

class Demo_ApiserverController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction() {
        $this->getResponse()->setBody('Hello World');
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {      
        $array=array('key1'=>'value1','key2'=>'value2','id'=> $this->_getParam('id'));
        $this->getResponse()->setBody(json_encode($array));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $array=array('key1'=>'value11','key2'=>'value22','id'=> $this->_getParam('id'));
        $this->getResponse()->setBody(json_encode($array));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function putAction() {
        $this->getResponse()->setBody('resource updated'.$this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function deleteAction() {
        $this->getResponse()->setBody('resource deleted'.$this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }

}
