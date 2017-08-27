<?php
/**
* @file: BussinessDate.php
* @author: huuthanh3108@gmaill.com
* @date: 25-10-2012
* @company : http://dnict.vn
**/
class Core_Model_BussinessDate{
	static function IsNonWorkingDate($ngay){
		$db = Core::db();
		$nonewkd = new Zend_Session_Namespace('nonewkd');
		if(!isset($nonewkd->data)){
			$select = $db->select();
			$select->from('gen_nonworkingdates',array('*'));
			$nonewkd->data = $db->fetchAll($select);
		}
		//var_dump($nonewkd->data);
		foreach($nonewkd->data as $item){
			//echo ($ngay['mday']+$ngay['mon']*31) ."-". ($item['BDAY']+$item['BMON']*31).";";
			if($item['iscommon']==1 && $item['wday']==$ngay['wday']){
				return true;
			}else if($item['ismonth']==1 &&
					($ngay['mday']+$ngay['mon']*31 >= $item['bday']+$item['bmon']*31) &&
					($ngay['mday']+$ngay['mon']*31 <= $item['eday']+$item['emon']*31)){
				return true;
			}
		}
		return false;
	}
	static function IsNonWorkingWDate($wday){
		$db = Core::db();
		$select = $db->select();
		$select->from('gen_nonworkingdates',array('COUNT(*)'))
		->where('iscommon = 1')
		->where('wday = ?',$wday,'INTEGER');
		$r = $db->fetchOne($select);
		if($r > 0)return true;
		return false;
	}
	static function insertnonworking($ngay){
		$db = Core::db();
		$data = array(
				'bday'=>$ngay['mday'],
				'bmon'=>$ngay['mon'],
				'eday'=>$ngay['mday'],
				'emon'=>$ngay['mon'],
				'ismonth'=>1
		);
		$db->insert('gen_nonworkingdates',$data);
		self::updateSession();
	}
	static function deletenonworking($ngay){
		$db = Core::db();
		$db->delete('gen_nonworkingdates',"ismonth = 1
			AND ".($ngay['mday']+$ngay['mon']*31)." >= (bday+bmon*31) AND ".($ngay['mday']+$ngay['mon']*31)." <= (eday+emon*31)");
		self::updateSession();
	}
	static function insertnonworkingwday($wday){
		$db = Core::db();
		$data = array('wday'=>$wday,'iscommon'=>1);
		$db->insert('gen_nonworkingdates',$data);
		self::updateSession();
	}
	static function deletenonworkingwday($wday){
		$db = Core::db();
		$db->delete('gen_nonworkingdates',"iscommon=1 AND wday = ".$wday);
		self::updateSession();
	}
	static function updateSession(){
		$db = Core::db();
		$nonewkd = new Zend_Session_Namespace('nonewkd');
		//if(!isset($nonewkd->data)){
		$select = $db->select();
		$select->from('gen_nonworkingdates',array('*'));
		$nonewkd->data = $db->fetchAll($select);
		//}
	}
	/**
	 * Tra ve so gio bi tre (1 ngay co 8 gio)
	 */
	static function getTreHan($ngay,$hanxuly){
		if($hanxuly==0)return 0;
		$ngay = strtotime($ngay);
		$freedate = Core::session('freedate');
		$free = $freedate->free;
		$delay =$this->countdate($ngay,$free);
		return (int)($delay -($hanxuly*8));
	}
	/**
	 * Đếm ngày bị trễ từ ngày đến ngày hiện tại
	 * trả về: gio bị trễ
	 * Ngày làm 8 giờ
	 * Không tính thứ 7 chủ nhật
	 * @param $ngay int
	 * @param $arrngaynghilam lấy từ database (UTC date)
	 */
	static function countdate($ngay,$arrngaynghilam){
		$tre = 0;
		$curdate = time();
		if($ngay>$curdate){
			return 0;
		}
		//tao nagy bat dau
		$begindate = $ngay;
		//$begindate = strtotime($begindate['year']."-".$begindate['mon']."-".$begindate['mday']." 00:00:00");
		$enddate = $curdate;
		//$enddate = strtotime($enddate['year']."-".$enddate['mon']."-".$enddate['mday']." 00:00:00");
		$isbegin = true;
		while(true){
			//chekc ngay nghi
			if($this->IsNonWorkingDate(getdate($begindate))){
	
			}else{
				$hour = date("H",$begindate);
				if($hour>=8 && $hour<12){
					$tre++;
				}else if($hour>=13 && $hour<17){
					$tre++;
				}
			}
			$begindate += 3600;
			if($begindate>$enddate)break;
		}
		return $tre;
	}
	static function addDateAll($date,$value,$option = array()){
		$inc = 0;
		while(true){
			$date += 3600*24;
			$nocount = 1;
			if(Core_Model_BussinessDate::IsNonWorkingDate(getdate($date))){
				$nocount=0;
			}
			$inc += $nocount;
	
			if($inc>=$value)break;
		}
		if (is_array($option['none_process_wday'])) {
			$temp_date = getdate($date);
			if (in_array($temp_date['wday'], $option['none_process_wday'])) {
				if(!Core_Model_BussinessDate::IsNonWorkingDate(getdate($date))){
					self::addDateAll(&$date, 1,$option);
				}
					
			}
		}
		return $date;
	}
	
	static function addDate($date,$value,$option = array()){
		if($value > 0){
			$inc = 0;
			//Đếm ngày từ ngày đến ngày hiện tại
			$value = $value*8;
			while(true){
				$date += 3600;
				$nocount = 1;
				if(Core_Model_BussinessDate::IsNonWorkingDate(getdate($date))){
					$nocount=0;
				}
				$hour = date("H",$date);
				if($hour>=8 && $hour<12){
					$inc += $nocount;
				}else if($hour>=13 && $hour<17){
					$inc += $nocount;
				}
				if($inc>=$value)break;
			}
			if (is_array($option['none_process_wday'])) {
				$temp_date = getdate($date);
				if (in_array($temp_date['wday'], $option['none_process_wday'])) {
					if(!Core_Model_BussinessDate::IsNonWorkingDate(getdate($date))){
						self::addDate(&$date, 1,$option);
					}
	
				}
			}
			return $date;
		}else{
			return $date;
		}
			
	}
}