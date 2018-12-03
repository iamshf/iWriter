<?php
/**
 * Description of Js
 *
 * @author shf
 */
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    class JsController extends Resource{
        public function exec() {
            $this->getJs();
        }
        public function getJs(){
            $file = '../Scripts/' . $this->getPath($this->_request->_data['name']) . '.js';
            $expire = \Conf::CACHE_EXPIRE * 365;//缓存一天
            $this->_headers[] = 'Content-Type: application/javascript';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_headers[] = 'X-Accel-Redirect: ' . mb_substr($file, 2, null, 'UTF-8');
        }
        private function getPath($name){
            if(mb_strpos($name, 'jQueryPlugin', 0, 'UTF-8') !== false) {
                return $name;
            }
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
