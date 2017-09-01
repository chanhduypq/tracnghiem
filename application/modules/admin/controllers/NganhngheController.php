<?php

class Admin_NganhngheController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $mapper = new Default_Model_Nganhnghe();
        $rows = $mapper->getNganhNghes();
        $this->view->items = $rows;
        
        $this->view->message= $this->getMessage();
    }

    public function addAction() 
    {
        $form = new Admin_Form_Nganhnghe();

        $question_ids = array();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
            } 
            if ($form->isValid($formData)) {
                $mapper = new Default_Model_Nganhnghe();

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
        $this->view->question_ids=$question_ids;
    }

    public function editAction() 
    {

        $id_mat_hang = $this->_getParam('id');

        $where = "id=$id_mat_hang";
        $mapper = new Default_Model_Nganhnghe();
        $row = $mapper->fetchRow($where);
        $row = $row->toArray();
        $form = new Admin_Form_Nganhnghe();

        $question_ids = array();
        $temps = Core_Db_Table::getDefaultAdapter()->query("select question_id from nganhnghe_question  where nganhnghe_id='$id_mat_hang'")->fetchAll();
        if (is_array($temps) && count($temps) > 0) {
            foreach ($temps as $row1) {
                $question_ids[] = $row1['question_id'];
            }
        }
        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
            }

            if ($form->isValid($formData)) {

                $row = $mapper->fetchRow('id=' . $formData['id']);

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
        $this->view->question_ids=$question_ids;
        $this->render('add');
    }

    public function deleteAction() 
    {
        $item_id = $this->_request->getParam('id', null);

        
        if (Core_Common_Numeric::isInteger($item_id) == FALSE){
            return;
        }

        $where = "id=$item_id";
        $mapper = new Default_Model_Nganhnghe();

        $mapper->delete($where);

        Core::message()->addSuccess('Xóa thành công');
        $this->_helper->redirector('index', 'nganhnghe', 'admin');
    }

}
