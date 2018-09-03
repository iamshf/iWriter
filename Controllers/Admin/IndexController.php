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
            session_start();
            $this->_headers[] = 'Content-Type: application/json';
            $model = new UserModel(array_merge(array('count' => 1, 'columns' => 'id,name,pwd'), $this->_request->_data));
            if($model->verifyName() && $model->verifyPwd()) {
                $result = $model->get();
                if($result !== false && !empty($result) && $model->comparePwd($this->_request->_data['pwd'], $result['pwd'])) {
                    $_SESSION['uid'] = $result['id'];
                    $this->_body = $this->getJsonResult(1, '成功', 200, array('location' => './posts'));
                }
                else {
                    $this->_body = $this->getJsonResult(2, '帐号或密码不正确', 404);
                }
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
        }
    }
}
