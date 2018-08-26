<?php
namespace iWriter\Controllers\Admin {
    class Resource extends \iWriter\Controllers\Resource {
        public function exec(){
            session_start();
            parent::exec();
        }
    }
}
