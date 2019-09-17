<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin 
{
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\UserModel;
    class IndexController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Index.html';
            $this->_body = $this->render($file);

            $this->setEtag();
            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setLastModifiedSince(filemtime($file));
        }
        public function postJson() {
            session_start();
            $model = new UserModel(array_merge(array('count' => 1, 'columns' => 'id,name,pwd'), $this->_request->_data));
            if($model->verifyName() && $model->verifyPwd()) {
                if(!empty($result = $model->get()) && $model->comparePwd($this->_request->_data['pwd'], $result['pwd'])) {
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
