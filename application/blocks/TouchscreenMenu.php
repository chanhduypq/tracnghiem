<?php
/*
* @author: huuthanh3108
* @date: May 5, 2011
* @company : http://dnict.vn
*/
class Block_TouchScreenMenu extends Zend_View_Helper_Abstract{
	
	public function touchScreenMenu(){
		if( Core::cache()->load('touchscreenmenu') === FALSE )
		{
			$rows = Core::single('Core/Menu')
								->select()
								->order('lft')
								->where('id !=1')
								->where('status = 1')
								->where('menutype = ?','touchscreen','STRING')
								->fetchAll();
// 			$sql="SELECT node.id,node.name, (COUNT(parent.name) - 1) AS depth
// 		FROM core_menu AS node
// 		CROSS JOIN core_menu AS parent		
// 		WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.status = 1 AND node.menutype = 'touchscreen'		
// 		GROUP BY node.id,node.name,node.lft
// 		ORDER BY node.lft";
// 			$rows = Core::db()->fetchAll($sql);
			Core::cache()->save($rows, 'touchscreenmenu');			
		}
		$rows = Core::cache()->load('touchscreenmenu');
		return $this->buildMenu($rows);
	}
	private  function buildMenu($menuData)
	{
		$xhtml=array();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$uri = $request->getPathInfo();
		if (count($menuData) > 0 ) {
			$xhtml[] ='<ul data-role="listview" data-theme="c" data-dividertheme="e" class="menu">'."\n";
			
			for ($i = 0; $i < count($menuData); $i++) {
				$row = $menuData[$i];
				if ($row['link'] == '#') {
					$xhtml[] ='<li data-role="list-divider">'.$row['name'].'</li>'."\n";
				}
				else{
					$is_active = (strrpos($row['link'], $uri) === false)?'':'data-theme="b" ';
					//rel="external"
					$arrTitle = explode('\n', $row['name']);
					//var_dump(count($arrTitle));
					$xhtml[] = '<li '.$is_active.'><a href="'.$this->view->baseUrl().$row['link'].'" data-dom-cache="false" rel="external">'
							//.$row['name']
							.'<h3>'.$arrTitle[0].'</h3>'
							.((count($arrTitle) > 1)?'<p><strong>'.$arrTitle[1].'</strong></p>':'')
							.'</a></li>'."\n";
				}
			}
			$xhtml[] = '</ul>'."\n";
		}
		return  join ( PHP_EOL, $xhtml );
	}
	function renderTree($tree, $currDepth = -1) {
		$currNode = array_shift($tree);
		$result = '';
		// Going down?
		if ($currNode['depth'] > $currDepth) {
			// Yes, prepend <ul>
			$result .= '<ul>';
		}
		// Going up?
		if ($currNode['depth'] < $currDepth) {
			// Yes, close n open <ul>
			$result .= str_repeat('</ul>', $currDepth - $currNode['depth']);
		}
		// Always add the node
		$result .= '<li>' . $currNode['name'] . '</li>';
		// Anything left?
		if (!empty($tree)) {
			// Yes, recurse
			$result .=  $this->renderTree($tree, $currNode['depth']);
		}
		else {
			// No, close remaining <ul>
			$result .= str_repeat('</ul>', $currNode['depth'] + 1);
		}
		return $result;
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}