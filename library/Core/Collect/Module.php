<?php
/**
* @file: Module.php
* @author: huuthanh3108@gmaill.com
* @date: 21-09-2012
* @company : http://dnict.vn
**/
class Core_Collect_Module implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$model = Core::single('Core/Module');
		$select = $model->select(array('id','name'));
		if(isset($params['is_active'])){
			$select->where('is_active = ?',$params['is_active'],'INTEGER');
		}
		return $select->fetchPairs();
	}

	/**
	 *
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getName($id,$option = array())
	{
		return Core::single('Core/Module')->getNameById($id);
	}
}