<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\PostModel;
    use iWriter\Models\Admin\PostsModel;
    class PostsController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Posts.html';
            $model = new PostsModel($this->_request->_data);
            $this->_body = $this->render($file, $model->getViews());

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setEtag();
            $this->setLastModifiedSince(filemtime($file));
        }
        public function getJson() {
            $model = new PostModel($this->_request->_data);
            if(!empty($result = $model->get())) {
                $this->_body = $this->getJsonResult(1, '成功', 200, $result);
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 404);
            }
        }
        public function postJson() {
            $model = new PostModel($this->_request->_data);
            if($model->verifyContent()) {
                if(($result = $model->save()) > 0) {
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
            $model = new PostModel($this->_request->_data);
            if($model->verifyId()) {
                if($model->update() > 0) {
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
