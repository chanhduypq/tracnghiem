<?php 

class Admin_Form_Question extends Core_Form {

    public function init() {
        parent::init();
        $this->buildElementsAutoForFormByTableName('question', $primaryName);

        $this->getElement('content')->setLabel('Nội dung câu hỏi:')->setUnique(true, 'question', $primaryName);

        $this->removeElement("level");
        $level = new Core_Form_Element_Select('level');
        $level->addMultiOptions(array(
                                    Default_Model_Question::BAC1 => 'Bậc 1', 
                                    Default_Model_Question::BAC2 => 'Bậc 2', 
                                    Default_Model_Question::BAC3 => 'Bậc 3', 
                                    Default_Model_Question::BAC4 => 'Bậc 4', 
                                    Default_Model_Question::BAC5 => 'Bậc 5'
                                    )
                                )
                ->setLabel('level:')->setValue(Default_Model_Question::BAC1)->setSeparator('')->setRequired();
        $this->addElement($level);
        
        $this->removeElement("is_dao");
        $is_dao = new Core_Form_Element_Checkbox('is_dao');
        $is_dao->setChecked(TRUE)->setValue('1')->setLabel('Có thể đảo đáp án'); 
        $this->addElement($is_dao);
    }

}
