<?php

class Default_Model_Nganhnghe extends Core_Db_Table_Abstract 
{

    public $_name = "nganh_nghe";

    public function __construct() 
    {
        parent::__construct();
    }

    public function getNganhNghes() 
    {
        $items = $this->select("*")->fetchAll();
        return $items;
    }

    

}

?>