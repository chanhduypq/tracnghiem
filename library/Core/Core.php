<?php
/**
* @file: Core.php
* @author: chanhduypq@gmail.com
**/
class Core{
	/**
	 * Return customer id or null
	 *
	 * @static
	 * @return mixed (int|null)
	 */
	public static function getUserId()
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return null;
		}
		return Zend_Auth::getInstance()->getIdentity()->id;
	}
	
	public static function getUser()
	{
            return null;
		
	}
	/**
	 * Retrieve config object or config value,
	 * if name is requested
	 *
	 * @static	 
	 * @return object Core_Auth_Acl|mixed
	 */
	public static function acl(){
		if (!Zend_Registry::isRegistered('acl')) {
			Zend_Registry::set('acl', new Zend_Acl());
		}
		return Zend_Registry::get('acl');
	}
	/**
	 * Retrieve config object or config value,
	 * if name is requested
	 *
	 * @static
	 * @param string $name[optional] config value to load
	 * @param string $default[optional] default value to return
	 * @return object Onegate_Config|mixed
	 */
	public static function config($name = null,$default = null)
	{
		if (!Zend_Registry::isRegistered('config')) {
			throw new Core_Exception(
					Core::translate('core')->__(
							'Config is not initialized'
					)
			);
		}
		if (null !== $name) {
			return Zend_Registry::get('config')->get($name, $default);
		}
		return Zend_Registry::get('config');
	}
    /**
     * Retrieve database adapter object
     *
     * @static
     * @return \Doctrine\ORM\EntityManager      
     */
    public static function em()
    {
    	/* Initialize action controller here */
    	$registry = Zend_Registry::getInstance();
    	return $registry->entitymanager;
    }
    /**
     * Returns singleton object
     *
     * @static
     * @param string $class
     * @param array $arguments [optional]
     * @return Core_Db_Table_Abstract
     */
    public static function single($class, $arguments = array())
    {
    	$class = self::getClass($class);
    
    	if (!Zend_Registry::isRegistered($class)) {
    		$instance = new $class($arguments);
    		Zend_Registry::set($class, $instance);
    	}
    	return Zend_Registry::get($class);
    }
    
    /**
     * Return requested model instance
     *
     * @static
     * @param string $model
     * @param array $arguments class arguments
     * @return Core_Db_Table_Abstract
     */
    public static function model($model, $arguments = array())
    {
    	$class = self::getClass($model);
    
    	return new $class($arguments);
    }
    
    /**
     * Return class name by shortname
     *
     * @static
     * @param string $name
     * @param string $type
     * @return string
     */
    public static function getClass($name, $type = 'Model')
    {
    	$parts = explode('/', $name);
    
    	if (1 === count($parts)) {
    		return $name;
    	}
    
    	if (strstr($parts[0], '_')) {
    		list($namespace, $module) = explode('_', $parts[0]);
    		$namespace = ucfirst($namespace);
    	} else {
    		$namespace = 'Core';
    		$module   = $parts[0];
    	}
    	$module = ucfirst($module);
    	$name   = str_replace(' ', '_', ucwords(str_replace('_', ' ', $parts[1])));
    
    	return $module . '_' . $type . '_' . $name;
    }
    /**
     * Retrieve Core_Message object
     *
     * @static
     * @return Core_Message
     */
    public static function message($namespace = 'messenger')
    {
    	return Core_Message::getInstance($namespace);
    }
    /**
     * Retrieve database adapter object
     *
     * @static
     * @return Zend_Db_Adapter_Abstract
     */
    public static function db()
    {
    	return Zend_Registry::get('db');
    }
    
    public static function auth()
    {
    	return Zend_Registry::get('auth');
    }
    /**
     * Retrieve log object
     *
     * @static
     * @return Zend_Log
     */
    public static function log()
    {
    	return Zend_Registry::get('log');
    }
    /**
     * Retrieve cache object
     *
     * @static
     * @return Zend_Cache_Core
     */
    public static function cache()
    {
    	return Zend_Registry::get('cache');
    }
    /**
     * Retrieve session object
     *
     * @static
     * @return Zend_Session_Namespace
     */
    public static function session($namespace = 'Core')
    {    	
    	if (!Zend_Registry::isRegistered($namespace)) {
    		Zend_Registry::set($namespace, new Zend_Session_Namespace($namespace));
    	}
    	return Zend_Registry::get($namespace);
    }
    /**
     *
     * @param string $name
     * @return Core_Translate
     */
    public static function translate($module = 'Core')
    {
    	if (false === strpos($module, '_')) {
    		$module = ucfirst($module);
    	}
    	$module = str_replace(' ', '_', ucwords(str_replace('_', ' ', $module)));    	
    	return Core_Translate::getInstance($module);
    }
    /**
     * Dispatch event
     *
     * Calls all of the methods linked to dispatched event
     *
     * @static
     * @param string $name
     * @param array $data [optional]
     * @return Core_Event_Observer
     */
    public static function dispatch($name, $data = array())
    {
    	return Core_Event_Observer::getInstance()->dispatch($name, $data);
    }
    /**
     * Dispatch event
     *
     * Calls all of the methods linked to dispatched event
     *
     * @static
     * @param string $name
     * @param array $data [optional]
     * @return string
     */
    public static function getNameTable($name, array $option = null)
    {
    	return $name;
    }
}