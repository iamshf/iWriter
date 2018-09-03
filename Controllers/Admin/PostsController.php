<?php
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\PostModel;
    use iWriter\Models\Admin\PostsModel;
    class PostsController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Posts.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $model = new PostsModel($this->_request->_data);
            $this->_body = $this->render($file, $model->getViews());
        }
        public function getJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new PostModel($this->_request->_data);
            $result = $model->get();
            if($result !== false && !empty($result)) {
                $this->_body = $this->getJsonResult(1, '成功', 200, $result);
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 404);
            }
        }
        public function postJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new PostModel($this->_request->_data);
            if($model->verifyContent()) {
                $result = $model->save();
                if($result > 0) {
                    $this->_body = $this->getJsonResult(1, '成功', 201, array('id' => $result));
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
        }
        public function putJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new PostModel($this->_request->_data);
            if($model->verifyId()) {
                $result = $model->update();
                if($result > 0) {
                    $this->_body = $this->getJsonResult(1, '成功', 200);
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
        }
    }
}
