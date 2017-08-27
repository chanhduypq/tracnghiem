<?php
/**
* @file: ModuleMapper.php
* @author: huuthanh3108@gmaill.com
* @date: 25-08-2012
* @company : http://dnict.vn
**/
class System_Model_ModuleMapper {
	protected $_countItems = 0;
	public function create($formData, $option = array()) {
		$data = array (
				'package' => $formData ['package'],
				'code' => $formData ['code'],
				'name' => $formData ['name'],
				'version' => $formData ['version'],
				'is_active' => $formData ['is_active'] 
		);
		foreach ( $data as $key => $value ) {
			if ($value === null || $value === '') {
				unset ( $data [$key] );
			}
		}
		return Core::single ( 'Core/Module' )->insert ( $data );
	}
	public function update($formData, $option = array()) {
		$data = array (
				'package' => $formData ['package'],
				'code' => $formData ['code'],
				'name' => $formData ['name'],
				'version' => $formData ['version'],
				'is_active' => $formData ['is_active'] 
		);
		// var_dump($formData);exit;
		return Core::single ( 'Core/Module' )->update ( $data, array (
				'id = ?' => $formData ['id'] 
		) );
	}
	public function read($id, $option = array()) {
		$row = Core::single ( 'Core/Module' )->fetchRow ( array (
				'id = ?' => $id 
		) );
		if (null === $row) {
			return array ();
		}
		return $row->toArray ();
	}
	public function getAll() {
		$modules = Core::single ( 'Core/Module' )->fetchAll ( 'is_active=1' )->toArray ();
		return $modules;
	}
	public function delete($id, $option = array()) {
		if (is_array ( $id )) {
			return Core::single ( 'Core/Module' )->delete ( array (
					'id IN (?)' => $id 
			) );
		} else {
			return Core::single ( 'Core/Module' )->delete ( array (
					'id = ?' => $id 
			) );
		}
	}
	public function listItems($filter, $order, $limit, $start, $option = array()) {
		$select = Core::single ( 'Core/Module' )->select ();
		if ($filter != null) {
			$select->addFilters ( $filter );
		}
		$select->limit ( $limit, $start );
		$select->order ( $order );
// 		echo $select->__toString ();
		/**
		 * quan trong set query de lay count*
		 */
		$rows = $select->fetchAll ();
		$this->_countItems = $select->count ();
		return $rows;
	}
	public function getCountItems() {
		return $this->_countItems;
	}
}