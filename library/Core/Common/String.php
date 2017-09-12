<?php

class Core_Common_String {

    /**
     * function common
     * cắt những khoảng trắng bị dư
     * ví dụ: " tôi tên   là  nguyễn vĂn  Hùng"->"tôi tên là nguyễn vĂn Hùng"
     * @param string $string
     * @return string
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     */
    public static function removeUnnecessarySpaces($string) 
    {
        $string = trim($string);
        $words = preg_split('/\s+/', $string);
        $string = implode(" ", $words);
        return $string;
    }

    /**
     * function common
     * cắt chuỗi nếu chuỗi dài hơn một độ dài cho phép
     * @param string $text 
     * @param integer $len
     * @param bool $after cắt phía sau hay cắt phía trước, có nghĩa là [...] nằm phía sau hay nằm phía trước
     * @return string
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     */
    public static function crop($text, $len, $after = true) {
        if ($text == NULL) {
            return "";
        }
        if (!is_string($text)) {
            return "";
        }
        if (trim($text) == "") {
            return "";
        }

        if (Core_Common_Numeric::isInteger($len) == FALSE) {
            return $text;
        }

        if ($len > strlen(utf8_decode($text))) {
            $string = $text;
        } else {
            if ($after == true) {
                $string_cop = mb_substr($text, 0, $len, 'UTF-8');
                if (strlen(utf8_decode($text)) == strlen(utf8_decode($string_cop))) {
                    $string = $string_cop;
                } else {
                    $string = $string_cop . "...";
                }
            } else {
                $string_cop = mb_substr($text, (-1) * ($len), $len, 'UTF-8');
                if (strlen(utf8_decode($text)) == strlen(utf8_decode($string_cop))) {
                    $string = $string_cop;
                } else {
                    $string = "..." . $string_cop;
                }
            }
        }
        return $string;
    }

    /**
     * function common
     * @param string $str
     * @return string
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     */
    public static function utf8convert($str) {

        if (!$str)
            return false;

        $utf8 = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ|Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị|Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ|Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($utf8 as $ascii => $uni)
            $str = preg_replace("/($uni)/i", $ascii, $str);

        return $str;
    }

}
