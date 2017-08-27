<?php
/**
* @file: Activitypools.php
* @author: huuthanh3108@gmaill.com
* @date: 17-07-2012
* @company : http://dnict.vn
**/
class Core_Collect_Activitypools implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		$model = Core::single('CoreWorkflow/DbTable_ActivityPools');
		$select = $model->select(array('id_ap','alias','name'));
		$is_alias = false;
		$result = array();
		if(isset($params['ids'])){				
			$select->where('id_ap IN (?)',$params['ids'],'ARRAY');
		}elseif(isset($params['alias'])){
			$select->where('alias IN (?)',$params['alias'],'ARRAY');
			$is_alias = true;
		}
		if (isset($params['id_c'])) {
			$select->where('id_c = ?',$params['id_c'],'INTEGER');
		}
		$rows =$select->fetchAll(); 
		for ($i=0;$i < count($rows);$i++){
			if($is_alias == true){
				$result[$rows[$i]['alias']] = $rows[$i]['name'];
			}else{
				$result[$rows[$i]['id_ap']] = $rows[$i]['name'];
			}
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
		return Core::single('CoreWorkflow/DbTable_ActivityPools')->getNameById($id);
	}
}