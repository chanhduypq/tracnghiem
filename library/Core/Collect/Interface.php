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
 * @package     Core_Collect
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Collect
 * @author      Core Core Team <core@axiscommerce.com>
 */
interface Core_Collect_Interface
{
    /**
     * 
     * @param array $params
     * @param array $option
     * @static
     * @return array
     */
    public static function collect($params = array(),$option = array());

    /**
     *
     * @static
     * @param int $id
     * @return string
     */
    public static function getName($id,$option = array());
}