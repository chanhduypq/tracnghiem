<?php

require_once 'Core/Form/Element/Multi.php';

class Core_Form_Element_MultiCheckbox extends Core_Form_Element_Multi
{
    /**
     * Use formMultiCheckbox view helper by default
     * @var string
     */
    public $helper = 'formMultiCheckbox';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
