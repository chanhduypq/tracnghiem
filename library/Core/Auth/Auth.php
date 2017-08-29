<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
class Core_Auth_Auth{
	
	protected $_messageError = null;
	
	public function login($arrParam,$options = null){
            return true;
		
	}
	
	public function getError(){
		return $this->_messageError;
	}
	
	public function logout($arrParam = null,$options = null){
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		Core::acl()->destroy();
	}
	public function loginEmail($username,$password){
		$emailConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini','system');
// 		var_dump(explode('@', $username)) ;
		if(count(explode('@', $username)) > 0 ){
			$username = explode('@', $username);
			$username = $username[0];
// 			echo $username;exit;
		}	
		try{
		$protocol =  Core_System_Mail::createIncomingProtocol(
			$emailConfig->sys_email->incomingprotocol,
			$emailConfig->sys_email->incominghost,		
			$emailConfig->sys_email->incomingport,
			$emailConfig->sys_email->is_in_ssl
		);
			$protocol->login($username,$password);
			return 1;
		}catch (Exception $ex){
			//echo $ex->__toString();exit;
			return 0;
		}
	}
	
}