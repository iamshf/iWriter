<?php
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    use iWriter\Models\PostModel;
    class PostController extends Resource {
        public function getHtml() {
            $file = '../Views/Post.html';
            $expire = \Conf::CACHE_EXPIRE * 30 ;//缓存一天
            $model = new PostModel($this->_request->_data);
            $views = $model->getViews();

            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';
            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;
            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", (!empty($views) ? strtotime($views['gmt_modify']) : filemtime($file))) . ' GMT';

            $this->_body = $this->render($file, $views);
        }
    }
}
