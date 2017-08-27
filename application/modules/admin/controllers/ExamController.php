<?php

class Admin_ExamController extends Core_Controller_Action {

    public function init() {
        parent::init();
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('index', 'index', 'admin');
        } else {
            $identity = $auth->getIdentity();
            if (!isset($identity['user']) || $identity['user'] != 'admin') {
                $this->_helper->redirector('index', 'index', 'admin');
            }
        }
    }

    public function indexAction() {
        $db = Core_Db_Table::getDefaultAdapter();
        $data = $this->_request->getPost();
        $error_exam_time = $error_config_exam = $message = '';
        if (count($data) > 0) {
            if (trim($data['date']) != '') {
                $temp = explode('/', $data['date']);

                if (is_array($temp) && count($temp) == 3 && ctype_digit($temp[0]) && ctype_digit($temp[1]) && ctype_digit($temp[2]) && checkdate($temp[1], $temp[0], $temp[2])) {
                    $date = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
                    $row = $db->fetchRow("select DATE_FORMAT(exam_date,'%d/%m/%Y') AS exam_date from user_exam where DATE(exam_date)>='$date' ORDER BY exam_date DESC LIMIT 1");
                    if (is_array($row) && count($row) > 0) {
                        $error_exam_time = 'Ngày thi gần đây nhất là ngày ' . $row['exam_date'] . '. Vui lòng nhập lại.';
                    }
                } else {
                    $error_exam_time = 'Nhập ngày không đúng.';
                }
            } else {
                $error_exam_time = 'Vui lòng nhập ngày thi.';
            }

            if (!ctype_digit($data['phut']) || !ctype_digit($data['number'])) {
                $error_config_exam = 'Vui lòng nhập [Thời gian để hoàn thành một câu hỏi(phút)] và [Tổng số câu hỏi cho một lần thi] bằng số nguyên.';
            }
        }

        if (count($data) > 0 && $error_config_exam == '' && $error_exam_time == '') {
            $newtimestamp = strtotime($date . ' ' . $data['sh'] . ':' . $data['sm'] . ':00 + ' . ($data['phut'] * $data['number']) . ' minute');
            $end_time = date('Y-m-d H:i:s', $newtimestamp);
            $temp = explode(' ', $end_time);
            $temp = explode(':', $temp[1]);
            $eh = $temp[0];
            $em = $temp[1];
            $db->query("update exam_time set `date`='" . $date . "',sh=" . $data['sh'] . ",sm=" . $data['sm'] . ",eh=$eh,em=$em")->execute();

            $db->query("update config_exam set phan_tram=" . $data['phan_tram'] . ",phut=" . $data['phut'] . ",number=" . $data['number'])->execute();
            
            $db->query("update user_exam set allow_re_exam=0")->execute();
            $message = 'Lưu thành công.';
        }
        $row_config_exam = $db->fetchRow('select * from config_exam');
        $row_exam_time = $db->fetchRow('select * from exam_time');
        $temp = explode(" ", $row_exam_time['date']);
        $temp = explode('-', $temp[0]);
        $row_exam_time['date'] = $temp[2] . '/' . $temp[1] . '/' . $temp[0];
        if (count($data) > 0) {
            $this->view->date = $data['date'];
            $row_config_exam['phut'] = $data['phut'];
            $row_config_exam['number'] = $data['number'];
            $row_exam_time['sh'] = $data['sh'];
            $row_exam_time['sm'] = $data['sm'];
        } else {
            $this->view->date = $row_exam_time['date'];
        }

        $this->view->row_exam_time = $row_exam_time;
        $this->view->row_config_exam = $row_config_exam;
        $this->view->message = $message;
        $this->view->error_config_exam = $error_config_exam;
        $this->view->error_exam_time = $error_exam_time;
    }

}
