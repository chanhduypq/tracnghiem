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
        $this->setLayout();
        $this->redirectIfNotLogin();    

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
            if (!isset($message['message'])) {
                return '';
            }
            $message = $message['message'];
            return $message[0];
        } else {
            return '';
        }
    }
    
    public function download($path,$fileName=null) 
    { 
        Core_Common_Download::download($path, $fileName);
    }

    public function createFilePdf($html, $filename, $title_header = '') 
    {
        Core_Common_Pdf::createFilePdf($html, $filename, $title_header);
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
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param Core_Form $form
     * @param array $formData
     */
    public function processSpecialInput($form, &$formData) 
    {
        try {
            foreach ($form->getElements() as $element) {
                if ($element instanceof Core_Form_Element_Date) {
                    $array = explode("/", $element->getValue());
                    if (count($array) == 3) {
                        $date = $array[2] . '-' . $array[1] . '-' . $array[0];
                        $formData[$element->getName()] = $date;
                    }
                } elseif ($element instanceof Core_Form_Element_File) {
                    if ($element->getForInsertDB() == false) {
                        if ($_FILES[$element->getName()]['name'] != "") {
                            $file_name = $element->getRandomFileName();
                            if ($element->isUploaded($file_name) && $element->isValid($file_name)) {
                                $element->receive();
                            }
                            if (isset($file_name) && $file_name != "") {
                                $file_name = $element->getPathStoreFile() . $file_name;
                            }
                            $formData[$element->getName()] = $file_name;
                        }
                    } else if ($element->getForInsertDB() == true) {
                        $file_key_array = array('type', 'size', 'name');
                        foreach ($this->getElements() as $key => $value) {
                            if (in_array($key, $file_key_array)) {
                                $formData[$key] = $_FILES[$element->getName()][$key];
                            }
                        }
                        $file = fopen($_FILES[$element->getName()]['tmp_name'], "r", 1);
                        if ($file) {
                            $fileContent = base64_encode(file_get_contents($_FILES[$element->getName()]['tmp_name']));
                        } else {
                            $fileContent = null;
                        }
                        $formData[$element->getName()] = $fileContent;
                    }
                }
            }
        } catch (Exception $e) {
            
        }
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
    
    private function setLayout(){
        if($this->_request->getModuleName()=='admin'){            
            $option = array ('layout' => 'admin');
        }
        else{            
            $option = array ('layout' => 'index');
        }
        
        $layout = Zend_Layout::getMvcInstance();
        $layout->setOptions($option); 
    }
    
    private function turnSessionPrevController()
    {
        $session=new Zend_Session_Namespace('url');
        $session->controller=$this->_request->getControllerName();
    }

    private function redirectIfNotLogin(){
        if($this->_request->getModuleName()=='admin'){
            if($this->_request->getControllerName()!='index'){
                $auth = Zend_Auth::getInstance();
                if (!$auth->hasIdentity()) {
                    $this->turnSessionPrevController();
                    $this->_helper->redirector('index', 'index', 'admin');
                } else {
                    $identity = $auth->getIdentity();
                    if (!isset($identity['user']) || $identity['user'] != 'admin') {
                        $this->turnSessionPrevController();
                        $this->_helper->redirector('index', 'index', 'admin');
                    }
                }
            }
        }
        else{
            if($this->_request->getControllerName()!='index'){
                $auth = Zend_Auth::getInstance();
                if (!$auth->hasIdentity()) {
                    $this->_helper->redirector('index', 'index', 'default');
                }
            }
        }   
    }
    
    /**
     * khởi tạo lại session ban đầu
     * có nghĩa là 
     *     ban đầu khi login, lưu thông tin session nào thi bây giờ chỉ lấy lại những thông tin đó, 
     *     những thông tin session mới thêm vào sau này thi hủy đi
     */
    public function resetSession() 
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $auth->clearIdentity();

        foreach ($identity as $key=>$value){
            if(!in_array($key, array(
                                    'id',
                                    'danh_xung',
                                    'full_name',
                                    'email',
                                    'phone',
                                    'password',
                                    'is_admin',
                                    'user'                
                                    )
                        )
            ){
                unset($identity["$key"]);
            }
        }

        $auth->getStorage()->write($identity);
    }
}
