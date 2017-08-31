<?php

/**
 * 
 * @category    Core
 * @package     Core_Form
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_Form_Form extends Zend_Form {

    /**
     * @var Zend_Form_Element_Text
     */
    public $textbox_require_string;

    /**
     * @var Zend_Form_Element_Text
     */
    public $textbox_require_digits;

    /**
     * @var Zend_Form_Element_Hidden
     */
    public $hidden;

    /**
     * @var Zend_Form_Element_Textarea
     */
    public $textarea;

    public function init() {
        
    }

}
