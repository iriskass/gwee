<?
    class Prototype_App extends Prototype implements Countable, ArrayAccess, IteratorAggregate {
        private $controllers = array();
        private $currentRoute = null;
        private $iterator = null;
        private $routes = null;

        private $html = null;
        private $loaded_templates = array();
        private $allowed_extensions = array('php', 'tpl', 'html', 'htm');

        public function __construct(){
            parent::__construct();
            //$this->setControllers(array());
        }

        public function setControllers($controllers = array()){
            $this->controllers = array_merge($this->controllers, $controllers);
            $this->setRoutes();
        }

        public function getController($name){
            return $this->controllers[$name];
        }

        public function current(){
            if($this->currentRoute === null){
                $this->currentRoute = key($this->controllers);
            }
            return $this->controllers[$this->currentRoute];
        }

        public function first(){
            $this->currentRoute = key($this->controllers);
            return reset($this->controllers);
        }

        public function next(){
            if($this->currentRoute === null){
                $this->currentRoute = key($this->controllers);
                return reset($this->controllers);
            }else{
                $this->currentRoute = key($this->controllers);
                $next = next($this->controllers);
                if($next){
                    return $next;
                }else{
                    return reset($this->controllers);
                }
            }
        }

        public function prev(){
            $this->currentRoute = key($this->controllers);
            $prev = prev($this->controllers);
            if($prev){
                return $prev;
            }else{
                return end($this->controllers);
            }
        }

        public function last(){
            $this->currentRoute = key($this->controllers);
            return end($this->controllers);
        }

        public function output(){
            return $this->put('view');
        }

        public function put($tpl = 'view', $ext = "php"){
            $tpl = preg_replace("/[^-_a-z0-9]/i", "", $tpl);
            if(!in_array($ext, $this->allowed_extensions)){
                $this->fatal('<h1>Error in %s class ('.$this->getTemplateCaller().')</h1>Provided template file extension "' . $ext . '" is not allowed.');
            }
            if(!isset($this->loaded_templates[$tpl])){
                ob_start();
                $path = DIRECTORY_SEPARATOR.$tpl.".".$ext;
                $dir = $this->getDir(get_class($this));
                if(file_exists($dir.$path)){
                    include $dir.$path;
                }else{
                    include $this->getDir(get_class($this->parent)).$path;
                }
                $html = ob_get_contents();
                ob_end_clean();
                $this->loaded_templates[$tpl] = $html;
            }

            return $this->loaded_templates[$tpl];
        }

        public function setRoutes(){
            $this->routes = array_keys($this->controllers);
            $this->iterator = new Prototype_App_Iterator($this->controllers);
        }

        public function getRoutes(){
            if($this->routes === null){
                $this->setRoutes();
            }
            return $this->routes;
        }

        public function getIterator(){
            return $this->iterator;
        }

        public function count() {
            return count($this->controllers);
        }

        public function offsetExists($route) {
            return isset($this->controllers[$route]);
        }

        public function offsetGet($route){
            return $this->getController($route);
        }

        public function offsetSet($route, $class){
            return $this->setController(array($route=>new $class()));
        }

        public function offsetUnset($offset){

        }
    }