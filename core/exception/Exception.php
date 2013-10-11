<?
    class Gwee_Core_Exception {
        public static $errors = array();

        private static $html = null;

        public static function fatal($msg, $class=false){
            if($class === false){
                self::$errors[] = $msg;
            }else{
                self::$errors[] = sprintf($msg, $class);
            }
        }

        public static function hasErrors(){
            return count(self::$errors)>0;
        }

        public static function output(){
            ob_start();
            include "view.php";
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        }
    }