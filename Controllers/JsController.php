<?php
/**
 * Description of Js
 *
 * @author shf
 */
declare(strict_types=1);
namespace iWriter\Controllers 
{
    use iWriter\Controllers\Resource;
    class JsController extends Resource{
        public function exec(?string $methodName = NULL) {
            $file = '../Scripts/' . $this->getPath($this->_request->_data['name']) . '.js';
            $this->_headers[] = 'Content-Type: application/x-javascript; charset=utf-8';
            $this->setCacheControl('max-age=' . 365 * \Conf::CACHE_EXPIRE);
            $this->setLastModifiedSince(filemtime($file));
            $this->_headers[] = 'X-Accel-Redirect: ' . mb_substr($file, 2, null, 'UTF-8');
        }
        private function getPath(string $name){
            if(mb_strpos($name, 'jQueryPlugin', 0, 'UTF-8') !== false) {
                return $name;
            }
            $arr = explode('/', $name);
            return implode('/', array_map(function(string $str) {
                return ucfirst($str);
            }, $arr));
        }
    }
}
