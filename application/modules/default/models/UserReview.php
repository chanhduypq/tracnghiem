<?php

class Default_Model_Userreview extends Core_Db_Table_Abstract 
{

    public $_name = "user_review";

    public function __construct() 
    {
        parent::__construct();
    }

    public static function convert($number)
    {
        if($number<10){
            return '0'.$number;
        }
        return $number;
    }
    
    public static function getHtmlForReviewResult($user_review_id, &$title_header)
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchAll("select sh,sm,eh,em,es,user_review_detail.question_id,"
                . "user_review.nganh_nghe_id,"
                . "user_review.level,"
                . "DATE_FORMAT(user_review.review_date,'%d/%m/%Y') AS date,"
                . "DATE_FORMAT(user_review.review_date,'%Y') AS year,"
                . "user.danh_xung,"
                . "user.full_name,"
                . "user_review_detail.is_correct,"
                . "user_review_detail.dapan_sign,"
                . "user_review_detail.answer_sign,"
                . "user_review_detail.answer_id,"
                . "nganh_nghe.title "
                . "from user_review "
                . "JOIN user_review_detail ON user_review.id=user_review_detail.user_review_id "
                . "JOIN user ON user.id=user_review.user_id "
                . "JOIN nganh_nghe ON nganh_nghe.id=user_review.nganh_nghe_id "
                . "WHERE user_review.id=$user_review_id ORDER BY user_review_detail.id ASC");
        $count_correct = 0;
        $count_incorrect = 0;
        $questionIds = array();
        foreach ($row as $r) {
            if ($r['is_correct'] == '1') {
                $count_correct++;
            } else {
                $count_incorrect++;
            }
            $questionIds[] = $r['question_id'];
        }


        $diem = round($count_correct * 10 / count($row), 1);
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
        $newQuestions = array();
        foreach ($questions as $question) {
            $newQuestions[$question['id']]['question_content'] = $question['question_content'];
            $newQuestions[$question['id']]['answers'][] = array('answer_sign' => $question['answer_sign'], 'answer_content' => $question['answer_content'], 'is_dap_an' => ($question['answer_sign'] == $question['dap_an_sign']));
        }
        $div = '';
        $i = 1;
        foreach ($newQuestions as $key => $question) {
            if ($i > 1) {
                $div .= '<div>&nbsp;</div>';
            }
            $div .= '<div class="span12" style="color: blue;">
                    ' . $i . '. ' . $question['question_content'] . '
                </div>';
            foreach ($question['answers'] as $temp) {
                if ($temp['is_dap_an']) {
                    $div .= '<div class="span12">
                            <strong><i><u>' . $temp['answer_sign'] . '. ' . $temp['answer_content'] . ' (*)</u></i></strong>
                        </div>';
                } else {
                    $div .= '<div class="span12">
                            ' . $temp['answer_sign'] . '. ' . $temp['answer_content'] . '
                        </div>';
                }
            }
            $i++;
        }
        $motPhan = intval(ceil(count($row) / 3));
        $div1 = $div2 = $div3 = '';
        for ($i = 0; $i < $motPhan; $i++) {
            $div1 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($row[$i]['answer_id'] == '-1' ? '' : $row[$i]['answer_sign']) . '</td>
                        <td>' . $row[$i]['dapan_sign'] . '</td>
                        <td>' . ($row[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
        }
        for (; $i < $motPhan * 2; $i++) {
            if (isset($row[$i])) {
                $div2 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($row[$i]['answer_id'] == '-1' ? '' : $row[$i]['answer_sign']) . '</td>
                        <td>' . $row[$i]['dapan_sign'] . '</td>
                        <td>' . ($row[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
            }
        }
        for (; $i < count($row); $i++) {
            $div3 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($row[$i]['answer_id'] == '-1' ? '' : $row[$i]['answer_sign']) . '</td>
                        <td>' . $row[$i]['dapan_sign'] . '</td>
                        <td>' . ($row[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
        }


        $startTime = new DateTime(date('Y-m-d ' . $row[0]['sh'] . ':' . $row[0]['sm'] . ':00'));
        $endTime = new DateTime(date('Y-m-d ' . $row[0]['eh'] . ':' . $row[0]['em'] . ':'.$row[0]['es']));
        $diff = $endTime->diff($startTime);
        $diff = self::convert($diff->h) . ':' . (($diff->h == 0 && $diff->i == 0) ? '00' : self::convert($diff->i)) .':'. self::convert($diff->s); // . ':00';
        if ($row[0]['sh'] > 12) {
            $startTime = ($row[0]['sh'] - 12) . ':' . self::convert($row[0]['sm']) . ' PM';
        } else {
            $startTime = $row[0]['sh'] . ':' . self::convert($row[0]['sm']) . ' AM';
        }
        if ($row[0]['eh'] > 12) {
            $endTime = ($row[0]['eh'] - 12) . ':' . self::convert($row[0]['em']) . ' PM';
        } else {
            $endTime = $row[0]['eh'] . ':' . self::convert($row[0]['em']) . ' AM';
        }

