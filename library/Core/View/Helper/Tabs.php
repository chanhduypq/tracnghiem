<?php
/** 
 * @author Trần Công Tuệ
 */
class Core_View_Helper_Tabs extends Zend_View_Helper_Abstract{
	/**
	 * @author Trần Công Tuệ
	 * @param array $tabs	 
	 * @param Zend_View_Interface $view	 
	 * @param integer $active_tab_index
	 * @return string $html
	 */
	public function tabs($view,array $tabs,$active_tab_index){
		if($view==null||(!$view instanceof Zend_View_Interface)){
			return ;
		}
		if(!is_array($tabs)||count($tabs)==0){
			return ;
		}
		if(!is_numeric($active_tab_index)){
			return ;
		}
		?>
		<script type="text/javascript" src="<?php echo $view->baseUrl('/js/ui/jquery.ui.tabs.js');?>"></script>
		<script type="text/javascript">
		jQuery(function($) {
				$( "#tabs" ).tabs({
					beforeLoad: function( event, ui ) {
						ui.jqXHR.error(function() {
							ui.panel.html(
								"Vi sự cố đường truyền internet nên hệ thống không tải được nội dung này. " +
								"Xin vui lòng thử lại sau." );
						});
					},			
					heightStyle:"fill",
					active:<?php echo $active_tab_index;?>
				});
		});
		</script>
		<div id="tabs">
			<ul>
			    <?php 
			    foreach ($tabs as $key=>$value){?>
			           <li><a href="<?php echo $view->baseUrl($value);?>"><?php echo $key;?></a></li>
			    <?php
		        } 
			    ?>						
			</ul>	
		</div>	
	<?php 	
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}
