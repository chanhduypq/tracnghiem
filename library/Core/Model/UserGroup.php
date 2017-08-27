<?php
/**
* @file: Group.php
* @author: huuthanh3108@gmaill.com
* @date: 24-08-2012
* @company : http://dnict.vn
**/
class Core_Model_UserGroup extends Core_Db_Table_Abstract
{
	protected $_name = 'core_fk_user_group';
	protected $_primary = array('id_user','id_group');
}