<?php
declare(strict_types=1);
namespace iWriter\Models 
{
    use iWriter\Models\Admin\CategoryModel;
    class IndexModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
            !$this->verifyCount() && $this->_data['count'] = 20;
        }

        public function getViews(): array {
            return array (
                'posts' => !empty($posts = $this->get()) ? $posts : array(),
                'categories' => !empty($categories = $this->getCategories()) ? $categories : array(),
                'count' => $this->_data['count'],//首次加载数量
                'start_lt_time' => count($posts) > $this->_data['count'] ? $posts[(int)$this->_data['count'] - 1]['gmt_modify'] : ''
            );
        }
        public function get(): ?array {
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

        private function verifyCount(): bool {
            return array_key_exists('count', $this->_data) && is_numeric($this->_data['count']);
        }
        private function getCategories(): ?array {
            $model = new CategoryModel(array('deep' => '*'));
            $model->initReadDB();
            return $model->get();
        }
    }
}
