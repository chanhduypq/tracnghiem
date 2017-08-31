<?php

class Core_Common_Download 
{
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