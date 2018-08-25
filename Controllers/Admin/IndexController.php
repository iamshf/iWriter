<?php
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\UserModel;
    class IndexController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Index.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_body = $this->render($file);
        }
        public function postJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new UserModel($this->_request->_data);
            if($model->verifyName() && $model->verifyPwd()) {
                $this->_body = $this->getJsonResult(1, '成功', $this->_request->_data);
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法');
            }
            //$this->_body = '{"code": 1, "msg": "成功"}';
        }
    }
}
