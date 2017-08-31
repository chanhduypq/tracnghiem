<?php

/**
 * Person title helper
 *
 */
class Core_View_Helper_GetTopErrorMessage extends Zend_View_Helper_Abstract {

    /**
     * Get top error message
     * 
     * @return String
     */
    public function getTopErrorMessage(Zend_Form_Element $element, $tag = 'span') {
        $getMessage = $element->getMessages();
        $getError = $element->getErrors();
        $begin = '';
        $end = '';
        $oldClass = $element->getAttrib('class');
        if ($tag != '' && $tag != 'br') {
            $begin = '<' . $tag . ' class="' . $oldClass . ' formNote error" id="error-' . $element->getAttrib('id') . '">';
            $end = '</' . $tag . '>';
        } elseif ($tag != '' && $tag == 'br') {
            $begin = '<br /><span class="' . $oldClass . ' formNote error" id="error-' . $element->getAttrib('id') . '">';
            $end = '</span>';
        }
        if ($getMessage != null && $getError != null) {
            return $begin . $this->view->translate()->__($getMessage[$getError[0]]) . $end;
        } else
            return '';
    }

}
