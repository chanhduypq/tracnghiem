<?php
/*
* @author: huuthanh3108
* @date: Apr 27, 2011
* @company : http://dnict.vn
*/
class Core_View_Helper_Superfish extends Zend_View_Helper_Navigation_Menu
{
    public function superfish(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
        return $this;
    }
	public function htmlify(Zend_Navigation_Page $page)
	{	

	    // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }
  
	    // does page have subpages?
	    if ($page->count()) {
	        $sub_indicator = '<span class="sf-sub-indicator"> »</span>';
	        $attribs['class'] .= ' sf-with-ul';
	    } else {
	        $sub_indicator = '';
	    }
	
	    return '<' . $element . $this->_htmlAttribs($attribs) . '>'
	         . $this->view->escape($label)
	         . $sub_indicator
	         . '';
	}
}