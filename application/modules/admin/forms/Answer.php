<?php

/**
 * @file: Categories.php
 * @author: huuthanh3108@gmaill.com
 * @date: 11-10-2012
 * @company : http://dnict.vn
 * */
class Admin_Form_Answer extends Core_Form {

    public function init() {
        parent::init();

        $question_id = new Core_Form_Element_Hidden("question_id");


        $this->buildElementsAutoForFormByTableName('answer');

        $sign= new Core_Form_Element_Select('sign');
        $sign->setMultiOptions(array(
                                    'A'=>'A',
                                    'B'=>'B',
                                    'C'=>'C',
                                    'D'=>'D',
                                    'E'=>'E',
                                    'F'=>'F',
                                    'G'=>'G',
                                    )
                );
        $sign->setLabel('Ký hiệu:');
        
        
        $this->getElement('content')->setLabel('Nội dung câu trả lời:');
//        $this->getElement('sign')->setMaxStringLength(1)->setLabel('Ký hiệu:');
        
        $this->removeElement('question_id');
        $this->addElement($question_id);
        $this->removeElement('sign');
        $this->addElement($sign);
    }

}
