<?php
declare(strict_types=1);
namespace iWriter\Models\Admin 
{
    class UploadModel{
        private $_data;
        private $_max_size = 20971521;
        private $_msg;
        private $_url;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function __get($k) {
            return property_exists($this, $k) ? $this->$k : $this->_data[$k];
        }
        public function verifyUploadRequest(): bool {
            $result = false;
            if(!empty($_FILES)) {
                switch(current($_FILES)['error']) {
                    case \UPLOAD_ERR_OK:
                        $result = true;
                        break;
                    case \UPLOAD_ERR_INI_SIZE:
                        $this->_msg = '超过php.ini允许的大小。';
                        break;
                    case \UPLOAD_ERR_FORM_SIZE:
                        $this->_msg = '超过表单允许的大小。';
                        break;
                    case \UPLOAD_ERR_PARTIAL:
                        $this->_msg = '只有部分被上传。';
                        break;
                    case \UPLOAD_ERR_NO_FILE:
                        $this->_msg = '请选择文件。';
                        break;
                    case \UPLOAD_ERR_NO_TMP_DIR:
                        $this->_msg = '找不到临时目录。';
                        break;
                    case \UPLOAD_ERR_CANT_WRITE:
                        $this->_msg = '写文件到硬盘出错。';
                        break;
                    case \UPLOAD_ERR_EXTENSION:
                        $this->_msg = 'File upload stopped by extension。';
                        break;
                    default:
                        $this->_msg = '未知错误。';
                        break;
                }
            }
            else {
                $this->_msg = '请选择文件。';
            }
            return $result;
        }
        public function verifyFile(): bool {
            return array_key_exists('file', $this->_data) && !empty($this->_data['file']);
        }
        public function verifyExt(): bool {
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
                    return true;
                }
            }
            $this->_msg = '文件类型不允许！';
            return false;
        }
        public function verifyEnv(): bool {
            $upload_file = current($_FILES);
            $this->_data['save_path'] = \Conf::UPLOAD_PATH;
            $result = false;
            switch(true) {
                case is_dir($this->_data['save_path']) === false:
                    $this->_msg = '上传目录不存在';
                    break;
                case is_writable($this->_data['save_path']) === false:
                    $this->_msg = '上传目录没有权限';
                    break;
                case is_uploaded_file($upload_file['tmp_name']) === false:
                    $this->_msg = '上传失败';
                    break;
                case $upload_file['size'] > $this->_max_size:
                    $this->_msg = '上传文件大小超过限制';
                    break;
                default:
                    $result = true;
                    break;
            }
            return $result;
        }

        public function save(): bool {
            $savename = date('YmdHis') . mt_rand(10000,99999) . '.' . $this->_data['ext_name'];
            $folder = $this->_data['dir_name'] . '/' . substr($savename, 0, 4) . '/' . substr($savename, 4, 2) . '/' . substr($savename, 6,2) . '/';

            if(is_dir($this->_data['save_path'] . $folder) === false) {
                mkdir($this->_data['save_path'] . $folder, 0755, true);
            }

            if(move_uploaded_file(current($_FILES)['tmp_name'], $this->_data['save_path'] . $folder . $savename) === true) {
                $this->_url = '/upload/' . $folder . $savename;
                return true;
            }
            return false;
        }
        public function delete(): bool {
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
