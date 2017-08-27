<?php
/**
* @file: Layout.php
* @author: huuthanh3108@gmaill.com
* @date: 08-11-2012
* @company : http://dnict.vn
**/
class Core_Collect_Layout implements Core_Collect_Interface
{
	/**
	 *
	 * @static
	 * @return array
	 */
	public static function collect($params = array(),$option = array())
	{
	return array('frontend'=>'Front End','backend'=>'Back End','home'=>'Home','login'=>'Login');
	}

	/**
	 *
	 * @static
	 * @param int $id
	 * @return string
	 */
	public static function getName($id,$option = array())
	{
		$layout =  array('frontpage'=>'Front Page','backend'=>'Back End','touchscreen'=>'Touch Screen','login'=>'Login');
		return $layout($id);
	}
}