<?php
/**
 * Core
 *
 * This file is part of Core.
 *
 * Core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Core.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Core
 * @package     Core_Controller
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Controller
 * @author      Core Core Team <core@onegatecommerce.com>
 * @abstract
 */
abstract class Core_Controller_Action extends Zend_Controller_Action {

    /**
     * 
     * @var string
     */
    public $language;

    /**
     * 
     * @var integer
     */
    public $page;

    /**
     *
     * @var integer
     */
    public $limit;

    /**
     *
     * @var integer
     */
    public $start;

    /**
     *
     * @var integer
     */
    public $order;

    /**
     *  Main init
     */
    public function init() {
        parent::init();
        if($this->_request->getModuleName()=='admin'){
            if($this->_request->getControllerName()!='index'){
                $auth = Zend_Auth::getInstance();
                if (!$auth->hasIdentity()) {
                    $this->_helper->redirector('index', 'index', 'admin');
                } else {
                    $identity = $auth->getIdentity();
                    if (!isset($identity['user']) || $identity['user'] != 'admin') {
                        $this->_helper->redirector('index', 'index', 'admin');
                    }
                }
            }
            $option = array ('layout' => 'admin');
        }
        else{
            if($this->_request->getControllerName()!='index'){
                $auth = Zend_Auth::getInstance();
                if (!$auth->hasIdentity()) {
                    $this->_helper->redirector('index', 'index', 'default');
                }
            }
            $option = array ('layout' => 'index');
        }
        
        $layout = Zend_Layout::getMvcInstance();
        $layout->setOptions($option);       

        set_time_limit(2000);

        $this->view->headMeta()->appendName('author', 'Trần Công Tuệ email:chanhduypq@gmail.com');
        $this->view->headMeta()->appendName('copyright', 'Công ty TNHH VietAgar  website: http://vietagar.com.vn');
        $this->view->headMeta()->appendName('description', 'Chúng tôi không ngừng nổ lực phát triển website');
        $this->view->headMeta()->appendName('keywords', 'Trần Công Tuệ, chanhduypq@gmail.com');

        $this->initPaginator();
    }

    public function getUserId() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return -1;
        } else {
            $identity = $auth->getIdentity();
            return $identity['id'];
        }
    }

    public function getMessage() {
        $message = Core::message()->getAll();
        if (is_array($message) && count($message) > 0) {
            $message = $message['message'];
            return $message[0];
        } else {
            return '';
        }
    }

    public function createFilePdf($html, $filename, $title_header = '') {

        require_once 'tcpdf/tcpdf_autoconfig.php';
        require_once 'tcpdf/tcpdf.php';
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);




        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);

        $pdf->SetHeaderData('', 0, $title_header, '');

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------
// set font
        $pdf->SetFont('dejavusans', '', 10);

// add a page
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// reset pointer to the last page
        $pdf->lastPage();

// ---------------------------------------------------------
//Close and output PDF document
        ob_end_clean();
        $pdf->Output($filename, 'D');
        exit;
    }

    public function initPaginator() {
        $this->page = $this->_getParam('page', 1);
        $this->limit = $this->_getParam('limit', 10);
        $this->start = $this->_getParam('start', 0);
        $this->order = $this->_getParam('filter_order', 'id') . ' ' . $this->_getParam('filter_order_Dir', 'DESC');
    }

    /**
     * thumnail ảnh
     */
    public function echo_js_css_for_thumnail() {
        ?>
        <link href="<?php echo APPLICATION_URL; ?>/css/thumnail/prettyPhoto.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="<?php echo APPLICATION_URL; ?>/js/thumnail/jquery.prettyPhoto.js"></script>
        <script type="text/javascript" src="<?php echo APPLICATION_URL; ?>/js/thumnail/initPrettyPhoto.js"></script>               


        <?php
    }

    /**
     * HTTP_REFERER is not always present in _SERVER[]
     *
     * @return string
     */
    protected function _getBackUrl() {
        if (!$back = $this->getRequest()->getServer('HTTP_REFERER')) {
            $back = $this->view->href();
        }
        return $back;
    }

    /**
     * Write a snapshot to session
     *
     * @param string $snapshot
     * @return void
     */
    protected function _setSnapshot($snapshot) {
        Core::session()->snapshot = $snapshot;
    }

    /**
     * Retrieve snapshot from session
     *
     * @return string
     */
    protected function _getSnapshot() {
        $snapshot = Core::session()->snapshot;
        unset(Core::session()->snapshot);
        return $snapshot;
    }

    /**
     * @return bool
     */
    protected function _hasSnapshot() {
        return isset(Core::session()->snapshot) && !empty(Core::session()->snapshot);
    }

}
