<?php
/** 
 * @author Trần Công Tuệ
 */
class Core_View_Helper_FormSearch extends Zend_View_Helper_Abstract{
	/**
	 * @author Trần Công Tuệ
	 * @param string $url	 
	 * @param string $mau
	 * @param Zend_View_Interface $view
	 * @param string $id
	 * @return string $html
	 */
	public function formSearch($view,$url,$mau,$id=null){
		if($view==null||(!$view instanceof Zend_View_Interface)){
			return ;
		}
		if($url==null||!is_string($url)||trim($url)==""){
			return ;
		}
		if($mau==null||!is_string($mau)||trim($mau)==""){
			return ;
		}?>	    		
		<form class="search"
						action="<?php echo $url;?>"
						<?php if($id!=null){echo "id='$id'";}?> method="post">
			<link href="<?php echo $view->baseUrl('/css/form_search/'.$mau.'/'.$mau.'.css');?>" media="screen" rel="stylesheet" type="text/css" />					
						<input name="search_text" value="<?php echo $view->search_text;?>" type="text" size="20" placeholder="Tìm kiếm mặt hàng"/>							
						<input type="submit" value="Tìm"/>
		</form>
	<?php 	
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}
