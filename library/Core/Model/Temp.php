<?php
/**
* @file: Temp.php
* @author: huuthanh3108@gmaill.com
* @date: 20-10-2012
* @company : http://dnict.vn
**/
class Core_Model_Temp extends Core_Db_Table_Abstract
{
	protected $_name = 'core_temp';
	protected $_primary = array('object_id' , 'user_id');
}