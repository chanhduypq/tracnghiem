<?php
/**
* @file: ContentMapper.php
* @author: huuthanh3108@gmaill.com
* @date: 12-10-2012
* @company : http://dnict.vn
**/
class News_Model_ContentMapper{	
	protected $_countItems = 0;
	public function create($formData,$option = array()){
		$date = new Core_Date();
		if ($formData ['alias'] == null || $formData ['alias'] == '') {
			$formData ['alias'] = $formData ['title'];
		}
		if ($formData ['publish_up'] != null || $formData ['publish_up'] != '') {
			$formData ['publish_up'] = $date->VntoSQLString($formData ['publish_up']);
		}
		if ($formData ['publish_down'] != null || $formData ['publish_down'] != '') {
			$formData ['publish_down'] = $date->VntoSQLString($formData ['publish_down']);
		}
		$filter = new Zend_Filter ();
		$multiFilter =$filter->addFilter ( new Zend_Filter_Alnum ( true ) )
				->addFilter ( new Zend_Filter_StringTrim () )
				->addFilter ( new Zend_Filter_Word_SeparatorToDash () )
				->addFilter ( new Core_Filter_RemoveCircumflex () )
				->addFilter ( new Zend_Filter_StringToLower ( array ('encoding' => 'UTF-8' ) ) )
		;
		$formData ['alias'] = $multiFilter->filter($formData ['alias']);
		$user = Core::getUser();
		$model = Core::single('News/Content');
		
		$data = array(
				'id_cat' => $formData ['id_cat'],
				'title' => $formData ['title'],
				'alias' => $formData ['alias'],
				'introtext' => $formData ['introtext'],
				'fulltext' => $formData ['fulltext'],
				'state' => $formData ['state'],
				'created' => $date->now()->toSQLString(),
				'created_by' => $user->id,
				'created_by_alias' => $user->fullname,
// 				'modified' => $formData ['modified'],
// 				'modified_by' => $formData ['modified_by'],
				'publish_up' => $formData ['publish_up'],
				'publish_down' => $formData ['publish_down'],
				'orders' => $formData ['orders'],
				'access' => $formData ['access'],
				'hits' => $formData ['hits'],
				'params' => $formData ['params']
		);
		foreach ($data as $key => $value) {
			if ($value == null || $value == '') {
				unset($data[$key]);
			}
		}
		//var_dump($data);exit;
		return $model->insert($data);
	}
	public function update($formData,$option = array()){
		$date = new Core_Date();
		if ($formData ['alias'] == null || $formData ['alias'] == '') {
			$formData ['alias'] = $formData ['title'];
		}
		if ($formData ['publish_up'] != null || $formData ['publish_up'] != '') {
			$formData ['publish_up'] = $date->VntoSQLString($formData ['publish_up']);
		}
		if ($formData ['publish_down'] != null || $formData ['publish_down'] != '') {
			$formData ['publish_down'] = $date->VntoSQLString($formData ['publish_down']);
		}
		$filter = new Zend_Filter ();
		$multiFilter =$filter->addFilter ( new Zend_Filter_Alnum ( true ) )
			->addFilter ( new Zend_Filter_StringTrim () )
			->addFilter ( new Zend_Filter_Word_SeparatorToDash () )
			->addFilter ( new Core_Filter_RemoveCircumflex () )
			->addFilter ( new Zend_Filter_StringToLower ( array ('encoding' => 'UTF-8' ) ) )
		;
		$formData ['alias'] = $multiFilter->filter($formData ['alias']);
		$user = Core::getUser();
		$model = Core::single('News/Content');
		
		$data = array(
				'id_cat' => $formData ['id_cat'],
				'title' => $formData ['title'],
				'alias' => $formData ['alias'],
				'introtext' => $formData ['introtext'],
				'fulltext' => $formData ['fulltext'],
				'state' => $formData ['state'],
				'modified' => $date->now()->toSQLString(),
				'modified_by' => $user->id,
				'publish_up' => $formData ['publish_up'],
				'publish_down' => $formData ['publish_down'],
				'orders' => $formData ['orders'],
				'access' => $formData ['access'],
				'hits' => $formData ['hits'],
				'params' => $formData ['params']
		);
		foreach ($data as $key => $value) {
			if ($value == null || $value == '') {
				unset($data[$key]);
			}
		}
		//var_dump($data);exit;
		return $model->update($data,array('id = ?'=>$formData['id']));
	}
	public function read($id,$option = array()){
		$model = Core::single('News/Content');
		$row =  $model->fetchRow(array('id = ?'=>$id));
		if (null !== $row) {
			$row = $row->toArray();
		}
		return $row;
	}
	public function delete($cid,$option = array()){
		return Core::single('News/Content')->delete(array('id IN (?)'=>$cid));
	}
	
	public function listItems($filters = array(),$order,$limit, $start,$option = array()){
		$select = Core::single('News/Content')->select(array('id','id_cat','title','state','created','created_by_alias','orders','access','hits'))
							->setIntegrityCheck(false)
							->addFilters($filters)
							->joinLeft('news_content_frontpage','nc.id = ncf.id_content',array(new Zend_Db_Expr('CASE WHEN ncf.id_content IS NULL THEN 0 ELSE 1 END AS frontpage')))
							->limit($limit, $start)
							->order('nc.created');
		//echo $select->__toString();
		//exit;
		/** quan trong set query de lay count**/
		$rows = $select->fetchAll();
		$this->_countItems = $select->count();
		return $rows;
	}
	public function getCountItems(){
		return $this->_countItems;
	}
	public function newIsFrontpage($option = array()){
		//$filters[] = array(); 
		$select = Core::single('News/Content')->select(array('id','title','introtext','created'))
							->setIntegrityCheck(false)
							->where('nc.state = 1')							
							->join('news_content_frontpage','nc.id = ncf.id_content',array())
							->limit(9)							
							->order('nc.created');		
		return $select->fetchAll();
	}

}