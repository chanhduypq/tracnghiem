<?php 

class Admin_Form_User extends Core_Form 
{

    public function init() 
    {
        parent::init();
        $this->buildElementsAutoForFormByTableName('user');
        
        $this->removeElement("password");
        
        $this->removeElement("is_admin");
        
        $this->removeElement("danh_xung");
        $danh_xung=new Core_Form_Element_Select('danh_xung');
        $danh_xung->setValue('Anh');
        $danh_xung->addMultiOptions(array('Anh'=>'Anh','Chị'=>'Chị'))->setLabel('Danh xưng:')->setValue('Anh')->setSeparator('')->setRequired();
        $this->addElement($danh_xung);
       
        $this->getElement('full_name')->setLabel('Họ và tên:');
        
        $this->getElement('phone')->setLabel('Phone:');
    }
    

}
