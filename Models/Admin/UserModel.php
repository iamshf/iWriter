<?php
namespace iWriter\Models\Admin {
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    require_once dirname(__FILE__) . '/../../CommLib/PasswordCompat/lib/password.php';
    class UserModel {
        private $_data;
        public function __construct($data = array()) {
            $this->_data = $data;
        }

        public function verifyName() {
            return array_key_exists('name', $this->_data) && Validate::username($this->_data['name']);
        }
        public function verifyPwd() {
            return array_key_exists('pwd', $this->_data) && Validate::pwd($this->_data['pwd']);
        }
        public function comparePwd($pwd, $hash) {
            return \password_verify($pwd, $hash);
        }
        public function get() {
            $columns = $this->verifyColumns() ? $this->_data['columns'] : 'id,name';
            $sql = 'select ' . $columns . ' from user';

            $sqlWhere = array();
            $params = array();
            $count = $this->verifyCount() ? $this->_data['count'] : 20;

            if($this->verifyId()) {
                $sqlWhere[] = 'id = :id';
                $params[':id'] = array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT);
                $count = 1;
            }
            if($this->verifyName()) {
                $sqlWhere[] = 'name = :name';
                $params[':name'] = array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR);
                $count = 1;
            }
            if(count($sqlWhere) > 0) {
                $sql .= ' where ' . implode(' and ', $sqlWhere);
            }
            $sql .= ' limit :count';
            $params[':count'] = array('value' => (int)$count, 'dataType' => \PDO::PARAM_INT);

            $result = MyPdo::init('r')->query($sql, $params);
            $rowCount = $result->rowCount();
            return $rowCount > 0 ? ($rowCount == 1 ? $result->fetch(\PDO::FETCH_ASSOC) : $result->fetchAll(\PDO::FETCH_ASSOC)) : false;
        }
        public function add(){
            $sql = 'insert into user (name, pwd) values (:name, :pwd)';
            $params = array(
                ':name' => array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR),
                ':pwd' => array('value' => password_hash($this->_data['pwd'], \PASSWORD_BCRYPT), 'dataType' => \PDO::PARAM_STR)
            );
            $myPdo = MyPdo::init();
            $myPdo->exec($sql, $params);
            return $myPdo->lastInsertId();
        }
        public function update(){
            $sql = 'update user set ';
            $values = array();
            $params = array(
                ':id' => array('value' => $this->_data['id'], 'dataType' => \PDO::PARAM_INT)
            );
            if($this->verifyName()) {
                $values[] = 'name = :name';
                $params[':name'] = array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR);
            }
            if($this->verifyPwd()) {
                $sqlWhere[] = 'pwd = :pwd';
                $params[':pwd'] = array('value' => \password_hash($this->_data['pwd'], \PASSWORD_BCRYPT), 'dataType' => \PDO::PARAM_INT);
            }
            if(count($values) > 0) {
                $sql .= implode(', ', $values) . ' where id = :id';
                return MyPdo::init()->exec($sql, $params);
            }
            return 0;
        }

        private function verifyId() {
            return array_key_exists('id', $this->_data) && is_numeric($this->_data['id']) && $this->_data['id'] > 0;
        }
        private function verifyColumns() {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
    }
}
