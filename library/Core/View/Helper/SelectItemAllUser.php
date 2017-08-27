<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
class Core_View_Helper_SelectItemAllUser extends Zend_View_Helper_Abstract
{
	/**
	 * Tạo form send next user. 
	 * 
	 * @return string $html
	 */
	public $view;
		public function selectItemAllUser($params = null,$option = null)
		{
		    
		$auth = Zend_Auth::getInstance();
		$user = $auth->getIdentity();
		//var_dump($user);
		//var_dump($user->id_dep);
		$db = Zend_Db_Table::getDefaultAdapter();
		$mapperGroup = new CoreAdmin_Model_GroupsMapper();
		$mapperPhongBan = new CoreBase_Model_PhongbanMapper();
		$arrGroup = $mapperGroup->itemInSelectbox(array('status'=>1));
		$select = $db->select();
		$is_phapnhan = $db->fetchOne($select->from('core_departments_config',array('is_phapnhan'))->where('id_dep = ?',$user->id_dep,'INTEGER'));
		
		//Lay danh sách các user
		
		$select1 = $db->select();
		$select1->from(array('u'=>_TABLE_PREFIX.'users'),array(new Zend_Db_Expr('-1 AS id_g'),'id_dep','id','fullname'))
		->join(array('dep'=>_TABLE_PREFIX.'departments'),'u.id_dep = dep.id',array())
		->where('u.status = 1')
		;
		
		$select2 = $db->select();
		$select2->from(array('ug'=>_TABLE_PREFIX.'user_group'),array('id_g'=>'id_group'))
		->join(array('u'=>_TABLE_PREFIX.'users'),'u.id = ug.id_user',array('id_dep','id','fullname'))
		->where('u.status = 1')
		;
		
		$select3 = $db->select();
		$select3->from(array('ug'=>_TABLE_PREFIX.'user_group'),array('id_g'=>'id_group'))
		->join(array('u'=>_TABLE_PREFIX.'users'),'u.id = ug.id_user',array(new Zend_Db_Expr('-1 AS dep'),'id','fullname'))
		->where('u.status = 1')
		;
		
		$select4 = $db->select();
		$select4->from(array('u'=>_TABLE_PREFIX.'users'),array(new Zend_Db_Expr('-1 AS id_g'),new Zend_Db_Expr('-1 AS dep'),'id','fullname'))
		->where('u.status = 1')
		;

		
		if ($is_phapnhan == 1) {		    
		    $arrDep = $db->fetchPairs($db->select()->from(_TABLE_PREFIX.'departments',array('id','name'))->where('id = ?',$user->id_dep,'INTEGER'))  + $mapperPhongBan->itemInSelectboxForQLGV($user->id_dep,array('is_phapnhan'=>0,'is_leader'=>$user->dep_leader));
		}else{
		    $arrDep = $db->fetchPairs($db->select()->from(_TABLE_PREFIX.'departments',array('id','name'))->where('id = ?',$user->id_dep,'INTEGER'));
		    //$arrDep = $db->fetchPairs($db->select()->from(_TABLE_PREFIX.'departments',array('id','name'))->where('id = ?',$user->id_dep,'INTEGER')) + $mapperPhongBan->itemInSelectboxForQLGV($user->id_dep,array('is_phapnhan'=>0));
		    $select3->where('u.id_dep = ?',$user->id_dep,'INTEGER');
		}
	
		$select = $db->select()->union(array($select1,$select2,$select3,$select4))->order('fullname');
		//echo '<pre>'.$select.'</pre>';
		//exit;
		$arrUser = $db->fetchAll($select);
		
		//var_dump($option['not_phoihop']);	
		//Lấy danh sách các group
	
		//Lấy danh sách các phòng
		


?>
<table width="90%" class="admintable">
	<tr>
		<td nowrap="nowrap" class="key">Đơn vị</td>
		<td>
		<?php echo $this->view->formSelect('dep_nguoigui', -1, array('class'=>'dep_nguoigui','onchange'=>'onchangeDep(this);','style'=>'min-width:200px'),$arrDep);?>
		<span id="ERRdep_nguoigui" class="error"></span>
		</td>
	</tr>	
	<tr>
		<td nowrap="nowrap" class="key">Nhóm</td><td><?php echo $this->view->formSelect('gdep_nguoigui', -1, array('class'=>'gdep_nguoigui','onchange'=>'onchangeGroups(this);','style'=>'min-width:200px'), array('-1'=>'-- Chọn bộ phận --') + $arrGroup);?>
		<span id="ERRgdep_nguoigui" class="error"></span>
		</td>
	</tr>	
	<tr>
		<td nowrap="nowrap" class="key">Người</td>
		<td>
		<?php echo $this->view->formSelect('nguoigui', -1, array('class'=>'nguoigui','style'=>'min-width:200px'));?>
		<span id="ERRnguoigui" class="error"></span>
		</td>
	</tr>		
</table>
<script type="text/javascript">
var ARR_NGUOIGUI = new Array();
<?php 
for($i=0;$i<count($arrUser);$i++){
	echo "ARR_NGUOIGUI[".$i."] = new Array('".$arrUser[$i]['id_g']."','".$arrUser[$i]['id_dep']."','".$arrUser[$i]['id']."','".$arrUser[$i]['fullname']."');\n";
}
?>
function onchangeDep(obj){	
	FillComboBy2Combo(document.getElementById("gdep_nguoigui"),obj,document.getElementById("nguoigui"),ARR_NGUOIGUI);	
}
function onchangeGroups(obj){
	FillComboBy2Combo(obj,document.getElementById("dep_nguoigui"),document.getElementById("nguoigui"),ARR_NGUOIGUI);	
}
document.getElementById("dep_nguoigui").value=-1;
FillComboBy2Combo(document.getElementById("gdep_nguoigui"),document.getElementById("dep_nguoigui"),document.getElementById("nguoigui"),ARR_NGUOIGUI);
if(document.getElementById("nguoigui").options.length==0){			
	document.getElementById("dep_nguoigui").value='-1';
	FillComboBy2Combo(document.getElementById("gdep_nguoigui"),document.getElementById("dep_nguoigui"),document.getElementById("nguoigui"),ARR_NGUOIGUI);
}
</script>
<?php 
		//echo $view->formSelect('id_a_begin[]', 1, array('multiple' => false,'class'=>'inputbox','style'=>'width:100px;'), $arrGroup);
//		var_dump($arrDep);
//		var_dump($arrGroup);
//		var_dump($arrUser);
	}
	public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
}