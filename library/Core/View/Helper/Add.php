<?php

/**
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_View_Helper_Add extends Zend_View_Helper_Abstract 
{

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $label
     * @param string $url
     * @param Zend_View_Interface $view
     * @return string $html
     */
    public function add($view, $label,$url) 
    {
        ?>
        <table width="100%" style="-moz-border-radius: 10px 10px 10px 10px;background: #D0E3F5;">
            <tr>           
                <td style="width: 80%;">&nbsp;</td>
                <td onclick="window.location = '<?php echo $url; ?>';" onmouseover="this.style.cursor = 'pointer';" style="color: blue;text-align: right;width: 20%;"><?php echo $label;?></td>
            </tr>
        </table>
        <?php

    }

    public function setView(Zend_View_Interface $view) 
    {
        $this->view = $view;
    }

}
