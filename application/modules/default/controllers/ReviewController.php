<?php

class ReviewController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();        
    }

    private function saveDB($data) 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $sh = $identity['sh_review'];
            $sm = $identity['sm_review'];

            $h = intval(date('H'));
            $m = intval(date('i'));
//            if ($m == 0) {
//                $m = 59;
//                $h--;
//            } else {
//                $m--;
//            }
            $modelUserExam = new Default_Model_Userreview();
            $userExamId = $modelUserExam->insert(
                    array(
                        'user_id' => $this->getUserId(),
                        'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                        'level' => $data['level_form2'],
                        'review_date' => date("Y-m-d $h:$m:s"),
                        'sh' => $sh,
                        'sm' => $sm,
                        'eh' => $h,
                        'em' => $m,
                        'es'=> rand(1, 59),
                    )
            );
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $count_correct = 0;
            $user_exam_detail = new Default_Model_Userreviewdetail();
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }
                $user_exam_detail->insert(array(
                    'user_review_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    

    
    public function viewresultAction() 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("SELECT * FROM user_review WHERE user_id=" . $this->getUserId() . " ORDER BY review_date DESC LIMIT 1");
        if (!is_array($row) || count($row) == 0) {
            $this->_helper->redirector('index', 'review', 'default');
            return;
        }
        $html = Default_Model_Userreview::getHtmlForReviewResult($row['id'], $title_header);

        $date = explode(' ', $row['review_date']);
        $date = explode('-', $date[0]);
        $this->createFilePdf($html, $date[0] . '_' . $date[1] . '_' . $date[2] . '.pdf', $title_header);
    }

    public function indexAction() 
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        $db = Core_Db_Table::getDefaultAdapter();

        $data = $this->_request->getPost();
        if (count($data) > 0) {
            if (isset($data['question_id'])) {
                $this->saveDB($data);
                $this->unsetSessionExaming();
                $this->_helper->redirector('index', 'review', 'default');
                return;
            } else {

                if (isset($identity['examing_review']) && $identity['examing_review'] == true) {
                    $nganhNgheId = $identity['nganh_nghe_id_review'];
                    $level = $identity['level_review'];
                    $questionIds = $identity['questionIds_review'];
                } else {
                    $nganhNgheId = $data['nganh_nghe_id'];
                    $level = $data['level'];
                    $config_exam = $db->fetchRow("SELECT * FROM config_exam");
                    $questionIds = $this->getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
                }
                $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
                $auth->clearIdentity();
                if (!isset($identity['examing_review']) || $identity['examing_review'] == FALSE) {
                    $identity['examing_review'] = true;
                    $identity['time_start_review'] = time();
                    $identity['nganh_nghe_id_review'] = $nganhNgheId;
                    $identity['level_review'] = $level;
                    $identity['questionIds_review'] = $questionIds;
                    $identity['sh_review'] = date('H');
                    $identity['sm_review'] = date('i');
                }
                $auth->getStorage()->write($identity);
            }
        } else {

            if (isset($identity['examing_review']) && $identity['examing_review'] == true) {
                $level = $identity['level_review'];
                $nganhNgheId = $identity['nganh_nghe_id_review'];
                $questionIds = $identity['questionIds_review'];
                $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
            } else {
                $nganhNgheId = $level = 0;
                $newQuestions = array();
            }
        }

        if (isset($identity['examing_review']) && $identity['examing_review'] == true) {
            $miniutes = (time() - $identity['time_start_review']) / 60;
            $miniutes = round($miniutes, 0);
        } else {
            $miniutes = 0;
        }

        $this->view->questions = $newQuestions;
        $this->view->nganhNghes = $db->fetchAll('SELECT * FROM nganh_nghe');
        $this->view->nganhNgheId = $nganhNgheId;
        $this->view->level = $level;
        $this->view->miniutes = $miniutes;
    }

    private function getQuestionsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) 
    {
        $questionIds = $this->getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number);
        return $this->getQuestionsByQuestionIds($questionIds);
    }

    private function getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $sql = "SELECT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=$level ORDER BY RAND() LIMIT " . $config_exam_number;
        $rows = $db->fetchAll($sql);
        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }
        return $questionIds;
    }

    private function getQuestionsByQuestionIds($questionIds) 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $newQuestions = array();
        $questions = $db->fetchAll("SELECT question.id,question.content,answer.sign,answer.content AS answer_content,answer.id AS answer_id,dap_an.sign AS dapan_sign FROM question JOIN nganhnghe_question ON question.id = nganhnghe_question.question_id JOIN answer ON answer.question_id=question.id JOIN dap_an ON dap_an.question_id=question.id WHERE question.id IN (" . implode(',', $questionIds) . ") ORDER BY question.id ASC,answer.sign ASC");
        foreach ($questions as $question) {
            $newQuestions[$question['id']]['id'] = $question['id'];
            $newQuestions[$question['id']]['content'] = $question['content'];
            $newQuestions[$question['id']]['answers'][$question['answer_id']] = array('content' => $question['answer_content'], 'sign' => $question['sign'], 'id' => $question['answer_id']);
            $newQuestions[$question['id']]['dapan_sign'] = $question['dapan_sign'];
        }
        return $newQuestions;
    }
    
    private function unsetSessionExaming() 
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $auth->clearIdentity();

        unset($identity['examing_review']);
        unset($identity['time_start_review']);
        unset($identity['level_review']);
        unset($identity['nganh_nghe_id_review']);
        unset($identity['questionIds_review']);

        $auth->getStorage()->write($identity);
    }

}
