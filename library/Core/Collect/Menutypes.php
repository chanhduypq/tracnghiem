<?php
/**
* @file: Actions.php
* @author: huuthanh3108@gmaill.com
* @date: 01-09-2012
* @company : http://dnict.vn
**/
class Core_Collect_Menutypes implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$model = Core::single('Core/Menutypes');
		$select = $model->select(array('alias','name'));
		if(isset($params['status'])){
			$select->where('status = ?',$params['status'],'INTEGER');
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
		return Core::single('Core/Menutypes')->getNameById($id);
	}
}