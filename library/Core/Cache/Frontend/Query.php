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
 * @package     Core_Cache
 * @subpackage  Core_Cache_Frontend
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Cache
 * @subpackage  Core_Cache_Frontend
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_Cache_Frontend_Query extends Core_Cache_Frontend_Abstract
{
    /**
     * @var string
     */
    private $_customId = null;

    /**
     * @var array
     */
    protected $_nonCachedMethods = array(
        'insert', 'update', 'delete', 'save'//, 'set*', 'add*', 'remove*'
    );

    /**
     * @param Object $instance model class __instance__
     * @param string $customId
     * @return Core_Cache_Frontend_Abstract Fluent interface
     */
    public function setInstance($instance, $customId = null)
    {
        $this->_instance = $instance;
        $this->_customId = $customId;
        return $this;
    }

    /**
     *
     * @return Core_Db_Table_Abstract
     */
    protected function _getModel()
    {
        return $this->_instance;
    }

    /**
     *
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments)
    {
        $cacheBool1 = $this->_cacheByDefault;
        $cacheBool2 = in_array($methodName, $this->_cachableMethods);
        $cacheBool3 = in_array($methodName, $this->_nonCachedMethods);
        $cache = (($cacheBool1 || $cacheBool2) && (!$cacheBool3));
        if (!$cache) {
            // We do not have not cache
            return call_user_func_array(
                array($this->_getModel(), $methodName), $arguments
            );
        }

        /** Get cache instance */
        $cache = Core::cache();

        $id = $this->_makeId($methodName, $arguments);
        if ($cache->test($id)) {
            // A cache is available
            $result = $cache->load($id);
            $output = $result[0];
            $return = $result[1];
        } else {
            // A cache is not available
            ob_start();
            ob_implicit_flush(false);
            $return = call_user_func_array(
                array($this->_getModel(), $methodName), $arguments
            );
            $output = ob_get_contents();
            ob_end_clean();
            $data = array($output, $return);
            $cache->save(
                $data, $id,
                array_merge($this->_tags, array('query')),
                $this->_specificLifetime,
                $this->_priority
            );
        }
        echo $output;
        return $return;
    }

    /**
     * Make a cache id from the method name and parameters
     *
     * @param  string $methodName       Method name
     * @param  array  $parameters Method parameters
     * @return string Cache id
     */
    protected function _makeId($methodName, $parameters)
    {
        asort($parameters);
        return md5(
            get_class($this->_getModel())
            . $methodName
            . serialize($parameters)            
            . Core_Locale::getLanguageId()
            . $this->_customId
            //. Core::getCustomerId()
        );
    }
}