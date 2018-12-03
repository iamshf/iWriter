<?php
namespace iWriter\Controllers {
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\PostModel;
    use iWriter\Models\IndexModel;
    class RecorderDemoController extends Resource {
        public function getHtml() {
            $file = '../Views/RecorderDemo.html';
            $expire = \Conf::CACHE_EXPIRE;
            $model = new IndexModel($this->_request->_data);
            $views = $model->getViews();

            $this->_headers[] = 'Content-Type: text/html;charset=UTF-8';
            $this->_headers[] = 'Expires: ' . gmdate("D, d M Y H:i:s", time() + $expire) . ' GMT';
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;
            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", (!empty($views['posts']) ? strtotime($views['posts'][0]['gmt_modify']) : filemtime($file))) . ' GMT';

            $this->_body = $this->render($file, $model->getViews());
        }
    }
}
