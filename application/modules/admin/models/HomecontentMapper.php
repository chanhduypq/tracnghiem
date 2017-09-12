<?php

class Admin_Model_HomecontentMapper 
{

    public function save($data) 
    {
        try {
            $this->getDB()->update('home_content', $data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getContent() 
    {

        try {
            return $this->getDB()->fetchOne("select content from home_content");
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
