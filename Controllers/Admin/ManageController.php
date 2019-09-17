<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin 
{
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\UserModel;
    class ManageController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Manage.html';
            $this->_body = $this->render($file);

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setEtag();
            $this->setLastModifiedSince(filemtime($file));
        }
    }
}
