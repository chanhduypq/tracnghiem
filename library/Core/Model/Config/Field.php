<?php
/**
 * Core
 *
 * This file is part of Core.
 *
 * Core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Core.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Core
 * @package     Core
 * @subpackage  Core_Model
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core
 * @subpackage  Core_Model
 * @author      Core Core Team <core@axiscommerce.com>
 */
class Core_Model_Config_Field extends Core_Db_Table
{
    protected $_name = 'core_config_field';

    protected $_primary = 'id';

    protected $_rowClass = 'Core_Model_Config_Field_Row';

    protected $_selectClass = 'Core_Model_Config_Field_Select';

    /**
     * Insert or update config field
     *
     * @param array $data
     * @return Core_Db_Table_Row
     */
    public function save(array $data)
    {
        $row = $this->select()
            ->where('path = ?', $data['path'])
            ->fetchRow();

        if (!$row) {
            $row = $this->createRow();
        } 
        $row->setFromArray($data);
        
        $row->lvl = count(explode('/', $row->path));
        
        if ($row->lvl <= 2) {
            $row->type = '';
        }
        
        $row->save();
        
        return $row;
    }
}
