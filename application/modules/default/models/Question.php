<?php

class Default_Model_Question extends Core_Db_Table_Abstract {

    const BAC1 = '1';
    const BAC2 = '2';
    const BAC3 = '3';
    const BAC4 = '4';
    const BAC5 = '5';

    public $_name = "question";

    public function __construct() {
        parent::__construct();
    }

    public function getQuestions(&$total, $limit = null, $start = null) {


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

    public function getAnswers($parent_id) {
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
    public static function getFullQuestions($db, $questionIds) {
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

    public static function getQuestionsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $questionIds = self::getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number);
        return self::getQuestionsByQuestionIds($questionIds);
    }

    public static function getQuestionsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level) {
        $questionIds = self::getQuestionIdsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level);
        return self::getQuestionsByQuestionIds($questionIds);
    }

    public static function getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $db = Core_Db_Table::getDefaultAdapter();
        if ($level == '1') {//nếu là bậc 1
            $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1 ORDER BY RAND() LIMIT " . $config_exam_number;
            $rows = $db->fetchAll($sql);
        } else if ($level == '2' || $level == '3') {//nếu là bậc 2/3
            $levelJsonString = $db->fetchOne("SELECT data from config_exam_level WHERE level=$level");
            $levelJsonArray = json_decode(html_entity_decode($levelJsonString), true);
            if ($levelJsonArray['b2'] == '100') {//nếu hệ thống muốn lấy 100% câu b2 cho bậc 2/3
                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2 ORDER BY RAND() LIMIT " . $config_exam_number;
                $rows = $db->fetchAll($sql);
                if (count($rows) < $config_exam_number) {//nếu lấy chưa đủ thi phải lấy thêm b1 bù vào cho đủ $config_exam_number
                    $number = $config_exam_number - count($rows);
                    $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1 ORDER BY RAND() LIMIT " . $number;
                    $rows = array_merge($rows, $db->fetchAll($sql));
                }
            } else {
                $b2Number = intval($config_exam_number * $levelJsonArray['b2'] / 100);
                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2 ORDER BY RAND() LIMIT " . $b2Number;
                $rows = $db->fetchAll($sql);
                if ($b2Number > count($rows)) {//nếu trong db chỉ có 50 câu b2 mà config lại muốn lấy 60 câu b2
                    $b1Number = $config_exam_number - count($rows);
                } else {
                    $b1Number = $config_exam_number - $b2Number;
                }

                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1 ORDER BY RAND() LIMIT " . $b1Number;
                $rows = array_merge($rows, $db->fetchAll($sql));
            }
        } else {//nếu là bậc 4/5
            $levelJsonString = $db->fetchOne("SELECT data from config_exam_level WHERE level=$level");
            $levelJsonArray = json_decode(html_entity_decode($levelJsonString), true);

            if ($levelJsonArray['b3'] == '100') {//nếu hệ thống muốn lấy 100% câu b3 cho bậc 4/5
                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=3 ORDER BY RAND() LIMIT " . $config_exam_number;
                $rows = $db->fetchAll($sql);
                if (count($rows) < $config_exam_number) {//nếu lấy chưa đủ thi phải lấy thêm b1,b2 bù vào cho đủ $config_exam_number
                    $number = $config_exam_number - count($rows);
                    $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=2 ORDER BY RAND() LIMIT " . $number;
                    $rows = array_merge($rows, $db->fetchAll($sql));
                }
            } else {
                $b3Number = intval($config_exam_number * $levelJsonArray['b3'] / 100);
                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=3 ORDER BY RAND() LIMIT " . $b3Number;
                $rows = $db->fetchAll($sql);

                $b2Number = intval($config_exam_number * $levelJsonArray['b2'] / 100);
                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2 ORDER BY RAND() LIMIT " . $b2Number;
                $rows = array_merge($rows, $db->fetchAll($sql));

                if ($b2Number + $b3Number > count($rows)) {//nếu trong db chỉ có 50 câu b2,b3 mà config lại muốn lấy 60 câu b2,b3
                    $b1Number = $config_exam_number - count($rows);
                } else {
                    $b1Number = $config_exam_number - $b2Number - $b3Number;
                }

                $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1 ORDER BY RAND() LIMIT " . $b1Number;
                $rows = array_merge($rows, $db->fetchAll($sql));

                if (count($rows) < $config_exam_number) {
                    $tempIds = array();
                    foreach ($rows as $row) {
                        $tempIds[] = $row['id'];
                    }
                    if (count($tempIds) > 0) {
                        $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=3 AND question.id NOT IN (" . implode(",", $tempIds) . ") ORDER BY RAND() LIMIT " . ($config_exam_number - count($rows));
                        $rows = array_merge($rows, $db->fetchAll($sql));
                    }
                }
            }
        }

        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }

        return $questionIds;
    }

    public static function getQuestionIdsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level) {
        if ($level == '1') {
            $level = '1';
        } else if ($level == '2' || $level == '3') {
            $level = '2';
        } else if ($level == '4' || $level == '5') {
            $level = '3';
        }
        $db = Core_Db_Table::getDefaultAdapter();
        $sql = "SELECT DISTINCT question.id from nganhnghe_question JOIN question ON question.id=nganhnghe_question.question_id WHERE nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=$level ORDER BY question.id ASC";

        $rows = $db->fetchAll($sql);
        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }

        return $questionIds;
    }

    public static function getQuestionsByQuestionIds($questionIds) {
        if (!is_array($questionIds) || count($questionIds) == 0) {
            return array();
        }
        $db = Core_Db_Table::getDefaultAdapter();
        $newQuestions = array();
        $questions = $db->fetchAll("SELECT question.id,question.is_dao,question.content,answer.sign,answer.content AS answer_content,answer.id AS answer_id,dap_an.sign AS dapan_sign FROM question JOIN nganhnghe_question ON question.id = nganhnghe_question.question_id JOIN answer ON answer.question_id=question.id JOIN dap_an ON dap_an.question_id=question.id WHERE question.id IN (" . implode(',', $questionIds) . ") ORDER BY question.id ASC,answer.sign ASC");
        foreach ($questions as $question) {
            $newQuestions[$question['id']]['id'] = $question['id'];
            $newQuestions[$question['id']]['content'] = $question['content'];
            $newQuestions[$question['id']]['answers'][$question['answer_id']] = array('content' => $question['answer_content'], 'sign' => $question['sign'], 'id' => $question['answer_id']);
            $newQuestions[$question['id']]['dapan_sign'] = $question['dapan_sign'];
            $newQuestions[$question['id']]['is_dao'] = $question['is_dao'];
        }
        return $newQuestions;
    }

}

?>