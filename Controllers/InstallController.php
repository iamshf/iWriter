<?php
namespace iWriter\Controllers {
    use \MiniRest\Resource;
    class InstallController extends Resource {
        public function getHtml() {
            $file = '../Views/Install.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_body = $this->render($file);
        }
        public function postJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $this->_body = '{"code": 1, "msg": "成功"}';
        }
    }
}
