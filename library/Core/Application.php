<?php
/**
 * Axis
 *
 * This file is part of Axis.
 *
 * Axis is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Axis is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Axis.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Axis
 * @package     Axis_Core
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

 /**
  * @see Zend_Application
  */
@include_once 'Zend/Application.php';

if (!class_exists('Zend_Application')) {
    echo 'Please, copy Zend Framework to the "library" folder: '
        . realpath('library');
    exit();
}

/**
 *
 * @uses        Zend_Application
 * @category    Axis
 * @package     Axis_Core
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Application extends Zend_Application
{
    /**
     * Checks is Axis is installed already
     *
     * @static
     * @return bool
     */
    public static function isInstalled()
    {
        return file_exists(CORE_ROOT . '/app/etc/config.php');
    }

    /**
     * Retrieve the list of active, installed modules
     *
     * @return array code => path pairs
     */
    public function getModules()
    {
        if (Zend_Registry::isRegistered('modules')) {
            return Zend_Registry::get('modules');
        }
        if (!$modules = Core::cache()->load('modules_list')) {
            $list = Core::single('core/module')->getList('is_active = 1');
            $result = array();
            foreach ($list as $moduleCode => $values) {
                list($namespace, $module) = explode('_', $moduleCode, 2);
                $modules[$moduleCode] = Core::config()->system->path
                    . '/app/modules/' . $namespace . '/' . $module;
            }
            Core::cache()->save($modules, 'modules_list', array('modules'));
        }
        Zend_Registry::set('modules', $modules);
        return Zend_Registry::get('modules');
    }

    /**
     * Retrieve the controllers paths
     *
     * @return array code => path pairs
     */
    public function getControllers()
    {
        if (!$result = Core::cache()->load('controllers_list')) {
            $modules = $this->getModules();
            $result = array();
            foreach ($modules as $moduleCode => $path) {
                if (is_readable($path . '/controllers')) {
                    $result[$moduleCode] = $path . '/controllers';
                }
            }
            Core::cache()->save($result, 'controllers_list', array('modules'));
        }
        return $result;
    }

    /**
     * Retrieve array of paths to route files
     *
     * @return array
     */
    public function getRoutes()
    {
        if (!($routes = Core::cache()->load('routes_list'))) {
            $modules = $this->getModules();
            $routes = array();
            foreach ($modules as $moduleCode => $path) {
                if (file_exists($path . '/etc/routes.php')
                    && is_readable($path . '/etc/routes.php')) {

                    $routes[] = $path . '/etc/routes.php';
                }
            }
            Core::cache()->save(
                $routes, 'routes_list', array('modules')
            );
        }
        return $routes;
    }

    /**
     *
     * @return array
     */
    public function getNamespaces()
    {
        $namespaces = array();
        $codePath = CORE_ROOT . '/app/modules';
        try {
            $codeDir = new DirectoryIterator($codePath);
        } catch (Exception $e) {
            throw new Core_Exception(
                Core::translate('core')->__(
                    "Directory %s not readable", $codePath
                )
            );
        }
        foreach ($codeDir as $namespace) {
            $namespace = $namespace->getFilename();
            if ($namespace[0] == '.') {
                continue;
            }
            $namespaces[] = $namespace;
        }
        return $namespaces;
    }

    /**
     * Return current Axis version
     *
     * @return string
     */
    public function getVersion()
    {
        return '0.8.7.2';
    }
}
