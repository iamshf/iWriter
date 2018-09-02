<?php
namespace iWriter\Models {
    use iWriter\Models\Admin\CategoryModel;
    class IndexModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function getViews(){
            $views = array('posts' => array());
            $posts = $this->get();
            $categories = $this->getCategories();
            return array(
                'posts' => ($posts !== false && !empty($posts) ? $posts : array()),
                'categories' => ($categories !== false && !empty($categories) ? $categories : array())
            );
        }
        public function get() {
            $model = new \iWriter\Models\Admin\PostModel(
                array_merge(
                    array(
                        'columns' => 'id,title,subtitle,foreword,content,gmt_modify'
                    ),
                    $this->_data
                )
            );
            return $model->get();
        }

        private function getCategories(){
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return $model->get();
        }
    }
}
