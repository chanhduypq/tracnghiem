<?php
/**
 * @author: huuthanh3108
 * @date: Aug 25, 2011
 * @company : http://dnict.vn
**/
class Core_View_Helper_ConvertSohoso extends Zend_View_Helper_Abstract
{
		/**
	     * 
	     * 
	     * @return String
	     */
		public function convertSohoso($value = '',$option = null)
		{			
			return substr($value, 0,4).'.'.substr($value, 4,2).'.'.substr($value, 6);
		}
}