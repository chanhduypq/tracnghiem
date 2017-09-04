<?php 

class Admin_Form_Nganhnghe extends Core_Form 
{

    public function init() 
    {
        parent::init();
        $this->buildElementsAutoForFormByTableName('nganh_nghe');        
        $this->getElement('title')->setLabel('Tên ngành nghề:');   
    }

}
