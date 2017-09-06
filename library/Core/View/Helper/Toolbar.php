<?php

/**
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_View_Helper_Toolbar extends Zend_View_Helper_Abstract 
{

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param array $buttons
     * @return string $html
     */
    public function toolbar($buttons) 
    {
        ?>
        <div class="span12" style='margin-bottom: 20px;text-align: right;'>
            <?php 
            foreach ($buttons as $button){?>
            <input onclick="<?php echo str_replace('"', "'", $button['onclick']);?>" type="button" value="<?php echo $button['label'];?>" class="button" />
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
