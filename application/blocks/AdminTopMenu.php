<?php
/*
* @author: huuthanh3108
* @date: May 5, 2011
* @company : http://dnict.vn
*/
class Block_AdminTopMenu extends Zend_View_Helper_Abstract{
	public $view;
	public function adminTopMenu(){
		if( Core::cache()->load('admintopmenu') === FALSE ){
			$rows = Core::single('Core/Menu')
								->select()
								->order('lft')
								->where('id !=1')
								->where('status = 1')
								->where('menutype = ?','adminmenu','STRING')
								->fetchAll();			
			Core::cache()->save($this->buildMenuUL_LI($rows), 'admintopmenu');			
		}
		return Core::cache()->load('admintopmenu');
	}
	private  function buildMenuUL_LI($menuData)
	{
		$result='<ul class="menu" id="menu">';	
		for ($i = 0; $i < count($menuData); $i++) {
			$menu = $menuData[$i];
			$class = ($menu["class"]!="")?"class='".$menu["class"]."'":"";
			//$kclass_li = ($menu["level"]==2)?'class="node"':'';
			if($menuData[$i+1]["level"] > $menu["level"]){
				$html = "<li class='node'>\n"
						."<a ".$class." href='".$menu["link"]."'>".$menu["name"]."</a>\n"
						."<ul>\n";
				$result .= $html;
			}else{
				$html = "<li>\n"
						."<a ".$class." href='".$menu["link"]."'>".$menu["name"]."</a>\n"
						."</li>\n";
				if($i==count($menuData)-1){
					for($j=1;$j<=$menuData[$i]["level"] - 2;$j++){
						$html .= "</ul>\n</li>\n";
					}
				}else{
					for($j=1;$j<=$menuData[$i]["level"] - $menuData[$i+1]["level"];$j++){
						$html .= "</ul>\n</li>\n";
					}
				}
				$result .= $html;
			}
		}
		return $result ."</ul>\n";
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}