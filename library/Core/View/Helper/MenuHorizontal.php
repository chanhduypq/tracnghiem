<?php
/**
 * @author Trần Công Tuệ
 */
class Core_View_Helper_MenuHorizontal extends Zend_View_Helper_Abstract{
	/**
	 * @author Trần Công Tuệ
	 * @param array $texts
	 * @param array $hrefs	 
	 * @param string $mau
	 * @param Zend_View_Interface $view
	 * @param string $href_active
	 * @return string $html
	 */	
	public function menuHorizontal($view,$mau,$texts,$hrefs,$href_active,$function_onclicks=null){		 
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
	    
		<div class="<?php echo $mau;?>-h">
		<link href="<?php echo $view->baseUrl('/css/menu/horizontal/'.$mau.'/'.$mau.'.css');?>" media="screen" rel="stylesheet" type="text/css" />
		<?php if($mau=='mau2'){?>
		      <script type="text/javascript" src="<?php echo $view->baseUrl('/js/menu/horizontal/'.$mau.'/gjs.js'); ?>"></script>
		      <nav class="main-menu">
		          <ul class="three columns primary-nav">			    			    
					    <?php				
						for($i=0,$n=count($texts);$i<$n;$i++){	
							?>
						<li class="nav-item<?php if($i==$n-1) echo ' last-child';?>"
							<?php if($hrefs[$i]==$href_active) {}?>>
							<a data-state='<?php echo $texts[$i];?>'
							href="<?php echo $hrefs[$i];?>"<?php if($function_onclicks!=null&&$function_onclicks[$i]!=null){echo ' onclick="'.$function_onclicks[$i].';"';}?>>
								<span><?php echo $texts[$i];?></span>
						</a>
						</li>
						<?php
						}
						?>
						<li
							style="display: none; left: 157px; background-color: rgb(135, 101, 214); width: 59px;"
							class="main-menu-underline nav-item out"></li>
		</ul>
		      </nav>
		<?php 
		}
		else if($mau=='mau1'){
		?>		 
			<ul>			    			    
			    <?php				
				for($i=0,$n=count($texts);$i<$n;$i++){	
					?>
				<li
					<?php if($hrefs[$i]==$href_active) echo ' class="active"';?>>
					<a
					href="<?php echo $hrefs[$i];?>"<?php if($function_onclicks!=null&&$function_onclicks[$i]!=null){echo ' onclick="'.$function_onclicks[$i].';"';}?>>
						<span><?php echo $texts[$i];?></span>
				</a>
				</li>
				<?php
				}
				?>						
			</ul>
			<?php
		} 
			?>
		</div>
	<?php		
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}