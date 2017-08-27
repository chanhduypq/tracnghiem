<?php
/**
* @file: module.php
* @author: huuthanh3108@gmaill.com
* @date: 24-08-2012
* @company : http://dnict.vn
**/
class System_Form_Module extends Core_Form{
	public function __construct($options = array())
	{
		$default = array(
				'id' => 'frmModuleCreate',
				'name' => 'frmModuleCreate',
				'class' => 'zend_form'
		);
		if (null !== $options || !count($options)) {
			$default = array_merge($default, $options);
		}		
		$this->setTranslator(Core::translate('Core')->getAdapter());
		parent::__construct($default);
	}
	public function init()
	{	
		$this->addElement('text', 'name', array(
				'required' => true,
				'label'    => 'Tên Module',
				'class' => 'input-text required',
			    'validators' => array('NotEmpty')
		));
		$this->addElement('text', 'package', array(
				'required' => true,
				'label'    => 'Package',
				'class' => 'input-text required',
				'validators' => array('NotEmpty')
		));
		$this->addElement('text', 'code', array(
				'required' => true,
				'label'    => 'Code',
				'class' => 'input-text required',
				'validators' => array('NotEmpty')
		));
		$this->addElement('text', 'version', array(
				'required' => true,
				'label'    => 'Version',
				'class' => 'input-text required',
				'validators' => array('NotEmpty')
		));		
		$this->addElement('checkbox', 'is_active', array(				
				'label'    => 'Sử dụng',
				'class' => 'input-checkbox'
		));
		$this->addElement('hidden', 'id');	
						  
	}
}