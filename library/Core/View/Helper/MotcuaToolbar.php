<?php
/*
* @author: huuthanh3108
* @date: May 4, 2011
* @company : http://dnict.vn
* @params1 array('name'=>'Ten buton','action'=>'url','class'=>'class buton','script','script chay trong onclick')
* @params2 array('title'=>'Ten title ben trai','class'=>'style title')
* @return html toolbar
*/
class Core_View_Helper_MotcuaToolbar extends Zend_View_Helper_Abstract
{
		/**
	     * Get top error message
	     * 
	     * @return String
	     */
		public function motcuaToolbar($arrtoolbar,$arrtitle=array(),$option = null)
		{
			$html = '<div id="toolbar-box">
						<div class="t">
							<div class="t">
								<div class="t"></div>
							</div>
						</div>
					<div class="m">
						<div class="toolbar" id="toolbar">';
			$html .= self::drawButton($arrtoolbar);
			$html .='</div>';		
			$html .= self::drawTitle($arrtitle);
			$html .='<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>';
			return $html;		
		}
	public function drawButton($data){
		$html = "";
		$html .= "<table class=\"toolbar\"><tr>";		
		for($i=0,$n=count($data);$i<$n;$i++){						
			$item = &$data[$i];		
			$html.="<td class=\"button\" id=\"toolbar-".$item['class']."\">";			
			if($item['script']==""){
				$html.="<a target=\"".$item['target']."\" href=\"".$item['action']."\" class=\"toolbar\"><span class=\"icon-32-".$item['class']."\" title=\"".$item['name']."\"></span>".$item['name']."</a>";
			}else{
				$html.="<a href=\"#\" class=\"toolbar\" onclick=\"".$item['script']."\"><span class=\"icon-32-".$item['class']."\" title=\"".$item['name']."\"></span>".$item['name']."</a>";
			}			
			$html.="</td>";
		}
		$html .= "</tr></table>";
		return $html;
	}
	public function drawTitle($data){
		$html='';
		if(is_string($data) && $data !=''){
			$html .='<div class="header">'.$data.'</div>';
		}
		elseif(is_array($data) && $data!=null) 
		{
			if(empty($data['class'])){
				$data['class']='';
			}
			if($data['subtitle'] != null){
				$html .='<div class="header icon-48-'.$data['class'].'">'.$data['title'].'<span style="font-size:80%;color:red;"> ['.$data['subtitle'].']</span></div>';	
			}
			else{
				$html .='<div class="header icon-48-'.$data['class'].'">'.$data['title'].'</div>';
			}
		}
		else{
			$html='';	
		}
		return $html;
	}
}