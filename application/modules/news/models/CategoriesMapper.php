<?php
/**
* @file: CategoriesMapper.php
* @author: huuthanh3108@gmaill.com
* @date: 11-10-2012
* @company : http://dnict.vn
**/
class News_Model_CategoriesMapper{
	protected $_name = 'news_categories';
	protected $_countItems = 0;
	public function create($formData,$option = array()){		
		try {
			if ($formData ['alias'] == null || $formData ['alias'] == '') {
				$formData ['alias'] = $formData ['title'];
			}
			$filter = new Zend_Filter ();
			$multiFilter = $filter->addFilter ( new Zend_Filter_StringToLower ( array ('encoding' => 'UTF-8' ) ) )
						->addFilter ( new Zend_Filter_StringTrim () )
						->addFilter ( new Zend_Filter_Alnum ( true ) )
						//->addFilter ( new Zend_Filter_PregReplace ( array ('match' => '#\s+#', 'replace' => '-' ) ) )
						->addFilter ( new Zend_Filter_Word_SeparatorToDash () )
						->addFilter ( new Core_Filter_RemoveCircumflex () );
			$formData ['alias'] = $multiFilter->filter($formData ['alias']);
			$model =  new Core_Model_Nested(array('table'=>$this->_name));
			$data = array(
				'title' => $formData ['title'],
				'alias' => $formData ['alias'],
				'published' => $formData ['published'],
				'access' => $formData ['access'],
				'params' => $formData ['params']				
			);
			foreach ($data as $key => $value) {
				if ($value == null || $value == '') {
					unset($data[$key]);
				}
			}
			//var_dump($data);
			//var_dump($formData['id_parent']);
			//exit;
			$model->insertNode($data,$formData['id_parent']);
			return true;
		} catch (Exception $e) {
			Core::message()->addError($e->__toString());
			return false;
		}
	}
	public function update($formData,$option = array()){
		try {
			if ($formData ['alias'] == null || $formData ['alias'] == '') {
				$formData ['alias'] = $formData ['title'];
			}
			$filter = new Zend_Filter ();
			$multiFilter = $filter->addFilter ( new Zend_Filter_StringToLower ( array ('encoding' => 'UTF-8' ) ) )
			->addFilter ( new Zend_Filter_StringTrim () )
			->addFilter ( new Zend_Filter_Alnum ( true ) )
			->addFilter ( new Zend_Filter_PregReplace ( array ('match' => '#\s+#', 'replace' => '-' ) ) )
			->addFilter ( new Zend_Filter_Word_SeparatorToDash () )
			->addFilter ( new Core_Filter_RemoveCircumflex () );
			$formData ['alias'] = $multiFilter->filter($formData ['alias']);
			$model =  new Core_Model_Nested(array('table'=>$this->_name));
			$data = array(
				'title' => $formData ['title'],
				'alias' => $formData ['alias'],
				'published' => $formData ['published'],
				'access' => $formData ['access'],
				'params' => $formData ['params']	
			);
			//var_dump($data);exit;
			$model->updateNode($data,$formData['id'],$formData['id_parent']);
			return true;
		} catch (Exception $e) {
			Core::message()->addError($e->__toString());
			return false;
		}
	}
	public function read($id,$option = array()){
		$model = Core::single('News/Categories');
		$row =  $model->fetchRow(array('id = ?'=>$id));
		if (null !== $row) {
			$row = $row->toArray();
		}
		return $row;
	}
	public function delete($cid,$option = array()){
		try {
			$model =  new Core_Model_Nested(array('table'=>$this->_name));
			if (is_array($cid)) {
				for ($i = 0; $i < count($cid); $i++) {
					$model->removeNode($cid[$i]);
				}				
			}else{
				$model->removeNode($cid);
			}
			return true;
		} catch (Exception $e) {
			Core::message()->addError($e->__toString());
			return false;
		}
	}
	public function moveUp($id,$option = array()){
		try {
			$model =  new Core_Model_Nested(array('table'=>$this->_name));
			$model->moveUp($id);
			return true;
		} catch (Exception $e) {
			Core::message()->addError($e->__toString());
			return false;
		}
	}
	
	public function moveDown($id,$option = array()){
		try {
			$model =  new Core_Model_Nested(array('table'=>$this->_name));
			$model->moveDown($id);
			return true;
		} catch (Exception $e) {
			Core::message()->addError($e->__toString());
			return false;
		}
	}
	public function listItems($filters = array(),$order,$limit, $start,$option = array()){
		$select = Core::single('News/Categories')->select();
		$filters[] = array(
					'field'       => 'id',
					'value'       => '1',
					'operator'    => 'NE'
		);
		$select->addFilters($filters)
				->limit($limit, $start)
				->order('lft');
		//echo $select->__toString();
		/** quan trong set query de lay count**/
		$rows = $select->fetchAll();
		$this->_countItems = $select->count();
		return $rows;
	}
	public function getCountItems(){
		return $this->_countItems;
	}
	
}