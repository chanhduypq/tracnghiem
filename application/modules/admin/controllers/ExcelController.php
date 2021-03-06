<?php

class Admin_ExcelController extends Core_Controller_Action {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $this->view->message = $this->getMessage();
    }

    public function saveAction() {
        if (isset($_FILES['excel']) && isset($_FILES['excel']['name']) && $_FILES['excel']['name'] != '') {
            $files = scandir(UPLOAD . "/public/excel/", 0);
            foreach ($files as $file) {
                if ($file != '.' || $file != '..') {
                    @unlink(UPLOAD . "/public/excel/" . $file);
                }
            }


            $item = $_FILES['excel']['name'];
            if (isset($item) && $item != "") {
                $extension = @explode(".", $item);
                $extension = $extension[count($extension) - 1];
                $item = 'data.' . $extension;
                $path = UPLOAD . "/public/excel/" . $item;
                move_uploaded_file($_FILES['excel']['tmp_name'], $path);

                $db = Core_Db_Table::getDefaultAdapter();

                $db->query('TRUNCATE TABLE nganh_nghe')->execute();
                $db->query('TRUNCATE TABLE answer')->execute();
                $db->query('TRUNCATE TABLE dap_an')->execute();
                $db->query('TRUNCATE TABLE nganhnghe_question')->execute();
                $db->query('TRUNCATE TABLE question')->execute();

                $this->importExcel('excel/' . $item);
                @unlink($path);
            }
            Core::message()->addSuccess('Lưu thành công');
        }

        $this->_helper->redirector('index', 'excel', 'admin');
    }

    private function importExcel($file_name) {

        $excel = new Zend_Excel();
        $excel->setOutputEncoding('UTF-8');
        $excel->read($file_name);


        $this->saveNganhNghe($excel->sheets[0], $end);
        $this->saveQuestion($excel->sheets[0], $end);

//        $nr_sheets = count($excel->sheets);       // gets the number of worksheets
//        $excel_data = '';              // to store the the html tables with data of each sheet
//// traverses the number of sheets and sets html table with each sheet data in $excel_data
//        for ($i = 0; $i < $nr_sheets; $i++) {
//            $excel_data .= '<h4>Sheet ' . ($i + 1) . ' (<em>' . $excel->boundsheets[$i]['name'] . '</em>)</h4>' . $this->sheetData($excel->sheets[$i]) . '<br/>';
//        }
//echo '<meta http-equiv="content-type" content="text/html;charset=utf-8;" />';
//        echo $excel_data;      // outputs HTML tables with excel file data
    }

    private function saveNganhNghe($sheet, &$end) {
        $mapper = new Default_Model_Nganhnghe();
        for ($i = 9;; $i++) {
            if (!isset($sheet['cells'][4][$i]) || trim($sheet['cells'][4][$i]) == '') {
                $end = $i - 1;
                break;
            }
            $mapper->insert(array(
                'title' => $sheet['cells'][4][$i]
            ));
        }
    }

    private function saveQuestion($sheet, $end) {
        $x = 7;
        while ($x <= $sheet['numRows']) {

            $mapper_question = new Default_Model_Question();
            if (strtoupper(trim($sheet['cells'][$x][2])) == 'B1') {
                $level = Default_Model_Question::BAC1;
            } else if (strtoupper(trim($sheet['cells'][$x][2])) == 'B2') {
                $level = Default_Model_Question::BAC2;
            } else if (strtoupper(trim($sheet['cells'][$x][2])) == 'B3') {
                $level = Default_Model_Question::BAC3;
            } else if (strtoupper(trim($sheet['cells'][$x][2])) == 'B4') {
                $level = Default_Model_Question::BAC4;
            } else if (strtoupper(trim($sheet['cells'][$x][2])) == 'B5') {
                $level = Default_Model_Question::BAC5;
            } else {
                $level = Default_Model_Question::BAC1;
            }
            $data_question = array(
                'content' => iconv(mb_detect_encoding($sheet['cells'][$x][6], mb_detect_order(), true), "UTF-8", $sheet['cells'][$x][6]),
                'level' => $level,
                'is_dao' => $sheet['cells'][$x][8]
            );
            $id_question = $mapper_question->insert($data_question);

            $mapper_nganhnghe_question = new Default_Model_NganhngheQuestion();
            for ($i = 9; $i <= $end; $i++) {
                if (isset($sheet['cells'][$x][$i]) && trim($sheet['cells'][$x][$i]) == '1') {

                    $mapper_nganhnghe_question->insert(array(
                        'nganhnghe_id' => ($i - 8),
                        'question_id' => $id_question,
                    ));
                }
            }

            $mapper_dapan = new Default_Model_Dapan();
            for ($i = 1; $i <= 4; $i++) {
                $mapper_answer = new Default_Model_Answer();
                if ($i == 1) {
                    $sign = 'A';
                } else if ($i == 2) {
                    $sign = 'B';
                } else if ($i == 3) {
                    $sign = 'C';
                } else if ($i == 4) {
                    $sign = 'D';
                }
                $data_answer = array(
                    'content' => iconv(mb_detect_encoding($sheet['cells'][$x + $i][6], mb_detect_order(), true), "UTF-8", $sheet['cells'][$x + $i][6]),
                    'question_id' => $id_question,
                    'sign' => $sign
                );
                $id_answer = $mapper_answer->insert($data_answer);
                if (trim($sheet['cells'][$x + $i][7]) == '1') {
                    $mapper_dapan->insert(array(
                        'answer_id' => $id_answer,
                        'question_id' => $id_question,
                        'sign' => $sign
                    ));
                }
            }
            $x += 5;
        }
    }

}
