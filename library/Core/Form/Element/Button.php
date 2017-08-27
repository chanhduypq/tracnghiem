<?php

require_once 'Core/Form/Element/Submit.php';

class Core_Form_Element_Button extends Core_Form_Element_Submit
{
    /**
     * Use formButton view helper by default
     * @var string
     */
    public $helper = 'formButton';
}
