<?php

require_once 'Zend/Form/Element.php';

abstract class Core_Form_Element_Xhtml extends Zend_Form_Element {

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param bool $isUnique
     * @param string $excludeField
     * @return void
     */
    public function setUnique($isUnique, $excludeField = NULL) 
    {
        if (!is_bool($isUnique)) {
            return;
        }
        if ($isUnique === TRUE) {
            if (!is_string($excludeField)) {
                return;
            }
            $this->addValidator('Db_NoRecordExists', false, array('table' => 'user',
                'field' => 'email',
                'messages' => array(
                    'recordFound' => 'Email này đã tồn tại rồi'
                ),
                'exclude' => array('field' => $excludeField, 'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null))
                    )
            );
        } else {
            $this->removeValidator('Db_NoRecordExists');
        }
    }

}
