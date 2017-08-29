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
 * @package     Core_Config
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @uses        Zend_Config
 * @category    Core
 * @package     Core_Config
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_Config extends Zend_Config
{
    /**
     * @param  array   $array
     * @param  boolean $allowModifications
     * @return void
     */
    public function __construct(array $array, $allowModifications = false)
    {
        $this->_allowModifications = (boolean) $allowModifications;
        $this->_loadedSection = null;
        $this->_index = 0;
        $this->_data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value, $this->_allowModifications);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    public function get($name,$default = null)
    {
        if (strstr($name, '/')) {
            $sections = explode('/', $name);
        } else {
            $sections = array($name);
        }

        $section = array_shift($sections);
        

        $result = isset($this->_data[$section]) ? $this->_data[$section] : $default;

        foreach ($sections as $section) {
            if (!$result instanceof Core_Config) {
                $result = $default;
                break;
            }
            $result = isset($result->_data[$section]) ? $result->_data[$section] : $default;
        }

        return $result;
    }

    

}