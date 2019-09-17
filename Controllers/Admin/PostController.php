<?php
declare(strict_types=1);
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\PostModel;
    class PostController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Post.html';
            $model = new PostModel(array_merge($this->_request->_data, array('count' => 1)));
            $this->_body = $this->render($file, $model->getViews());

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setEtag();
            $this->setLastModifiedSince(filemtime($file));
        }
    }
}
