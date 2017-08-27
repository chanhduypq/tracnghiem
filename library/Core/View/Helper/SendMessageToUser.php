<?php
/**
 * @author: huuthanh3108
 * @date: Sep 24, 2011
 * @company : http://dnict.vn
**/
class Core_View_Helper_SendMessageToUser extends Zend_View_Helper_Abstract
{
    public function sendMessageToUser ()
    {
        return $this;
    }
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    public function setSendMessageForUser($text,$s_uid,$r_uid,$type){
        $auth = Zend_Auth::getInstance();        
    	if(($s_uid != $r_uid) && ($auth->hasIdentity())){
    	?>    	
			<a title="Click gửi tin nhắn đến <?php echo $text;?>" href="#" onclick="showMessage('<?php echo $text;?>',<?php echo $s_uid;?>,<?php echo $r_uid;?>,<?php echo $type;?>);return false;" >
				<?php echo $text;?>							
			</a>
			
    	<?php 
    	}else{
    		echo $text;
    	}
    }
    public function render(){
        $auth = Zend_Auth::getInstance();
        //return '';
        if ( ! $auth->hasIdentity()) {
        	return '';
        }
?>
<script type="text/javascript">
function showMessage(title,s_id,r_id,type){	
	jQuery( "#dialog-form-sendmessagetouer" ).dialog( "option", "title", 'Gửi tin nhắn đến: '+title );	
	jQuery('#form-sendmessagetouer input[name="from"]').val(s_id);
	jQuery('#form-sendmessagetouer input[name="to"]').val(r_id);
	jQuery('#form-sendmessagetouer input[name="type"]').val(type);
	jQuery('#form-sendmessagetouer textarea[name="content"]').val('');
	
	jQuery('#dialog-form-sendmessagetouer').dialog( 'open' );
	
}
jQuery(document).ready(function($){
	//jQuery('a').tooltip();
$( "#dialog-form-sendmessagetouer" ).dialog({
			autoOpen: false,
			height: 200,
			width: 350,
			modal: true,
			buttons: {
				"Gửi": function() {
					$( this ).dialog( "close" );
					jQuery.post('<?php echo $this->view->baseUrl('/core/chat/service/index/t/3');?>',jQuery('#form-sendmessagetouer').serialize(),function(){
						
					});
					
				},
				"Thoát": function() {
					$( this ).dialog( "close" );
				}
			}
		});
});		
</script>		
    	<div id="dialog-form-sendmessagetouer" title="Gửi tin nhắn đến:" style="display:none">					
			<form id="form-sendmessagetouer" name="form-sendmessagetouer" method="post" action="">			
				<label for="content">Nội dung</label>
				<textarea name="content" id="content" class="text ui-widget-content ui-corner-all" ></textarea>
				<input type="hidden" name="from" value="" />
				<input type="hidden" name="to" value="" />
				<input type="hidden" name="type" value="1" />			
			</form>
		</div>
<?php 
    }
}