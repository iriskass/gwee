<?
    class Prototype_App_Iterator implements Iterator {
        private $stack = array();

        public function __construct($array){
            if (is_array($array)) {
                $this->stack = $array;
            }
        }

        public function rewind(){
            reset($this->stack);
        }

        public function current(){
            $stack = current($this->stack);
            return $stack;
        }

        public function key(){
            $stack = key($this->stack);
            return $stack;
        }

        public function next(){
            $stack = next($this->stack);
            return $stack;
        }

        public function valid(){
            $key = key($this->stack);
            $stack = ($key !== NULL && $key !== FALSE);
            return $stack;
        }
    }