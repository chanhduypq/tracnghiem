<?php
class Core_Model_Config_Field_Select extends Core_Db_Table_Select
{
    /**
     * Adds core_config_value columns to select
     */
    public function addValue()
    {
        return $this->joinLeft(
            'core_config_value',
            'ccf.id = ccv.config_field_id',
            array('value')
        );
    }
}