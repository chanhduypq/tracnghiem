<?php
class Core_Controller_Plugin_Permission extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        $infoAuth = $auth->getIdentity();
     
        $moduleName = $this->_request->getModuleName();
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();        
        /*
		 * luon luon cho phep vao module default
		 */
        if (($moduleName == 'default')) {
            return;
        }
        //----------START-KIEM TRA QUYEN TRUY CAP VAO ADMIN -------------
        $flagAdmin = true;
        $flagPage = 'none';
		if (Core::acl()->check(array('module'=>$moduleName,'controller'=>$controllerName,'action'=>$actionName)) == false) {
        	$flagPage = 'no-access';
        }
        //----------END-KIEM TRA QUYEN TRUY CAP VAO ADMIN -------------
        //echo '<br>' . $flagPage;
        if ($flagPage != 'none') {
            if ($flagPage == 'login') {
                $this->_request->setModuleName('default');
                $this->_request->setControllerName('auth');
                $this->_request->setActionName('login');
            }
            if ($flagPage == 'no-access') {
                $this->_request->setModuleName('default');
                $this->_request->setControllerName('auth');
                $this->_request->setActionName('no-access');
            }
        }
    }
}

