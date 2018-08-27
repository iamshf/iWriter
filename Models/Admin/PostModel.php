<?php
namespace iWriter\Models\Admin {
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    class PostModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function verifyId() {
            return array_key_exists('id', $this->_data) && is_numeric($this->_data['id']) && $this->_data['id'] > 0;
        }
        public function verifyContent() {
            return array_key_exists('content', $this->_data) && !empty($this->_data['content']);
        }
        public function get() {
            $columns = $this->verifyColumns() ? $this->_data['columns'] : 'id,title,subtitle,foreword,content';
            $sql = 'select ' . $columns . ' from post';

            $sqlWhere = array();
            $params = array();
            $count = $this->verifyCount() ? $this->_data['count'] : 20;

            if($this->verifyId()) {
                $sqlWhere[] = 'id = :id';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
                $count = 1;
            }
            if($this->verifyTitle()) {
                $sqlWhere[] = 'title like :title';
                $params[':title'] = array('value' => ('%' . $this->_data['name'] . '%'), 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifySearch()) {
                $sqlWhere[] = '(title like :search or subtitle like :search or foreword like :search or content like :search)';
                $params[':search'] = array('value' => ('%' . $this->_data['name'] . '%'), 'dataType' => \PDO::PARAM_STR);
            }
            if(count($sqlWhere) > 0) {
                $sql .= ' where ' . implode(' and ', $sqlWhere);
            }
            $sql .= ' order by gmt_modify desc limit :count';
            $params[':count'] = array('value' => (int)$count, 'dataType' => \PDO::PARAM_INT);

            $result = MyPdo::init('r')->query($sql, $params);
            $rowCount = $result->rowCount();
            return $rowCount > 0 ? ($rowCount == 1 ? ($count == 1 ? $result->fetch(\PDO::FETCH_ASSOC) : array($result->fetch(\PDO::FETCH_ASSOC))) : $result->fetchAll(\PDO::FETCH_ASSOC)) : false;
        }
        public function add(){
            $sql = 'insert into post (title, subtitle, foreword, content, gmt_add, gmt_modify, status) values (:title, :subtitle, :foreword, :content, from_unixtime(:gmt_add), from_unixtime(:gmt_modify), :status)';
            $params = array(
                ':title' => array('value' => ($this->verifyTitle() ? $this->_data['title'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':subtitle' => array('value' => ($this->verifySubtitle() ?  $this->_data['subtitle'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':foreword' => array('value' => ($this->verifyForeword() ?  $this->_data['foreword'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':content' => array('value' => ($this->verifyContent() ?  $this->_data['content'] : ''), 'dataType' => \PDO::PARAM_STR),
                ':gmt_add' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR),
                ':gmt_modify' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR),
                ':status' => array('value' => $this->verifyStatus() ? $this->_data['status'] : 1, 'dataType' => \PDO::PARAM_INT)
            );
            $myPdo = MyPdo::init();
            $myPdo->exec($sql, $params);
            return $myPdo->lastInsertId();
        }
        public function update(){
            $sql = 'update post set gmt_modify = from_unixtime(:gmt_modify)';
            $values = array();
            $params = array(
                ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT),
                ':gmt_modify' => array('value' => $_SERVER['REQUEST_TIME'], 'dataType' => \PDO::PARAM_STR)
            );
            if($this->verifyTitle()) {
                $sql .= ', title = :title';
                $params[':title'] = array('value' => $this->_data['title'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifySubtitle()) {
                $sql .= ', subtitle = :subtitle';
                $params[':subtitle'] = array('value' => $this->_data['subtitle'], 'dataType' => \PDO::PARAM_INT);
            }
            if($this->verifyForeword()) {
                $sql .= ', foreword = :foreword';
                $params[':foreword'] = array('value' => $this->_data['foreword'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyContent()) {
                $sql .= ', content = :content';
                $params[':content'] = array('value' => $this->_data['content'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyStatus()) {
                $sql .= ', status = :status';
                $params[':status'] = array('value' => $this->_data['status'], 'dataType' => \PDO::PARAM_STR);
            }

            $sql .= ' where id = :id';
            //var_dump($sql, $params);exit;
            return MyPdo::init()->exec($sql, $params);
        }

        private function verifyTitle() {
            return array_key_exists('title', $this->_data);
        }
        private function verifySubtitle() {
            return array_key_exists('subtitle', $this->_data);
        }
        private function verifyForeword() {
            return array_key_exists('foreword', $this->_data);
        }
        private function verifySearch() {
            return array_key_exists('search', $this->_data) && !empty($this->_data['search']);
        }
        private function verifyColumns() {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
        private function verifyStatus(){
            return array_key_exists('status', $this->_data) && in_array($this->_data['status'], array(0, 1, 2));
        }
        private function verifyCount(){
            return array_key_exists('count', $this->_data) && is_numeric($this->_data['count']);
        }
    }
}