<?php
/**
 * PHP Class
 *
 * LICENSE
 *
 * Processing Nested Set Model
 *
 * @category   	PHP classes
 * @package    	Database
 * @copyright  	Copyright (c) 2007-2012 Professional World company(http://www.worldprovn.com/)  
 *									   & Vietnam Zend Framework community (http://www.zend.vn/)
 * @author		KhanhPham (khanhpham@worldprovn.com)
 * @license    	GNU General Public License Version 2 or later (the "GPL")
 * @version     v1.0 5:46:57 PM Feb 20, 2012
 */

class Core_Db_Table_Nested extends Core_Db_Table_Abstract{
	protected $_name = null;
	protected $_id = null;
	protected $_db = null;
	public function __construct($params = array(), $options = array()) {
		$this->_name = $params['table'];
		$this->_db = $this->getDefaultAdapter();
	}
	
 	/**
	 * Get info of node
	 * 
	 * @author KhanhPham
	 *
	 * @param  int Left value of node
	 * 
	 * @return array Node info.
	 * 
	 */
	protected function getNodeByLeft($left){
		$select			= $this->_db->select()
								->from($this->_name)
								->where("lft = ?",$left);
		$row 	= $this->_db->fetchRow($select);
		return $row;
	}
	
	/**
	 * Processing move node to before position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @param int ID of node which you want move current node to before postion
	 * 
	 * @return Change tree structure
	 * 
	 */
	protected function moveBefore($brother_id){
		
		$infoMoveNode = $this->getNodeInfo($this->_id);
		
		$lftMoveNode = $infoMoveNode['lft'];
		$rgtMoveNode = $infoMoveNode['rgt'];
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);		
		
		$bind		= array("rgt"	=>  new Zend_db_Expr('rgt -  ' . $rgtMoveNode),
							"lft" 	=>   new Zend_db_Expr('lft -  ' . $lftMoveNode )
							);
		$where		= 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"	=>new Zend_db_Expr('rgt -  ' . $widthMoveNode)  );
		$where		= 'rgt > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("lft"	=>  new Zend_db_Expr('lft -  ' . $widthMoveNode));
		$where		= 'lft > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
				
		$infoBrotherNode = $this->getNodeInfo($brother_id);
		$lftBrotherNode = $infoBrotherNode['lft'];
		
		$bind		= array("lft"	=>  new Zend_db_Expr('lft +  ' . $widthMoveNode));
		$where		= 'lft >= ' . $lftBrotherNode.' AND rgt>0';
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"	=>  new Zend_db_Expr('rgt +  ' . $widthMoveNode));
		$where		= 'rgt >= ' . $lftBrotherNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$infoParentNode 	= $this->getNodeInfo($this->_parent);
		$levelMoveNode 		= $infoMoveNode['level'];
		$levelParentNode	= $infoParentNode['level'];
		$newLevelMoveNode  = $levelParentNode + 1;
		$bind		= array("level"	=>  new Zend_db_Expr('level -  ' . $levelMoveNode. ' + ' . $newLevelMoveNode));
		$where		= 'rgt <= 0';
		$this->_db->update($this->_name,$bind,$where);
		
