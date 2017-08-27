<?php
/**
* @file: User.php
* @author: huuthanh3108@gmaill.com
* @date: 04-10-2012
* @company : http://dnict.vn
**/
class Core_Collect_User implements Core_Collect_Interface
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
		$sql = 'SELECT cu.id,cu.fullname
				FROM base_coquan AS node,
				        base_coquan AS parent,core_users AS cu			
				WHERE cu.id_coquan = node.id AND node.lft BETWEEN parent.lft AND parent.rgt AND parent."id" = '.$root_id.'
				ORDER BY node.lft,cu.fullname';
		return Core::db()->fetchPairs($sql);		
	}

	/**
	 *
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getName($id,$option = array())
	{	
		return Core::single('Core/User')->getFullnameById($id);
	}
}