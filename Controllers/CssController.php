<?php
/**
 * Description of Js
 *
 * @author shf
 */
namespace iWriter\Controllers {
    class CssController extends \MiniRest\Resource{
        public function exec() {
            $this->getJs();
        }
        public function getJs(){
            $file = '../Styles/' . $this->getPath($this->_request->_data['name']) . '.css';
            $expire = \Conf::CACHE_EXPIRE * 365;//缓存一天
            $this->_headers[] = 'Content-Type: text/css';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $this->_headers[] = 'X-Accel-Redirect: ' . mb_substr($file, 2, null, 'UTF-8');
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
