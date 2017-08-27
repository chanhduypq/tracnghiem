<?php

require_once 'Core/Form/Element/Xhtml.php';


class Core_Form_Element_Date extends Core_Form_Element_Xhtml
{
	const STYLE_SHOW_DATEPICKER_CLIP='clip';
	const STYLE_SHOW_DATEPICKER_BLIND='blind';
	const STYLE_SHOW_DATEPICKER_BOUNCE='bounce';
	const STYLE_SHOW_DATEPICKER_DROP='drop';
	const STYLE_SHOW_DATEPICKER_FOLD='fold';
	const STYLE_SHOW_DATEPICKER_SLIDE='slide';
	const STYLE_SHOW_DATEPICKER_FADEIN='fadeIn';
	const STYLE_SHOW_DATEPICKER_SLIDE_DOWN='slideDown';
	/**
	 * @var string
	 */
	private $style_show_datepicker='clip';
	/**
	 * @var string
	 *
	 */
	private $min_date_value=null;
	/**
	 * @var string
	 *
	 */
	private $max_date_value=null;	
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formText';   
    /**  
     * @author Trần Công Tuệ
     * @param string $format   
     * @return Core_Form_Element_Date
     */
    public function setValidateDate($format=null){   	
    	if($format!=null){
    		$validate_date=new Core_Validate_Date();
    		$validate_date->setFormat($format);
    	}    	
    	else {
    		$validate_date=new Core_Validate_Date(Zend_Date_Cities::City('Hanoi'));    		
    	}
    	if(false!==$this->getValidator('Date')){
    		$this->removeValidator('Date');
    	}
    	$this->addValidator($validate_date);   	    	
    	return $this;
    }    
    /**
     * @author Trần Công Tuệ   
     * @return Core_Form_Element_Date
     * @param string $min
     */
    public function setValidateMinDateValue($min){    		
    	$this->min_date_value=$min;    	
    	$this->setValidateDate();
    	return $this;
    }
    /**
     * @author Trần Công Tuệ   
     * @return Core_Form_Element_Date
     * @param string $max
     */
    public function setValidateMaxDateValue($max){
    	$this->max_date_value=$max;    	
    	$this->setValidateDate();
    	return $this;	
    }   
    /**
     * @author Trần Công Tuệ   
     * @return string
     */
    public function  getMinDateValue(){
    	return $this->min_date_value;
    }
    /**
     * @author Trần Công Tuệ   
     * @return string
     */
    public function  getMaxDateValue(){
    	return $this->max_date_value;    
    }
    /**
     * @author Trần Công Tuệ   
     * @return string
     */
    public function  getStyleShowDatepicker(){
    	return $this->style_show_datepicker;
    }
    /**
     * @author Trần Công Tuệ   
     * @param string
     * @return Core_Form_Element_Date
     */
    public function  setStyleShowDatepicker($style_show_datepicker){
    	$this->style_show_datepicker=$style_show_datepicker;
    	return $this;
    }
     
}

