<?php

class Admin_Model_HomecontentMapper {

    public function save($data) {
        try {
            $this->getDB()->update('home_content', $data);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getContent() {

        try {
            $ret = $this->getDB()->fetchRow("select * from home_content");
        } catch (Exception $e) {
            return array();
        }
        return $ret;
    }

    private function getDB() {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        return $db;
    }

}
