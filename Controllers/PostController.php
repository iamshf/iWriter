<?php
declare(strict_types=1);
namespace iWriter\Controllers 
{
    use iWriter\Controllers\Resource;
    use iWriter\Models\PostModel;
    class PostController extends Resource {
        public function getHtml() {
            $file = '../Views/Post.html';
            $model = new PostModel($this->_request->_data);
            $views = $model->getViews();
            $this->_body = $this->render($file, $views);

            $this->setCacheControl('max-age=' . (30 * \Conf::CACHE_EXPIRE));
            $this->setETag();
            !empty($views) && $this->setLastModifiedSince(strtotime($views['gmt_modify']));
        }
    }
}
