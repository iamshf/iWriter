<?php
namespace iWriter\Models\Admin {
    use iWriter\Common\MyPdo;
    class RelCategoryPostModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
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
    }
}
