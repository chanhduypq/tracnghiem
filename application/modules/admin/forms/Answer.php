<?php 

class Admin_Form_Answer extends Core_Form 
{

    public function init() 
    {
        parent::init();        
        $this->buildElementsAutoForFormByTableName('answer');

        $this->getElement('content')->setLabel('Nội dung câu trả lời:');
        
        $this->removeElement('question_id');
        $question_id = new Core_Form_Element_Hidden("question_id");
        $this->addElement($question_id);
        
        $this->removeElement('sign');
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
                )
                ->setLabel('Ký hiệu:')
                ;
        $this->addElement($sign);
    }

}
