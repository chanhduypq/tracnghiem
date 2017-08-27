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
class Core_View_Helper_Href
{
    /**
     * @var bool
     */
    private $_enabledSsl;

    public function __construct()
    {
        $this->_enabledSsl = Core::config('core/frontend/ssl');
    }

    /**
     *
     * @param string $href
     * @param bool $ssl [optional]
     * @return string
     */
    public function href($href = '', $ssl = false)
    {
        $baseUrl = ($ssl && $this->_enabledSsl) ?
            $this->view->secureUrl : $this->view->baseUrl;
        $href = trim($href, '/');
        if (!empty($href)) {
            $href = '/' . $href;
        }

        return $baseUrl . $href;
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}