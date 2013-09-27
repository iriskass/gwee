<?
    class System {
        public static $db = null;
        public static $view = '';

        public static function getDB(){
            if(self::$db === null){
                self::$db = new System_Core_DB();
            }
            return self::$db;
        }

        public static function compile(){
            if(System_Core_Exception::hasErrors()){
                echo System_Core_Exception::output();
            }else{
                echo self::$view;
            }
        }
    }