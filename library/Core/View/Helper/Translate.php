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
 * @package     Core_View
 * @subpackage  Core_View_Helper
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_View
 * @subpackage  Core_View_Helper
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_View_Helper_Translate
{
    /**
     *
     * @var Core_Translate
     */
    protected $_translate;

    /**
     *  @param  string $module
     *  @return string
     */
    public function translate($module = null)
    {
        if (null === $module) {
            if (null !== $this->view->box) {
                //box
                $module = $this->view->box->box_namespace . '_'
                        . $this->view->box->box_module;
            } elseif (null !== $this->view->module) {
                //controller render
                $module = $this->view->namespace . '_'
                        . $this->view->moduleName;
            } else {
                $module = 'Core';
            }
        } elseif (false === strpos($module, '_')) {
            $module = '' . $module;
        }
        $module = str_replace(' ', '_', ucwords(str_replace('_', ' ', $module)));        
        $this->_translate = Core::translate($module);      
        return $this;
    }

    /**
     * @param string $text
     * @param array $args
     * @return string
     */
    public function __()
    {
    	//var_dump( $this->_translate->translate(func_get_args()));exit;
        return $this->_translate->translate(func_get_args());
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}