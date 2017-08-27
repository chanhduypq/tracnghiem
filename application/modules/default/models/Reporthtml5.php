<?php 
class Default_Model_Reporthtml5{
	public function getPupilsForReportBar(){
		$db=$this->getDB();
		$select=$db->select();
		$select
		->from('hoc_sinh',array('tuoi','count'=>'count(tuoi)'))
		->group('tuoi')
		;
		$items=array();
		$items=$db->fetchAll($select);
		return $items;
	}
	public function getMaxCountAge(){
		$db=$this->getDB();
		$select=$db->select();
		$select
		->from('hoc_sinh',array('count'=>'count(tuoi)'))
		->group('tuoi')
		->having('COUNT(tuoi)>= ALL (SELECT count(tuoi) FROM hoc_sinh GROUP BY tuoi)')
		;
		$row=$db->fetchRow($select);
		if($row!=null||count($row)>0){
			return $row['count'];
		}
		return null;
	}
	public function getMaxPupilAge(){
		$db=$this->getDB();
		$select=$db->select();
		$select
		->from('hoc_sinh',array('max'=>'max(tuoi)'))		
		;		
		$row=$db->fetchRow($select);
		if($row!=null||count($row)>0){
			return $row['max'];
		}
		return null;
	}
	public function getMinPupilAge(){
		$db=$this->getDB();
		$select=$db->select();
		$select
		->from('hoc_sinh',array('min'=>'min(tuoi)'))
		;
		$row=$db->fetchRow($select);
		if($row!=null||count($row)>0){
			return $row['min'];
		}
		return null;
	}
	private function getDB(){
		$db=Core_Db_Table::getDefaultAdapter();
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);
		return $db;
	}
}