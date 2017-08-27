<?php

class Common_Model_CommonMapper {

    /**
     * fix các input đặc biệt để lưu vào $formData cho đúng như:
     *           date,file...
     * @author Trần Công Tuệ          
     * @param Core_Form $form
     * @param array $formData
     * @param string $path_for_upload
     * @return void
     */
    public static function fixSpecialElements($form, &$formData, $path_for_upload) {
        if (!($form instanceof Core_Form) || count($form->getElements()) == 0) {
            return;
        }
        if (!is_array($formData) || count($formData) == 0) {
            return;
        }
        if ($path_for_upload == null || (!is_string($path_for_upload)) || trim($path_for_upload) == '') {
            return;
        }
        foreach ($form->getElements() as $element) {
            if ($element instanceof Core_Form_Element_Date) {
                $array = explode("/", $element->getValue());
                if (count($array) == 3) {
                    $date = $array[2] . '-' . $array[1] . '-' . $array[0];
                    $formData[$element->getName()] = $date;
                }
            } elseif ($element instanceof Core_Form_Element_File) {
                if ($_FILES[$element->getName()]['name'] != "") {
                    $item_image = $element->getRandomFileName();
                    if ($element->isUploaded($item_image) && $element->isValid($item_image)) {
                        $element->receive();
                    }
                    if (isset($item_image) && $item_image != "") {
                        $item_image = $path_for_upload . $item_image;
                    }
                    $formData[$element->getName()] = $item_image;
                }
            }
        }
    }

   

    /**
     * @author Trần Công Tuệ
     * upload tất cả các file có trong form
     * return array chứa các fileName
     * @param string $path
     * @return array
     */
    public static function upload($path) {
        if ($path == null || (!is_string($path)) || trim($path) == '') {
            return array();
        }
        $file_names = array();
        try {
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->addValidator('Count', false, array('min' => 1))
            ;
            $adapter->setDestination($path);

            $files = $adapter->getFileInfo();
            if (count($files) > 0) {
                foreach ($files as $fieldname => $fileinfo) {
                    if ($adapter->isUploaded($fileinfo['name']) && $adapter->isValid($fileinfo['name'])) {
                        $adapter->receive($fileinfo['name']);                        
                        Zend_Loader::loadFile('./../library/Core/Common/File.php',null,true);
                        $file_names[] = File::fixFileName($fileinfo['name']);
                    }
                }
            }
        } catch (Exception $ex) {
            return array();
        }
        return $file_names;
    }

}
