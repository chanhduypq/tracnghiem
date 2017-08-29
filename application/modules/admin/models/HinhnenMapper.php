<?php

class Admin_Model_HinhnenMapper 
{

    public function save($item_image) 
    {
        $data = array();
        $data['file_name'] = $item_image;


        try {
            $ret = $this->getDB()->fetchRow("select file_name from hinh_nen");
            $file_name = $ret['file_name'];
            if ($file_name == '') {
                $this->getDB()->insert('hinh_nen', $data);
            } else {

                $this->getDB()->update('hinh_nen', $data);
            }
        } catch (Exception $e) {
            return array('success' => false, 'file_name' => $file_name);
        }
        return array('success' => TRUE, 'file_name' => $file_name);
    }

    public function getInfo() 
    {
        $ret = array();
        try {
            $ret = $this->getDB()->fetchRow("select * from hinh_nen");
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
