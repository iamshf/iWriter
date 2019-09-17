<?php
declare(strict_types=1);
namespace iWriter\Models\Admin 
{
    use iWriter\Common\MyPdo;
    use iWriter\Common\Validate;
    class UserModel {
        private $_data;
        public function __construct(array $data = array()) {
            $this->_data = $data;
        }
        public function verifyName(): bool {
            return array_key_exists('name', $this->_data) && Validate::username($this->_data['name']);
        }
        public function verifyPwd(): bool {
            return array_key_exists('pwd', $this->_data) && Validate::pwd($this->_data['pwd']);
        }
        public function comparePwd($pwd, $hash): bool {
            return \password_verify($pwd, $hash);
        }
        public function get(): ?array {
            $sqlWhere = $params = array();
            $count = $this->verifyCount() ? $this->_data['count'] : 20;
            $params[':count'] = array('value' => (int)$count, 'dataType' => \PDO::PARAM_INT);
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
            $sql = 'select ' . ($this->verifyColumns() ? $this->_data['columns'] : 'id,name') . ' from user ' . (!empty($sqlWhere) ? ' where ' . implode(' and ', $sqlWhere) : '') . ' limit :count';
            $sth = MyPdo::init('r')->query($sql, $params);
            $result = ($this->_data['count'] ?? 0) == 1 ? $sth->fetch(\PDO::FETCH_ASSOC) : $result->fetchAll(\PDO::FETCH_ASSOC);
            return is_array($result) && !empty($result) ? $result : null;
        }
        public function add(): int {
            $sql = 'insert into user (name, pwd) values (:name, :pwd)';
            $params = array(
                ':name' => array('value' => $this->_data['name'], 'dataType' => \PDO::PARAM_STR),
                ':pwd' => array('value' => password_hash($this->_data['pwd'], \PASSWORD_BCRYPT), 'dataType' => \PDO::PARAM_STR)
            );
            $myPdo = MyPdo::init();
            $myPdo->exec($sql, $params);
            return (int)($myPdo->lastInsertId());
        }
        public function update(): int {
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
                $sql = 'update user set ' . implode(', ', $values) . ' where id = :id';
                return MyPdo::init()->exec($sql, $params);
            }
            return 0;
        }

        private function verifyId(): bool {
            return array_key_exists('id', $this->_data) && is_numeric($this->_data['id']) && $this->_data['id'] > 0;
        }
        private function verifyColumns(): bool {
            return array_key_exists('columns', $this->_data) && Validate::sqlParam($this->_data['columns']);
        }
        private function verifyCount(): bool {
            return array_key_exists('count', $this->_data) && (is_numeric($this->_data['count']) || $this->_data['count'] == '*');
        }
    }
}
