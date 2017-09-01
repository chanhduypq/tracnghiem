<?php

class Admin_Model_HeaderpdfMapper
{

    public function save($data) 
    {
        try {

            $data['content'] = str_replace('&Agrave;', 'À', $data['content']);
            $data['content'] = str_replace('&Ocirc;', 'Ô', $data['content']);
            $data['content'] = str_replace('&Acirc;', 'Â', $data['content']);
            $data['content'] = str_replace('&nbsp;', ' ', $data['content']);
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
            $ret['content'] = str_replace('&Agrave;', 'À', $ret['content']);
            $ret['content'] = str_replace('&Ocirc;', 'Ô', $ret['content']);
            $ret['content'] = str_replace('&Acirc;', 'Â', $ret['content']);
            $ret['content'] = str_replace('&nbsp;', ' ', $ret['content']);
        } catch (Exception $e) {
            return array();
        }
        return $ret;
    }
    
    public static function getHeader(){
        try {
            $ret = Core_Db_Table::getDefaultAdapter()->fetchRow("select * from header_pdf");
            $ret['content'] = str_replace('&Agrave;', 'À', $ret['content']);
            $ret['content'] = str_replace('&Ocirc;', 'Ô', $ret['content']);
            $ret['content'] = str_replace('&Acirc;', 'Â', $ret['content']);
            $ret['content'] = str_replace('&nbsp;', ' ', $ret['content']);
            return $ret['content'];
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
