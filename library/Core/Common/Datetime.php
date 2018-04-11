<?php

class Core_Common_Datetime 
{
    private $startDate;
    private $endDate;
    private $diff;

    public function __construct($startDate = NULL, $endDate = NULL) {
        if ($startDate == NULL) {
            $this->startDate = new DateTime(date('Y-m-d H:i:s'));
        } else {
            $this->startDate = $startDate;
        }
        if ($endDate == NULL) {
            $this->endDate = new DateTime(date('Y-m-d H:i:s'));
        } else {
            $this->endDate = $endDate;
        }
        
        $this->diff = $this->endDate->diff($this->startDate);
    }
    
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        $this->diff = $this->endDate->diff($this->startDate);
    }

    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        $this->diff = $this->endDate->diff($this->startDate);
    }
    
    public function getSecondDiff($only = TRUE) {
        if ($only == FALSE) {
            return $this->diff->s;
        } else {
            return $this->getMinuteDiff() * 60 + $this->diff->s;
        }
    }
    
    public function getMinuteDiff($only = TRUE) {
        if ($only == FALSE) {
            return $this->diff->i;
        } else {
            return $this->getHourDiff() * 60 + $this->diff->i;
        }
    }
    
    public function getHourDiff($only = TRUE) {
        if ($only == FALSE) {
            return $this->diff->h;
        } else {
            return $this->getDayDiff() * 24 + $this->diff->h;
        }
    }
    
    public function getDayDiff($only = TRUE) { 
        return $this->diff->d;
//        if ($only == FALSE) {
//            return $this->diff->d;
//        } else {
//            return $this->diff->m * 24 + $this->diff->d;
//        }
    }

    /**
     * 
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $path
     * @param string $fileName
     */
    public static function download($path,$fileName=null) 
    { 
        if(!is_string($fileName)||trim($fileName)==''){
            $fileNameForDownload='';
            $files = scandir($path, 0);
            foreach ($files as $file){
                if ($file != '.' || $file != '..') {
                    $fileNameForDownload=$file;
                }
            }     
        }
        else{
            $fileNameForDownload=$fileName;
        }
         
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' .$fileNameForDownload . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(rtrim($path,'/').'/'.$fileNameForDownload));
        readfile(rtrim($path,'/').'/'.$fileNameForDownload);
        exit;
    }
}