<?php
namespace iWriter\Controllers\Admin {
    use iWriter\Controllers\Admin\Resource;
    use iWriter\Models\Admin\PostModel;
    class PostController extends Resource {
        public function getHtml() {
            $file = '../Views/Admin/Post.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';

            $model = new PostModel($this->_request->_data);
            $this->_body = $this->render($file, $model->getViews());
        }
    }
}
