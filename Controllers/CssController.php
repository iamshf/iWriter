<?php
/**
 * Description of Js
 *
 * @author shf
 */
declare(strict_types=1);
namespace iWriter\Controllers 
{
    class CssController extends \MiniRest\Resource{
        public function getCss() {
            $file = '../Styles/' . $this->getPath($this->_request->_data['name']) . '.css';
            $this->setCacheControl('max-age=' . 365 * \Conf::CACHE_EXPIRE);
            $this->setLastModifiedSince(filemtime($file));
            $this->_headers[] = 'X-Accel-Redirect: ' . mb_substr($file, 2, null, 'UTF-8');
        }
        private function getPath(string $name){
            $arr = explode('/', $name);
            return implode('/', array_map(function($str){
                return ucfirst($str);
            }, $arr));
        }
    }
}
