<?php
class Core_View_Helper_Message
{
    private $_messages = array();

    public function __construct()
    {
        $this->_messages = Core_Message::getInstance()->get();
    }

    public function message()
    {
        return $this;
    }    
    public function __toString()
    {
        $result = '<dl id="system-message"><dt class="message">Thông báo:</dt>';
        if (count($this->_messages)) {          
            foreach ($this->_messages as $type => $messageArray) {
                $result .= "<dd class='message ".$type."'><ul>";                
                foreach ($messageArray as $message) {
                    $message = $this->view->escape($message);
                    $result .= "<li>{$message}</li>";
                }                
                $result .= "</ul></dd>";
            }
            $this->_messages = array();
        }
        $result .= '</dl>';
        return $result;
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}