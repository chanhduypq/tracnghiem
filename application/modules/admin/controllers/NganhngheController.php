<?php

class Admin_NganhngheController extends Core_Controller_Action {

    public function init() {
        parent::init();
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

    public function indexAction() {
        $mapper = new Default_Model_Index();
        $rows = $mapper->getMatHangs();
        $this->view->items = $rows;
        
        $this->view->message= $this->getMessage();
    }

    public function addAction() {
        $form = new Admin_Form_Nganhnghe();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
                unset($formData['question_id']);
            } else {
                $question_ids = array();
            }
            if ($form->isValid($formData)) {
                unset($formData['for_confirm']);
                $mapper = new Default_Model_Index();


                foreach ($formData as $key => $value) {
                    if ($value == "") {
                        $formData["$key"] = NULL;
                    }
                }
                if ($id = $mapper->insert($formData)) {

                    if (is_array($question_ids) && count($question_ids) > 0) {
                        foreach ($question_ids as $question_id) {

                            $mapper_nganhnghe_question = new Default_Model_NganhngheQuestion();
                            $mapper_nganhnghe_question->insert(array(
                                'nganhnghe_id' => $id,
                                'question_id' => $question_id,
                            ));
                        }
                    }

                    Core::message()->addSuccess('Thêm mới thành công');
                    $this->_helper->redirector('index', 'nganhnghe', 'admin');
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
    }

    public function editAction() {

        $id_mat_hang = $this->_getParam('id');

        $where = "id=$id_mat_hang";
        $mapper = new Default_Model_Index();
        $row = $mapper->fetchRow($where);
        $row = $row->toArray();
        $form = new Admin_Form_Nganhnghe();

        if ($this->_request->isPost() && isset($_POST['for_confirm'])) {

            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
                unset($formData['question_id']);
            } else {
                $question_ids = array();
            }
            unset($formData['for_confirm']);

            if ($form->isValid($formData)) {

                $row = $mapper->fetchRow('id=' . $formData['id']);

                foreach ($formData as $key => $value) {
                    if ($value == "") {
                        $formData["$key"] = NULL;
                    }
                }

                $mapper->update($formData, 'id=' . $formData['id']);

                Core_Db_Table::getDefaultAdapter()->query('delete from nganhnghe_question where nganhnghe_id=' . $formData['id'])->execute();
                if (is_array($question_ids) && count($question_ids) > 0) {
                    foreach ($question_ids as $question_id) {

                        $mapper_nganhnghe_question = new Default_Model_NganhngheQuestion();
                        $mapper_nganhnghe_question->insert(array(
                            'nganhnghe_id' => $formData['id'],
                            'question_id' => $question_id,
                        ));
                    }
                }

                Core::message()->addSuccess('Sửa thành công');
                $this->_helper->redirector('index', 'nganhnghe', 'admin');
            } else {
                $form->populate($formData);
            }
        } else {
            $form->setDefaults($row);
        }
        $this->view->form = $form;
        $this->render('add');
    }

    public function deleteAction() {
        $item_id = $this->_request->getParam('id', null);

        Zend_Loader::loadFile('Numeric.php', "./../library/Core/Common/", true);
        if (Numeric::isInteger($item_id) == FALSE){
            return;
        }

        $where = "id=$item_id";
        $mapper = new Default_Model_Index();

        $mapper->delete($where);

        Core::message()->addSuccess('Xóa thành công');
        $this->_helper->redirector('index', 'nganhnghe', 'admin');
    }

}