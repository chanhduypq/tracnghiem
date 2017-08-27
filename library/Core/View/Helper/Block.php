<?php
/**
  *@file:  Theme.php
  *@encoding:UTF-8
  *@auth: huuthanh3108
  *@date: Jan 6, 2012
  *@company: http://dnict.vn
 **/
class Core_View_Helper_Block extends Zend_View_Helper_Abstract
{
	public function block($data,$option = null)
	{
		if(empty($data['class_name'])){
			return '';
		}
		/**
		 * add duong dan helper
		 * 
		 */
		if(!empty($data['class_name'])){
			$this->view->addHelperPath( APPLICATION_PATH . DS.'blocks'.DS.'block_'. $data['class_name'], 'Block_' );
		}
		$preendHtml = '';
		$addendHtml = '';
		$data['params'] = unserialize($data['params']);
		$data['params']['content'] = $data['content'];
		$act = explode(':', $data['action']);
		if(empty($data['params']['notemplate'])){
			$preendHtml = $this->preendHtml(array('show_title'=>$data['show_title'],'title'=>$data['name']));
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
				
			} elseif (strtolower ( $data ['class_name'] ) == 'blockhtml') {			
				$this->view->blockHtml()->render ( $data ['content'] );			
			} else {
				/*
				 * dung helper cua trong block
				 */
				//var_dump($data ['class_name']);							
 				echo $this->view->$data ['class_name'] ( $data ['params'] );
			}
		} catch ( Exception $e ) {
			Core::log()->log($e->__toString(), Zend_Log::CRIT);			 
			 //Core_System_Common::writeLog($e->__toString());
		}
		echo $addendHtml;
	}
	private function preendHtml($params=array(),$option = null){
		$xhtml = "";
		if ($option==null) {
			$title = ($params['show_title'] == 1)?$params['title']:'';
			$xhtml .="<!-- BEGIN BLOCK -->\n"
				   ."<div class=\"box themed_box\">\n"
		           ."\t<h2 class=\"box-header\">$title</h2>\n"
		           ."\t\t<div class=\"box-content\">\n"
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
					."\t\t</div>"
					//."\t\t\t<div class=\"clear\"></div>"
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