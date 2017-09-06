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
        <div class="span12" align='center' style='padding-bottom:20px;color: red;margin-bottom: 20px;'>
            <div>	  
                <div style="float: right;cursor: pointer;">
                    <input onclick="document.form.submit();" type="button" value="Lưu" class="button" />
                </div>
                <div style="float: right;margin-right: 10px;cursor: pointer;">
                    <input onclick="window.location = '<?php echo $url; ?>';" type="button" value="Quay lại" class="button" />
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>        
        <?php

    }

    public function setView(Zend_View_Interface $view) 
    {
        $this->view = $view;
    }

}
