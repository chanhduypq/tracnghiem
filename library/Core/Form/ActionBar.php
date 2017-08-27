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
 * @package     Core_Form
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 * 
 * @category    Core
 * @package     Core_Form
 * @author      Core Core Team <core@axiscommerce.com>
 */
class Core_Form_ActionBar extends Zend_Form_DisplayGroup
{
    /**
     * Load default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements');
        }
    }
}