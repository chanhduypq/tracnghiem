<?php
/**
  *@file:  Theme.php
  *@encoding:UTF-8
  *@auth: huuthanh3108
  *@date: Jan 6, 2012
  *@company: http://dnict.vn
 **/
class Core_View_Helper_Theme extends Zend_View_Helper_Abstract
{
	public function theme($data,$option = null)
	{
		if(empty($data['action'])){
			return '';
		}
		/**
		 * add duong dan helper
		 * 
		 */
		if(!empty($data['block'])){
			$this->view->addHelperPath ( BLOCK_PATH . DS . $data['block'], 'Block_' );
		}
		$preendHtml = '';
		$addendHtml = '';
		$data['params'] = unserialize($data['params']);
		$act = explode(':', $data['action']);
		if(empty($data['params']['notemplate'])){
			$preendHtml = $this->preendHtml(array('showtitle'=>$data['showtitle'],'title'=>$data['title']));
			$addendHtml = $this->addendHtml();
		}
		else{
			$preendHtml = '';
			$addendHtml = '';
		}
		echo $preendHtml;
		try {			
			if (is_array ( $act ) && count ( $act ) == 3) {
				/*
				 * neu la action
				 */				
				echo $this->view->action ( $act [2], $act [1], $act [0], $data ['params'] );
				
			} elseif (strtolower ( $data ['action'] ) == 'blockhtml') {			
				$this->view->blockHtml ()->render ( $data ['content'] );			
			} else {
				/*
				 * dung helper cua trong block
				 */							
				echo $this->view->$data ['action'] ( $data ['params'] );				
			}
		} catch ( Exception $e ) {			 
			 Core_System_Common::writeLog($e->__toString());
		}
		echo $addendHtml;
	}
	private function preendHtml($params=array(),$option = null){
		$xhtml = "";
		if ($option==null) {
			$title = ($params['showtitle'] == 1)?'<h3>'.$params['title'].'</h3>':'';
			$xhtml .="<!-- BEGIN BLOCK -->\n"
				   ."<div class=\"sidebar_bottom_left\" style=\"padding-top: 5px;\">\n"
		           ."\t<div class=\"block block_content\">\n"
		           ."\t\t<div class=\"block_header\"></div>\n"
		           ."\t\t\t".$title."\n"
		           ."\t\t\t<div class=\"block_center\">\n"
				   ;
		}else{			
			$xhtml = $params['xhtml'];
		}		
		return $xhtml;
	}
	
	private function addendHtml($params=array(),$option = null){
		$xhtml = "";
		if ($option==null) {
			$xhtml .="\t\t\t</div>\n" 
					."\t\t<div class=\"block_bottom\"></div>"
					."\t</div>\n"
					."</div>\n"
					."<!-- END BLOCK -->\n";
		}else{			
			$xhtml = $params['xhtml'];
		}		
		return $xhtml;
	
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}