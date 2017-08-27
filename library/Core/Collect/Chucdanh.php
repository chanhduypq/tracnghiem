<?php
/**
* @file: Chucdanh.php
* @author: huuthanh3108@gmaill.com
* @date: 06-09-2012
* @company : http://dnict.vn
**/
class Core_Collect_Chucdanh implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$model = Core::single('Base/Chucdanh');
		$select = $model->select(array('id','name'));
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
		return Core::single('Base/Chucdanh')->getNameById($id);
	}
}