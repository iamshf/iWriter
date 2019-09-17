<?php
declare(strict_types=1);
class Conf{
    public const DB_CONF = array(
        'DB_RW' => array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
        'DB_R' => array(
            array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8'),
            array('server' => '127.0.0.1', 'database' => 'iWriter', 'username' => 'root', 'password' => 'root', 'dbtype' => 'mysql', 'charset' => 'utf8')
        )
    );
    public const MEM_CONF = array(
        array('127.0.0.1', 11211),
    );

    const IS_DEBUG = false;
    const CACHE_EXPIRE = 86400;
    const CONTROLLER_NAMESPACE = '\\iWriter\\Controllers\\';
    const CONTROLLER_SUFFIX = 'Controller';
    const UPLOAD_PATH = '';

    const SITE_NAME = '';
    const SITE_VERSION = '';
    const SITE_AUTHOR = '';
    const SITE_INFO = '';

    public function init(){
        spl_autoload_register(array($this,"autoload"));
        $this->setResource();
        $this->setError();
    }
    public function autoload(string $classname){
        $path = dirname(__FILE__) . '/../../' .
            strtr($classname, array(
                'MiniRest\\' => 'CommLib/MiniRest/Class/',
                'iWriter\\Common' => 'CommLib/Common/',
                'iWriter\\'=>'',
                "\\"=>"/"
            )) . '.php';
        file_exists($path) && require_once($path);
        if(!file_exists($path) && self::IS_DEBUG) { echo '<br />', $classname, '<br />', $path, '<br />'; }
    }
    private function setError() {
        $is_debug = self::IS_DEBUG === true || (($_REQUEST['is_debug'] ?? 0) == 1);
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', ($is_debug ? 'On' : 'Off'));

        $is_debug && register_shutdown_function(function() {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $debug_log = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . ($_SERVER['HTTP_ACCEPT'] ?? '') . ' ' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' ' . $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' ' . (!empty($_REQUEST) && json_encode($_REQUEST));
            $this->addDebugLog($debug_log);
        });
        set_error_handler(function($errno, $errstr, $errfile, $errline) use($is_debug){
            $this->addDebugLog(date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $errno . ': ' . $errstr . ' ' . $errfile . ' ' . $errline);
            return !$is_debug;
        });
        set_exception_handler(function(Throwable $e){
            $this->addDebugLog(date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $e->getCode() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getTraceAsString() . ' ' . $e->getMessage());
        });
    }
    private function addDebugLog(string $log = '') {
        if(self::IS_DEBUG) { echo $log; }
        if(($file = fopen(dirname(__FILE__) . '/../../Log/iWriter.log', 'a')) !== false) {
            fwrite($file, $log . "\r\n");
            fclose($file);
        }
    }

    private function setResource(){
        $route = \MiniRest\Route::getInstance();
        $route->addRoutes(
            array(
                'post' => array(
                    'url' => '/\/(?<controller>(admin\/)?post)\/(?<id>\d+)(\/(?<status>(-1|0|1|2)))?/',
                    'status' => true
                ),
                'css' => array(
                    'url' => '/\/(?<controller>(css|js))\/(?<name>[a-zA-Z0-9\/.]+)/',
                    'status' => true
                ),
                'image' => array(
                    'url' => '/\/(?<controller>image|upload)\/(?<path>[\w\/\.]+\.(?<extension>(jpg|gif|png|svg|mp3|mp4)))\w?/',
                    'status' => true
                ),
                'index' => array(
                    'url' => '/\/((?<controller>(index))\/)?(?<category_id>\d+)/',
                    'status' => true
                )
            )
        );
    }
}
$conf = new Conf();
$conf->init();
