<?php
/**
* @file: Newslist.php
* @author: huuthanh3108@gmaill.com
* @date: 07-12-2012
* @company : http://dnict.vn
**/
class Block_Newslist extends Zend_View_Helper_Abstract{

	public function newslist($data){
		$rows = Core::model('News/Content')
		->select(array('id','title','introtext','fulltext'))
		->where('id_cat IN ('.$data['id_cat'].') ')
		->order('orders ASC')
		->limit(4)
		->fetchAll();
		//var_dump($rows);
// 		echo Core::model('News/Content')
// 		->select(array('id','title','introtext','fulltext'))
// 		->where('id_cat IN ('.$data['id_cat'].') ',$data['id_cat'])->__toString();		
	
		for ($i = 0; $i < count($rows); $i++) {
			$image = ($this->parseImage($rows[$i]['introtext'])=='')?$this->parseImage($rows[$i]['fulltext']):'';
			if ($image != '') {
				$rows[$i]['image'] = '<img src="/timthumb.php?src='.$image.'&w='.$data['width'].'&h='.$data['height'].'" title="'.$rows[$i]['title'].'" alt="'.$rows[$i]['title'].'" />';
			}
			$rows[$i]['introtext'] = $this->processIntrotext($rows[$i]['introtext'],'-1');
			unset($rows[$i]['fulltext']);
		}
		include 'html/default.phtml';
	}
	/**
	 * check the image source is existed ?
	 *
	 * @param string $imageSource the path of image source.
	 * @access public,
	 * @return boolean,
	 */
	function parseImage($text)
	{
		$regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/";
		preg_match($regex, $text, $matches);
		$images = (count($matches)) ? $matches : array();
		$image = count($images) > 1 ? $images[1] : '';
		return $image;
	}
	/**
	 *
	 * Render image before display it
	 * @param string $title
	 * @param string $link
	 * @param string $image
	 * @param object $params
	 * @param int $width
	 * @param int $height
	 * @param string $attrs
	 * @param string $returnURL
	 * @return string image
	 */
	function renderImage($title, $link, $image, $params, $width = 0, $height = 0, $attrs = '', $returnURL = false)
	{
		if ($image !='') {
			$title = strip_tags($title);
			$w = $width ? '&w='.$width: "";
			$h = $height ? '&h='.$height: "";
			$width = $width ? "width=\"$width\"" : "";
			$height = $height ? "height=\"$height\"" : "";
			$image = "<img $attrs src=\"".$this->view->baseUrl()."/timthumb.php?src=".$image.$w.$h."\" alt=\"{$title}\"   title=\"{$title}\" $width $height />";
		}
		//$image = '<a href="' . $link . '" title="" class="ja-image">' . $image . '</a>';
		// clean up globals
		return $image;
	}
	/**
	 * Process introtext
	 * @param string $introtext
	 * @param int $maxchars
	 * @return string
	 */
	function processIntrotext($introtext, $maxchars)
	{
		// expression to search for
		$introtext1 = strip_tags($introtext);
		if ($maxchars && mb_strlen($introtext1,'UTF-8') > $maxchars) {
			$introtext1 = mb_substr($introtext1, 0, $maxchars,'UTF-8') . "...";
		}
		if(trim($maxchars) == '-1'){
			$introtext1 = strip_tags($introtext);
		}
		if(trim($maxchars) == '0' || trim($maxchars) == ''){
			$introtext1 = '';
			$introtext = '';
		}
		// clean up globals
		return $introtext1;
	}
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
}