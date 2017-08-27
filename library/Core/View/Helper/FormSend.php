<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
class Core_View_Helper_FormSend extends Zend_View_Helper_Abstract
{
	/**
	 * Tạo form send next user.
	 * 
	 * @param int $transition_id
	 * @return string $html
	 */
		public function formSend($transition_id,$forceone = true,$option = null)
		{
		$db = Core::db();
		$configOption = array();	
		//var_dump($option['not_phoihop']);	
		//Lấy danh sách các group
		$select = $db->select();
		$select->from(array('ac'=>'wf_activityaccesses'),array())
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())
				->join(array('g'=>'core_groups'),'ac.id_g = g.id',array('id','name'))
				->where('tr.id_t IN (?)',$transition_id)				
				->order('g.orders');
		//$arrGroup = $db->fetchAll($select);
		$arrGroup = $db->fetchPairs($select);
		//Lấy danh sách các phòng	
		$select = $db->select();
		$select->from(array('ac'=>'wf_activityaccesses'),array())
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())
				->join(array('dep'=>'base_coquan'),'ac.id_dep = dep.id',array('id','name'))
				->where('tr.id_t IN (?)',$transition_id)				
				->order('dep.lft');
		//echo $select;exit;
		$arrDep = $db->fetchPairs($select);
		//Lay danh sách các user
						
		$select1 = $db->select();
		$select1->from(array('ac'=>'wf_activityaccesses'),array(new Zend_Db_Expr('-1 AS id_g')))
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())
				->join(array('dep'=>'base_coquan'),'ac.id_dep = dep.id',array('id_dep'=>'id'))
				->join(array('u'=>'core_users'),'dep.id = u.id_coquan',array('id','fullname'))		
				->where('tr.id_t IN (?)',$transition_id)
				->where('u.id <> ?',Core::getUserId(),'INTEGER')
				->where('u.status = 1')									
				;
				
		$select2 = $db->select();
		$select2->from(array('ac'=>'wf_activityaccesses'),array(new Zend_Db_Expr('ug.id_group AS id_g')))
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())
				->join(array('dep'=>'base_coquan'),'ac.id_dep = dep.id',array('id_dep'=>'id'))				
				->join(array('u'=>'core_users'),'dep.id = u.id_coquan',array('id','fullname'))
				->join(array('ug'=>'core_fk_user_group'),'u.id = ug.id_user',array())
				->where('tr.id_t IN (?)',$transition_id)
				->where('u.id <> ?',Core::getUserId(),'INTEGER')
				->where('u.status = 1')									
				;
				
		$select3 = $db->select();
		$select3->from(array('ac'=>'wf_activityaccesses'),array(new Zend_Db_Expr('g.id AS id_g'),new Zend_Db_Expr('-1 AS dep')))
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())
				->join(array('g'=>'core_groups'),'ac.id_g = g.id',array())
				->join(array('ug'=>'core_fk_user_group'),'g.id = ug.id_group',array())				
				->join(array('u'=>'core_users'),'ug.id_user = u.id',array('id','fullname'))						
				->where('tr.id_t IN (?)',$transition_id)
				->where('u.id <> ?',Core::getUserId(),'INTEGER')
				->where('u.status = 1')									
				;
				
		$select4 = $db->select();
		$select4->from(array('ac'=>'wf_activityaccesses'),array(new Zend_Db_Expr('-1 AS id_g'),new Zend_Db_Expr('-1 AS dep')))
				->join(array('tr'=>'wf_transitions'),'ac.id_a = tr.id_a_end',array())				
				->join(array('u'=>'core_users'),'ac.id_u = u.id',array('id','fullname'))		
				->where('tr.id_t IN (?)',$transition_id)
				->where('u.status = 1')									
				;
		//echo $select4;exit;
		$select = $db->select()->union(array($select1,$select2,$select3,$select4))->order('fullname');
		//echo $select;exit;		
		$arrUser = $db->fetchAll($select);
?>
<fieldset>
<table width="90%" class="admintable">
	<tr>
		<td nowrap="nowrap" class="key">Đơn vị</td>
		<td>
		<?php echo $this->view->formSelect('wf_seldep', -1, array('class'=>'wf_seldep','onchange'=>'onchangeDep(this);','style'=>'min-width:200px'), array('-1'=>'-- Chọn đơn vị --') + $arrDep);?>
		<span id="ERRwf_seldep" class="error"></span>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="key">Nhóm</td><td><?php echo $this->view->formSelect('wf_selg', -1, array('class'=>'wf_selg','onchange'=>'onchangeGroups(this);','style'=>'min-width:200px'), array('-1'=>'-- Chọn bộ phận --') + $arrGroup);?>
		<span id="ERRwf_selg" class="error"></span>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="key">Người nhận</td>
		<td>
		<?php echo $this->view->formSelect('wf_nextuser', -1, array('class'=>'wf_nextuser required','style'=>'min-width:200px'));?>
		<span id="ERRwf_nextuser" class="error"></span>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" class="key">Nội dung</td>
		<td><?php echo $this->view->formTextarea('wf_noidung', '', array('rows'=>'3','cols'=>'30','class'=>'wf_noidung required','style'=>'width:200px;')); ?>
		<span id="ERRwf_noidung" class="error"></span>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top" class="key">Tập tin đính kèm</td>
		<td>
			<?php 
			echo $this->view->attachment('idFileDinhKem',0,1,Core::getYear(),-1);
			?>
		</td>
	</tr>		
</table>
</fieldset>
<?php
/**
 * kiem tra co phai multisend hay forceone
 */

if(is_array($transition_id)){
	for($i=0,$n=count($transition_id);$i<$n;$i++){
		if(Wf_Model_Engine::IsMulti($transition_id[$i]) && $forceone==false){
			echo '<input type="hidden" name="wf_nexttransition[]" value="'.$transition_id[$i].'" />';		
		}	
	}		
}else{
		$select = $db->select();
		$select->from(array('a'=>'wf_transitions'),array('hanxuly'))
				->where('id_t = ?',$transition_id,'INTEGER')
		;
		$hanxuly = $db->fetchOne($select);
	echo '<input type="hidden" name="wf_nexttransition" value="'.$transition_id.'" />'."\n";
	echo '<input type="hidden" name="wf_hanxuly_user" id="wf_hanxuly_user" value="'.$hanxuly.'" />'."\n";
} 
?>
<script type="text/javascript">
var wf_arr_user = new Array();
<?php 
for($i=0;$i<count($arrUser);$i++){
	echo "wf_arr_user[".$i."] = new Array('".$arrUser[$i]['id_g']."','".$arrUser[$i]['id_dep']."','".$arrUser[$i]['id']."','".$arrUser[$i]['fullname']."');\n";
}
?>
function onchangeDep(obj){	
	FillComboBy2Combo(document.getElementById("wf_selg"),obj,document.getElementById("wf_nextuser"),wf_arr_user);	
}
function onchangeGroups(obj){
	FillComboBy2Combo(obj,document.getElementById("wf_seldep"),document.getElementById("wf_nextuser"),wf_arr_user);	
}
document.getElementById("wf_seldep").value=-1;
FillComboBy2Combo(document.getElementById("wf_selg"),document.getElementById("wf_seldep"),document.getElementById("wf_nextuser"),wf_arr_user);
if(document.getElementById("wf_nextuser").options.length==0){			
	document.getElementById("wf_seldep").value='-1';
	FillComboBy2Combo(document.getElementById("wf_selg"),document.getElementById("wf_seldep"),document.getElementById("wf_nextuser"),wf_arr_user);
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