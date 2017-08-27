<?php

class Core_Object implements ArrayAccess
{
    /**
     *
     * @var array
     */
    protected $_data = array();

    /**
     *
     * @param array $data
     */
    public function  __construct($data = null)
    {
        if (is_array($data)) {
            $this->setFromArray($data);
        }
    }

    /**
     *
     * @param string $key
     * @return string
     */
    protected function _underscore($key)
    {
        return strtolower(preg_replace(
            array('/(.)([A-Z])/', '/(.)(\d+)/'), "$1_$2", $key
        ));
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if (null === $key) {
            return $this->_data;
        }

        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getData($key);
    }

     /**
     *
     * @param string $key
     * @param mixed $value
     * @return Core_Object Fluent interface
     */
    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return Core_Object Fluent interface
     */
    public function __set($name, $value)
    {
        return $this->setData($name, $value);
    }

    /**
     *
     * @param  string  $key   The column key.
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Unset row field value
     *
     * @param  string $key
     * @return Core_Object Provides a fluent interface
     * @throws Core_Exception
     */
    public function __unset($key)
    {
        if (!array_key_exists($key, $this->_data)) {
            throw new Core_Exception("Specified property \"$key\" is not in the object");
        }

        unset($this->_data[$key]);
        return $this;
    }

    /**
     * Sets all data from an array.
     *
     * @param  array $data
     * @return Core_Object Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }

        return $this;
    }

    /**
     * Convert object to array
     *
     * @param  array $attributes array of required attributes
     * @return array
     */
    public function __toArray(array $attributes = array())
    {
        if (empty($attributes)) {
            return $this->_data;
        }

        $result = array();
        foreach ($attributes as $attribute) {
            if (isset($this->_data[$attribute])) {
                $result[$attribute] = $this->_data[$attribute];
            } else {
                $result[$attribute] = null;
            }
        }
        return $result;
    }

    /**
     * Public wrapper for __toArray
     *
     * @param array $attributes
     * @return array
     */
    public function toArray(array $attributes = array())
    {
        return $this->__toArray($attributes);
    }

    /**
     *
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $key = $this->_underscore(substr($name, 3));

        switch (substr($name, 0, 3)) {
            case 'get':
                return $this->getData($key);
                break;
            case 'set':
                if (!count($arguments)) {
                    $arguments[] = null;
                }
                return $this->setData($key, $arguments[0]);
                break;
            case 'has':
                return (bool) $this->getData($key);
                break;
        }

        throw new Core_Exception("Call to undefined method". get_class($this) . '::' . $name);
    }

   /**
     * Proxy to __isset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Proxy to __get
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return string
     */
     public function offsetGet($offset)
     {
         return $this->__get($offset);
     }

     /**
      * Proxy to __set
      * Required by the ArrayAccess implementation
      *
      * @param string $offset
      * @param mixed $value
      */
     public function offsetSet($offset, $value)
     {
         $this->__set($offset, $value);
     }

     /**
      * Proxy to __unset
      * Required by the ArrayAccess implementation
      *
      * @param string $offset
      */
     public function offsetUnset($offset)
     {
         return $this->__unset($offset);
     }
}