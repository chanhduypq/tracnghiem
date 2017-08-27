<?php
/**
* @file: Content.php
* @author: huuthanh3108@gmaill.com
* @date: 12-10-2012
* @company : http://dnict.vn
**/
class News_Form_Content extends Zend_Form{
	protected $_id;
	public function init()
	{
		$this->setTranslator(Core::translate('Core')->getAdapter());
		$itemsSelect = News_Model_Collect_Categories::collect();
		$id_cat = new Zend_Form_Element_Select('id_cat');
		$id_cat->setLabel('Chủ đề')
		->setRequired(true)
		->setAttribs ( array('class'=>'required'))
		->setMultiOptions(array(''=>'-Chủ đề-')+$itemsSelect);
		
		$title = new Zend_Form_Element_Text ( 'title' );
		$title->setRequired ( true )
		->setLabel ( 'Tiêu đề' )
		->setAttribs ( array('size'=>50,'class'=>'required','maxlength'=>225) )
		->addFilter ( 'StringTrim' )
		->addValidator ( 'StringLength', false, array ('min'=>3,'max'=>225 ) );
		
		$alias = new Zend_Form_Element_Text ( 'alias' );
		$alias->setLabel ( 'Alias' )
		->setAttribs ( array('size'=>50,'maxlength'=>225) )
		->addFilter ( 'StringTrim' )
		->addValidator ( 'StringLength', false, array ('max'=>225 ) );
		
		
		$introtext = new Core_Form_Element_CKEditor('introtext' );
		$introtext->setLabel ( 'Trích dẫn' )				
				->setRequired(true)
				->addValidator('NotEmpty', false)
				->addFilter(new Core_Filter_Quota());
		
		$fulltext = new Core_Form_Element_CKEditor('fulltext' );
		$fulltext->setLabel ( 'Nội dung' )				
				->setRequired(true)
				->addValidator('NotEmpty', false)
				->addFilter(new Core_Filter_Quota());
		$state = new Zend_Form_Element_Checkbox ( 'state' );
		$state->setLabel ( 'Trạng thái' )->setValue(1);

		$created_by_alias = new Zend_Form_Element_Text ( 'created_by_alias' );
		$created_by_alias->setLabel ( 'Người soạn' )
				->setAttribs ( array('size'=>20,'readonly'=>'true') );
		
		$publish_up = new Zend_Form_Element_Text ( 'publish_up' );
		$publish_up->setLabel ( 'Ngày Up' )
					->setAttribs ( array('size'=>20,'maxlength'=>12,'class'=>'input-date') )
					->addFilter ( 'StringTrim' )
					->addValidator('Date', false,Core_Locale::getDateFormat())
					->addValidator ( 'StringLength', false, array ('max'=>12 ) );
		
		$publish_down = new Zend_Form_Element_Text ( 'publish_down' );
		$publish_down->setLabel ( 'Ngày Down' )
					->setAttribs ( array('size'=>20,'maxlength'=>12,'class'=>'input-date') )
					->addFilter ( 'StringTrim' )
					->addValidator('Date', false,Core_Locale::getDateFormat())
					->addValidator ( 'StringLength', false, array ('max'=>12 ) );
		$orders = new Zend_Form_Element_Text ( 'orders' );
		$orders->setLabel ( 'Thứ tự' )
				->setAttribs ( array('size'=>10,'maxlength'=>12) )
				->setValue(99)
				->addFilter ( 'StringTrim' )
				->addValidator ( 'Int', false );		
		$hits = new Zend_Form_Element_Text ( 'hits' );
		$hits->setLabel ( 'Hits' )
				->setAttribs ( array('size'=>10,'maxlength'=>12) )
				->setValue(0)
				->addFilter ( 'StringTrim' )
				->addValidator ( 'Int', false );
		$access = new Zend_Form_Element_Select('access');
		$access->setLabel('Quyền')
					->setRequired(true)
					->setAttribs ( array('class'=>'required'))
					->setMultiOptions(array('0'=>'Không cần đăng nhập','1'=>'Bắt buộc phải đăng nhập'));

		$id = new Zend_Form_Element_Hidden ( 'id' );
		$created_by = new Zend_Form_Element_Hidden ( 'created_by' );
		$this->addElements ( array ($id, $id_cat, $title,$alias,$introtext,$fulltext,$publish_up,$state,$publish_down,$orders,$hits,$access,$created_by,$created_by_alias) );
		// do custom rendering of the form
	}	
}