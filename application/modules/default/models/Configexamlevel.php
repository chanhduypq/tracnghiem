<?php

class Default_Model_Configexamlevel extends Core_Db_Table_Abstract 
{

    public $_name="config_exam_level";    
    public function __construct() 
    {
        parent::__construct();
             
    }
    public function getConfigExamLevels() 
    {                 
        $items=  $this->select("*")->order('level')->fetchAll();          
        return $items;        
    }     
      
    

}

?>