<?
    class Gwee_Prototype {
        public $title = '';
        public $db = null;
        public $models = array();
        public function __construct(){
            $this->db = Gwee::getDB();
        }

        public function fatal($msg){
            Gwee_Core_Exception::fatal($msg, get_class($this));
        }

        public function __toString(){
            if($this->title === ''){
                $this->title = get_class($this);
            }
            return $this->title;
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