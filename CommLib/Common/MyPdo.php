<?php

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
            private $_connObj = NULL;
            private $_sth = NULL;

            private function __construct($db_conf) {
                $db_conf['dsn'] = $this->getDSN($db_conf);
                $this->_connObj = new \PDO($db_conf['dsn'], $db_conf['username'], $db_conf['password'], array(\PDO::MYSQL_ATTR_INIT_COMMAND=>"set names '{$db_conf['charset']}'"));
            }

            private static function getInstanse($db_conf){
                $key = $db_conf['server'] . '_' . $db_conf['database'] . '_' . $db_conf['dbtype'] . '_' . $db_conf['username'] . '_' . $db_conf['charset'];
                if(empty(self::$_instance[$key])){
                    self::$_instance[$key] = new self($db_conf);
                }
                return self::$_instance[$key];
            }

            private function bindValues($params){
                if(count($params) > 0){
                    $key = array_keys($params);
                    if(!is_numeric($key[0]) && (substr($key[0], 0 , 1) == ':')){
                        foreach ($params as $k => $v){
                            $this->_sth->bindValue($k, $v['value'],$v['dataType']);
                        }
                    }
                }
            }
            private function getDSN($db_conf){
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
            /**
             * 初始化PDO对象；
             * 初始化默认数据库链接字符串配置统一为根命名空间的\Conf::dbInfo；
             * 配置示例(编码为UTF8)：class Conf{const DB_INFO = '{"dsn":"mysql:host=127.0.0.1;dbname=test;","user":"123","password":"123"}'}
             * 编码不为UTF8时：class Conf{const DB_INFO = '{"dsn":"mysql:host=127.0.0.1;dbname=test;","user":"123","password":"123","charset":"gbk"}'}
             * @param string $type rw--默认读写；r-只读
             * @return multitype:当前实例
             */
            public static function init($type = 'rw') {
                $db_conf = \Conf::$DB_CONF['DB_RW'];

                if($type == 'r' && array_key_exists('DB_R', \Conf::$DB_CONF)) {
                    $db_r_count = count(\Conf::$DB_CONF['DB_R']);
                    if($db_r_count == 1){
                        $db_conf = \Conf::$DB_CONF['DB_R'][0];
                    }
                    else {
                        $ind = mt_rand(0, ($db_r_count - 1));
                        $db_conf = \Conf::$DB_CONF['DB_R'][$ind];
                    }
                }

                if(!array_key_exists('charset', $db_conf) || empty($db_conf['charset'])){
                    $db_conf['charset'] = 'utf8';
                }

                return self::getInstanse($db_conf);
            }
            
            /**
             * 创建一个新的数据链接
             * @param string $dsn 连接字符串
             * @param string $user 用户名
             * @param string $password 密码
             * @param string $charset='utf8'，默认编码UTF8
             * @return multitype:当前实例
             */
            public static function create($server,$database,$user,$password,$dbtype='mysql',$charset='utf8'){
                return self::getInstanse(array(
                    'server' => $server, 
                    'database' => $database, 
                    'username' => $user, 
                    'password' => $password, 
                    'dbtype' => $dbtype, 
                    'charset' => $charset));
            }
            public function __clone(){
                throw new \Exception('Class MyPdo can not be cloned');
            }

            public function prepare($query,$options=array()){
                $this->_sth = $this->_connObj->prepare($query,$options);
            }
            public function execute($params=array()){
                $this->bindValues($params);
                $this->_sth->execute();
                return $this->_sth;
            }
            public function query($query,$params = array()){
                $this->prepare($query);
                return $this->execute($params);
            }
            public function exec($query,$params=array()){
                $this->prepare($query);
                return $this->execute($params)->rowCount();
            }
            public function quote($value,$paramType=\PDO::PARAM_STR){
                return $this->_connObj->quote($value,$paramType);
            }
            public function lastInsertId($name=NULL){
                return $this->_connObj->lastInsertId($name);
            }
            public function isExists($query,$params){
                return $this->exec($query,$params) > 0;
            }
            public function closeCursor(){
                $this->_sth->closeCursor();
            }
            public function closeConnection(){
                $this->_connObj = null;
            }

            public function setAttribute($name, $value) {
                $this->_connObj->setAttribute($name, $value);
            }
            public function getObj() {
                return $this->_connObj;
            }
            function __destruct() {
                $this->_sth = null;
                $this->_connObj = null;
            }
        }
    }
?>
