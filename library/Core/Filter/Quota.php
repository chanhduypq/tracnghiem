<?php
class Core_Filter_Quota implements Zend_Filter_Interface{
	
	
	public function filter($value){
		
		$value = str_replace("\'","'",$value);
		$value = str_replace('\"','"',$value);
		
		return $value;
		
	}
}