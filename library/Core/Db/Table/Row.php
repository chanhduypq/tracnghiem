<?php

class Core_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
//    /**
//     * @var array
//     */
//    protected $_dataTypes = array(
//        'bit'       => 'int',
//        'tinyint'   => 'int',
//        'bool'      => 'bool',
//        'boolean'   => 'bool',
//        'smallint'  => 'int',
//        'mediumint' => 'int',
//        'int'       => 'int',
//        'integer'   => 'int',
//        'bigint'    => 'float',
//        'serial'    => 'int',
//        'float'     => 'float',
//        'double'    => 'float',
//        'decimal'   => 'float',
//        'dec'       => 'float',
//        'fixed'     => 'float',
//        'year'      => 'int'
//    );

    /**
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->_prefix = $this->getTable()->info(Core_Db_Table::PREFIX);

//        // auto type converting
//        $cols = $this->getTable()->info(Zend_Db_Table_Abstract::METADATA);
//        foreach ($cols as $name => $col) {
//            $dataType = strtolower($col['DATA_TYPE']);
//            if (array_key_exists($dataType, $this->_dataTypes)) {
//                settype($this->_data[$name], $this->_dataTypes[$dataType]);
//            }
//        }
    }

    /**
     * Sets all data in the row from an array.
     *
     * @param  array $data
     * @return Core_Db_Table_Row Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        foreach ($this->getTable()->info('cols') as $fieldName) {
            if (isset($data[$fieldName])) {
                $this->$fieldName = $data[$fieldName];
            }
        }
        return $this;
    }

    /**
     * Returns the table object, or null if this is disconnected row
     *
     * @return Zend_Db_Table_Abstract|null
     */
    public function getTable()
    {
        $table = $this->_table;
        if (null === $table && !empty($this->_tableClass)) {
            $tableClass = $this->_tableClass;
            $table = Core::single($tableClass);
            $this->setTable($table);
        }
        return $table;
    }

    /**
     * Retrun current datebase adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->getTable()->getAdapter();
    }

    /**
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    public function save()
    {
        try {
            $this->_preSave();
            $return = parent::save();
            $this->_postSave();
            return $return;
        } catch (Exception $e) {
            Core::message()->addError($e->getMessage());
            return false;
        }
    }
    
    /**
     * 
     */
    protected function _preSave() 
    {
        
    }
    
    /**
     * 
     */
    protected function _postSave() 
    {
        
    }

    /**
     * @return Core_Db_Table_Row
     */
    public function cache()
    {
        $frontend = Core::single('Core_Cache_Frontend_Query');

        $primaryKeys = array();
        foreach ($this->_primary as $primary) {
            $primaryKeys[$primary] = $this->{$primary};
        }

        $args = func_get_args();
        $args = array_merge($primaryKeys, $args);

        return $frontend->setInstance($this, serialize($args));
    }
}