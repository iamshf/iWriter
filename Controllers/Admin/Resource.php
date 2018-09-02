<?php
namespace iWriter\Controllers\Admin {
    class Resource extends \iWriter\Controllers\Resource {
        public function exec(){
            session_start();
            if(isset($_SESSION['uid']) && is_numeric($_SESSION['uid']) && $_SESSION['uid'] > 0) {
                parent::exec();
            }
            else {
                $this->_headers[] = 'Location: ./index';
            }
        }
    }
}
