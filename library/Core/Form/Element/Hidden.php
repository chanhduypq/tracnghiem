<?php

require_once 'Core/Form/Element/Xhtml.php';

class Core_Form_Element_Hidden extends Core_Form_Element_Xhtml
{
    /**
     * Use formHidden view helper by default
     * @var string
     */
    public $helper = 'formHidden';   
    private $isPrimary=true;
    public function getIsPrimary() {
        return $this->isPrimary;
    }

    public function setIsPrimary($isPrimary) {
        $this->isPrimary = $isPrimary;
    }


    
}
