<?php
/**
 * 
 * 
 * @author: huuthanh
 * @date: Apr 30, 2011
 * @version
 * @
 */
class Core_Form_Element_CKEditor extends Zend_Form_Element_Textarea{
 
public function __construct($spec, $options = null)
{
    parent::__construct($spec, $options);

       //grab a reference to the view rendering the form element
       $view = $this->getView();

       //include scripts and initialize the ckeditor
       
       $view->headScript()->appendFile($view->baseUrl('/editor/ckeditor/ckeditor.js'),'text/javascript');
       $view->headScript()->appendFile($view->baseUrl('/editor/ckeditor/adapters/jquery.js'),'text/javascript');
       $view->headScript()->appendFile($view->baseUrl('/editor/ckeditor/config.js'),'text/javascript');
       //give the textarea a class name that ckeditor recognises
       $this->setAttrib('class', 'jquery_ckeditor');
       $this->setValue($view->cmsReplaceString($this->getValue()));
  }
}