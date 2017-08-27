<?php

/**
 * @file: Categories.php
 * @author: huuthanh3108@gmaill.com
 * @date: 11-10-2012
 * @company : http://dnict.vn
 * */
class Admin_Form_Nganhnghe extends Core_Form {

    public function init() {
        parent::init();

        




        $this->buildElementsAutoForFormByTableName('nganh_nghe');

        
        
        $this->getElement('title')->setLabel('Tên ngành nghề:');
        
        
        
    }

}
