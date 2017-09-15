<?php

class Admin_Model_HinhnentrangchuMapper 
{

    public function save($item_image) 
    {
        $data = array();
        $data['bg'] = $item_image;


        try {
            $ret = $this->getDB()->fetchRow("select bg from home_content");
            $file_name = $ret['bg'];
            $this->getDB()->update('home_content', $data);
        } catch (Exception $e) {
            return array('success' => false, 'file_name' => $file_name);
        }
        return array('success' => TRUE, 'file_name' => $file_name);
    }

    public function getInfo() 
    {
        $ret = array();
        try {
            $ret = $this->getDB()->fetchRow("select * from home_content");
        } catch (Exception $e) {
            return array();
        }
        return $ret;
    }

    private function getDB() 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        return $db;
    }

}
