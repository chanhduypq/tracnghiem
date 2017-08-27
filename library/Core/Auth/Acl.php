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
		$db = Core::db();
		$auth = Zend_Auth::getInstance();		
		if (!$auth->hasIdentity()) {
			return $db->fetchCol($db->select()
											->from(array('ca'=>'core_actions'),array (new Zend_Db_Expr('"ca"."module_name"||\'_\'|| "ca"."controller_name"||\'_\'||"ca"."action_name" AS privileges')))
											->where( 'ca.is_public = 1'));
			
		}else{
		$user = $auth->getIdentity();
		
		$select1 = $db->select()
						->from(array('ca'=>'core_actions'),array (new Zend_Db_Expr('"ca"."module_name"||\'_\'|| "ca"."controller_name"||\'_\'||"ca"."action_name" AS privileges')))
						->join(array('cfga'=>'core_fk_group_action'), 'ca.id=cfga.id_action',array())
						->join(array('cg'=>'core_groups'), 'cfga.id_group = cg.id',array())
						->where( 'cg.code IN (?)', $user->roles);		
		$select2 = $db->select()
						->from(array('ca'=>'core_actions'),array (new Zend_Db_Expr('"ca"."module_name"||\'_\'|| "ca"."controller_name"||\'_\'||"ca"."action_name" AS privileges')))
						->join(array('cfua'=>'core_fk_user_action'), 'ca.id=cfua.id_action',array())
						->where( 'cfua.id_user = ?',$user->id,'INTEGER' );
		
		$select3 = $db->select()
						->from(array('ca'=>'core_actions'),array (new Zend_Db_Expr('"ca"."module_name"||\'_\'|| "ca"."controller_name"||\'_\'||"ca"."action_name" AS privileges')))
						->where( 'ca.is_public = 1');
		
		$select = $db->select ()->union ( array (
				$select1,
				$select2,
				$select3 
		) );
		//echo $select->__toString();exit;
		return  $db->fetchCol($select);		
		}
	}
}