<?php
/**
* @file: Menu.php
* @author: huuthanh3108@gmaill.com
* @date: 01-09-2012
* @company : http://dnict.vn
**/
class Core_Collect_Coquan implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$root_id = 1;
		if(isset($params['id_root'])){
			$root_id = $params['id_root'];
		}
		$sql = 'SELECT node.id,node.name, node."level" AS depth
				FROM base_coquan AS node,
				        base_coquan AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt AND parent."id" = '.$root_id.'
				ORDER BY node.lft';
		$rows = Core::db()->fetchAll($sql);
		$result = array();
		for ($i = 0; $i < count($rows); $i++) {
			$result[$rows[$i]['id']] = str_repeat("--", $rows[$i]['depth']).$rows[$i]['name'];
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
		if (isset($option['code'])) {
			return Core::single('Base/Coquan')->getNameByCode($id);
		}
		else{
			return Core::single('Base/Coquan')->getNameById($id);
		}
		
	}
}