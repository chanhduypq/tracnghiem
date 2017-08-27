<?php
class Core_View_Helper_CmsReplaceString extends  Zend_View_Helper_Abstract{
	
	public function cmsReplaceString($string,$options = null){
		if($options == null){
			$str = str_replace('\"','"',$string);
			$str = str_replace("\'","'",$str);
		}
		
		return $str;
	}
}

