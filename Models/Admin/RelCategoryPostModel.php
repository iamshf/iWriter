<?php
namespace iWriter\Models\Admin {
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    class RelCategoryPostModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function get() {
            $columns = $this->verifyColumns() ? $this->_data['columns'] : 'post_id, category_id';
            $sql = 'select ' . $columns . ' from rel_category_post';
            $sqlWhere = array();
            $params = array();
            if($this->verifyPostId()) {
                $sqlWhere[] = 'post_id = :post_id';
                $params[':post_id'] = array('value' => $this->_data['post_id'], 'dataType' => \PDO::PARAM_INT);
            }

            if(count($sqlWhere) > 0) {
                $sql .= ' where ' . implode(' and ', $sqlWhere);
            }
            $result = MyPdo::init('r')->query($sql, $params);
            $rowCount = $result->rowCount();
            return $rowCount > 0 ? ($rowCount == 1 ? $result->fetch(\PDO::FETCH_ASSOC) : $result->fetchAll(\PDO::FETCH_ASSOC)) : false;
        }

        public function save() {
            $myPdo = MyPdo::init();
            foreach($this->_data['category_ids'] as $category_id) {
                $myPdo->exec(
                    'insert into rel_category_post (post_id, category_id) values (:post_id, :category_id)',
                    array(
                        ':post_id' => array('value' => $this->_data['post_id'], 'dataType' => \PDO::PARAM_INT),
                        ':category_id' => array('value' => $category_id, 'dataType' => \PDO::PARAM_INT)
                    )
                );
            }
            $myPdo->exec(
                'delete from rel_category_post where post_id = :post_id and category_id not in (' . implode(', ', $this->_data['category_ids']) . ')',
                array(':post_id' => array('value' => $this->_data['post_id'], 'dataType' => \PDO::PARAM_INT))
            );

            return true;
        }

        private function verifyPostId() {
            return array_key_exists('post_id', $this->_data) && is_numeric($this->_data['post_id']) && $this->_data['post_id'] > 0;
        }
        private function verifyColumns() {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
    }
}
