<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Image
 *
 * @author shf
 */
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\UploadModel;
    class UploadController extends Resource{
        public function postJson(){
            $this->_headers[] = 'Accept:application/json';
            $model = new UploadModel($this->_request->_data);
            if(($result = $model->verifyUploadRequest()) !== true || ($result = $model->verifyExt()) !== true || ($result = $model->verifyEnv()) !== true) {
                $this->_body = $this->getResult(2, $result);
            }
            else {
                if(($result = $model->save()) !== false) {
                    $this->_body = $this->getResult(1, '上传成功', 200, array('url' => $result));
                }
                else {
                    $this->_body = $this->getResult(3, '上传失败');
                }
            }
        }
        /*为兼容kindeditor*/
        public function postHtml() {
            $this->_headers[] = 'Accept:application/json';
            $model = new UploadModel($this->_request->_data);
            if(($result = $model->verifyUploadRequest()) !== true || ($result = $model->verifyExt()) !== true || ($result = $model->verifyEnv()) !== true) {
                $this->_body = '{"error": 1, "message": "' . $result . '"}';
            }
            else {
                if(($result = $model->save()) !== false) {
                    $this->_body = '{"error": 0, "url": "' . $result . '"}';
                }
                else {
                    $this->_body = '{"error": 1, "message": "' . $result . '"}';
                }
            }
        }
        public function deleteJson(){
            $this->_headers[] = 'Accept:application/json';
            $model = new UploadModel($this->_request->_data);
            if($model->verifyFile() && $model->verifyName()) {
                if($model->delete()) {
                    $this->_body = $this->getResult(1, '成功');
                }
                else {
                    $this->_body = $this->getResult(2, '请求出错', 500);
                }
            }
            else {
                $this->_body = $this->getResult(0, '请求非法', 400);
            }
        }
    }
}
