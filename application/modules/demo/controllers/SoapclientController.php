<?php

class Demo_SoapclientController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function demosoapAction() 
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $ws = new Zend_Soap_Client('http://tracnghiem.local/demo/soapserver/wsdl', array(
            "soap_version" => SOAP_1_2,
        ));
        //        //một cách khác để gọi function
        //        var_dump($ws->getSoapClient()->__soapCall('noArgument', array()));
        var_dump($ws->__call('noArgument', array()));
        var_dump($ws->__call('hasArgument', array(5, 10)));
        var_dump($ws->getFunctions());
    }

}
