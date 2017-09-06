<?php

/**
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_View_Helper_Add extends Zend_View_Helper_Abstract {

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $label
     * @param string $url
     * @return string $html
     */
    public function add($label, $url) 
    {
        ?>
        <table width="100%" style="margin-bottom: 30px;">
            <tr>           
                <td style="width: 80%;"></td>
                <td style="text-align: right;width: 20%;">                    
                    <input onclick="window.location = '<?php echo $url; ?>';" type="button" value="<?php echo $label; ?>" class="button" />
                </td>
            </tr>
        </table>        
        <?php
    }

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

}
