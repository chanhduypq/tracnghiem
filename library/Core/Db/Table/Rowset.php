<?php
class Core_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    /**
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return Core_Db_Table_Abstract
     */
    public function getTable()
    {
        $table = $this->_table;
        if (null === $table && !empty($this->_tableClass)) {
            $tableClass = $this->_tableClass;
            $tableClass = strtolower($tableClass);
            $tableClass = str_replace('Core_', '', $tableClass);
            $tableClass = str_replace('_model_', '/', $tableClass);
            //$this->setTable(Core::single($tableClass));
            $table = Core::single($tableClass);
        }
        return $table;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Row_Abstract current element from the collection
     */
    public function current()
    {
        if (false === $this->valid()) {
            return null;
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'table'    => $this->getTable(),//<-- modified here
                    'data'     => $this->_data[$this->_pointer],
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
    }
//
//    /**
//     * @return Core_Db_Table_Rowset
//     */
//    public function cache()
//    {
//        $args = func_num_args() ? serialize(func_get_args()) : '';
//        return Core::single('Core_Cache_Frontend_Query')->setInstance(
//            $this, $args
//        );
//    }
}