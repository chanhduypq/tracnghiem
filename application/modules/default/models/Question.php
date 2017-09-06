<?php

class Default_Model_Question extends Core_Db_Table_Abstract 
{

    const BAC1 = '1';
    const BAC2 = '2';
    const BAC3 = '3';
    const BAC4 = '4';
    const BAC5 = '5';

    public $_name = "question";

    public function __construct() 
    {
        parent::__construct();
    }

    public function getQuestions(&$total, $limit = null, $start = null) 
    {

        
        if (Core_Common_Numeric::isInteger($limit) && Core_Common_Numeric::isInteger($start)) {
            $items = $this->select("*")->order(array('id'))->limit($limit, $start)->fetchAll();
        } else {
            $items = $this->select("*")->order(array('id'))->fetchAll();
        }

        $total = $this->select("count(*)")->fetchOne();


        for ($i = 0, $n = count($items); $i < $n; $i++) {
            $items[$i]['answers'] = $this->getAnswers($items[$i]['id']);
        }
        return $items;
    }

    public function getAnswers($parent_id) 
    {
        if (!is_numeric($parent_id)) {
            return array();
        }
        $mapper = new Default_Model_Answer();
        $items = $mapper->getAnswers($parent_id);
        return $items;
    }
    /**
     * lấy thông tin cả câu hỏi lẫn câu trả lời, đáp án cho mỗi câu hỏi đó
     * @param Core_Db_Table $db
     * @param array $questionIds
     * @return array
     */
    public static function getFullQuestions($db, $questionIds) 
    {        
        $questions = $db->fetchAll("select "
                . "question.content AS question_content,"
                . "answer.content AS answer_content,"
                . "dap_an.sign AS dap_an_sign,"
                . "answer.sign AS answer_sign,"
                . "question.id "
                . "from question "
                . "JOIN answer ON question.id=answer.question_id "
                . "JOIN dap_an ON dap_an.question_id=question.id "
                . "WHERE question.id IN (" . implode(',', $questionIds) . ")");
        $returnQuestions = array();
        foreach ($questions as $question) {
            $returnQuestions[$question['id']]['question_content'] = $question['question_content'];
            $returnQuestions[$question['id']]['answers'][] = array('answer_sign' => $question['answer_sign'], 'answer_content' => $question['answer_content'], 'is_dap_an' => ($question['answer_sign'] == $question['dap_an_sign']));
        }
        
        return $returnQuestions;
    }

}

?>