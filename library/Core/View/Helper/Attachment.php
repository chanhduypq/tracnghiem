<?php
/*
* @author: huuthanh3108
* @date: May 12, 2011
* @company : http://dnict.vn
*/
class Core_View_Helper_Attachment extends Zend_View_Helper_Abstract
{
	public function attachment($iddiv,$idObject,$is_new,$year,$type,$isreadonly=0,$is_getcontent=0,$pdf=0)
	{
		return '
		<div id="'.$iddiv.'"></div>
		<script type="text/javascript">
		jQuery(function($){
			jQuery("#'.$iddiv.'").load("/attachment/index/index?iddiv='.$iddiv.'&idObject='.$idObject.'&is_new='.$is_new.'&year='.$year.'&type='.$type.'&is_getcontent='.$is_getcontent.'&pdf='.$pdf.'",function(){
				$("#'.$iddiv.' input:checkbox").attr("checked","cheched");
			});
		});
		</script>
		';
	}
}
