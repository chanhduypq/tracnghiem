<?php

class Core_Model_Config_Builder
{
    /**
     *
     * @var array
     */
    protected $_rowField = array();

    /**
     *
     * @var array
     */
    protected $_defaultsRowField = array();

    /**
     *
     * @var mixed
     */
    protected $_rawValue = null;

    /**
     *
     * @var array
     */
    protected $_path = array();

    /**
     *
     * @var bool
     */
    protected $_isContainer = false;

    public function __construct()
    {
        $this->flushDefaults();
    }

    /**
     *
     * @return Core_Model_Config_Builder
     */
    public function flushDefaults()
    {
        $this->_defaultsRowField = array(
            'type'               => 'text',
            'description'        => '',
            'model'              => '',
            'translation_module' => new Zend_Db_Expr('NULL')
        );
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setDefaultType($value)
    {
        $this->_defaultsRowField['type'] = $value;
        return $this;
    }

    /**
     *
     * @param mixed $value
     * @return Core_Model_Config_Builder
     */
    public function setDefaultDescription($value)
    {
        $this->_defaultsRowField['description'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setDefaultModel($value)
    {
        $this->_defaultsRowField['model'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setDefaultTranslation($value)
    {
        $this->_defaultsRowField['translation_module'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setTitle($value)
    {
        $this->_rowField['title'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setType($value)
    {
        $this->_rowField['type'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setModel($value)
    {
        $this->_rowField['model'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setDescription($value)
    {
        $this->_rowField['description'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setTranslation($value)
    {
        $this->_rowField['translation_module'] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Core_Model_Config_Builder
     */
    public function setValue($value)
    {
        $this->_rawValue = $value;
        return $this;
    }

    /**
     *
     * @param string $path
     * @param string $title
     * @return Core_Model_Config_Builder
     */
    public function section($path = null, $title = null)
    {
        $this->_savePrevious();
        if ('/' === $path) {
            $this->_path = array();
        } elseif (in_array(ltrim($path, '/'), $this->_path)) {
            while (ltrim($path, '/') !== array_pop($this->_path)) {}
        } else {
            $this->_isContainer = true;
            array_push($this->_path, $path);
            $this->_rowField = array(
                'path' => implode('/', $this->_path),
                'lvl'  => count($this->_path)
            );
            if (null !== $title) {
                $this->setTitle($title);
            }
        }
        return $this;
    }

    /**
     *
     * @param string $path
     * @param string $title
     * @param mixed $value
     * @return Core_Model_Config_Builder
     */
    public function option($path, $title = null, $value = null)
    {
        $this->_savePrevious();
        $this->_rawValue = null;
        $this->_isContainer = false;
        array_push($this->_path, $path);
        $this->_rowField = array(
            'path' => implode('/', $this->_path),
            'lvl'  => count($this->_path)
        );
        array_pop($this->_path);
        if (null !== $title) {
            $this->setTitle($title);
        }
        if (null !== $value) {
            $this->setValue($value);
        }
        return $this;
    }

    protected function _savePrevious()
    {
        $rowData = $this->_rowField;
        $this->_rowField = array();
        if (empty($rowData)) {
          return;
        }
        $modelField = Core::single('core/config_field');
        $rowField = $modelField->select()
            ->where('path = ?', $rowData['path'])
            ->fetchRow();
        if (!$rowField) {
            $rowData = array_merge($this->_defaultsRowField, $rowData);
            $rowField = $modelField->createRow();
        }
        if ($this->_isContainer) {

            $rowData = array_merge($rowData, array(
                'type'  => '',
                'model' => '',
            ));
        }
        $rowField->setFromArray($rowData);
        $rowField->save();

        if ($this->_isContainer) {
            return;
        }
        $modelValue = Core::single('core/config_value');
        $rowValue = $modelValue->select()
            ->where('path = ?', $rowData['path'])
            ->where('config_field_id = ?', $rowField->id)            
            ->fetchRow();
        if (!$rowValue) {
            $rowValue = $modelValue->createRow(array(
                'config_field_id' => $rowField->id,
                'path'            => $rowData['path']
            ));
        }
        if (null !== $this->_rawValue) {
            $value = $this->_rawValue;

            if (!empty($rowData['model'])) {
                $class = Core::getClass($rowData['model']);
                if (class_exists($class)
                    && in_array('Core_Config_Option_Encodable_Interface', class_implements($class))) {

                    $value = Core::model($rowData['model'])->encode($value);
                }
            }
            $rowValue->value = $value;
        }
        $rowValue->save();
    }

    /**
     * Removes config field, and all of it childrens
     * Provide fluent interface
     * @param string $path
     * @return Core_Model_Config_Builder
     */
    public function remove($path)
    {
        Core::single('core/config_value')->delete("path LIKE '{$path}%'");
        Core::single('core/config_field')->delete("path LIKE '{$path}%'");
        return $this;
    }
}