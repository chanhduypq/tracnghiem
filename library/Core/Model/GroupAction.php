<?php
class Core_Model_GroupAction extends Core_Db_Table_Abstract{
	protected $_name = 'core_fk_group_action';
	protected $_primary = array('id_action','id_group');
} 
?>