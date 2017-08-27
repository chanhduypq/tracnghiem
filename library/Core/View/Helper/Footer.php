<?php
/**
 * @author Trần Công Tuệ
 */
class Core_View_Helper_Footer extends Zend_View_Helper_Abstract{
	/**
	 * @author Trần Công Tuệ	
	 * @param array $type	
	 * @return string $html
	 */
	public function footer(array $texts){?>
	
			<table width="100%">
			   <tbody>
			   <?php	   
			   for($i=0,$n=count($texts);$i<$n;$i++){?>
			   	       <tr>
				         <td width="100%" align="center"><?php echo $texts[$i];?></td>
				      </tr>
			   <?php 
			          if($i<$n-1){?>
			          	  <tr>
					         <td width="100%" align="center"><hr/></td>
					      </tr>
			          	<?php
			          }
			   } 
			   ?>	      
			   </tbody>
			</table>
		
    <?php 
	}
	
}