<?php
declare(strict_types=1);
namespace iWriter\Models\Admin 
{
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    class RelCategoryPostModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function get(): ?array {
            $sqlWhere = $params = array();
            if($this->verifyPostId()) {
                $sqlWhere[] = 'post_id = :post_id';
                $params[':post_id'] = array('value' => $this->_data['post_id'], 'dataType' => \PDO::PARAM_INT);
            }
            $sql = 'select ' . ($this->verifyColumns() ? $this->_data['columns'] : 'post_id, category_id') . ' from rel_category_post ' . (!empty($sqlWhere) ? ' where ' . implode(' and ', $sqlWhere) : '');
            $sth = MyPdo::init('r')->query($sql, $params);
            $result = ($this->_data['count'] ?? 0) == 1 ? $sth->fetch(\PDO::FETCH_ASSOC) : $sth->fetchAll(\PDO::FETCH_ASSOC);
            return is_array($result) && !empty($result) ? $result : null;
        }
        public function save(): bool {
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
        private function verifyPostId(): bool {
            return array_key_exists('post_id', $this->_data) && is_numeric($this->_data['post_id']) && $this->_data['post_id'] > 0;
        }
        private function verifyColumns(): bool {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
    }
}
