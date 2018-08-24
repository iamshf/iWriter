<?php
namespace iWriter\Controllers {
    use \MiniRest\Resource;
    class IndexController extends Resource {
        public function getHtml() {
            $this->_body = 'hello world!';
        }
    }
}
