<?php

class Common_Model_TableMapper {

    protected $_countItems = 0;

    public function getCountItems() {
        return $this->_countItems;
    }

    /**
     * @author Trần Công Tuệ
     * @var string
     */
    private $model = null;

    /**
     * @author Trần Công Tuệ 
     * @param string $model
     * @return Common_Model_CommonMapper
     */
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }

    /**
     * @author Trần Công Tuệ
     * @param string $model
     */
    public function __construct($model = null) {
        $this->model = $model;
    }

    /**
     * @return int|string 
     * @param 
     */
    public function deleteByPk($value, $delete_file = false) {
        Zend_Loader::loadFile('Numeric.php', "./../library/Core/Common/", true);
        if (Numeric::isInteger($value) == FALSE) {
            return 0;
        }
        $affect = 0;
        $model = Core::single($this->model);
        $db = Core_Db_Table::getDefaultAdapter();
        $metadata = $db->describeTable($model->_name);

        $keys = array_keys($metadata);
        foreach ($keys as $key) {
            $column_difinition = $metadata[$key];
            if ($column_difinition['PRIMARY'] == true) {
                $where = $db->quoteInto($column_difinition['COLUMN_NAME'] . "=?", $value, 'INTEGER');
                if (is_bool($delete_file) && $delete_file == TRUE) {
                    $row = $model->fetchRow($where);
                    if ($row != NULL) {
                        $row = $row->toArray();
                    }
                }
                $affect = $model->delete($where);
            }
        }
        if ($affect > 0) {
            if (is_bool($delete_file) && $delete_file == TRUE) {
                if (is_array($row)) {
                    $this->deleteFile($row);
                }
            }
        }
        return $affect;
    }

    private function deleteFile($row) {
        if (is_array($row) && count($row) > 0) {
            foreach ($row as $key => $value) {
                if (file_exists(UPLOAD . "/public" . $value)) {
                    unlink(UPLOAD . "/public" . $value);
                }
            }
        }
    }

    /**
     * @author Trần Công Tuệ
     * @param integer $id
     * @param array $option
     * @return array
     */
    public function read($id, $option = array()) {
        $model = Core::single($this->model);
        $result = $model->find($id)->toArray();
        if (count($result) > 0) {
            return $result[0];
        }
        return array();
    }

    public function listItems($limit = null, $start = null, $where = null, $option = array()) {
        $select = Core::single($this->model)->select();
//        $select
//                ->order("ngay_them_moi desc")
//        ;
        Zend_Loader::loadFile('Numeric.php', "./../library/Core/Common/", true);

        if (Numeric::isInteger($limit) && Numeric::isInteger($start)) {
            $select->limit($limit, $start);
        }
        if (is_array($where) && count($where) > 0) {
            foreach ($where as $opt) {
                if (count($opt) == 3) {
                    $select->where($opt[0], $opt[1], $opt[2]);
                } else if (count($opt) == 2) {
                    $select->where($opt[0], $opt[1]);
                } else if (count($opt) == 1) {
                    $select->where($opt[0]);
                }
            }
        } else if (is_string($where) && trim($where) != "") {
            $select->where($where);
        }
        $rows = $select->fetchAll();
        $this->_countItems = $select->count();
        return $rows;
    }

    /**
     * @author Trần Công Tuệ
     * @param Core_Form $form
     * @param array $formData
     * @param string|array $where
     * @param array $option
     * @return boolean
     */
    public function update($form,$formData, $where, $has_file = false) {
        $this->processSpecialInput($form, $formData);
        $model = Core::single($this->model);
        $db = Zend_Db_Table::getDefaultAdapter();
        $metadata = $db->describeTable($model->_name);
        $column_names = array_keys($metadata);
        if ($has_file == true) {
            $row = $model->fetchRow($where);
            if ($row != NULL) {
                $old_data = $row->toArray();
            } else {
                $old_data = array();
            }
        }

        foreach ($formData as $key => $value) {
            if ($value == null || $value == '' || !in_array($key, $column_names)) {
                unset($formData[$key]);
            }
        }


        try {
            
            
            
            $affect = $model->update($formData, $where);
            
            if ($affect > 0) {
                if (is_bool($has_file) && $has_file == true) {
                    $this->processFile($new_data=$formData, $old_data, $column_names);
                }
                return $affect;
            }
        } catch (Exception $e) {            
            return 0;
        }
        return 0;
    }

    /**
     * @author Trần Công Tuệ
     * @param array $new_data
     * @param array $old_data
     * @param array $column_names
     * @return void 
     */
    private function processFile($new_data, $old_data, $column_names) {
        foreach ($column_names as $column_name) {
            if (file_exists(UPLOAD . '/public' . $new_data[$column_name]) || file_exists(UPLOAD . '/public' . $old_data[$column_name])) {
                if (file_exists(UPLOAD . '/public' . $old_data[$column_name]) && $old_data[$column_name] != $new_data[$column_name]) {
                    unlink((UPLOAD . '/public' . $old_data[$column_name]));
                }
            }
        }
    }

    /**
     * @author Trần Công Tuệ
     * @param Core_Form $form
     * @param array $formData
     * @param array $option
     * @return void
     */
    private function processSpecialInput($form, &$formData) {
        try {
            foreach ($form->getElements() as $element) {                
                    if ($element instanceof Core_Form_Element_Date) {
                        $array = explode("/", $element->getValue());
                        if (count($array) == 3) {
                            $date = $array[2] . '-' . $array[1] . '-' . $array[0];
                            $formData[$element->getName()] = $date;
                        }
                    } elseif ($element instanceof Core_Form_Element_File) {                        
                        if ($element->getForInsertDB() == false) {
                            if ($_FILES[$element->getName()]['name'] != "") {
                                $file_name = $element->getRandomFileName();
                                if ($element->isUploaded($file_name) && $element->isValid($file_name)) {
                                    $element->receive();
                                }
                                if (isset($file_name) && $file_name != "") {
                                    $file_name = $element->getPathStoreFile() . $file_name;
                                }
                                $formData[$element->getName()] = $file_name;
                            }
                            else{                                
                                $field_name=$element->getName();
                                $model = Core::single($this->model);
                                $formData[$field_name]=$model->select(array($field_name))->where("id_mat_hang=".$formData['id_mat_hang'])->fetchOne();
                            }
                        } else if ($element->getForInsertDB() == true) {
                            $file_key_array = array('type', 'size', 'name');
                            foreach ($this->getElements() as $key => $value) {
                                if (in_array($key, $file_key_array)) {
                                    $formData[$key] = $_FILES[$element->getName()][$key];
                                }
                            }
                            $file = fopen($_FILES[$element->getName()]['tmp_name'], "r", 1);
                            if ($file) {
                                $fileContent = base64_encode(file_get_contents($_FILES[$element->getName()]['tmp_name']));
                            } else {
                                $fileContent = null;
                            }
                            $formData[$element->getName()] = $fileContent;
                        }
                    }
                
            }
        } catch (Exception $e) {
            
        }
    }

    /**
     * @author Trần Công Tuệ
     * @param Core_Form $form
     * @param array $formData
     * @param array $option
     * @return integer
     */
    public function create($form, $formData, $has_file = false) {

        $this->processSpecialInput($form, $formData,$has_file);
        $model = Core::single($this->model);
        $db = Zend_Db_Table::getDefaultAdapter();
        $metadata = $db->describeTable($model->_name);
        $column_names = array_keys($metadata);
        foreach ($formData as $key => $value) {
            if ($value == null || $value == '' || !in_array($key, $column_names)) {
                unset($formData[$key]);
            }
        }
        return $model->insert($formData);
    }

}
