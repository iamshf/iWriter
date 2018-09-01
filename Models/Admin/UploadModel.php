<?php
namespace iWriter\Models\Admin {
    class UploadModel{
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }
        private $_ext = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','lrc')
        );
        private $_max_size = 20971520;

        private $_code;
        private $_msg;
        private function verifyUploadRequest() {
            if (!empty($_FILES['file_info']['error'])) {
                $this->_code = $_FILES['file_info']['error'];
                switch($this->_code){
                    case '1':
                        $this->_msg = '超过php.ini允许的大小。';
                        break;
                    case '2':
                        $this->_msg = '超过表单允许的大小。';
                        break;
                    case '3':
                        $this->_msg = '图片只有部分被上传。';
                        break;
                    case '4':
                        $this->_msg = '请选择图片。';
                        break;
                    case '6':
                        $this->_msg = '找不到临时目录。';
                        break;
                    case '7':
                        $this->_msg = '写文件到硬盘出错。';
                        break;
                    case '8':
                        $this->_msg = 'File upload stopped by extension。';
                        break;
                    case '999':
                    default:
                        $this->_msg = '未知错误。';
                }
                return false;
            }
            return true;
        }
        private function verifyFile(){
            if(empty($_FILES) === false){
                $filename = $_FILES['file_info']['name'];
                $tmpname = $_FILES['file_info']['tmp_name'];
                $filesize = $_FILES['file_info']['size'];


                $dirname = empty($this->_data['dir']) ? 'image' : trim($this->_data['dir']);
                $tmp = explode('.', $filename);
                $fileext = array_pop($tmp);
                $fileext = trim($fileext);
                $fileext = strtolower($fileext);
                $savename = isset($this->_data['folder']) ? basename($this->_data['folder']) :  date('YmdHis') . rand(10000,99999) . '.' . $fileext;
                $folder = $dirname . '/' . substr($savename, 0, 4) . '/' . substr($savename, 4, 2) . '/' . substr($savename, 6,2);
                $savePath = \Conf::FILE_PATH . 'Uploads/';

                if(!$filename){
                    $this->_code = 1;
                    $this->_msg = '请选择文件';
                    return false;
                }
                if(is_dir($savePath) === false){
                    $this->_code = 1;
                    $this->_msg = '上传目录不存在';
                    return false;
                }
                if(is_writable($savePath) === false){
                    $this->_code = 1;
                    $this->_msg = '上传目录没有权限';
                    return false;
                }
                if(is_uploaded_file($tmpname) === false){
                    $this->_code = 1;
                    $this->_msg = '上传失败';
                    return false; 
                }
                if($filesize > $this->_max_size){
                    $this->_code = 1;
                    $this->_msg = '上传文件大小超过限制';
                    return false; 
                }

                if(in_array($fileext, $this->_ext[$dirname]) === false){
                    $this->_code = 1;
                    $this->_msg = '文件扩展名不允许被上传';
                    return false;
                }
                if(is_dir($savePath . $folder) === false) {
                    mkdir($savePath . $folder, 0755, true);
                }
                
                if(move_uploaded_file($tmpname, $savePath . $folder . '/' . $savename)) {
                    $this->_code = 0;
                    $this->_msg = '上传成功';
                    $this->_url = '/upload/' . $folder . '/' . $savename;
                    return true;
                }
                else {
                    $this->_code = 2;
                    $this->_msg = '上传失败';
                    return false;
                }
            }
            $this->_code = 1;
            $this->_msg = '请选择文件';
            return false;
        }
        public function upload(){
            $result = array();
            if($this->verifyUploadRequest()){
                if($this->verifyFile()){
                    $result['url'] = $this->_url;
                }
            }

            $result['error'] = $this->_code;
            if(!array_key_exists('url', $result)){
                $result['message'] = $this->_msg;
            }
            return $result;
        }
    }
}
