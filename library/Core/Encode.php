<?php
class Core_Encode{
	
	public function password($value,$options = null){
		$newPass = md5($value);
		return $newPass;
	}
}