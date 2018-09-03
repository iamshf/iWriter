<?php
namespace iWriter\Models\Admin {
    use iWriter\Models\Admin\CategoryModel;
    class PostsModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function getViews() {
            $categories = $this->getCategories();
            return array(
                'categories' => $categories !== false && !empty($categories) ? $categories : array()
            );
        }
        private function getCategories() {
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return $model->get();
        }
    }
}
