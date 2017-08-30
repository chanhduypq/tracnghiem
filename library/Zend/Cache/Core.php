<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Core.php 24594 2012-01-05 21:27:01Z matthew $
 */


/**
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Core
{
    /**
     * Messages
     */
    const BACKEND_NOT_SUPPORTS_TAG = 'tags are not supported by the current backend';
    const BACKEND_NOT_IMPLEMENTS_EXTENDED_IF = 'Current backend doesn\'t implement the Zend_Cache_Backend_ExtendedInterface, so this method is not available';

    /**
     * Backend Object
     *
     * @var Zend_Cache_Backend_Interface $_backend
     */
    protected $_backend = null;

    /**
     * Available options
     *
     * ====> (boolean) write_control :
     * - Enable / disable write control (the cache is read just after writing to detect corrupt entries)
     * - Enable write control will lightly slow the cache writing but not the cache reading
     * Write control can detect some corrupt cache files but maybe it's not a perfect control
     *
     * ====> (boolean) caching :
     * - Enable / disable caching
     * (can be very useful for the debug of cached scripts)
     *
     * =====> (string) cache_id_prefix :
     * - prefix for cache ids (namespace)
     *
     * ====> (boolean) automatic_serialization :
     * - Enable / disable automatic serialization
     * - It can be used to save directly datas which aren't strings (but it's slower)
     *
     * ====> (int) automatic_cleaning_factor :
     * - Disable / Tune the automatic cleaning process
     * - The automatic cleaning process destroy too old (for the given life time)
     *   cache files when a new cache file is written :
     *     0               => no automatic cache cleaning
     *     1               => systematic cache cleaning
     *     x (integer) > 1 => automatic cleaning randomly 1 times on x cache write
     *
     * ====> (int) lifetime :
     * - Cache lifetime (in seconds)
     * - If null, the cache is valid forever.
     *
     * ====> (boolean) logging :
     * - If set to true, logging is activated (but the system is slower)
     *
     * ====> (boolean) ignore_user_abort
     * - If set to true, the core will set the ignore_user_abort PHP flag inside the
     *   save() method to avoid cache corruptions in some cases (default false)
     *
     * @var array $_options available options
     */
    protected $_options = array(
        'write_control'             => true,
        'caching'                   => true,
        'cache_id_prefix'           => null,
        'automatic_serialization'   => false,
        'automatic_cleaning_factor' => 10,
        'lifetime'                  => 3600,
        'logging'                   => false,
        'logger'                    => null,
        'ignore_user_abort'         => false
    );

    /**
     * Array of options which have to be transfered to backend
     *
     * @var array $_directivesList
     */
    protected static $_directivesList = array('lifetime', 'logging', 'logger');

    /**
     * Not used for the core, just a sort a hint to get a common setOption() method (for the core and for frontends)
     *
     * @var array $_specificOptions
     */
    protected $_specificOptions = array();

    /**
     * Last used cache id
     *
     * @var string $_lastId
     */
    private $_lastId = null;

    /**
     * True if the backend implements Zend_Cache_Backend_ExtendedInterface
     *
     * @var boolean $_extendedBackend
     */
    protected $_extendedBackend = false;

    /**
     * Array of capabilities of the backend (only if it implements Zend_Cache_Backend_ExtendedInterface)
     *
     * @var array
     */
    protected $_backendCapabilities = array();

    /**
     * Constructor
     *
     * @param  array|Zend_Config $options Associative array of options or Zend_Config instance
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        if (!is_array($options)) {
            Zend_Cache::throwException("Options passed were not an array"
            . " or Zend_Config instance.");
        }
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        $this->_loggerSanity();
    }

    /**
     * Set options using an instance of type Zend_Config
     *
     * @param Zend_Config $config
     * @return Zend_Cache_Core
     */
    public function setConfig(Zend_Config $config)
    {
        $options = $config->toArray();
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
        return $this;
    }

    /**
     * Set the backend
     *
     * @param  Zend_Cache_Backend $backendObject
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setBackend(Zend_Cache_Backend $backendObject)
    {
        $this->_backend= $backendObject;        
    }

    /**
     * Returns the backend
     *
     * @return Zend_Cache_Backend backend object
     */
    public function getBackend()
    {
        return $this->_backend;
    }

    /**
     * Public frontend to set an option
     *
     * There is an additional validation (relatively to the protected _setOption method)
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function setOption($name, $value)
    {
        if (!is_string($name)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        $name = strtolower($name);
        if (array_key_exists($name, $this->_options)) {
            // This is a Core option
            $this->_setOption($name, $value);
            return;
        }
        if (array_key_exists($name, $this->_specificOptions)) {
            // This a specic option of this frontend
            $this->_specificOptions[$name] = $value;
            return;
        }
    }

    /**
     * Public frontend to get an option value
     *
     * @param  string $name  Name of the option
     * @throws Zend_Cache_Exception
     * @return mixed option value
     */
    public function getOption($name)
    {
        if (is_string($name)) {
            $name = strtolower($name);
            if (array_key_exists($name, $this->_options)) {
                // This is a Core option
                return $this->_options[$name];
            }
            if (array_key_exists($name, $this->_specificOptions)) {
                // This a specic option of this frontend
                return $this->_specificOptions[$name];
            }
        }
        Zend_Cache::throwException("Incorrect option name : $name");
    }

    /**
     * Set an option
     *
     * @param  string $name  Name of the option
     * @param  mixed  $value Value of the option
     * @throws Zend_Cache_Exception
     * @return void
     */
    private function _setOption($name, $value)
    {
        if (!is_string($name) || !array_key_exists($name, $this->_options)) {
            Zend_Cache::throwException("Incorrect option name : $name");
        }
        if ($name == 'lifetime' && empty($value)) {
            $value = null;
        }
        $this->_options[$name] = $value;
    }

    /**
     * Force a new lifetime
     *
     * The new value is set for the core/frontend but for the backend too (directive)
     *
     * @param  int $newLifetime New lifetime (in seconds)
     * @return void
     */
    public function setLifetime($newLifetime)
    {
        $this->_options['lifetime'] = $newLifetime;
        
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id                     Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @param  boolean $doNotUnserialize       Do not serialize (even if automatic_serialization is true) => for internal use
     * @return mixed|false Cached datas
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        return FALSE;        
    }

    /**
     * Test if a cache is available for the given id
     *
     * @param  string $id Cache id
     * @return int|false Last modified time of cache entry if it is available, false otherwise
     */
    public function test($id)
    {
        return true;
    }

    /**
     * Save some data in a cache
     *
     * @param  mixed $data           Data to put in cache (can be another type than string if automatic_serialization is on)
     * @param  string $id             Cache id (if not set, the last cache id will be used)
     * @param  array $tags           Cache tags
     * @param  int $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @param  int   $priority         integer between 0 (very low priority) and 10 (maximum priority) used by some particular backends
     * @throws Zend_Cache_Exception
     * @return boolean True if no problem
     */
    public function save($data, $id = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        return true;
        
    }

    /**
     * Remove a cache
     *
     * @param  string $id Cache id to remove
     * @return boolean True if ok
     */
    public function remove($id)
    {
        return true;
    }

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param  string       $mode
     * @param  array|string $tags
     * @throws Zend_Cache_Exception
     * @return boolean True if ok
     */
    public function clean($mode = 'all', $tags = array())
    {
        return true;
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        return array();        
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        return array();
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching any cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
        return array();
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        return array();
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        return array();
    }

    /**
     * Return the filling percentage of the backend storage
     *
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        return 1;
    }

    /**
     * Return an array of metadatas for the given cache id
     *
     * The array will include these keys :
     * - expire : the expire timestamp
     * - tags : a string array of tags
     * - mtime : timestamp of last modification time
     *
     * @param string $id cache id
     * @return array array of metadatas (false if the cache id is not found)
     */
    public function getMetadatas($id)
    {
        return array();
    }

    /**
     * Give (if possible) an extra lifetime to the given cache id
     *
     * @param string $id cache id
     * @param int $extraLifetime
     * @return boolean true if ok
     */
    public function touch($id, $extraLifetime)
    {
        return TRUE;
    }

    /**
     * Validate a cache id or a tag (security, reliable filenames, reserved prefixes...)
     *
     * Throw an exception if a problem is found
     *
     * @param  string $string Cache id or tag
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected static function _validateIdOrTag($string)
    {
        if (!is_string($string)) {
            Zend_Cache::throwException('Invalid id or tag : must be a string');
        }
        if (substr($string, 0, 9) == 'internal-') {
            Zend_Cache::throwException('"internal-*" ids or tags are reserved');
        }
        if (!preg_match('~^[a-zA-Z0-9_]+$~D', $string)) {
            Zend_Cache::throwException("Invalid id or tag '$string' : must use only [a-zA-Z0-9_]");
        }
    }

    /**
     * Validate a tags array (security, reliable filenames, reserved prefixes...)
     *
     * Throw an exception if a problem is found
     *
     * @param  array $tags Array of tags
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected static function _validateTagsArray($tags)
    {
        if (!is_array($tags)) {
            Zend_Cache::throwException('Invalid tags array : must be an array');
        }
        foreach($tags as $tag) {
            self::_validateIdOrTag($tag);
        }
        reset($tags);
    }

    /**
     * Make sure if we enable logging that the Zend_Log class
     * is available.
     * Create a default log object if none is set.
     *
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _loggerSanity()
    {
        if (!isset($this->_options['logging']) || !$this->_options['logging']) {
            return;
        }

        if (isset($this->_options['logger']) && $this->_options['logger'] instanceof Zend_Log) {
            return;
        }

        // Create a default logger to the standard output stream
        require_once 'Zend/Log.php';
        require_once 'Zend/Log/Writer/Stream.php';
        require_once 'Zend/Log/Filter/Priority.php';
        $logger = new Zend_Log(new Zend_Log_Writer_Stream('php://output'));
        $logger->addFilter(new Zend_Log_Filter_Priority(Zend_Log::WARN, '<='));
        $this->_options['logger'] = $logger;
    }

    /**
     * Log a message at the WARN (4) priority.
     *
     * @param string $message
     * @throws Zend_Cache_Exception
     * @return void
     */
    protected function _log($message, $priority = 4)
    {
        if (!$this->_options['logging']) {
            return;
        }
        if (!(isset($this->_options['logger']) || $this->_options['logger'] instanceof Zend_Log)) {
            Zend_Cache::throwException('Logging is enabled but logger is not set');
        }
        $logger = $this->_options['logger'];
        $logger->log($message, $priority);
    }

    /**
     * Make and return a cache id
     *
     * Checks 'cache_id_prefix' and returns new id with prefix or simply the id if null
     *
     * @param  string $id Cache id
     * @return string Cache id (with or without prefix)
     */
    protected function _id($id)
    {
        if (($id !== null) && isset($this->_options['cache_id_prefix'])) {
            return $this->_options['cache_id_prefix'] . $id; // return with prefix
        }
        return $id; // no prefix, just return the $id passed
    }

}
