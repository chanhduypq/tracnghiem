<?php

class Admin_Model_HeaderpdfMapper
{

    public function save($data) 
    {
        try {
            $data['json']=json_encode($data['text']);
            unset($data['text']);
            unset($data['content']);
            $this->getDB()->update('header_pdf', $data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getContent() 
    {

        try {
            $ret = $this->getDB()->fetchRow("select * from header_pdf");
        } catch (Exception $e) {
            return array();
        }
        return $ret;
    }
    
    public static function getHeader(){
        try {
            $ret = Core_Db_Table::getDefaultAdapter()->fetchRow("select json from header_pdf");
            return $ret['json'];
        } catch (Exception $e) {
            return '';
        }
    }

    private function getDB() 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        return $db;
    }

}
