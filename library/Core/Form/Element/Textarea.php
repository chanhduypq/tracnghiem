<?php

require_once 'Core/Form/Element/Xhtml.php';

class Core_Form_Element_Textarea extends Core_Form_Element_Xhtml {

    /**
     * @var integer
     *
     */
    private $min_string_length = 1;

    /**
     * @var integer
     *
     */
    private $max_string_length = 1000;

    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formTextarea';

    /**
     * @return Core_Form_Element_Textarea
     * @var integer
     */
    public function setMinStringLength($min) {
        $this->min_string_length = $min;
        $validate_string_length = new Core_Validate_StringLength();
        $validate_string_length->setMax($this->max_string_length)->setMin($this->min_string_length);
        if (false !== $this->getValidator('StringLength')) {
            $this->removeValidator('StringLength');
        }
        $this->addValidator($validate_string_length);
        return $this;
    }

    /**
     * @return Core_Form_Element_Textarea
     * @var integer
     */
    public function setMaxStringLength($max) {
        $this->max_string_length = $max;
        $validate_string_length = new Core_Validate_StringLength();
        $validate_string_length->setMax($this->max_string_length)->setMin($this->min_string_length);
        if (false !== $this->getValidator('StringLength')) {
            $this->removeValidator('StringLength');
        }
        $this->addValidator($validate_string_length);
        return $this;
    }

    /**
     * Load default decorators
     *
     * @return Core_Form_Element_Textarea
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('ViewHelper')
                    ->addDecorator('File')
                    ->addDecorator('Errors')
                    ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                    ->addDecorator('HtmlTag', array(
                        'tag' => 'dd',
                        'id' => array('callback' => array(get_class($this), 'resolveElementId'))
                    ))
                    ->addDecorator('Label', array('tag' => 'dt'));
        }
        return $this;
    }

}
