<?php

class QuestionController extends Core_Controller_Action {

    public function init() {
        parent::init();
        $this->view->headTitle('Ngân hàng câu hỏi', true);
    }

    

    public function indexAction() 
    {

        $nganhNgheId = $this->_getParam('nganhNgheId');
        $level = $this->_getParam('level');

        if (Core_Common_Numeric::isInteger($level) === FALSE) {
            $level = 1;
        }
        if (Core_Common_Numeric::isInteger($nganhNgheId) === FALSE) {
            $nganhNgheId = 0;
        }

        $this->view->questionArray = Default_Model_Question::getQuestionsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level);
    }

    
}
