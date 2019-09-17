<?php
declare(strict_types=1);
namespace iWriter\Common
{
    /**
     * PDO类
     * Date 2014-10-29
     * @version Release:1.0.0
     * @author 盛浩锋
     */
    class MyPdo {
        private static $_instance = array();
        private $_dbh = NULL;
        private $_sth = NULL;

        /**
         * 初始化PDO对象；
         * 初始化默认数据库链接字符串配置统一为根命名空间的\Conf::dbInfo；
         * 配置示例：
         * class Conf { 
         *      const DB_CONF = array(
         *          'DB_RW' => array('server' => '127.0.0.1', 'database' => 'cards', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
         *          'DB_R' => array(
         *              array('server' => '127.0.0.1', 'database' => 'cards', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
         *              array('server' => '127.0.0.1', 'database' => 'cards', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
         *          )
         *      );
         * }
         * @param string $type rw--默认读写；r-只读
         * @return multitype:当前实例
         */
        public static function init(string $type = 'rw'): self {
            $db_conf = \Conf::DB_CONF['DB_RW'];
            if($type == 'r' && array_key_exists('DB_R', \Conf::DB_CONF) && ($count = count(\Conf::DB_CONF['DB_R'])) > 0) {
                $db_conf = $count > 1 ? \Conf::DB_CONF['DB_R'][mt_rand(0, $count - 1)] : \Conf::DB_CONF['DB_R'][0];
            }
            $db_conf['charset'] = $db_conf['charset'] ?? 'utf8';
            return self::getInstance($db_conf);
        }

        /**
         * 创建一个新的数据链接
         * @param string $server 服务器
         * @param string $database 库名
         * @param string $user 用户名
         * @param string $password 密码
         * @param string $dbtype 数据库类型，默认为mysql
         * @param string $charset='utf8'，默认编码UTF8
         * @return multitype:当前实例
         */
        public static function create(string $server, string $database, string $user, string $password, string $dbtype='mysql', string $charset='utf8'): self {
            return self::getInstance(array('server' => $server, 'database' => $database, 'username' => $user, 'password' => $password, 'dbtype' => $dbtype, 'charset' => $charset));
        }
        public function prepare(string $query, array $options = array()): \PDOStatement {
            $this->_sth = $this->_dbh->prepare($query, $options);
            return $this->_sth;
        }
        public function bindValues(array $params): bool {
            $result = true;
            foreach($params as $k => $v) {
                $result = $result && ((!empty($k) && substr($k, 0, 1) == ':') || is_numeric($k)) && $this->_sth->bindValue($k, $v['value'], $v['dataType']);
            }
            return $result;
        }
        public function bindParams(array $params): bool {
            foreach($params as $k => $v) {
                $this->_sth->bindParam($k, $v['value'], $v['dataType'], ($v['length'] ?? null));
            }
        }
        public function execute(array $params=array()): \PDOStatement {
            $this->bindValues($params);
            $this->_sth->execute();
            return $this->_sth;
        }
        public function query(string $query, array $params = array()): \PDOStatement {
            $this->prepare($query);
            return $this->execute($params);
        }
        public function exec(string $query, array $params=array()): int{
            $this->prepare($query);
            return $this->execute($params)->rowCount();
        }
        public function quote(string $value, int $paramType=\PDO::PARAM_STR) {
            return $this->_dbh->quote($value,$paramType);
        }
        public function lastInsertId(?string $name = NULL): string {
            return $this->_dbh->lastInsertId($name);
        }
        public function isExists(string $query, array $params): bool{
            return $this->exec($query,$params) > 0;
        }
        public function closeCursor(): bool {
            $this->_sth->closeCursor();
        }
        public function closeConnection(){
            $this->_dbh = null;
        }
        public function setAttribute(int $name, $value): bool {
            return $this->_dbh->setAttribute($name, $value);
        }
        public function getObj() {
            return $this->_dbh;
        }

        private function __construct(array $db_conf) {
            $db_conf['dsn'] = $this->getDSN($db_conf);
            $this->_dbh = new \PDO($db_conf['dsn'], $db_conf['username'], $db_conf['password'], array(\PDO::MYSQL_ATTR_INIT_COMMAND=>"set names '{$db_conf['charset']}'"));
        }
        private static function getInstance(array $db_conf): self {
            $k = hash('md5', $db_conf['server'] . '_' . $db_conf['database'] . '_' . $db_conf['dbtype'] . '_' . $db_conf['username'] . '_' . $db_conf['charset']);
            return self::$_instance[$k] ?? self::$_instance[$k] = new self($db_conf);
        }
        private function getDSN(array $db_conf): string{
            switch($db_conf['dbtype']){
                case 'mysql' : 
                    $dsn = 'mysql:host=' . $db_conf['server'] . ';dbname=' . $db_conf['database']; 
                    break;
                default : 
                    $dsn = 'mysql:host=' . $db_conf['server'] . ';dbname=' . $db_conf['database']; 
                    break;
            }
            return $dsn;
        }
        function __destruct() {
            $this->_sth = null;
            $this->_dbh = null;
        }
        private function __clone(){}
    }
}
