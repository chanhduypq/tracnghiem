<?php
class Core_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract {
	
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		$module = $request->getParam ( 'module', 'default' );
		$controller = $request->getParam('controller','index');
		$action = $request->getParam('action','index');
		Core_Blocks::beforProcess($module, $controller, $action);
		$layout_type = $this->getLayoutType($module, $controller, $action);
		//var_dump($layout_type);
		if ($layout_type == null) {
			//$layout_type = 'frontpage';
			$layout_type = 'backend';
		}
		$layout = Zend_Layout::getMvcInstance();
		//$layoutPath = 'layouts/scripts/';
		//$option = array ('layout' => strtolower($layout_type), 'layoutPath' => $layoutPath );		
		$option = array ('layout' => strtolower($layout_type));
		$layout->setOptions($option);
		$view = $layout->getView();
		/* $CssAndJs = Core_Blocks::getAssets();
		for ($i = 0; $i < count($CssAndJs['cs']); $i++) {
			$view->headLink()->appendStylesheet($view->baseUrl().$CssAndJs['cs'][$i]);							
		}


		for ($i = 0; $i < count($CssAndJs['js']); $i++) {
			$view->headScript()->appendFile($view->baseUrl().$CssAndJs['js'][$i]);
		} */
		
			
	}	
	private function getLayoutType($module, $controller, $action){
            if($module=='default'){
                return 'index';
            }
            else if($module=='admin'){
                return 'admin';
            }
            else{
                return 'unknown';
            }
	}
}