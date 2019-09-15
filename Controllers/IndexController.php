<?php
declare(strict_types=1);
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
            $this->_body = $this->render($file, $views);

            $this->setCacheControl('max-age=' . \Conf::CACHE_EXPIRE);
            $this->setETag();
            !empty($views) && !empty($views['posts']) && $this->setLastModifiedSince(strtotime($views['posts'][0]['gmt_modify']));
        }
        public function getJson() {
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
