<?php
class Core_View_Helper_FormTreeView extends Zend_View_Helper_FormElement
{
	protected $_info = null;

	protected function _renderBranch($nodes,$type)
	{
		$output = '<ul id="'.$this->_info['id'].'"'.$this->_htmlAttribs($this->_info['attribs']).'>' ."\n";
		$id = $this->_info['id'];

			foreach ($nodes as $node_id=>$node) {
				$node_control_id = $id.'-'.$node_id;
				$node_control_name = $this->_info['name'];
				if($type == 'radio'){
					if ('[]' == substr($node_control_name, -2)) {				    	
				    	$node_control_name  = substr($node_control_name, 0, strlen($node_control_name)-2);  
				    }
				}
			        
				if (is_array($this->_info['value'])) {
					$checked = in_array($node_id, $this->_info['value'])?'checked="checked"':'';
				} else {
					$checked = '';
				}

				$output .= '<li>'."\n";					
					$output .= ($node_id !=1)?'<input '.$checked.' value="'.$node_id.'" id="'.$node_control_id.'" name="'.$node_control_name.'" type="'.$type.'" /> ':'';
					$output .= '<label for="'.$node_control_id.'">'.$node['title'].'</label>';
					if (!empty($node['children'])) {
						$output	.= $this->_renderBranch($node['children'],$type);
					}
				$output .= '</li>'."\n";
			}
		$output .= '</ul>'."\n";

		return $output;
	}

	public function formTreeView ($name, $value = null, $attribs = null, $options = null)
	{
		$this->_info = $this->_getInfo($name, $value, $attribs, $options);
		// Normalize type tag	    
        if (array_key_exists('radio', $this->_info['attribs'])) {
            $type = 'radio';
        }else{
        	$type = 'checkbox';
        }
		return $this->_renderBranch($this->_info['options'],$type);
	}
}