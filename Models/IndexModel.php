<?php
namespace iWriter\Models {
    use iWriter\Models\Admin\CategoryModel;
    class IndexModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
            !$this->verifyCount() && $this->_data['count'] = 2;
        }

        public function getViews(){
            $posts = $this->get();
            $categories = $this->getCategories();
            return array (
                'posts' => ($posts !== false && !empty($posts) ? $posts : array()),
                'categories' => ($categories !== false && !empty($categories) ? $categories : array()),
                'count' => $this->_data['count'],//首次加载数量
                'start_lt_time' => count($posts) > $this->_data['count'] ? $posts[(int)$this->_data['count'] - 1]['gmt_modify'] : ''
            );
        }
        public function get() {
            $params = array_merge(array(
                    'columns' => 'id,title,subtitle,foreword,content,gmt_add,gmt_modify',
                    'status' => 1
                ),
                $this->_data
            );
            $params['count']++;
            $model = new \iWriter\Models\Admin\PostModel($params);
            return $model->get();
        }

        private function verifyCount(){
            return array_key_exists('count', $this->_data) && is_numeric($this->_data['count']);
        }
        private function getCategories(){
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return $model->get();
        }
    }
}
