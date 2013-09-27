<?
    class Prototype {
        public $db = null;
        public $models = array();
        public function __construct(){
            $this->db = System::getDB();
        }

        public function fatal($msg){
            System_Core_Exception::fatal($msg, get_class($this));
        }

        public function __toString(){
            return get_class($this);
        }

        public function getTemplateCaller(){
            $caller = debug_backtrace(false);
            return str_replace(ROOT, "", $caller[1]['file']);
        }

        protected static function getDir($class = false) {
            if($class === false){
                $class = get_class($this);
            }
            $reflector = new ReflectionClass($class);
            return dirname($reflector->getFileName());
        }

        protected function loadModel($model){
            include $this->getDir().DIRECTORY_SEPARATOR.preg_replace("/[^-_a-z0-9]/i","",$model).".php";
            $this->models[] = $m = new $model();
            return $m;
        }

        public function getModel($model){
            if(!isset($this->models[$model])){
                $this->loadModel($model);
            }
            return $this->models[$model];
        }
    }