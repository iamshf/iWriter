<?php
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\CategoryModel;
    class CategoryController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Category.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_body = $this->render($file);
        }
        public function getJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new CategoryModel($this->_request->_data);
            $model->initReadDB();
            $result = $model->get();

            if($result !== false && !empty($result)) {
                $this->_body = $this->getJsonResult(1, '成功', 200, $result);
            }
            else {
                $this->_body = $this->getJsonResult(2, '无数据', 404);
            }
        }
        public function postJson(){
            $this->_headers[] = 'Content-Type:application/json';
            $model = new CategoryModel($this->_request->_data);
            $model->lockTable();
            if($model->verifyName() && ($model->verifyBeforeId() || $model->verifyAfterId() || $model->verifyPID())) {
                $result = $model->add();
                if($result > 0) {
                    $this->_body = $this->getJsonResult(1, '成功', 201, array('id' => $result));
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
            }
            else if($model->verifyId() && ($model->verifyPID() || $model->verifyAfterId() || $model->verifyBeforeId())) {
                $result = $model->restore();
                if($result) {
                    $this->_body = $this->getJsonResult(1, '成功', 200);
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
            $model->unlockTable();
        }
        public function putJson() {
            $this->_headers[] = 'Content-Type:application/json';
            $model = new CategoryModel($this->_request->_data);
            if($model->verifyId() && ($model->verifyName() || $model->verifyRemark())){
                $model->lockTable();
                if($model->update()) {
                    $this->_body = $this->getJsonResult(1, '成功', 200);
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
                $model->unlockTable();
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
        }
        public function deleteJson() {
            $this->_headers[] = 'Content-Type:application/json';
            $model = new CategoryModel($this->_request->_data);
            if($model->verifyId()) {
                $model->lockTable();
                $model->delete();
                $this->_body = $this->getJsonResult(1, '成功', 200);
                $model->unlockTable();
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 400);
            }
        }
    }
}
