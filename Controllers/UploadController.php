<?php
/**
 * Description of Js
 *
 * @author shf
 */
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    class UploadController extends Resource{
        public function exec() {
            $file = '../Uploads/' . $this->_request->_data['path'];
            $expire = \Conf::CACHE_EXPIRE * 365;//缓存一天
            $this->setContentType();

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_headers[] = 'X-Accel-Redirect: ' . mb_substr($file, 2, null, 'UTF-8');
        }
        private function setContentType(){
            switch ($this->_request->_data['extension']){
            case 'jpg':
                $this->_headers[] = 'Content-Type: image/jpeg ';
                break;
            case 'jpeg':
                $this->_headers[] = 'Content-Type: image/jpeg ';
                break;
            case 'png':
                $this->_headers[] = 'Content-Type: image/png ';
                break;
            case 'gif':
                $this->_headers[] = 'Content-Type: image/gif ';
                break;
            }
        }
        private function getPath($name){
            $arr = explode('/', $name);
            return implode('/', 
                array_map(function($str){
                    return ucfirst($str);
                }, 
                $arr)
            );
        }
    }
}
