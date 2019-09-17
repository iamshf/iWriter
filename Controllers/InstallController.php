<?php
declare(strict_types=1);
namespace iWriter\Controllers 
{
    use iWriter\Controllers\Resource;
    class InstallController extends Resource {
        public function getHtml() {
            $file = '../Views/Install.html';
            $this->_body = $this->render($file);

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setETag();
            $this->setLastModifiedSince(filemtime($file));
        }
        public function postJson() {
            $this->_body = $this->getJsonResult(1, '成功');
        }
    }
}
