<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
/*
 * class nay phan quyen cho guest nhung nguoi chua dang nhap
 * se lay su lieu tu privileges voi is_public = 1
 */
class Core_Auth_GuestAcl{
		protected $_acl;
		protected $_roles;	
		public function __construct($options = null){
			$acl = new Zend_Acl();
			$acl->addRole(new Zend_Acl_Role('guest'));
			$frontendOptions = array(
			          'lifetime' => 7200,
			          'automatic_serialization' => TRUE
			       );
			       $backendOptions = array('cache_dir' => FILE_CACHE_DIRECTORY);
			       $cache = Zend_Cache::factory('Core',
			                               'File',
			                               $frontendOptions,
			                               $backendOptions);
	        		$db = Core::single('Core/Action');
					$select = $db->select(array('id','module_name','controller_name','action_name'))
								 ->where('ca.is_public = 1');					
					$result  = $db->fetchAll($select);
			foreach ($result as $key){
				$acl->allow('guest', null, array($key['module_name'] . '_' . $key['controller_name'] . '_' . $key['action_name']));
			}
			$this->_acl = $acl;
		}		
		public function isAllowed($arrParam){
		$privilege = $arrParam['module'] . '_' . $arrParam['controller'] . '_' .$arrParam['action'];
		$flagAccess = false;
		if($this->_acl->isAllowed('guest',null,$privilege)){
				return true;
		}
	    return $flagAccess;
		}
		/**
	 * @return the $_acl
	 */
	public function getAcl() {
		return $this->_acl;
	}

		/**
	 * @param Zend_Acl $_acl
	 */
	public function setAcl($_acl) {
		$this->_acl = $_acl;
	}
		
}