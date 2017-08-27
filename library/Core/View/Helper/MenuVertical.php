<?php
/**
 * @author Trần Công Tuệ
 */
class Core_View_Helper_MenuVertical extends Zend_View_Helper_Abstract{
	/**
	 * @author Trần Công Tuệ
	 * @param array $texts
	 * @param array $hrefs	 
	 * @param string $mau
	 * @param Zend_View_Interface $view
	 * @param string $href_active
	 * @return string $html
	 */	
	public function menuVertical($view,$mau,$texts,$hrefs,$href_active,$function_onclicks=null){		
	    if($mau==null||!is_string($mau)||trim($mau)==""){
			return ;
		}
		if($view==null||(!$view instanceof  Zend_View_Interface)){
			return ;
		}
		if(!is_array($texts)||!is_array($hrefs)||count($texts)==0||count($hrefs)==0){
			return ;
		}
		if(count($texts)!=count($hrefs)){
			return ;
		}
		?>
	    
		<div class="<?php echo $mau;?>-v">
		    <link href="<?php echo $view->baseUrl('/css/menu/vertical/'.$mau.'/'.$mau.'.css');?>" media="screen" rel="stylesheet" type="text/css" />    
			<ul>			    			    
			    <?php				
				for($i=0,$n=count($texts);$i<$n;$i++){	
					?>
				<li
					<?php if($hrefs[$i]==$href_active) echo 'class="active"';?>>
					<a
					href="<?php echo $hrefs[$i];?>"<?php if($function_onclicks!=null&&$function_onclicks[$i]!=null){echo ' onclick="'.$function_onclicks[$i].';"';}?>>
						<span><?php echo $texts[$i];?></span>
				</a>
				</li>
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