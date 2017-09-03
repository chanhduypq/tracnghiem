<?php

/**
 * @author Trần Công Tuệ <chanhduypq@gmail.com>
 */
class Core_View_Helper_Update extends Zend_View_Helper_Abstract 
{

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $url
     * @return string $html
     */
    public function update($url) 
    {
        ?>
        <div class="span3">&nbsp;</div>
        <div class="span6" align='center' style='padding-bottom:20px;color: red;margin-bottom: 20px;'>
            <div class="textHeader" style="-moz-border-radius: 10px 10px 10px 10px;background-color:#f2e8e8;">	  
                <div onclick="document.form.submit();" style="float: right;text-decoration: underline;color: blue;cursor: pointer;">Lưu</div>
                <div onclick="window.location = '<?php echo $url;?>';" style="float: right;margin-right: 10px;text-decoration: underline;color: blue;cursor: pointer;">Đóng</div>
                <div style="clear: both;"></div>
            </div>

        </div>
        <div class="span3">&nbsp;</div>
        <?php

    }

    public function setView(Zend_View_Interface $view) 
    {
        $this->view = $view;
    }

}
