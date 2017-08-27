<?php
class Core_View_Helper_Toolbar extends Zend_View_Helper_HtmlElement
{
		/**
	     * Get top error message
	     * 
	     * @return String
	     */
		public function toolbar($arrtoolbar,$arrtitle=null,$option = null)
		{
			//$arrtitle = null;
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
			//var_dump($item);
			$html.="<td class=\"button\" >";			
			$html.= $this->drawLink($item['lable'], $item['url'],$item['attribs']);		
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
	public function drawLink($label, $url, $options=null, $params=null){
		$attribs = array();		
        if(isset($options['attribs']) && is_array($options['attribs'])) {
            $attribs = $options['attribs'];
        }

        //
        // The next following 4 conditions check for html attributes that the link might need
        //
        if(empty($options['noscript']) || $options['noscript'] == false) {
            $attribs['href'] = "#";
        } else {
            $attribs['href'] = $url;
        }

        if(!empty($options['title'])) {
            $attribs['title'] = $options['title'];
        }else{
        	$attribs['title'] = $label;
        }

        // class value is an array because the jQuery CSS selector
        // click event needs its own classname later on
        if(!isset($attribs['class'])) {
            $attribs['class'] = array();
        } elseif(is_string($attribs['class'])) {
            $attribs['class'] = explode(" ", $attribs['class']);
        }
        if(!empty($options['class'])) {
            $attribs['class'][] = $options['class'];
        }

        if(!empty($options['id'])) {
            $attribs['id'] = $options['id'];
        }
        /*
         * even javascript
         */
	    if(!empty($options['onclick'])) {
            $attribs['onclick'] = $options['onclick'];
        }        
	    if(!empty($options['rel'])) {
            $attribs['rel'] = $options['rel'];
        }
		if(!empty($options['target'])) {
            $attribs['target'] = $options['target'];
        }
        if(count($attribs['class']) > 0) {
            $attribs['class'] = implode(" ", $attribs['class']);
        } else {
            unset($attribs['class']);
        }
        //var_dump($attribs);
		$label = "<span class=\"icon-32-".$attribs['class']."\"></span>".$label;
        $html = '<a'
            . $this->_htmlAttribs($attribs)
            . '>'
            . $label
            . '</a>';
        return $html;
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}