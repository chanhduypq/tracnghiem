<?php

class Demo_SoapserverController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
        Zend_Loader::loadClass('WsClass_General');
    }

    public function soapAction() 
    {
        // disable layouts and renderers
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // initialize server and set URI
        $server = new Zend_Soap_Server('http://tracnghiem.local/demo/soapserver/wsdl');
        // set SOAP service class      
        $server->setClass('WsClass_General');

        // register exceptions that generate SOAP faults
        $server->registerFaultException(array('WsClass_Exception'));

        // handle request
        $server->handle();
    }

    public function wsdlAction() 
    {
        // disable layouts and renderers
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // set up WSDL auto-discovery
        $wsdl = new Zend_Soap_AutoDiscover();

        // attach SOAP service class
        $wsdl->setClass('WsClass_General');

        // set SOAP action URI
        $wsdl->setUri('http://tracnghiem.local/demo/soapserver/soap');

        // handle request 
        $wsdl->handle();
    }

}
