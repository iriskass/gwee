<?
    define("GWEE_ROOT", dirname(__FILE__));
    define("ROOT", dirname(dirname(__FILE__)));
    $script = explode("/", $_SERVER['SCRIPT_NAME']);
    array_pop($script);
    $prefix = implode("/", $script);
    define("PATH_PREFIX", $prefix);
    Gwee_Core_Router::getRouteMap();

    function __autoload($class_name){
        $namespaces = array(
            'Application'   =>'Gwee_Prototype_App',
            'Controller'    =>'Gwee_Prototype_Controller',
            'Entity'        =>'Gwee_Core_Entity',
        );
        $ext = ".php";
        $ds = DIRECTORY_SEPARATOR;

        if(isset($namespaces[$class_name])){
            class_alias($namespaces[$class_name], $class_name, true);
            $class_name = $namespaces[$class_name];
        }
        preg_match_all('/([a-z]+)_?/i', $class_name, $m);

        $path = strtolower(preg_replace("/([a-z])([A-Z])/", "$1_$2", implode($ds, $m[1])));
        $file = $m[1][count($m[1])-1].$ext;

        if($path!=''){
            $file = ROOT.$ds.$path.$ds.$file;
        }

        require_once($file);
    }

    function get_ancestors_class($class){
        if(is_object($class)){
            $class = get_class($class);
        }
        $father = get_parent_class($class);

        if ($father != "") {
            $ancestors = get_ancestors_class($father);
            $ancestors[] = $father;
        }
        return $ancestors;
    }

    function getParentClass($class){
        $a = get_ancestors_class($class);
        return end($a);
    }