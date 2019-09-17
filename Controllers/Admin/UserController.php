<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin 
{
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\UserModel;
    class UserController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Index.html';
            $this->_body = $this->render($file);

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setEtag();
            $this->setLastModifiedSince(filemtime($file));
        }
        public function postJson() {
            $model = new UserModel($this->_request->_data);
            if($model->verifyName() && $model->verifyPwd()) {
                if(($result = $model->add()) > 0) {
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
            $model = new UserModel($this->_request->_data);
            if($model->verifyId() && ($model->verifyName() || $model->verifyPwd())) {
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
