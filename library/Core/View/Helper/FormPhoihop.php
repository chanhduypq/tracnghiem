<?php
/**
 *
 * @author User
 * @version 
 */
class Core_View_Helper_FormPhoihop extends Zend_View_Helper_Abstract
{
	public function formPhoihop()
	{
		$auth = Zend_Auth::getInstance();
		$user = $auth->getIdentity();
		$db = Zend_Db_Table::getDefaultAdapter();
		$mapperPhongBan = new CoreBase_Model_PhongbanMapper();
		$arr_phongban = $mapperPhongBan->itemInSelectbox();
		$select = $db->select();
		$select->from(array('a'=>_TABLE_PREFIX.'users'),array('id','fullname','id_dep'))				
				->where('a.id NOT IN (?)',$user->id)
				->where('a.status = 1')
				->where('a.is_banned <> 1')
		;
		$rows = $db->fetchAll($select);
		$arr_user = array();
		foreach ($arr_phongban as $key=>$val) {			
			$arr_temp = array();
			for ($k = 0; $k < count($rows); $k++) {				
				if($rows[$k]['id_dep']==$key){
					$arr_temp[$rows[$k]['id']] = $rows[$k]['fullname'];
				}
			}
			if (count($arr_temp) > 0 ) {
				$arr_user[$val] = $arr_temp;
			}			
			//var_dump($key); 
		}
		//echo $this->view->formSelect('phongban-phoihop', -1, array('style'=>"width:370px"),$arr_phongban);
		//echo '<br />';
		$js="
<script>		
jQuery(document).ready(function() { 
	jQuery('#id_u_phs').multiselect({
      noneSelectedText: 'Chọn người phối hợp xử lý',
      selectedList: 9,
      selectedText: 'đã chọn # người',
      //minWidth:300,
      height:200,
      checkAllText:'Tất cả',
      uncheckAllText:'Hủy',
      //autoOpen:true,
      position: {
		 my: 'center',
		 at: 'center'

   	  }
   }).multiselectfilter({placeholder:'nhập từ khóa'});
});
</script>";
		return $js. $this->view->formSelect('id_u_phs', -1, array('multiple'=>'true', 'size'=>5,'style'=>"width:350px"),$arr_user);		
	}
}
