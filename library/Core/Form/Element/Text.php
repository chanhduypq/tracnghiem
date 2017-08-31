<?php

require_once 'Core/Form/Element/Xhtml.php';


class Core_Form_Element_Text extends Core_Form_Element_Xhtml
{
	/**
	 * @var integer
	 *
	 */
	private $min_int_value=0;
	/**
	 * @var integer
	 *
	 */
	private $max_int_value=1000;
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
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formText';    
    /**
     * @return Core_Form_Element_Text 
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
     * @return Core_Form_Element_Text
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
    /**
     * @return void
     * @return Core_Form_Element_Text 
     */
    public function setValidateDigits(){   	
    	$validate_digits=new Core_Validate_Digits();    	
    	if(false!==$this->getValidator('Digits')){
    		$this->removeValidator('Digits');
    	}    	
    	$this->addValidator($validate_digits);
    	return $this;
    }
    /**     
     * @return Core_Form_Element_Text
     */
    public function setValidateDate(){   	
    	$validate_date=new Core_Validate_Date(Zend_Date_Cities::City('Hanoi'));    	    	 
    	if(false!==$this->getValidator('Date')){
    		$this->removeValidator('Date');
    	}    	
    	$this->addValidator($validate_date);    	
    	return $this;
    } 
    /**
     * @var string $table_name
     * @var array $exclude     
     * @return Core_Form_Element_Text
     */
    public function setValidateDatabaseUnique($table_name,$exclude=null){
    	$db=new Core_Validate_Db_NoRecordExists(array(
    			                                   'table' => $table_name,
    			                                   'field' => $this->getName()
    	                                             )
    			                                );
    	if(null!==$exclude&&(is_string($exclude)||is_array($exclude))){
    		$db->setExclude($exclude);
    	}    	
    	if(false!==$this->getValidator('Db_NoRecordExists')){
    		$this->removeValidator('Db_NoRecordExists');
    	}
    	$this->addValidator($db);   	
    	return $this;
    }
    /**
     * @return Core_Form_Element_Text
     * @var integer
     */
    public function setMinIntValue($min){
    	$this->min_int_value=$min;
    	$validate_int=new Core_Validate_Int();
    	$validate_lessThan=new Core_Validate_LessThan($this->max_int_value);
    	$validate_greaterThan=new Core_Validate_GreaterThan($this->min_int_value);    	    	    	
    	if(false!==$this->getValidator('Int')){
    		$this->removeValidator('Int');    	
    	}
    	if(false!==$this->getValidator('LessThan')){
    		$this->removeValidator('LessThan');
    	}
    	if(false!==$this->getValidator('GreaterThan')){
    		$this->removeValidator('GreaterThan');
    	}
    	$this->addValidator($validate_int);
    	$this->addValidator($validate_greaterThan);
    	$this->addValidator($validate_lessThan);
    	return $this;
    }
    /**
     * @return Core_Form_Element_Text
     * @var integer
     */
    public function setMaxIntValue($max){
    	$this->max_int_value=$max;
    	$validate_int=new Core_Validate_Int();    	
    	$validate_lessThan=new Core_Validate_LessThan($this->max_int_value);
    	$validate_greaterThan=new Core_Validate_GreaterThan($this->min_int_value);    	    	    	
    	if(false!==$this->getValidator('Int')){
    		$this->removeValidator('Int');    	
    	}
    	if(false!==$this->getValidator('LessThan')){
    		$this->removeValidator('LessThan');
    	}
    	if(false!==$this->getValidator('GreaterThan')){
    		$this->removeValidator('GreaterThan');
    	}
    	$this->addValidator($validate_int);
    	$this->addValidator($validate_greaterThan);
    	$this->addValidator($validate_lessThan);
    	return $this;    	
    }  
    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param bool $isEmail
     * @return void
     */
    public function setIsEmail($isEmail){
        if(!is_bool($isEmail)){
            return ;
        }
        if($isEmail===TRUE){
            $this->addValidator('EmailAddress',  TRUE);
        }
        else{            
            $this->removeValidator('EmailAddress');
        }
    }
     
}

