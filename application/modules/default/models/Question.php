<?php

class Default_Model_Question extends Core_Db_Table_Abstract 
{

    const SO_CAP = '1';
    const TRUNG_CAP = '2';
    const CAO_CAP = '3';

    public $_name = "question";

    public function __construct() 
    {
        parent::__construct();
    }

    public function getQuestions(&$total, $limit = null, $start = null) 
    {

        Zend_Loader::loadFile('Numeric.php', "./../library/Core/Common/", true);
        if (Numeric::isInteger($limit) && Numeric::isInteger($start)) {
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

    public function getAnswers($parent_id) 
    {
        if (!is_numeric($parent_id)) {
            return array();
        }
        $mapper = new Default_Model_Answer();
        $items = $mapper->getAnswers($parent_id);
        return $items;
    }

}

?>