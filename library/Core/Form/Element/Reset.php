<?php

require_once 'Core/Form/Element/Submit.php';


class Core_Form_Element_Reset extends Core_Form_Element_Submit
{
    /**
     * Use formReset view helper by default
     * @var string
     */
    public $helper = 'formReset';
}
