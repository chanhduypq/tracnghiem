<?php
//	require_once 'qtht/models/BussinessDateModel.php';
//	require_once 'auth/models/ResourceUserModel.php';
//require_once 'Zend/Controller/Plugin/Abstract.php';
	class Core_Controller_Plugin_Common extends Zend_Controller_Plugin_Abstract{
    public  static function  hello()
    {
    	echo "Hello Core_Controller_Plugin_Common";
    }
	static function PaginatorWithAction($numrows,$posrows,$valuepage,$formname,$currentpage,$action){
			$html = "";
			if(floor($numrows/$valuepage) < $posrows){
				if($numrows % $valuepage == 0){
					$posrows = floor($numrows/$valuepage);	
				}
				else{
					$posrows = floor($numrows/$valuepage)+1;
				}
			}
			$pageCurrent = $currentpage;
			$beginpage = $currentpage - $posrows + 1;
			if($beginpage<=0){
				$beginpage=1;
			}
			$endpage = $beginpage + $posrows - 1;
			
			if($currentpage!=1){
				$hasFirst = "
					<div class='button2-right'><div class='start'><a href='#' title='Bắt đầu' onclick=\"
						document.".$formname.".page.value=1;
						document.".$formname.".action='".$action."';
						document.".$formname.".submit();
					\">Bắt đầu</a></div></div>
					<div class='button2-right'><div class='prev'><a href='#' title='Trước' onclick=\"
						document.".$formname.".page.value=".($currentpage-1).";
						document.".$formname.".action='".$action."';
						document.".$formname.".submit();
					\">Trước</a></div></div>
				";
			}
			
			if($currentpage < $numrows/$valuepage){
				$hasNext = "
					<div class='button2-left'><div class='next'><a href='#' title='Tiếp' onclick=\"
						document.".$formname.".page.value=".($currentpage+1).";
						document.".$formname.".action='".$action."';
						document.".$formname.".submit();	
					\">Tiếp</a></div></div>
					<div class='button2-left'><div class='end'><a href='#' title='Cuối' onclick=\"
						document.".$formname.".page.value=".(ceil($numrows/$valuepage)).";
						document.".$formname.".action='".$action."';
						document.".$formname.".submit();
					\">Cuối</a></div></div>
				";
			}
			$html .= $hasFirst;
			$html .= "<div class='button2-left'><div class='page'>";
			for($i=$beginpage;$i<=$endpage;$i++){
				if($i==$currentpage){
					$html .= "<span><font color=red>".$i."</font></span>";
				}else{
					$html .= "<a href='#' onclick=\"
						document.".$formname.".page.value=".$i.";
						document.".$formname.".action='".$action."';
						document.".$formname.".submit();
					\"> ".$i." </a>";
				}
			}
			$html .= "</div></div>";
			$html .= $hasNext;
			return $html;
		}
}