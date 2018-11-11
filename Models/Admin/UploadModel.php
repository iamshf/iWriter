<?php
namespace iWriter\Models\Admin {
    class UploadModel{
        private $_data;
        private $_max_size = 20971521;
        public function __construct($data = array()) {
            $this->_data = $data;
        }
        public function verifyUploadRequest() {
            $result = true;
            if(!empty($_FILES)) {
                switch(current($_FILES)['error']) {
                    case \UPLOAD_ERR_OK:
                        $result = true;
                        break;
                    case \UPLOAD_ERR_INI_SIZE:
                        $result = '超过php.ini允许的大小。';
                        break;
                    case \UPLOAD_ERR_FORM_SIZE:
                        $result = '超过表单允许的大小。';
                        break;
                    case \UPLOAD_ERR_PARTIAL:
                        $result = '只有部分被上传。';
                        break;
                    case \UPLOAD_ERR_NO_FILE:
                        $result = '请选择文件。';
                        break;
                    case \UPLOAD_ERR_NO_TMP_DIR:
                        $result = '找不到临时目录。';
                        break;
                    case \UPLOAD_ERR_CANT_WRITE:
                        $result = '写文件到硬盘出错。';
                        break;
                    case \UPLOAD_ERR_EXTENSION:
                        $result = 'File upload stopped by extension。';
                        break;
                    default:
                        $result = '未知错误。';
                        break;
                }
            }
            else {
                $result = '请选择文件。';
            }
            return $result;
        }
        public function verifyFile() {
            return array_key_exists('file', $this->_data) && !empty($this->_data['file']);
        }
        public function verifyExt() {
            $result = '文件类型不允许！';
            $exts = array (
                'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
                'flash' => array('swf', 'flv'),
                'media' => array('swf', 'flv', 'mp3','mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
                'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','lrc')
            );
            if(!array_key_exists('ext_name', $this->_data) || empty($this->_data['ext_name'])) {
                $this->_data['ext_name'] = strtolower(trim(mb_substr(current($_FILES)['name'], mb_strrpos(current($_FILES)['name'], '.', 'UTF-8') + 1, NULL, 'UTF-8')));
            }
            foreach($exts as $k => $v) {
                if(in_array($this->_data['ext_name'], $exts[$k])) {
                    $this->_data['dir_name'] = $k;
                    $result = true;
                    break;
                }
            }
            return $result;
        }
        public function verifyEnv() {
            $upload_file = current($_FILES);
            $this->_data['save_path'] = \Conf::FILE_PATH . '/Uploads/';
            if(is_dir($this->_data['save_path']) === false){
                return '上传目录不存在';
            }
            if(is_writable($this->_data['save_path']) === false){
                return '上传目录没有权限';
            }
            if(is_uploaded_file($upload_file['tmp_name']) === false) {
                return '上传失败';
            }
            if($upload_file['size'] > $this->_max_size) {
                return '上传文件大小超过限制';
            }
            return true;
        }

        public function save() {
            $savename = date('YmdHis') . mt_rand(10000,99999) . '.' . $this->_data['ext_name'];
            $folder = $this->_data['dir_name'] . '/' . substr($savename, 0, 4) . '/' . substr($savename, 4, 2) . '/' . substr($savename, 6,2) . '/';

            if(is_dir($this->_data['save_path'] . $folder) === false) {
                mkdir($this->_data['save_path'] . $folder, 0755, true);
            }

            if(move_uploaded_file(current($_FILES)['tmp_name'], $this->_data['save_path'] . $folder . $savename) === true) {
                return '/upload/' . $folder . $savename;
            }
            return true;
        }
        public function delete() {
            if($this->verifyFile()) {
                $filePath = \Conf::$UPLOAD_CONF['path'][$this->_data['name']] . mb_substr($this->_data['file'], 7, NULL, 'UTF-8');
                if(file_exists($filePath)) {
                    return unlink($filePath);
                }
            }
            return false;
        }
    }
}
