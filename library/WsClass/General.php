<?php

class WsClass_General {

    /**
     * test function for soap server
     *
     * @return array
     */
    public function noArgument() {
        $db = Core_Db_Table::getDefaultAdapter();
        $sql = "SELECT * FROM menu";
        return $db->fetchAll($sql);
    }

    /**
     * test function for soap server
     *     
     * @param int|string $argument1
     * @param int|string $argument2
     * @return int
     */
    public function hasArgument($argument1, $argument2) {
        return $argument1 + $argument2;
    }

}