        if ($row[0]['level'] == '1') {
            $level = 'SƠ CẤP';
        } else if ($row[0]['level'] == '2') {
            $level = 'TRUNG CẤP';
        } else {
            $level = 'CAO CẤP';
        }
        $title_header = $row[0]['date'];
        $header=Admin_Model_HeaderpdfMapper::getHeader();
        $header = str_replace('{level}', $level, $header);
        $header = str_replace('{nam}', $row[0]['year'], $header);
        $header = str_replace('&Agrave;', 'À', $header);
        $header = str_replace('&Ocirc;', 'Ô', $header);
        $header = str_replace('&Acirc;', 'Â', $header);
        $header = str_replace('&nbsp;', ' ', $header);
        $header=iconv(mb_detect_encoding($header, mb_detect_order(), true), "UTF-8", $header);
//        $header='<table style="width: 100%;">
//                    <tbody>
//                        <tr>
//                            <td style="width: 50%;text-align: center;">
//                                <h3>TẬP ĐOÀN ĐIỆN LỰC VIỆT NAM</h3>
//                        <h3>TỔNG CÔNG TY ĐIỆN LỰC MIỀN TRUNG</h3>
//                            </td>
//                            <td style="width: 50%;text-align: center;">
//                                <h3>ÔN TẬP TRỰC TUYẾN</h3>
//                        <h3>CÔNG NHÂN KỸ THUẬT ' . $level . ' NĂM ' . $row[0]['year'] . '</h3>
//                            </td>
//                        </tr>
//                        <tr>
//                            <td colspan="2" style="width: 100%;text-align: center;">
//                                <h3>KẾT QUẢ LUYỆN THI KIẾN THỨC AN TOÀN ĐIỆN</h3>
//                            </td>                            
//                        </tr>
//                    </tbody>
//                </table>';
        $html = '<style>
                  html, body {
                    width: 210mm;
                    height: 297mm;
                  } 
                    
                  .span12 {
                    width: 100%;
                    *width: 99.94680851063829%;
                  }
                  
                  table.chitiet{
                      width: 80%;
                      border-collapse: collapse;
                  }
                  table.chitiet td{
                      width: 20%;
                      border: 2px solid #666666;
                text-align: center;
                vertical-align: middle;
                  }
                  tr.header td{
                      color: #cccccc;
                  }

                </style>
                <body>
                '.$header.'
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 20%;text-align: left;">
                                <strong>Họ và tên:</strong>
                            </td>
                            <td style="width: 20%;text-align: left;">
                                ' . $row[0]['full_name'] . '
                            </td>
                            <td style="width: 60%;text-align: left;">
                                <strong>Đơn vị:</strong> Công ty CP Thủy điện miền Trung
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;text-align: left;">
                                <strong>Nghề dự thi:</strong>
                            </td>
                            <td style="width: 20%;text-align: left;">
                                ' . $row[0]['title'] . '
                            </td>
                            <td style="width: 60%;text-align: left;">
                                <!--<strong>Năm sinh:</strong>-->
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;text-align: left;">
                                <strong>Ngày kiểm tra:</strong>
                            </td>
                            <td style="width: 20%;text-align: left;">
                                ' . $row[0]['date'] . '
                            </td>
                            <td style="width: 60%;text-align: left;">
                                <strong>Bắt đầu:</strong>&nbsp;&nbsp;' . $startTime . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <strong>Kết thúc:</strong>&nbsp;&nbsp;' . $endTime . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <strong>Thời gian:</strong>&nbsp;&nbsp;' . $diff . '   
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 33%;text-align: center;">
                                <table class="chitiet">
                                    <tbody>
                                        <tr class="header">
                                            <td>Câu hỏi</td>
                                            <td>Câu đã chọn</td>
                                            <td>Đáp án đúng</td>
                                            <td>Kết quả</td>
                                        </tr>
                                        ' . $div1 . '
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 33%;text-align: center;">
                                <table class="chitiet">
                                    <tbody>
                                        <tr class="header">
                                            <td>Câu hỏi</td>
                                            <td>Câu đã chọn</td>
                                            <td>Đáp án đúng</td>
                                            <td>Kết quả</td>
                                        </tr>
                                        ' . $div2 . '
                                    </tbody>
                                </table>
                            </td>
                            <td style="width: 33%;text-align: center;">
                                <table class="chitiet">
                                    <tbody>
                                        <tr class="header">
                                            <td>Câu hỏi</td>
                                            <td>Câu đã chọn</td>
                                            <td>Đáp án đúng</td>
                                            <td>Kết quả</td>
                                        </tr>
                                        ' . $div3 . '
                                    </tbody>
                                </table>
                            </td>
                        </tr>                       
                        
                    </tbody>
                </table>
                
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 1%;">&nbsp;</td>
                            <td style="width: 98%;text-align: left;border: 2px solid #cccccc;padding: 20px;">
                                <div>&nbsp;</div>
                                &nbsp;&nbsp;Số câu đúng:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $count_correct . '<br>
                                Điểm kiểm tra:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $diem . '<br>
                                
                            </td>
                            <td style="width: 1%;">&nbsp;</td>
                        </tr>                       
                        
                    </tbody>
                </table>
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                            <td colspan="3" style="width: 100%;text-align: center;font-size: 20px;">ĐÁP ÁN CHI TIẾT</td>
                            
                            
                        </tr>  
                        <tr>
                            <td style="width: 1%;">&nbsp;</td>
                            <td style="width: 98%;text-align: left;border: 2px solid #666666;">
                                ' . $div . '
                            </td>
                            <td style="width: 1%;">&nbsp;</td>
                            
                        </tr>                       
                        
                    </tbody>
                </table>
                </body>
                ';

        return $html;
    }

}

?>