<?php
/**
* @file: Categories.php
* @author: huuthanh3108@gmaill.com
* @date: 11-10-2012
* @company : http://dnict.vn
**/
class News_Form_Categories extends Zend_Form{
	protected $_id;
	public function init()
	{
		$itemsSelect = array();
		if ((int)$this->_id > 0 ) {
			$itemsSelect = News_Model_Collect_Categories::collect(array(),array('not_id_branch'=>$this->_id));
		}else{
			$itemsSelect = News_Model_Collect_Categories::collect();
		}
		$id_parent = new Zend_Form_Element_Select('id_parent');
		$id_parent->setLabel('Chủ đề cha')
				->setRequired(true)
				->setAttribs ( array('class'=>'required'))
				->setMultiOptions(array(''=>'-Chủ đề cha-')+$itemsSelect);
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
		
		$published = new Zend_Form_Element_Checkbox ( 'published' );
		$published->setLabel ( 'Xuất bản' )->setValue(1);
		$access = new Zend_Form_Element_Select('access');
		$access->setLabel('Quyền')
				->setRequired(true)
				->setAttribs ( array('class'=>'required'))
				->setMultiOptions(array('0'=>'Không cần đăng nhập','1'=>'Bắt buộc phải đăng nhập'));		
		
		$id = new Zend_Form_Element_Hidden ( 'id' );
		$this->addElements ( array ($id, $id_parent, $title,$alias,$published,$access) );
		// do custom rendering of the form
	}
	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @param field_type $_id
	 */
	public function setId($_id) {
		$this->_id = $_id;
	}

}