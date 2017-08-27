<?php
class Core_Model_UserAction extends Core_Db_Table_Abstract
{
	protected $_name = 'core_fk_user_action';
	protected $_primary = array('id_user','id_action');
}