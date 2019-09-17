<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin 
{
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\CategoryModel;
    class CategoryController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Category.html';
            $this->_body = $this->render($file);

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setEtag();
            $this->setLastModifiedSince(filemtime($file));
        }
        public function getJson() {
            $model = new CategoryModel($this->_request->_data);
            $model->initReadDB();
            $result = $model->get();
            if(!empty($result)) {
                $this->_body = $this->getJsonResult(1, '成功', 200, $result);
            }
            else {
                $this->_body = $this->getJsonResult(2, '无数据', 404);
            }
        }
        public function postJson(){
            $model = new CategoryModel($this->_request->_data);
            $model->lockTable();
            if($model->verifyName() && ($model->verifyBeforeId() || $model->verifyAfterId() || $model->verifyPID())) {
                if(($result = $model->add()) > 0) {
                    $this->_body = $this->getJsonResult(1, '成功', 201, array('id' => $result));
                }
                else {
                    $this->_body = $this->getJsonResult(2, '失败', 500);
                }
            }
            else if($model->verifyId() && ($model->verifyPID() || $model->verifyAfterId() || $model->verifyBeforeId())) {
                if($model->restore()) {
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
            $model = new CategoryModel($this->_request->_data);
            if($model->verifyId() && ($model->verifyName() || $model->verifyRemark())){
                $model->lockTable();
                if($model->update() > -1) {
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
