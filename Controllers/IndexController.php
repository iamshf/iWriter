<?php
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\PostModel;
    use iWriter\Models\IndexModel;
    class IndexController extends Resource {
        public function getHtml() {
            $file = '../Views/Index.html';
            $expire = \Conf::CACHE_EXPIRE;
            $model = new IndexModel($this->_request->_data);
            $views = $model->getViews();

            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';
            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;
            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", (!empty($views['posts']) ? strtotime($views['posts'][0]['gmt_modify']) : filemtime($file))) . ' GMT';

            $this->_body = $this->render($file, $model->getViews());
        }
        public function getJson() {
            $this->_headers[] = 'Content-Type: application/json';
            $model = new PostModel(array_merge(array('columns' => 'id,title,subtitle,foreword,gmt_add,gmt_modify'), $this->_request->_data));
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
