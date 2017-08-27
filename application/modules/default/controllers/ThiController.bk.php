<?php

class ThibkController extends Core_Controller_Action {

    private $user_id = null;

    public function init() {
        parent::init();
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('index', 'index', 'default');
        } else {
            $identity = $auth->getIdentity();
            $this->user_id = $identity['id'];
        }
    }

    private function saveDB($data) {
        $date = date('Y-m-d');
        $h=$data['h'];
        $m=$data['i'];
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("select * from user_exam where DATE(exam_date)='" . $date . "' AND user_id=" . $this->user_id);
        if (is_array($row) && count($row) > 0) {
            return;
        }

        $db->beginTransaction();
        try {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $sh = $identity['sh'];
            $sm = $identity['sm'];
            $modelUserExam = new Default_Model_Userexam();
            $userExamId = $modelUserExam->insert(
                    array(
                        'user_id' => $this->user_id,
                        'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                        'level' => $data['level_form2'],
                        'exam_date' => date("Y-m-d $h:$m:s"),
                        'sh' => $sh,
                        'sm' => $sm,
                        'eh' => $h,
                        'em' => $m,
                    )
            );
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $count_correct = 0;
            $user_exam_detail = new Default_Model_Userexamdetail();
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $user_exam_detail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                ));
            }

            $config_exam = $db->fetchRow("SELECT * FROM config_exam");

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $user_pass = new Default_Model_Userpass();
                $user_pass->insert(array(
                    'user_id' => $this->user_id,
                    'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                    'level' => $data['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    private function saveDBAgain($data) {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $user_exam = $db->fetchRow("select * from user_exam where user_id=" . $this->user_id . ' ORDER BY exam_date DESC LIMIT 1');
            if (is_array($user_exam) && count($user_exam) > 0) {
                $userExamId = $user_exam['id'];
            } else {
                $userExamId = -1;
            }
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $count_correct = 0;
            $user_exam_detail = new Default_Model_Userexamdetail();
            $user_exam_detail->delete('user_exam_id=' . $userExamId);
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $user_exam_detail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                ));
            }

            $modelUserExam = new Default_Model_Userexam();

            $config_exam = $db->fetchRow("SELECT * FROM config_exam");

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $user_pass = new Default_Model_Userpass();
                $user_pass->insert(array(
                    'user_id' => $this->user_id,
                    'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                    'level' => $data['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
                $allow_re_exam = 0;
            } else {
                $allow_re_exam = 1;
            }
            $modelUserExam->update(array('allow_re_exam' => $allow_re_exam, 'nganh_nghe_id' => $data['nganh_nghe_id_form2'], 'level' => $data['level_form2']), 'id=' . $userExamId);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    private function unsetSessionExaming() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $auth->clearIdentity();

        unset($identity['examing']);
        unset($identity['level']);
        unset($identity['nganh_nghe_id']);
        unset($identity['questionIds']);

        $auth->getStorage()->write($identity);
    }

    public function unsetsessionexamingAction() {
        $this->unsetSessionExaming();
    }

    public function viewresultAction() {
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("SELECT * FROM user_exam WHERE user_id=" . $this->user_id . " ORDER BY exam_date DESC LIMIT 1");
        if (!is_array($row) || count($row) == 0) {
            $this->_helper->redirector('index', 'thi', 'default');
            return;
        }
        $html = Default_Model_Userexam::getHtmlForExamResult($row['id'], $title_header);

        $date = explode(' ', $row['exam_date']);
        $date = explode('-', $date[0]);
        $this->createFilePdf($html, $date[0] . '_' . $date[1] . '_' . $date[2] . '.pdf', $title_header);
    }

    public function indexAction() {
        $message = Core::message()->getAll();
        if (is_array($message) && count($message) > 0) {
            $message = $message['message'];
            $this->view->success = $message[0];
        } else {
            $this->view->success = '';
        }

        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();

        $db = Core_Db_Table::getDefaultAdapter();

        $data = $this->_request->getPost();

        $user_exam = $db->fetchRow("select * from user_exam where user_id=" . $this->user_id . ' ORDER BY exam_date DESC LIMIT 1');
        if (is_array($user_exam) && count($user_exam) > 0 && $user_exam['allow_re_exam'] == '1') {
            if (count($data) > 0) {
                if (isset($data['question_id'])) {
                    $this->saveDBAgain($data);
                    $this->unsetSessionExaming();
                    Core::message()->addSuccess('Chúc mừng bạn đã hoàn thành kỳ thi lần này.');
                    $this->_helper->redirector('index', 'thi', 'default');
                    return;
                } else {
                    if (isset($identity['examing']) && $identity['examing'] == true) {
                        $nganhNgheId = $identity['nganh_nghe_id'];
                        $level = $identity['level'];
                        $questionIds = $identity['questionIds'];
                    } else {
                        $nganhNgheId = $data['nganh_nghe_id'];
                        $level = $data['level'];
                        $config_exam = $db->fetchRow("SELECT * FROM config_exam");
                        $questionIds = $this->getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
                    }
                    $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
                    $auth->clearIdentity();
                    if (!isset($identity['examing']) || $identity['examing'] == FALSE) {
                        $identity['examing'] = true;
                        $identity['nganh_nghe_id'] = $nganhNgheId;
                        $identity['level'] = $level;
                        $identity['questionIds'] = $questionIds;
                        $identity['sh'] = date('H');
                        $identity['sm'] = date('i');
                    }
                    $auth->getStorage()->write($identity);
                }
                $this->view->showFormNganhNgheCapBac = FALSE;
            } else {
                if (isset($identity['examing']) && $identity['examing'] == true) {
                    $level = $identity['level'];
                    $nganhNgheId = $identity['nganh_nghe_id'];
                    $questionIds = $identity['questionIds'];
                    $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
                    $this->view->showFormNganhNgheCapBac = false;
                } else {
                    $nganhNgheId = $level = 0;
                    $newQuestions = array();
                    $this->view->showFormNganhNgheCapBac = true;
                }
            }
            $this->view->questions = $newQuestions;
            $this->view->nganhNghes = $db->fetchAll('SELECT * FROM nganh_nghe');
            $this->view->nganhNgheId = $nganhNgheId;
            $this->view->level = $level;
            $this->view->miniutes = 0;
        } else {
            $date = date('Y-m-d');
            $h = $this->_getParam('h',date('H')) ;
            $m = $this->_getParam('i',date('i'));
            $row = $db->fetchRow("select DATE(`date`) AS date,sh,sm,eh,em from exam_time where DATE(`date`)='$date' AND ($h>sh OR ($h=sh AND $m>=sm)) AND ($h < eh OR ($h=eh AND $m<=em))");
            if (is_array($row) && count($row) > 0) {
                $start = new DateTime($row['date'] . ' ' . $row['sh'] . ':' . $row['sm'] . ':00');
                $current = new DateTime(date('Y-m-d H:i:00'));
                $diff = $current->diff($start);
                $this->view->eh=$row['eh'];
                $this->view->em=$row['em'];
            }
            if (!is_array($row) || count($row) == 0) {
                $this->view->questions = array();
                $this->view->nganhNghes = array();
                $this->view->nganhNgheId = 0;
                $this->view->level = 0;
                $this->view->miniutes = 0;
                $this->view->message = 'Thời điểm này không nằm trong thời gian thi hoặc bạn đã hết giờ thi.';
                $this->view->showFormNganhNgheCapBac = false;
            } else {
                $row = $db->fetchRow("select * from user_exam where DATE(exam_date)='" . $row['date'] . "' AND user_id=" . $this->user_id);
                if (is_array($row) && count($row) > 0) {
                    $this->view->questions = array();
                    $this->view->nganhNghes = array();
                    $this->view->nganhNgheId = 0;
                    $this->view->level = 0;
                    $this->view->miniutes = 0;
                    $this->view->message = '';
                    $this->view->showFormNganhNgheCapBac = false;
                } else {
                    if (count($data) > 0) {
                        if (isset($data['question_id'])) {
                            $this->saveDB($data);
                            $this->unsetSessionExaming();
                            
                            Core::message()->addSuccess('Chúc mừng bạn đã hoàn thành kỳ thi lần này.');
                            $this->_helper->redirector('index', 'thi', 'default');
                            return;
                        } else {


                            if (isset($identity['examing']) && $identity['examing'] == true) {
                                $nganhNgheId = $identity['nganh_nghe_id'];
                                $level = $identity['level'];
                                $questionIds = $identity['questionIds'];
                            } else {
                                $nganhNgheId = $data['nganh_nghe_id'];
                                $level = $data['level'];

                                $config_exam = $db->fetchRow("SELECT * FROM config_exam");
                                $questionIds = $this->getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
                            }
                            $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
                            $auth->clearIdentity();
                            if (!isset($identity['examing']) || $identity['examing'] == FALSE) {
                                $identity['examing'] = true;
                                $identity['nganh_nghe_id'] = $nganhNgheId;
                                $identity['level'] = $level;
                                $identity['questionIds'] = $questionIds;
                                $identity['sh'] = date('H');
                                $identity['sm'] = date('i');
                            }
                            $auth->getStorage()->write($identity);
                        }
                        $this->view->showFormNganhNgheCapBac = FALSE;
                    } else {
                        if (isset($identity['examing']) && $identity['examing'] == true) {
                            $level = $identity['level'];
                            $nganhNgheId = $identity['nganh_nghe_id'];
                            $questionIds = $identity['questionIds'];
                            $newQuestions = $this->getQuestionsByQuestionIds($questionIds);
                            $this->view->showFormNganhNgheCapBac = false;
                        } else {
                            $nganhNgheId = $level = 0;
                            $newQuestions = array();
                            $this->view->showFormNganhNgheCapBac = true;
                        }
                    }
                    $this->view->questions = $newQuestions;
                    $this->view->nganhNghes = $db->fetchAll('SELECT * FROM nganh_nghe');
                    $this->view->nganhNgheId = $nganhNgheId;
                    $this->view->level = $level;
                    $this->view->miniutes = $diff->h * 60 + $diff->i;
                }
            }
        }
    }

    private function getQuestionsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $questionIds = $this->getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number);
        return $this->getQuestionsByQuestionIds($questionIds);
    }

    private function getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $db = Core_Db_Table::getDefaultAdapter();
        $sql = "SELECT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=$level ORDER BY RAND() LIMIT " . $config_exam_number;
        $rows = $db->fetchAll($sql);
        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }
        return $questionIds;
    }

    private function getQuestionsByQuestionIds($questionIds) {
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

}
