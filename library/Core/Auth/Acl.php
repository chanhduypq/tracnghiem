<?php
/*
 * @author: huuthanh3108 @date: May 12, 2011 @company : http://dnict.vn
 */
class Core_Auth_Acl extends Zend_Acl {
	private $_roles;
	public function __construct($options = null) {
			$ns = Core::session('info');
			$nsInfo = $ns->getIterator();
			$info = $nsInfo['acl'];
			$auth = Zend_Auth::getInstance();
			if ($info ['roles'] == null) {
				if (!$auth->hasIdentity()) {
					$this->_roles[] = 'guest';
				}else{
					$this->_roles = $auth->getIdentity()->roles;
				}
				for ($i = 0; $i < count($this->_roles); $i++) {
					$this->addRole (new Zend_Acl_Role($this->_roles[$i]));
				}
				$info ['roles'] = $this->_roles;
			}else{
				$this->_roles = $info ['roles'];
			}	
			
			$groupPrivileges = ($info ['privileges']==null)?$this->createPrivilegeArray():$info ['privileges'];
			//var_dump($groupPrivileges);
			if ($groupPrivileges != null) {
				$this->allow($this->_roles, null, $groupPrivileges );
			}					
		}
	//Huy thong tin nguoi khi logout
	public function destroy(){
		Core::session('info')->unsetAll();		
	}
	public function check($arrParam = null) {
		$privilege = $arrParam ['module'] . '_' . $arrParam ['controller'] . '_' . $arrParam ['action'];
		$flagAccess = false;
		if (count ( $this->_roles ) > 0) {
			foreach ( $this->_roles as $role ) {
				if ($this->isAllowed ( $role, null, $privilege )) {					
					return true;
				}
			}
		}
		return $flagAccess;
	}
	public function createPrivilegeArray($opstions = null) {
            return array();
		
	}
}