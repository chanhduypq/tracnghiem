<?php

class Admin_UserController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $limit = $this->_getParam('limit', 5);
        $page = $this->_getParam('page', 1);

        if(Core_Common_Numeric::isInteger($page)==FALSE){
            $page=1;
        }

        $start = (($page - 1) * $limit);
        $mapper = new Default_Model_User();
        $rows = $mapper->getUsers($total, $limit, $start);
        $this->view->items = $rows;

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($total));
        $paginator->setDefaultScrollingStyle();
        $paginator->setItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;        

        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->page=$page;

        $this->view->message= $this->getMessage();        
    }

    private function processSpecialInput($form, &$formData) 
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

    public function addAction() 
    {
        $form = new Admin_Form_User();


        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if ($form->isValid($formData)) {
                $mapper = new Default_Model_User();
                $this->processSpecialInput($form, $formData);

                $formData['password'] = sha1($formData['email']);

                if ($mapper->insert($formData)) {
                    Core::message()->addSuccess('Thêm mới thành công');
                    $this->_helper->redirector('index', 'user', 'admin');
                } else {
                    Core::message()->addSuccess('Lỗi. Xử lý thất bại.');
                    $message = Core::message()->getAll();
                    if (is_array($message) && count($message) > 0) {
                        $message = $message['message'];
                        $this->view->message = $message[0];
                    } else {
                        $this->view->message = '';
                    }
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
        $this->view->page = $this->_getParam('page');
    }

    public function editAction() 
    {

        $id = $this->_getParam('id');
        $where = "id=$id";
        $mapper = new Default_Model_User();
        $row = $mapper->fetchRow($where);
        $row = $row->toArray();
        $form = new Admin_Form_User();

        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            $form->getElement('email')->getValidator('Db_NoRecordExists')->setExclude('id != ' . $formData['id']);

            if ($form->isValid($formData)) {
                $this->processSpecialInput($form, $formData);
                $row = $mapper->fetchRow('id=' . $formData['id']);

                $mapper->update($formData, 'id=' . $formData['id']);
                Core::message()->addSuccess('Sửa thành công');
                $this->_helper->redirector('index', 'user', 'admin', array('page' => $this->_getParam('page')));
            } else {
                $form->populate($formData);
            }
        } else {
            $form->setDefaults($row);
        }
        $this->view->form = $form;
        $db = Core_Db_Table::getDefaultAdapter();
        $history = $db->fetchAll("select user_exam.allow_re_exam,user_exam.user_id,user_exam.nganh_nghe_id,user_exam.level,user_exam.id,user_exam.exam_date,user_pass.user_exam_id,nganh_nghe.title from user_exam JOIN nganh_nghe ON nganh_nghe.id=user_exam.nganh_nghe_id LEFT JOIN user_pass ON user_pass.user_exam_id=user_exam.id WHERE user_exam.user_id=$id ORDER BY user_exam.exam_date ASC");
        $this->view->history = $history;
        $this->render('add');
    }

    public function deleteAction() 
    {
        $id = $this->_request->getParam('id', null);


        if (Core_Common_Numeric::isInteger($id) == FALSE) {
            $this->_helper->redirector('index', 'user', 'admin');
            return;
        }

        $where = "id=$id";
        $mapper = new Default_Model_User();
        $mapper->delete($where);
        Core::message()->addSuccess('Xóa thành công');
        $this->_helper->redirector('index', 'user', 'admin');
    }

    public function allowreexamAction() 
    {
        $user_id = $this->_request->getParam('user_id', null);
        $exam_id = $this->_request->getParam('exam_id', null);
        $db = Core_Db_Table::getDefaultAdapter();
        $db->query('UPDATE user_exam SET allow_re_exam=1 WHERE id=' . $exam_id)->execute();
        $this->_helper->redirector('edit', 'user', 'admin', array('id' => $user_id));
    }
    public function cancelreexamAction() 
    {
        $user_id = $this->_request->getParam('user_id', null);
        $exam_id = $this->_request->getParam('exam_id', null);
        $db = Core_Db_Table::getDefaultAdapter();
        $db->query('UPDATE user_exam SET allow_re_exam=0 WHERE id=' . $exam_id)->execute();
        $this->_helper->redirector('edit', 'user', 'admin', array('id' => $user_id));
    }

    public function ketquathiAction() 
    {
        $user_exam_id = $this->_getParam('user_exam_id');
        $html = Default_Model_Userexam::getHtmlForExamResult($user_exam_id, $title_header);

        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("select "
                . "DATE_FORMAT(user_exam.exam_date,'%Y_%m_%d') AS date,"
                . "user.id "
                . "from user_exam "
                . "JOIN user ON user.id=user_exam.user_id "
                . "WHERE user_exam.id=$user_exam_id");

        $this->createFilePdf($html, $row['id'] . '___' . $row['date'] . '.pdf', $title_header);
    }

}
