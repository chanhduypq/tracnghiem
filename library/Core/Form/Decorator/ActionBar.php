<?php
/**
 * Core
 *
 * This file is part of Core.
 *
 * Core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Core.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Core
 * @package     Core_Form
 * @subpackage  Core_Form_Decorator
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Form
 * @subpackage  Core_Form_Decorator
 * @author      Core Core Team <core@axiscommerce.com>
 */
class Core_Form_Decorator_ActionBar extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $form = $this->getElement();
        $translator = $form->getTranslator();
        $view       = $form->getView();

        if (null === $form->getActionBar()) {
            return $content;
        }

        $items = array();
        foreach ($form->getActionBar()->getElements() as $item) {
            $item->setView($view)->setTranslator($translator);
            $items[] = $item->render();
        }
        $elementContent = implode('', $items);

        $decorator = new Zend_Form_Decorator_HtmlTag(array('tag' => 'li', 'class' => 'actions'));
        $elementContent = $decorator->render($elementContent);

        return $content . $elementContent;
    }
}