<?php
declare(strict_types=1);
namespace iWriter\Controllers 
{
    use iWriter\Controllers\Resource;
    use iWriter\Models\Admin\PostModel;
    use iWriter\Models\IndexModel;
    class RecorderDemoController extends Resource {
        public function getHtml() {
            $file = '../Views/RecorderDemo.html';
            $model = new IndexModel($this->_request->_data);
            $views = $model->getViews();
            $this->_body = $this->render($file, $model->getViews());

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setETag();
            $this->setLastModifiedSince(filemtime($file));
        }
    }
}
