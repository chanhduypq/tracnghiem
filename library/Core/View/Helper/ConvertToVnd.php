<?php
/**
 *
 * @author huuthanh3108
 * @version 
 */
class Core_View_Helper_ConvertToVnd extends Zend_View_Helper_Abstract
{
		public function convertToVnd($value = 0,$donvi = ' đồng',$option = null)
		{
			return  number_format($value,0,',','.').$donvi;
		}
}
