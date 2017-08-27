<?php
/**
* @file: Categories.php
* @author: huuthanh3108@gmaill.com
* @date: 11-10-2012
* @company : http://dnict.vn
**/
class News_Model_Collect_Categories implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($filters = array(),$option = array())
	{
		$table = Core::single('News/Categories');

		if (isset($option['not_id_branch'])) {
			$row = $table->select(array('lft','rgt'))->where('id = ?',$option['not_id_branch'],'INTEGER')->fetchRow();
			$filters[] = array(
					'field'       => 'nc.lft < '.$row->lft,
					'value'       => 'nc.lft > '.$row->rgt,
					'operator'    => 'OR'
			);	
		}
		$select  = $table->select(array('id','level AS depth','title'))->setIntegrityCheck(false);
		$select->join(array('parent'=>'news_categories'), 'nc.lft BETWEEN parent.lft AND parent.rgt AND parent.id = 1',array())
				->addFilters($filters)
				->order('nc.lft')
		;
		//echo $select->__toString();exit;
		$rows = $select->fetchAll();
		$result = array();
		for ($i = 0; $i < count($rows); $i++) {
			$result[$rows[$i]['id']] = str_repeat("--", $rows[$i]['depth']).$rows[$i]['title'];
		}
		return $result;
	}

	/**
	 *
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getName($id,$option = array())
	{
		return Core::single('News/Categories')->getNameById($id);
	}
}