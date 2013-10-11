<?
    class Gwee_Core_Router {
        public static $active = false;
        public static $route_map = array();
        public static $route_map_left = array();

        public static function getRouteMap(){
            if(count(self::$route_map)>0){
                return self::$route_map;
            }else{
                $request = str_replace(dirname($_SERVER['SCRIPT_NAME']), "", $_SERVER['REQUEST_URI']);
                //preg_match_all("/(\/([-_a-z0-9]+)(&([-_a-z0-9]+)=([-_a-z0-9]+))*)/", str_replace(dirname($_SERVER['SCRIPT_NAME']), "", $_SERVER['REQUEST_URI']), $m);
                $routes = array();
                preg_match_all("/\/[^\/]+/", $request, $r);
                foreach($r[0] as $k=>$v){
                    preg_match_all("/&([-_a-z0-9]+)=([-_a-z0-9]+)/", $v, $m);
                    $args = !empty($m[1]) && !empty($m[2]) ? array_combine($m[1], $m[2]) : array();
                    $key = preg_replace("/\/([-_a-z0-9]+).*/", "$1", $v);
                    $routes[] = array('route'=> $key, 'args'=>$args);
                }
                
                return self::$route_map = self::$route_map_left = $routes;
            }
        }

        public static function getRoute(){
            $route = self::$active = array_shift(self::$route_map_left);
            return $route;
        }
    }