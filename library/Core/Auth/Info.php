<?php
class Core_Auth_Info{

	//Ham khoi tao cua lop
	public function __construct(){
		$ns = new Zend_Session_Namespace('info');
		//$ns->setExpirationSeconds(1800);
	}
	
	//Tao thong tin cua nguoi dang nhap
	public function createInfo(){
		//echo '<br>' . __METHOD__;
		$auth = Zend_Auth::getInstance();
		$infoAuth = $auth->getIdentity();		
		$this->setMemberInfo($infoAuth);
		$this->setGroupInfo($infoAuth);
		$this->setAclInfo();
	}
	
	//Huy thong tin nguoi khi logout
	public function destroyInfo(){
		$ns = new Zend_Session_Namespace('info');
		$ns->unsetAll();
	}
	
	//Thiet lap thong tin cua User khi ho login
	public function setMemberInfo($infoAuth){
		$db = Core::single('Core/User');
		$select  = $db->select(array('*'))
					  ->where('cu.id = ? ',$infoAuth->id,'INTEGER');
		$result  = $db->fetchRow($select);	
		
		$ns = new Zend_Session_Namespace('info');
		$ns->member = $result;
	}
	
	//Thiet lap thong tin cua nhom chua User khi ho login
	public function setGroupInfo($infoAuth){
		$db = Core::single('Core/Group');
		$select  = $db->select(array('*'))->setIntegrityCheck(false)
					  ->join(array('b'=>'core_fk_user_group'),'b.id_group = cg.id',array())
					  ->where('b.id_user = ? ',$infoAuth->id,INTEGER);
		$result  = $db->fetchAll($select);
		$ns = new Zend_Session_Namespace('info');		
		$ns->group = $result;
	}
	
	//Thiet lap thong phan quyen cua nhom
	public function setAclInfo(){
		$acl = new Core_Auth_Acl();		
		$acl->createPrivilegeArray();
		$acl->createRole();
	}
	
	//Lay thong phan quyen cua nhom
	public function getAclInfo($part = null){
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();		
		if($part == null){
			$info = $nsInfo['acl'];
		}else{
			$info = $nsInfo['acl'];
			$info = $info[$part];
		}
		
		return $info;
	}
	
	//Lay thong tin cua user da su he thong
	public function getMemberInfo($part = null){
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
		
		if($part == null){
			$info = $nsInfo['member'];
		}else{
			$info = $nsInfo['member'];
			$info = $info[$part];
		}
		
		return $info;
	}
	
	//Lay thong tin cua nbhom ma user da su he thong
	public function getGroupInfo($part = null){
		$ns = new Zend_Session_Namespace('info');
		$nsInfo = $ns->getIterator();
		
		if($part == null){
			$info = $nsInfo['group'];
		}else{
			$info = $nsInfo['group'];
			$info = $info[$part];
		}
		return $info;
	}
	
	//Lay tat ca cac thong tin cua user da dang nhap
	public function getInfo(){
		$ns = new Zend_Session_Namespace('info');
		$info = $ns->getIterator();
		return $info;
	}
}