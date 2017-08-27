<?php 
class Core_View_Helper_HomeMenu extends Zend_View_Helper_Navigation_Menu
{
    public function homeMenu(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
        return $this;
    }
    protected function _renderMenu(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive)
    {
        // modified code here
    }

}