<?php 

class Admin_Form_Question extends Core_Form {

    public function init() {
        parent::init();
        $this->buildElementsAutoForFormByTableName('question', $primaryName);

        $this->getElement('content')->setLabel('Nội dung câu hỏi:')->setUnique(true, 'question', $primaryName);

        $this->removeElement("level");
        $level = new Core_Form_Element_Select('level');
        $level->setValue(Default_Model_Question::SO_CAP);
        $level->addMultiOptions(array(Default_Model_Question::SO_CAP => 'Sơ cấp', Default_Model_Question::TRUNG_CAP => 'Trung cấp', Default_Model_Question::CAO_CAP => 'Cao cấp'))->setLabel('level:')->setValue(Default_Model_Question::SO_CAP)->setSeparator('')->setRequired();
        $this->addElement($level);
    }

}
