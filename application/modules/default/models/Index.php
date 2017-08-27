<?php

class Default_Model_Index extends Core_Db_Table_Abstract {

    public $_name = "nganh_nghe";

    public function __construct() {
        parent::__construct();
    }

    public function getMatHangs() {
        $items = $this->select("*")->fetchAll();
        return $items;
    }

    

}

?>