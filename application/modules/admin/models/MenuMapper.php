<?php

class Admin_Model_MenuMapper 
{

    public function save($data, $where) 
    {
        try {
            $this->getDB()->update('menu', $data, $where);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getData() 
    {
        $row = $this->getDB()->fetchAll("select * from menu");
        return $row;
    }

    private function getDB() 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        return $db;
    }

}
