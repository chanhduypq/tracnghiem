<?php

require_once 'Core/Form/Element/Multi.php';


class Core_Form_Element_Select extends Core_Form_Element_Multi
{
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelect';
}
