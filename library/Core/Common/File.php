<?php

class Core_Common_File {

    /**
     * function common
     * Đọc tất cả các file trong một folder, rồi trả về array chứa filename trong folder đó
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $path
     * @return array 
     */
    public static function readAllFiles($path) {
        if (!is_string($path)) {
            return array();
        }
        if (!file_exists($path)) {
            return array();
        }
        if (!is_dir($path)) {
            return array();
        }

        $file_name_array = array();
        if ($handle = opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != "." && $file != "..") {
                    $file_name_array[] = $file;
                }
            }
            closedir($handle);
        } else {
            return array();
        }

        return $file_name_array;
    }

    /**
     * function common
     * fix fileName với các vấn đề: có khoảng trắng, chứa ký tự có dấu...
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $fileName
     * @return string
     */
    public static function fixFileName($fileName) {
        if ($fileName == null || (!is_string($fileName)) || trim($fileName) == "") {
            return $fileName;
        }
        $fileName = str_replace("%", "", $fileName);
        $fileName = str_replace(" ", "_", $fileName);
        require_once 'String.php';
        $fileName = Core_Common_String::utf8convert($fileName);
        return $fileName;
    }

}
