<?php
    class Conf{
        /*
         * php 5.4 不支持使用复杂类型的const常量，暂时使用静态变量代替
         */
        public static $DB_CONF = array(
            'DB_RW' => array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
            'DB_R' => array(
                array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
                array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8')
            )
        );
        public static $MEM_CONF = array(
            array('127.0.0.1', 11211),
        );

        const IS_DEBUG = true;
        const CACHE_EXPIRE = 0;
        const CONTROLLER_NAMESPACE = '\\iWriter\\Controllers\\';
        const CONTROLLER_SUFFIX = 'Controller';

        const FILE_PATH = '/home/shf/Documents/Work/Project/iWriter/';
        //const FILE_PATH = '/home/shf/mydata/documents/work/project/iWriter/';
        const SITE_NAME = '';
        const SITE_VERSION = '';
        const SITE_AUTHOR = '';
        const SITE_INFO = '';

        public function init(){
            $this->setError();
            spl_autoload_register(array($this,"autoload"));
            $this->setResource();
        }

        /**
         * 设置自动包含
         */
        public function autoload($classname){
            $path = Conf::FILE_PATH .
            strtr($classname, array(
                'MiniRest\\' => 'CommLib/MiniRest/Class/',
                'iWriter\\Common' => 'CommLib/Common/',
                'iWriter\\'=>'',
                "\\"=>"/"
            )).'.php';
            if(file_exists($path)) {
                require_once $path;
            }
            else {
                echo '<br />',$classname,'<br />', $path,'<br />';
                exit;
            }
        }
        /**
         * 设置错误显示级别
         */
        private function setError() {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        }

        private function setResource(){
            $route = new MiniRest\Route();
            $route->addRoutes(
                array(
                    'post' => array(
                        'url' => '/(?<controller>(admin/)?post)/(?<id>\d+)(/(?<status>(-1|0|1|2)))?',
                        'status' => true
                    ),
                    'css' => array(
                        'url' => '/(?<controller>(css|js))/(?<name>[a-zA-Z0-9/.]+)',
                        'status' => true
                    ),
                    'image' => array(
                        'url' => '/(?<controller>image|upload)/(?<path>[\w/\.]+\.(?<extension>(jpg|gif|png|svg|mp3|mp4)))\w?',
                        'status' => true
                    ),
                    'index' => array(
                        'url' => '/((?<controller>(index))/)?(?<category_id>\d+)',
                        'status' => true
                    ),
                    'rootDire' => array(
                        'url' => '/(?<controller>[a-zA-Z0-9/_]+)',
                        'status' => true
                    )
                )
            );
        }
    }
    $conf = new Conf();
    $conf->init();
