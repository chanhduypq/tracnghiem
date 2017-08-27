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
 * @package     Core_Controller
 * @subpackage  Plugin
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Controller
 * @subpackage  Plugin
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_Controller_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (null !== $request->getParam('locale')//_hasParam('locale')
            /*&& Core_Controller_Router_Route_Front::hasLocaleInUrl()*/) {

            $locale = $request->getParam('locale');
        } elseif (isset(Core::session()->locale)) {
            $locale = Core::session()->locale;
        } else {
            $locale = Core_Locale::getDefaultLocale();
        }
        //var_dump($locale);exit;        
        Core_Locale::setLocale($locale);
    }
}
