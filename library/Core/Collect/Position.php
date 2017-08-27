<?php
/**
* @file: Position.php
* @author: huuthanh3108@gmaill.com
* @date: 08-11-2012
* @company : http://dnict.vn
**/
class Core_Collect_Position implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
		
		$config = new Zend_Config_Xml(APPLICATION_PATH.DS.'configs'.DS.'themes.xml','production');
		$rows = $config->positions->position->toArray();
		$returnArray = array();
		for($i=0,$n=count($rows);$i<$n;$i++){
			$returnArray[$rows[$i]] = $rows[$i];
		}
		return $returnArray;	
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