		$newParent 	= $infoParentNode['id'];
		$newLeft 	= $infoBrotherNode['lft'];
		$newRight 	= $infoBrotherNode['lft'] + $widthMoveNode - 1;
		$bind		= array("id_parent"	=> $newParent,
							"lft"		=> $newLeft,
							"rgt"		=> $newRight);
		$where		= 'id = '.$this->_id;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("lft"		=> new Zend_db_Expr("lft + ".$newLeft),
							"rgt"		=> new Zend_db_Expr("rgt + ".$newRight));
		$where		= 'rgt <0';
		$this->_db->update($this->_name,$bind,$where);
	}
	
	/**
	 * Processing move node to after position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @param int ID of node which you want move current node to after postion
	 * 
	 * @return Change tree structure
	 * 
	 */
	protected function movetAfter($brother_id){

		$infoMoveNode = $this->getNodeInfo($this->_id);
		
		$lftMoveNode = $infoMoveNode['lft'];
		$rgtMoveNode = $infoMoveNode['rgt'];
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);
		
		$bind		= array("rgt"		=> new Zend_db_Expr("rgt - ".$rgtMoveNode),
							"lft"		=> new Zend_db_Expr("lft - ".$lftMoveNode));
		$where		= 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr("rgt - ".$widthMoveNode));
		$where		= 'rgt > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("lft"		=> new Zend_db_Expr("lft - ".$widthMoveNode));
		$where		= 'lft > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$infoBrotherNode = $this->getNodeInfo($brother_id);
		$rgtBrotherNode = $infoBrotherNode['rgt'];
				
		$bind		= array("lft"		=> new Zend_db_Expr("lft + ".$widthMoveNode));
		$where		= 'lft > ' . $rgtBrotherNode.' AND rgt>0';
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr("rgt + ".$widthMoveNode));
		$where		= 'rgt > ' . $rgtBrotherNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$infoParentNode = $this->getNodeInfo($this->_parent);
		$levelMoveNode 		= $infoMoveNode['level'];
		$levelParentNode	= $infoParentNode['level'];
		$newLevelMoveNode  = $levelParentNode + 1;
		
		$bind		= array("level"		=> new Zend_db_Expr('level - '. $levelMoveNode . ' + ' . $newLevelMoveNode));
		$where		= 'rgt <= 0 ';
		$this->_db->update($this->_name,$bind,$where);
		
		$newParent 	= $infoParentNode['id'];
		$newLeft 	= $infoBrotherNode['rgt'] + 1;
		$newRight 	= $infoBrotherNode['rgt'] + $widthMoveNode;
		
		$bind		= array("id_parent"		=>$newParent,
							"lft"			=>$newLeft,
							"rgt"			=>$newRight );
		$where		= 'id = '.$this->_id;
		$this->_db->update($this->_name,$bind,$where);

		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + '. $newRight),
							"lft"		=> new Zend_db_Expr('lft + '.$newLeft));
		$where		= 'rgt < 0 ';
		$this->_db->update($this->_name,$bind,$where);
	}
	
	/**
	 * Processing move node to left position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function moveLeft(){
		$infoMoveNode = $this->getNodeInfo($this->_id);
		
		$lftMoveNode = $infoMoveNode['lft'];
		$rgtMoveNode = $infoMoveNode['rgt'];
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt - '. $rgtMoveNode),
							"lft"		=> new Zend_db_Expr('lft - '.$lftMoveNode));
		$where		= 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt - '. $widthMoveNode));
		$where		= 'rgt > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft - '. $widthMoveNode));
		$where		= 'lft > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$infoParentNode = $this->getNodeInfo($this->_parent);
		$lftParentNode = $infoParentNode['lft'];
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft + '. $widthMoveNode));
		$where		= 'lft > ' . $lftParentNode.' AND rgt > 0 ';
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + '. $widthMoveNode));
		$where		= 'rgt > ' . $lftParentNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$levelMoveNode 		= $infoMoveNode['level'];
		$levelParentNode	= $infoParentNode['level'];
		$newLevelMoveNode  = $levelParentNode + 1;
		
		$bind		= array("level"		=> new Zend_db_Expr('level - '. $levelMoveNode . ' + ' . $newLevelMoveNode));
		$where		= 'rgt <= 0';
		$this->_db->update($this->_name,$bind,$where);
		
		$newParent 	= $infoParentNode['id'];
		$newLeft 	= $infoParentNode['lft'] + 1;
		$newRight 	= $infoParentNode['lft'] + $widthMoveNode;
		
		$bind		= array("id_parent"=>$newParent,
							"lft"	=>$newLeft,
							"rgt"	=>$newRight);
		$where		= 'id = ' . $this->_id;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + '. $newRight),
							"lft"		=> new Zend_db_Expr('lft + '.$newLeft));
		$where		= 'rgt < 0';
		$this->_db->update($this->_name,$bind,$where);
	}
	
	/**
	 * Processing move node to right position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function moveRight(){
		$infoMoveNode = $this->getNodeInfo($this->_id);
		
		$lftMoveNode = $infoMoveNode['lft'];
		$rgtMoveNode = $infoMoveNode['rgt'];
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt - '. $rgtMoveNode),
							"lft"		=> new Zend_db_Expr('lft - '.$lftMoveNode));
		$where		= 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt - '. $widthMoveNode));
		$where		= 'rgt > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft - '. $widthMoveNode));
		$where		= 'lft > ' . $rgtMoveNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$infoParentNode = $this->getNodeInfo($this->_parent);
		$rgtParentNode = $infoParentNode['rgt'];
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft + '. $widthMoveNode));
		$where		= 'lft >= ' . $rgtParentNode. ' AND rgt > 0';
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + '. $widthMoveNode));
		$where		= 'rgt >= ' . $rgtParentNode;
		$this->_db->update($this->_name,$bind,$where);
		
		$levelMoveNode 		= $infoMoveNode['level'];
		$levelParentNode	= $infoParentNode['level'];
		$newLevelMoveNode  = $levelParentNode + 1;
		
		$bind		= array("level" => new Zend_db_Expr('level - '. $levelMoveNode . ' + ' . $newLevelMoveNode));
		$where		= 'rgt <= 0';
		$this->_db->update($this->_name,$bind,$where);
		
		$newParent 	= $infoParentNode['id'];
		$newLeft 	= $infoParentNode['rgt'];
		$newRight 	= $infoParentNode['rgt'] + $widthMoveNode - 1;
		
		$bind		= array("id_parent"		=> $newParent,
							"lft"			=>$newLeft,
							"rgt"			=>$newRight);
		$where		= 'id = ' . $this->_id;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + '. $newRight),
							"lft"		=> new Zend_db_Expr('lft + '. $newLeft));
		$where		= 'rgt <0';
		$this->_db->update($this->_name,$bind,$where);
	}
	/**
	 * Insert a new node to right position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function insertRight(){
		$parentInfo =  $this->getNodeInfo($this->_parent);

        $parentRight = ($parentInfo['rgt'])?$parentInfo['rgt'] : 0;

		$bind		= array("lft"	=> new Zend_db_Expr('lft+2'));
		$where		= 'lft > '. $parentRight;
		$this->_db->update($this->_name,$bind,$where);
		
		
		$bind		= array("rgt"=> new Zend_db_Expr('rgt+2'));
		$where		= 'rgt >= '. $parentRight;
		$this->_db->update($this->_name,$bind,$where);

		$data = $this->_data;		
		$data['id_parent']	= $this->_parent;
		$data['lft'] 		= $parentRight;
		$data['rgt'] 		= $parentRight + 1;
		$data['level'] 		= $parentInfo['level'] + 1;
		
		$this->_db->insert($this->_name,$data);
	}
	
	/**
	 * Insert a new node to left position of other node
	 * 
	 * @author KhanhPham
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function insertLeft(){
		$parentInfo =  $this->getNodeInfo($this->_parent);
		$parentLeft = $parentInfo['lft'];
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft + 2'));
		$where		= 'lft >= '. $parentLeft;
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + 2'));
		$where		= 'rgt >= '. ($parentLeft + 1);
		$this->_db->update($this->_name,$bind,$where);
		
		$data = $this->_data;		
		$data['id_parent']	= $this->_parent;
		$data['lft'] 		= $parentLeft + 1;
		$data['rgt'] 		= $parentLeft + 2;
		$data['level'] 		= $parentInfo['level'] + 1;
		
		$this->_db->insert($this->_name,$data);
	}
	
	/**
	 * Insert a new node to after position of other node
	 * 
	 * @author KhanhPham
	 * 
	 * @param int ID of node which you want insert new node to after postion
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function insertAfter($brother_id){
		
		$parentInfo =  $this->getNodeInfo($this->_parent);		
		$brotherInfo =  $this->getNodeInfo($brother_id);		
		$bind		= array("lft"		=> new Zend_db_Expr('lft + 2'));
		$where		= 'lft > '. $brotherInfo['rgt'];
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + 2'));
		$where		= 'rgt > '. $brotherInfo['rgt'];
		$this->_db->update($this->_name,$bind,$where); 
		
		$data = $this->_data;		
		$data['id_parent']	= $this->_parent;
		$data['lft'] 		= $brotherInfo['rgt'] + 1;
		$data['rgt'] 		= $brotherInfo['rgt'] + 2;
		$data['level'] 		= $parentInfo['level'] + 1;
		
		$this->_db->insert($this->_name,$data);
	}
	
	/**
	 * Insert a new node to before position of other node
	 * 
	 * @author KhanhPham
	 * 
	 * @param int ID of node which you want insert new node to before postion
	 *
	 * @return Change tree structure
	 * 
	 */
	protected function insertBefore($brother_id){
		
		$parentInfo =  $this->getNodeInfo($this->_parent);		
		$brotherInfo =  $this->getNodeInfo($brother_id);		
		$bind		= array("lft"		=> new Zend_db_Expr('lft + 2'));
		$where		= 'lft >= '. $brotherInfo['lft'];
		$this->_db->update($this->_name,$bind,$where);
		
		$bind		= array("rgt"		=> new Zend_db_Expr('rgt + 2'));
		$where		= 'rgt >= '. ($brotherInfo['lft'] + 1);
		$this->_db->update($this->_name,$bind,$where);
		
		$data = $this->_data;		
		$data['id_parent']	= $this->_parent;
		$data['lft'] 		= $brotherInfo['lft'];
		$data['rgt'] 		= $brotherInfo['lft'] + 1;
		$data['level'] 		= $parentInfo['level'] + 1;
		$this->_db->insert($this->_name, $data);
	}
	
	/**
	 * Remove a branch of tree
	 * 
	 * @author KhanhPham
	 * 
	 * @return Change tree structure
	 *
	 */
	protected  function removeBranch(){
		$infoNodeRemove =  $this->getNodeInfo($this->_id);
	
		$rgtNodeRemove 		= $infoNodeRemove['rgt'];
		$lftNodeRemove 		= $infoNodeRemove['lft'];
		$widthNodeRemove 	= $this->widthNode($lftNodeRemove,$rgtNodeRemove);
		
		$where = ' lft BETWEEN ' . $lftNodeRemove . ' AND ' . $rgtNodeRemove;
		$this->_db->delete($this->_name, $where);
		
		$bind		= array("lft"		=> new Zend_db_Expr('lft - ' . $widthNodeRemove ));
		$where		= 'lft >= '. $rgtNodeRemove;
		$this->_db->update($this->_name,$bind,$where);

		$bind		= array("rgt"		=> new Zend_db_Expr('rgt - ' . $widthNodeRemove ));
		$where		= 'rgt >= '. $rgtNodeRemove;
		$this->_db->update($this->_name,$bind,$where);
	}
	
	/**
	 * Remove an one of tree
	 * 
	 * @author KhanhPham
	 * 
	 * @return Change tree structure
	 *
	 */
	protected function removeOne(){
		$nodeInfo = $this->getNodeInfo($this->_id);
		$select = $this->_db->select()
						->from($this->_name)
						->where('id_parent = ?', $nodeInfo['id'], INTEGER)
						->order('lft ASC');
		$result	= $this->_db->fetchAll($select);
		foreach ($result as $k => $v){
			$childIds[] = $v['id'];
		}
		rsort($childIds);
		
		if(count($childIds)>0){
			foreach ($childIds as $key => $val){
				$id = $val;
				$parent = $nodeInfo['id_parent'];
				$options = array('position'=>'after','brother_id'=>$nodeInfo['id']);
				$this->moveNode($id, $parent, $options);
			}
			$this->removeNode($nodeInfo['id']);
		}
	}
}
