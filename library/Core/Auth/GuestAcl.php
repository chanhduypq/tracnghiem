<?php

/*
 * @author: huuthanh3108
 * @date: May 12, 2011
 * @company : http://dnict.vn
 */
/*
 * class nay phan quyen cho guest nhung nguoi chua dang nhap
 * se lay su lieu tu privileges voi is_public = 1
 */

class Core_Auth_GuestAcl {

    protected $_acl;
    protected $_roles;

    public function __construct($options = null) {
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('guest'));
        $frontendOptions = array(
            'lifetime' => 7200,
            'automatic_serialization' => TRUE
        );
        $backendOptions = array('cache_dir' => FILE_CACHE_DIRECTORY);

        $this->_acl = $acl;
    }

    public function isAllowed($arrParam) {
        $privilege = $arrParam['module'] . '_' . $arrParam['controller'] . '_' . $arrParam['action'];
        $flagAccess = false;
        if ($this->_acl->isAllowed('guest', null, $privilege)) {
            return true;
        }
        return $flagAccess;
    }

    /**
     * @return the $_acl
     */
    public function getAcl() {
        return $this->_acl;
    }

    /**
     * @param Zend_Acl $_acl
     */
    public function setAcl($_acl) {
        $this->_acl = $_acl;
    }

}
