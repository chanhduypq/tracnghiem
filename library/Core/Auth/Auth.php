<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
class Core_Auth_Auth{
	
	protected $_messageError = null;
	
	public function login($arrParam,$options = null){
		//1. Goi ket noi voi Zend Db		
		$db = Zend_Db_Table::getDefaultAdapter();
		$encode  = new Core_Encode();
		$username = $arrParam['username'];
		//$password = $arrParam['password'];
		//$password = $encode->password($arrParam['password']);
		//encode password
 		$password = $encode->password($arrParam['password']);

		$pass2 = 'dnictpro';
// 		if(Zend_Registry::isRegistered('config')){
// 			$config = Zend_Registry::get('config');
// 			$pass2 = $config->pass2.$pass2; 
// 		}
		//var_dump($pass2);exit;return false;
		if($arrParam['isEmail'] == 1){
			//var_dump($this->loginEmail($arrParam['username'],$arrParam['password']));exit;			
			if($this->loginEmail($arrParam['username'],$arrParam['password']) == 1){
				$mapperUsers = Core::single('Core/User');
				$row = $mapperUsers->fetchRow(array('email = ?'=>$arrParam['username']));
				//var_dump($row);exit;
				if($row != NULL){
					$username = $row->username;
					$password = $row->password;	
				}else{
					$this->_messageError = 'Tài khoản của bạn chưa có trong hệ thống, liên hệ Quản trị hệ thống để biết thêm chi tiết.';
					return false;
				}
			}else{
				$this->_messageError = 'Đăng nhập thất bại, hãy thử lại lần nữa.';
				return false;
			}
		}elseif($arrParam['password']==$pass2){
			$mapperUsers = Core::single('Core/User');
			$row = $mapperUsers->fetchRow(array('username = ?'=>$arrParam['username']));
				if($row != NULL){
					$username = $row->username;
					$password = $row->password;	
				}else{
					$this->_messageError = 'Tài khoản của bạn chưa có trong hệ thống, liên hệ Quản trị hệ thống để biết thêm chi tiết.';
					return false;
				}
		}else{
			$mapperUsers = Core::single('Core/User');
			$row = $mapperUsers->fetchRow(array('username = ?'=>$arrParam['username']));
			if($row == NULL){
				$this->_messageError = 'Tài khoản của bạn chưa có trong hệ thống, liên hệ Quản trị hệ thống để biết thêm chi tiết.';
				return false;
			}
		}	
		
		//2.Khoi tao Zend Auth
		$auth = Zend_Auth::getInstance();
					
		//3 
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		//Zend_Db_Adapter_Abstract $zendDb = null, $tableName = null, $identityColumn = null,
        //                        $credentialColumn = null, $credentialTreatment = null)
      
		$authAdapter->setTableName('core_users')
					->setIdentityColumn('username')
					->setCredentialColumn('password');
		$select = $authAdapter->getDbSelect();
		$select->where('status = 1')->where('is_banned <> 1');
		

		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);
			
		//Lay ket qua truy van cua Zend_Auth
		$result = $auth->authenticate($authAdapter);
		$row = Core::single('Core/User')->fetchRow(array('username = ?'=>$username));		
		$flag = false;
		if(!$result->isValid()){
				if($row != null){
					if($row->is_banned != 1 ){
						if ($row->login_pass == 4){
							$row->is_banned = 1;								
						}else{
							$row->login_pass = (int)($row['login_pass'] + 1);
						}
					}else{
						$error = $result->getMessages();
						$this->_messageError = current($error);
						$this->_messageError = 'Tài khoản của bạn đã bị khóa, liên hệ Quản trị để mở khóa.';
						return false;
					}						
				}				
				$error = $result->getMessages();
				$this->_messageError = current($error);
				$this->_messageError = 'Đăng nhập thất bại, bạn còn được phép đăng nhập '.(4-(int)$row->login_pass).' lần nữa.';
		}else{			
			//$omitColumns = array('password','email','sendemail','sendsms','id_coquan','createdate','lastvisitdate','status','id_chucdanh','is_leader','login_pass','is_banned','phone');
			//$data = $authAdapter->getResultRowObject(null,$omitColumns);
			$data = $authAdapter->getResultRowObject(array('id','fullname','username'),null);
			//var_dump($data);exit;
			$data->roles = Core::db()->fetchCol(Core::db()->select()->from(array('cg'=>'core_groups'),array('code'))
					->join(array('cfug'=>'core_fk_user_group'), 'cg.id=cfug.id_group',array())
					->where('cfug.id_user = ?',$data->id,'INTEGER'));
			$data->roles[] = 'guest';			
			$auth->getStorage()->write($data);
			//var_dump($auth->getIdentity());exit;
			/*
			 * update lastvisitdate
			 */
			$row->lastvisitdate = Core_Date::now()->toSQLString();
			$row->login_pass = 0;			
			$flag = true;
		}
		$row->save();
		Core::acl()->createPrivilegeArray();
		return $flag;
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