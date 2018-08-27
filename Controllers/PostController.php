<?php
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\PostModel;
    class PostController extends Resource {
        public function getHtml() {
            $file = '../Views/Post.html';
            $expire = \Conf::CACHE_EXPIRE * 86400 ;//缓存一天
            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';

            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;

            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime($file)) . ' GMT';
            $model = new PostModel(
                array_merge(
                    array('columns' => 'id,title,subtitle,foreword,gmt_modify,content', 'count' => 1), 
                    $this->_request->_data
                )
            );
            $views = $model->get();
            $this->_body = $this->render($file, $views);
        }
        public function getJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new PostModel(array_merge(array('columns' => 'id,title,subtitle,foreword,gmt_modify'), $this->_request->_data));
            $result = $model->get();
            if($result !== false && !empty($result)) {
                $this->_body = $this->getJsonResult(1, '成功', 200, $result);
            }
            else {
                $this->_body = $this->getJsonResult(0, '请求非法', 404);
            }
        }
    }
}
