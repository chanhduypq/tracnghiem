<?php
/**
* @file: Actions.php
* @author: huuthanh3108@gmaill.com
* @date: 01-09-2012
* @company : http://dnict.vn
**/
class Core_Collect_Actions implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$model = Core::single('Core/Action');
		$select = $model->select()->setIntegrityCheck(false);
		$select->from('core_actions',array('id','page_title','page_subtitle'))
				->join('core_modules','ca.id_module = cm.id', array('module_name'=>'name'))
				->order(array('ca.id_module','ca.page_title'));
		
		$result = array();
		if(isset($params['is_active'])){
			$select->where('cm.is_active = ?',$params['is_active'],'INTEGER');
		}
		if(isset($params['notlayout'])){
			$select->where('ca.layout_type != ?',$params['notlayout'],'STRING');
		}
		if(isset($params['is_layout'])){
			$select->where('ca.layout_type = ?',$params['is_layout'],'STRING');
		}
		//echo $select->__toString();exit;
		$rows =$select->fetchAll();
		for ($i=0;$i < count($rows);$i++){			 
			$result[$rows[$i]['module_name']][$rows[$i]['id']] = $rows[$i]['page_title'].' - '.$rows[$i]['page_subtitle'];
		}
		//var_dump($result);exit;
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
		return Core::single('Core/Action')->getNameById($id);
	}
}