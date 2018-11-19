<?php
namespace iWriter\Models {
    use iWriter\Models\Admin\CategoryModel;
    class PostModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function getViews(){
            return array_merge(
                $this->getCategories(),
                $this->getPost()
            );
        }

        private function getCategories(){
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            $categories = $model->get();

            return array('categories' => ($categories !== false && !empty($categories) ? $categories : array()));
        }
        private function getPost(){
            $model = new \iWriter\Models\Admin\PostModel(
                array_merge(
                    array('columns' => 'id,title,subtitle,foreword,gmt_add,gmt_modify,content', 'count' => 1), 
                    $this->_data
                )
            );
            $result = $model->get();
            return $result !== false && !empty($result) ? $result : array('id' => '', 'title' => '', 'subtitle' => '', 'foreword' => '', 'gmt_modify' => '', 'content' => '');
        }
    }
}
