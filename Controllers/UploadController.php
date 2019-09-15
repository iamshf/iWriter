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
declare(strict_types=1);
namespace iWriter\Controllers 
{
    use iWriter\Controllers\Resource;
    class UploadController extends Resource{
        public function exec(?string $methodName = NULL) {
            $file = '../Uploads/' . $this->_request->_data['path'];
            $this->setContentType();
            $this->setCacheControl('max-age=' . 365 * \Conf::CACHE_EXPIRE);
            $this->setLastModifiedSince(filemtime($file));
            $this->_headers[] = 'X-Accel-Redirect: '. mb_substr($file, 2, null, 'UTF-8');
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
                case 'lrc':
                    $this->_headers[] = 'Content-Type: text/plain;charset=UTF-8';
                    break;
                case 'txt':
                    $this->_headers[] = 'Content-Type: text/plain; charset=utf-8';
                    $this->_headers[] = 'Content-Disposition: attachment; filename='. date('YmdHis') .'.txt';
                    break;
                case 'mp3':
                    $this->_headers[] = 'Content-Type: audio/mp3';
                    break;
            }
        }
        private function getPath($name){
            $arr = explode('/', $name);
            return implode('/', array_map(function($str){
                return ucfirst($str);
            }, $arr));
        }
    }
}
