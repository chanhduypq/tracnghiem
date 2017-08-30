<?php

class Default_Model_User extends Core_Db_Table_Abstract 
{

    public $_name = "user";

    public function __construct() 
    {
        parent::__construct();
    }

    public function getUsers(&$total, $limit = null, $start = null) 
    {

        
        if (Core_Common_Numeric::isInteger($limit) && Core_Common_Numeric::isInteger($start)) {
            $items = $this->select("*")->where('is_admin is null OR is_admin=0')->order(array('id'))->limit($limit, $start)->fetchAll();
        } else {
            $items = $this->select("*")->where('is_admin is null OR is_admin=0')->order(array('id'))->fetchAll();
        }

        $total = $this->select("count(*)")->where('is_admin is null OR is_admin=0')->fetchOne();

        return $items;
    }

    public function getUser($id) 
    {
        $item = $this->select("*")->where("id=$id")->fetchRow();
        return $item;
    }

}

?>