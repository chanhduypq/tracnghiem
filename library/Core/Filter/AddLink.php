<?php
class Core_Filter_AddLink implements Zend_Filter_Interface{
	
	protected $_findWord;
	
	protected $_link;
	
	public function __construct($findWord,$link){
		$this->_findWord = $findWord;
		$this->_link = $link;
	}
	
	public function filter($value){
		$pattern = '#' . $this->_findWord .'#imsU';
		$replace = '<a href="' . $this->_link . '">' .$this->_findWord . '</a>';
		$valueFiltered = preg_replace($pattern,$replace,$value);

		return $valueFiltered;
		
	}
}