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
 * @package     Axis_Form
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_Form
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Core_Form extends Zend_Form
{	
    /**
     * @var Axis_Form_ActionBar
     */
    private $_actionBar = null;

    protected $_translatorModule = 'Core';

    protected $_eventPrefix = null;
    
    public function init()
    { 
    	
    }

    /**
     * Constructor. Overriden to add events after form initialization
     *
     * Registers form view helper as decorator
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {      	  	
        $this->setDefaultDisplayGroupClass('Core_Form_DisplayGroup');
        $this->addPrefixPath('Core_Form_Decorator', 'Core/Form/Decorator', 'decorator');
        $this->setTranslator(Core::translate($this->_translatorModule)->getAdapter());

        parent::__construct($options);

        Core::dispatch('form_construct_after', $this); // global event for all forms
        if ($this->_eventPrefix) {
            Core::dispatch($this->_eventPrefix . '_construct_after', $this);
        }
    }
    /**
     * @param array $element_names
     * @return Core_Form
     */ 
    public function orderElements($element_names){    
    	if(!is_array($element_names)||count($element_names)==0){
    		return $this;
    	}
    	$elements=$this->getElements();
    	/*
    	 * 
    	 */
    	if(count($elements)!=count($element_names)){
    		return $this;
    	}
    	/* 
    	 * Nếu trong đối array truyền vào, có một element_name nhập vào bị nhầm thi hủy ngắt, không làm gi cả 
    	 */    	
    	foreach ($element_names as $element_name){
    		if(!array_key_exists($element_name,$elements)){    			
    			return $this;
    		}
    	}
    	/*
    	 * tạo array mới chứa các elenment theo thứ tự
    	 */
    	$new_elements=array();
    	foreach ($element_names as $element_name){
    		$temp=$this->getElement($element_name);
    		if(null!==$temp){
    			$new_elements[]=$temp;
    		}
    		
    	}    	
    	/*
    	 * remove tất cả các elenment theo thứ tự cũ
    	 */    	
    	$keys=array_keys($elements);
    	foreach ($keys as $key){
    		$this->removeElement($key);
    	}
    	/*
    	 * đưa array chứa các elenment theo thứ tự vào 
    	 */
    	foreach ($new_elements as $new_element){
    		$this->addElement($new_element);
    	}
    	return $this;
    }
    /**
     * @param string $table_name
     * @return Core_Form
     */
    public function buildElementsAutoForFormByTableName($table_name, &$primaryName = '') 
    {
        $db=Zend_Db_Table::getDefaultAdapter();    	
    	$metadata = $db->describeTable($table_name);    	   	    	
    	if(!is_array($metadata)||count($metadata)==0){
    		return $this;
    	}    	
    	$keys = array_keys($metadata);
    	if(!is_array($keys)||count($keys)==0){
    		return $this;
    	}
        /**
         * get primary
         */
        $primaryName='';
        foreach ($keys as $key){
    		$column_difinition=$metadata[$key];
                if ($column_difinition['PRIMARY']==true){    				
                        $primaryName=$column_difinition['COLUMN_NAME'];
                        break;
                }   
        }
        
    	foreach ($keys as $key){
    		$column_difinition=$metadata[$key];    		
    		if($column_difinition['DATA_TYPE']=='varchar'
    		   ||$column_difinition['DATA_TYPE']=='char'		    			
    		)
    		{
    			$element=new Core_Form_Element_Text($column_difinition['COLUMN_NAME']);
    			if($column_difinition['NULLABLE']==false){
    				$element  			
    				->setRequired(true)
    				;
    				$element
    				->addFilter ( 'StringTrim' )
    				;
    			}   		
    			$element->setMaxStringLength((int)$column_difinition['LENGTH']);
    			if($column_difinition['DEFAULT']!=null){
    				$element->setValue($column_difinition['DEFAULT']);
    			}
                        if (strtolower($column_difinition['COLUMN_NAME']) == 'email') {
                            $element->setIsEmail(TRUE);
                            $element->setUnique(TRUE, $table_name, $excludeField = $primaryName);
                        }
            }
    		elseif ($column_difinition['DATA_TYPE']=='text'
    				||$column_difinition['DATA_TYPE']=='longtext'
    				||$column_difinition['DATA_TYPE']=='mediumtext'
                                ||$column_difinition['DATA_TYPE']=='tinytext'
    				
    		)
    		{
    			$element=new Core_Form_Element_Textarea($column_difinition['COLUMN_NAME']);
    			if($column_difinition['NULLABLE']==false){
    				$element    				
    				->setRequired(true);
    				$element->addFilter ( 'StringTrim' );
    			}
    			
    		}
    		elseif ($column_difinition['DATA_TYPE']=='tinyblob'
    				||$column_difinition['DATA_TYPE']=='blob'
    				||$column_difinition['DATA_TYPE']=='mediumblob'
    				||$column_difinition['DATA_TYPE']=='longblob'    		
    		)
    		{
    			$element=new Core_Form_Element_File($column_difinition['COLUMN_NAME']);
    			if($column_difinition['NULLABLE']==false){
    				$element  				
    				->setRequired(true)
    				;   				
    			}
    			 
    		}
    		elseif ($column_difinition['DATA_TYPE']=='int'
    				||$column_difinition['DATA_TYPE']=='bigint'
    				||$column_difinition['DATA_TYPE']=='tinyint'
    				||$column_difinition['DATA_TYPE']=='smallint'
    				||$column_difinition['DATA_TYPE']=='mediumint'    				
    		)
    		{    			
    			if ($column_difinition['PRIMARY']==true){    				
    				$element=new Core_Form_Element_Hidden($column_difinition['COLUMN_NAME']);
                                $element->setIsPrimary(true);                                
    			}    			
    			else{
    				$element=new Core_Form_Element_Text($column_difinition['COLUMN_NAME']);
    				if($column_difinition['NULLABLE']==false){
    					$element    					
    					->setRequired(true);
    					$element->addFilter ( 'StringTrim' );
    				}
    				$element
    				->setValidateDigits()
    				;
    			}
    			if($column_difinition['DEFAULT']!=null){    				
    				$element->setValue($column_difinition['DEFAULT']);
    			}
    					    
    		} 
    		elseif ($column_difinition['DATA_TYPE']=='date'
    				||$column_difinition['DATA_TYPE']=='datetime'
    				||$column_difinition['DATA_TYPE']=='time'
    				||$column_difinition['DATA_TYPE']=='timestamp'
    		
    		)
    		{
    			$element=new Core_Form_Element_Date($column_difinition['COLUMN_NAME']);
                        $element->setAttrib('readonly', 'readonly');                        
    			if($column_difinition['NULLABLE']==false){
    				$element
    				->setRequired(true);
    				$element->addFilter ( 'StringTrim' );    				
    			} 
    			if($column_difinition['DEFAULT']!=null){
    				$value='';
    				if($column_difinition['DEFAULT']=='CURRENT_TIMESTAMP'){
    					$value=date('d/m/Y');
    				}
    				else{    					
    					$array=explode(" ",$column_difinition['DEFAULT']);
    					$date=$array[0];
    					$array=explode("-",$date);    					
    					$value=$array[2]."/".$array[1]."/".$array[0];
    				}    				
    				$element->setValue($value);
    			}
    			$element
    			->setValidateDate()
    			;
    			   			
    		}   
    		elseif ($column_difinition['DATA_TYPE']=='binary(1)'    				
    		
    		)
    		{    			
    			$element=new Core_Form_Element_Checkbox($column_difinition['COLUMN_NAME']);
    			if($column_difinition['DEFAULT']!=null){
    				$element->setValue($column_difinition['DEFAULT']);
    			}
    			$element
    			->setCheckedValue(1)
    			->setUncheckedValue(0)
    			;
    		}		
    		$element->setLabel($element->getName());
    		$this->addElement($element);
    	}   	
    	return $this;
    }
    
    /**
     * @param string $table_name
     * @return Core_Form
     */
    public function buildElementsAutoForFormByFileTableName($table_name){
    	$db=Zend_Db_Table::getDefaultAdapter();
    	$metadata = $db->describeTable($table_name);
    	if(!is_array($metadata)||count($metadata)==0){
    		return $this;
    	}
    	$keys = array_keys($metadata);
    	if(!is_array($keys)||count($keys)==0){
    		return $this;
    	}
    	foreach ($keys as $key){
    		$column_difinition=$metadata[$key];   		
    		if ($column_difinition['DATA_TYPE']=='tinyblob'
    				||$column_difinition['DATA_TYPE']=='blob'
    				||$column_difinition['DATA_TYPE']=='mediumblob'
    				||$column_difinition['DATA_TYPE']=='longblob'
    		)
    		{
    			$element=new Core_Form_Element_File($column_difinition['COLUMN_NAME']);
    			$element
    			->setForInsertDB(true)
    			->setRequired(true)
    			;
    			
    
    		}
    		else{
    			$element=new Core_Form_Element_Hidden($column_difinition['COLUMN_NAME']);
    		}
    		
    		$element->setLabel($element->getName());
    		$this->addElement($element);
    	}
    	return $this;
    }
       
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
            'FormElements',
            'ActionBar',
            array('HtmlTag', array('tag' => 'ul')),
            array('Form', array('class' => 'core-form'))
        ));
    }

    /**
     * Add action bar with set of buttons to form
     *
     * @param array $elements
     * @return Axis_Form
     */
    public function addActionBar(array $elements)
    {
        if (null !== $this->_actionBar) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('Cannot assign more then one action bars to one form');
        }

        foreach ($elements as $element) {
            if (isset($this->_elements[$element])) {
                $add = $this->getElement($element);
                if (null !== $add) {
                    unset($this->_order[$element]);
                    $group[] = $add;
                }
            }
        }
        if (empty($group)) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception('No valid elements specified for actionbar');
        }

        $options = array('elements' => $group);

        $this->_actionBar = new Core_Form_ActionBar(
            'actionbar',
            $this->getPluginLoader(self::DECORATOR),
            $options
        );

        return $this;
    }

    /**
     * @return Axis_Form_ActionBar
     */
    public function getActionBar()
    {
        return $this->_actionBar;
    }

    /**
     * Create an element
     *
     * Acts as a factory for creating elements. Elements created with this
     * method will not be attached to the form, but will contain element
     * settings as specified in the form object (including plugin loader
     * prefix paths, default decorators, etc.).
     *
     * @param  string $type
     * @param  string $name
     * @param  array|Zend_Config $options
     * @return Zend_Form_Element
     */
    public function createElement($type, $name, $options = null)
    {
        $element = parent::createElement($type, $name, $options);

        $liOptions = array(
            'class' => 'element-row'
        );

        switch ($type) {
            case 'submit': case 'button':
                $element->clearDecorators()
                    ->addDecorator('ViewHelper');
            break;
            case 'hidden':
                $liOptions = array(
                    'class' => 'element-hidden',
                    'style' => 'display: none;'
                );
            default:
                $getId = create_function(
                    '$decorator',
                    'return $decorator->getElement()->getId() . "-row";'
                );
                $element->clearDecorators()
                    ->addDecorator('ViewHelper')
                    ->addDecorator('Errors')
                    ->addDecorator('Description', array('tag' => 'small', 'class' => 'description'))
                    ->addDecorator('Label', array(
                        'tag' => '',
                        'placement' => 'prepend'
                    ))
                    ->addDecorator('HtmlTag', array_merge($liOptions, array(
                        'tag' => 'li',
                        'id'  => array('callback' => $getId)
                    )));
            break;
        }

        return $element;
    }

    /**
     * Validate the form
     * Overloaded to load validation translations from core module
     * @author Trần Công Tuệ
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)  
    {
    	
        if (!is_array($data)) {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception(__CLASS__ . '::' . __METHOD__ . ' expects an array');
        }
        $translator = $this->getTranslator();
        $elementsTranslator = Core::translate('Core')->getAdapter();
        $valid      = true;

        if ($this->isArray()) {
            $data = $this->_dissolveArrayValue($data, $this->getElementsBelongTo());
        }

        foreach ($this->getElements() as $key => $element) {        	
            $element->setTranslator($elementsTranslator);            
            if (!isset($data[$key])) {
                $valid = $element->isValid(null, $data) && $valid;
            } else {
                $valid = $element->isValid($data[$key], $data) && $valid;                
            }            
        }
        
        foreach ($this->getSubForms() as $key => $form) {
            $form->setTranslator($translator);
            if (isset($data[$key])) {
                $valid = $form->isValid($data[$key]) && $valid;
            } else {
                $valid = $form->isValid($data) && $valid;
            }
        }

        $this->_errorsExist = !$valid;

        // If manually flagged as an error, return invalid status
        if ($this->_errorsForced) {
            return false;
        }

        
        return $valid;
    }

    /**
     * Validate a partial form
     * Overloaded to load validation translations from core module
     *
     * Does not check for required flags.
     *
     * @param  array $data
     * @return boolean
     */
    public function isValidPartial(array $data)
    {
        if ($this->isArray()) {
            $data = $this->_dissolveArrayValue($data, $this->getElementsBelongTo());
        }

        $translator        = $this->getTranslator();
        $elementsTranslator = Core::translate('Core')->getAdapter();
        $valid             = true;
        $validatedSubForms = array();

        foreach ($data as $key => $value) {
            if (null !== ($element = $this->getElement($key))) {
                if (null !== $elementsTranslator) {
                    $element->setTranslator($elementsTranslator);
                }
                $valid = $element->isValid($value, $data) && $valid;
            } elseif (null !== ($subForm = $this->getSubForm($key))) {
                if (null !== $translator) {
                    $subForm->setTranslator($translator);
                }
                $valid = $subForm->isValidPartial($data[$key]) && $valid;
                $validatedSubForms[] = $key;
            }
        }
        foreach ($this->getSubForms() as $key => $subForm) {
            if (!in_array($key, $validatedSubForms)) {
                if (null !== $translator) {
                    $subForm->setTranslator($translator);
                }

                $valid = $subForm->isValidPartial($data) && $valid;
            }
        }

        $this->_errorsExist = !$valid;
        return $valid;
    }
    /**
     * Set default values for elements
     *
     * Sets values for all elements specified in the array of $defaults.
     *
     * @param  array $defaults
     * @return Core_Form
     */
    public function setDefaults(array $defaults)
    {    	
        $eBelongTo = null;

        if ($this->isArray()) {
            $eBelongTo = $this->getElementsBelongTo();
            $defaults = $this->_dissolveArrayValue($defaults, $eBelongTo);
        }
        foreach ($this->getElements() as $name => $element) {
            
            $check = $defaults;
            if (($belongsTo = $element->getBelongsTo()) !== $eBelongTo) {
                $check = $this->_dissolveArrayValue($defaults, $belongsTo);
            }
            if (array_key_exists($name, $check)) {
                $this->setDefault($name, $check[$name]);
                $defaults = $this->_dissolveArrayUnsetKey($defaults, $belongsTo, $name);
            }
            if ($element instanceof Core_Form_Element_Date) {
                $array = explode(" ", $element->getValue());
                $date = $array[0];
                $array = explode("-", $date);
                if (count($array) == 3) {
                    $value = $array[2] . "/" . $array[1] . "/" . $array[0];
                    $element->setValue($value);
                }
            }
            /**
             * trường hợp edit, nếu là textbox hoặc textarea thi giải mã ngược trở lại cho đúng với nội dung mà user đã nhập
             * ví dụ user đã nhập <b>abc</b> thi phải hiển thị trở lại như vay chứ không được là &lt;b&gt;abc&lt;/b&gt;
             */
            if ($element instanceof Core_Form_Element_Text || $element instanceof Core_Form_Element_Textarea) {
                $element->setValue(html_entity_decode($element->getValue()));
            }
        }
        foreach ($this->getSubForms() as $name => $form) {
            if (!$form->isArray() && array_key_exists($name, $defaults)) {
                $form->setDefaults($defaults[$name]);
            } else {
                $form->setDefaults($defaults);
            }
        }
        return $this;
    }
    
}
