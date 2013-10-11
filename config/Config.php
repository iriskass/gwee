<?
    class Gwee_Config {
        public static $db = array(
            'host'      => '127.0.0.1',
            'port'      => '3306',
            'user'      => 'dbuser',
            'pass'      => 'dbpassword',
            'db'        => 'dbname',
            'charset'   => 'utf8'
        );

        public static function getDB(){
            $db_path = dirname(__file__).DIRECTORY_SEPARATOR."mysql.php";
            if(file_exists($db_path)){
                self::$db = include($db_path);
            }
            return self::$db;
        }
    }