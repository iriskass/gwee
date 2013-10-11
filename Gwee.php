<?
    class Gwee {
        public static $db = null;
        public static $view = '';
        public static $view3 = '';

        public static function getDB(){
            if(self::$db === null){
                self::$db = new Gwee_Core_DB();
            }
            return self::$db;
        }

        public static function compile(){
            if(Gwee_Core_Exception::hasErrors()){
                echo Gwee_Core_Exception::output();
            }else{
                echo self::$view;
            }
        }
    }