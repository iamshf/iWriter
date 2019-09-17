<?php
declare(strict_types=1);
namespace iWriter\Models\Admin 
{
    use iWriter\Models\Admin\CategoryModel;
    class PostsModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function getViews(): array {
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();

            return array(
                'categories' => !empty($categories = $model->get()) ? $categories : array()
            );
        }
    }
}
