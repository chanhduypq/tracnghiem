<?php

require_once 'Core/Form/Element/Xhtml.php';


class Core_Form_Element_Password extends Core_Form_Element_Xhtml
{
	/**
	 * @var integer
	 *
	 */
	private $min_string_length=1;
	/**
	 * @var integer
	 *
	 */
	private $max_string_length=1000;	
    /**
     * Use formPassword view helper by default
     * @var string
     */
    public $helper = 'formPassword';

    /**
     * Whether or not to render the password
     * @var bool
     */
    public $renderPassword = false;

    /**
     * Set flag indicating whether or not to render the password
     * @param  bool $flag
     * @return Core_Form_Element_Password
     */
    public function setRenderPassword($flag)
    {
        $this->renderPassword = (bool) $flag;
        return $this;
    }

    /**
     * Get value of renderPassword flag
     *
     * @return bool
     */
    public function renderPassword()
    {
        return $this->renderPassword;
    }

    /**
     * Override isValid()
     *
     * Ensure that validation error messages mask password value.
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof Zend_Validate_Abstract) {
                $validator->setObscureValue(true);
            }
        }
        return parent::isValid($value, $context);
    }
    /**
     * @return Core_Form_Element_Password
     * @var integer
     */
    public function setMinStringLength($min){
    	$this->min_string_length=$min;
    	$validate_string_length=new Core_Validate_StringLength();
    	$validate_string_length->setMax($this->max_string_length)->setMin($this->min_string_length);
    	if(false!==$this->getValidator('StringLength')){
    		$this->removeValidator('StringLength');
    	}
    	$this->addValidator($validate_string_length);
    	return $this;
    }
    /**
     * @return Core_Form_Element_Password
     * @var integer
     */
    public function setMaxStringLength($max){
    	$this->max_string_length=$max;
    	$validate_string_length=new Core_Validate_StringLength();
    	$validate_string_length->setMax($this->max_string_length)->setMin($this->min_string_length);
    	if(false!==$this->getValidator('StringLength')){
    		$this->removeValidator('StringLength');
    	}
    	$this->addValidator($validate_string_length);
    	return $this;
    }
}
