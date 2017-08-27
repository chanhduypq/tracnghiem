<?php
/**
* @file: HosoMapper.php
* @author: huuthanh3108@gmaill.com
* @date: 25-10-2012
* @company : http://dnict.vn
**/
class Core_Model_HosoMapper
{
	protected $_countItems = 0;
	/**
	 * 
	 * @param string $name TEN HO SO
	 * @param integer $id_thumuc THAM CHIEU DEN TABLE core_hoso_thumuc
	 * @param integer $id_loaicongviec THAM CHIEU DEN TABLE core_loaicongviec
	 * @param datetime $date_start
	 * @param datetime $date_end
	 * @param integer $id_u_send ID NGUOI GUI
	 * @param integer $id_u_receive ID NGUOI NHAN
	 * @param string $noidung NOI DUNG XU LY
	 * @param float $hanxuly HAN XU LY
	 * @param integer $before
	 * @param integer $sms
	 * @param integer $email
	 * @return integer NEU THANH CONG THI RETURN VE ID HOSO CON NGUOC LAI THI GUI VE MA LOI
	 */
	public function create($name,$id_thumuc,$id_loaicongviec,$date_start,$date_end,$id_u_send,$id_u_receive,$noidung,$before,$hanxuly=0,$sms=0,$email=0){
		 //Lấy tên table dựa trên ngày bắt đầu
		 $db = Core::db();		 
		 try{
			$masoquytrinh = Core::single('Core/Loaicongviec')->getMasoquytrinhById($id_loaicongviec);
			if($masoquytrinh == false){
				// Neu khong thay thi return -2;
			    Core::message()->addError('Không tìm thấy mã quy trình.');			    
				return -2;
			}
			else{
				$data = array(
					"id_thumuc"=>$id_thumuc,
					"id_loaicongviec"=>$id_loaicongviec,
					"date_start"=>$date_start,
					"date_end"=>$date_end,
					"name"=>$name,
					"before"=>$before,
					"sms"=>$sms,
					"email"=>$email,
					"id_u_receive"=>$id_u_receive
				);
				$id_hoso = Core::single('Core/Hoso')->insert($data);				
	   			/*
	   			 * Insert thanh cong thi tao process
	   			 */
				if ($id_hoso == false) {
					Core::message()->addError('Không tạo được hồ sơ công việc.');
	   				return -3;
				}
				$id_pi = Wf_Model_Engine::CreateProcess($masoquytrinh,$id_hoso,$name,$id_u_send,$id_u_receive,$noidung,$hanxuly);
				if ((int)$id_pi > 0 ) {
					Core::single('Core/Hoso')->update(array('id_pi'=>$id_pi),array('id = ?'=>$id_hoso));
				}else{
					Core::single('Core/Hoso')->delete(array('id = ?'=>$id_hoso));
					Core::message()->addError('Không tạo mới được luồng xử lý # '.$id_pi);
					return -4;					
				}				
			}			
			return $id_hoso;
		 }catch(Exception $ex){
		     Core::message()->addError($ex->__toString());
		 	return -1;
		 }
	}
	public function listItems($params, $order, $limit, $start, array $option = null) {
		// lay Id class tu config
		//var_dump($option);exit;
		$db = Core::db();
		$id_classes = Core::config('bcol/config/classes');
		$whereClasses = '';
		if ($params['id_classes'] != null || $params['id_classes']!='') {
			$whereClasses = 'AND class1.id_c='.$params['id_classes'];
		}
		$select = $db->select()->from(array('chc'=>Core::getNameTable('core_hoso_congviec')),array('*'));		
		if ($option['CODE']=='OLD') {
			$select->join(array('wfitem'=>new Zend_Db_Expr('('.
										$db->select()
											->from(array('pl'=>Core::getNameTable('wf_processlogs')),array('id_u'=>'id_u_send','id_a'=>'id_a_begin','lastchange'=>'datesend','id_p','id_pi'))
											->join(array('t'=>new Zend_Db_Expr('('.
													$db->select()->from(array('temp1'=>Core::getNameTable('wf_processlogs')),array('id_pl','cntpl'=>new Zend_Db_Expr('(select count(*) from wf_processlogs temp where temp.id_pi = temp1.id_pi)')))
										.')')), 't.id_pl = pl.id_pl',array('cntpl'))
										
			.')')), 'chc.id_pi = wfitem.id_pi',array('id_a','lastchange'));
		}else{
			$select->join(array('wfitem'=>Core::getNameTable('wf_processitems')),'chc.id_pi = wfitem.id_pi',array('id_a','lastchange'));
		}
		$select->join(array('lhs'=>'core_loaicongviec'),'lhs.id = chc.id_loaicongviec',array())			   
			   ->join(array('wfp1'=>'wf_processes'),'wfp1.id_p = wfitem.id_p',array())
			   ->join(array('class1'=>'wf_classes'),'class1.id_c = wfp1.id_c '.$whereClasses,array('alias'))
			   ->where('wfitem.id_u = ?',Core::getUserId(),'INTEGER');
		$filter = array();
		//$filter[] = array('field' => 'id_u_create', 'value'=>Core::getUserId());
		if ($params['dot_id'] != null || $params['dot_id']!='') {			
			$select->where('bh.dot_id = ?',$params['dot_id'],'INTEGER');
		}
		if ($params['id_a'] != null || $params['id_a']!='') {
			
			$select->where('wfitem.id_a = ?',$params['id_a'],'INTEGER');
		}
		
		$select->limit ( $limit, $start );
		$select->order ( $order );
		 		//echo $select->__toString ();
		 	//exit;
		/**
		 * quan trong set query de lay count*
		*/
		$rows = $db->fetchAll ($select);
		$this->_countItems = $db->fetchOne($select->reset(Zend_Db_Select::COLUMNS)
		 							->reset(Zend_Db_Select::GROUP)
		 							->reset(Zend_Db_Select::LIMIT_COUNT)
		 							->reset(Zend_Db_Select::LIMIT_OFFSET)
		 							->reset(Zend_Db_Select::ORDER)
		 							->columns('COUNT(*)'));
		return $rows;
	}
	public function getCountItems() {
		return $this->_countItems;
	}
	public function delete($id_hscv,$id_u,array $option = null){
		$db = Core::db();
		//lấy id wf
		$currentprocess = Wf_Model_Engine::GetCurrentTransitionInfoByIdHscv($id_hscv);
		if($currentprocess["id_u_nc"]==$id_u){
			$att = new Attachment_Model_AttachmentMapper();
			$id_pls = $db->fetchCol($db->select()->from(Core::getNameTable('wf_processlogs'),array('id_pl'))->where('id_pi = ?',$currentprocess["id_pi"]));
			for ($i = 0; $i < count($id_pls); $i++) {
				$att->deleteFileById_ObjectAndId_Type($id_pls[$i], Core::config('attachment/type/logs'));
			}
			//xóa dòng lc wf
			$db->delete(Core::getNameTable('wf_processlogs'),array("id_pi = ?"=>$currentprocess["id_pi"]));
			//xoa wf
			$db->delete(Core::getNameTable('wf_processitems'),array("id_pi = ?"=>$currentprocess["id_pi"]));
			//xóa hscv
			$db->delete(Core::getNameTable('core_hoso_congviec'),array("id = ?"=>$id_hscv));
			return true;
		}else{
			return false;
		}		
	}
}