<?php
declare(strict_types=1);
namespace iWriter\Models 
{
    use iWriter\Models\Admin\CategoryModel;
    class PostModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function getViews(): array {
            return array_merge(
                $this->getCategories(),
                $this->getPost()
            );
        }
        private function getCategories(): array {
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return array('categories' => !empty($categories = $model->get()) ? $categories : array());
        }
        private function getPost(): array {
            $model = new \iWriter\Models\Admin\PostModel(
                array_merge(
                    array('columns' => 'id,title,subtitle,foreword,gmt_add,gmt_modify,content', 'count' => 1), 
                    $this->_data
                )
            );
            $result = $model->get();
            return !empty($result = $model->get()) ? $result : array('id' => '', 'title' => '', 'subtitle' => '', 'foreword' => '', 'gmt_modify' => '', 'content' => '');
        }
    }
}
