<?
    class Gwee_Core_DB {
        private $db;
        private $lastQuery = false;
        private $values = array(), $types = '';

        public function __construct(){
            $config = Gwee_Config::getDB();
            $this->db = new mysqli(
                $config['host'], 
                $config['user'], 
                $config['pass'], 
                $config['db'], 
                (int)$config['port']
            );

            $this->db->set_charset($config['charset']);
        
            if($this->db->connect_error){
                throw new Exception($this->db->connect_error);
            }
        }

        public function transactionStart(){
            $this->db->autocommit(false);
        }

        public function transactionCommit(){
            $this->db->commit();
            $this->db->autocommit(true);
        }

        public function transactionRollBack(){
            $this->db->rollback();
        }

        public function raw($sql){
            return $this->db->query($sql);
        }

        public function get($sql, $args = array()){
            $data = $this->query($sql, $args);
            if(isset($data[0])){
                return $data[0];
            }
            return false;
        }

        public function getValue($sql, $args = array()){
            $data = $this->query($sql, $args);
            if(isset($data[0])){
                $keys = array_keys($data[0]);
                return $data[0][$keys[0]];
            }
            return false;
        }

        public function query($sql, $args = array()){
            /*
                provide params for prepared statement
                sql exqample: select * from table where name=?s:key and id=?i:id
            */
            $sqlMethods = array('select','insert','delete');
            preg_match("/(select|insert|update|delete)/i", $sql, $m);
            $sqlKeyWord = strtolower($m[1]);
            if(!in_array($sqlKeyWord, $sqlMethods)){
                die("DB: Restricted sql method");
            }
            $data = array();

            if ($this->stmt) $this->stmt->close();

            if (count($args) > 0) {
                preg_match_all("/\?([sibd]):([-_a-z0-9]+)/i", $sql, $m);
                foreach($m[0] as $k=>$v){
                    $sql = str_replace($v, "?", $sql);
                    $this->addBind($m[1][$k], $args[$m[2][$k]]);
                }
            }

            $this->stmt = $this->db->prepare($sql);
            if (!$this->stmt) return false;

            call_user_func_array(array($this->stmt, 'bind_param'), $this->getBinds());
            
            $this->stmt->execute();
            $this->lastQuery = $sql;

            switch($sqlKeyWord){
                case 'select': 
                    $result = $this->fetch($this->stmt);
                    $this->stmt->close();
                break;
                case 'update': 
                    $this->stmt->close();
                    return $this->db->affected_rows;
                break;
                case 'delete': 
                    return true;
                break;
                case 'insert':
                    $this->stmt->close();
                    return $this->db->insert_id>0 ? $this->db->insert_id : false;
                break;
            }

            if(count($result)>0){
                return $result;
            }

            return false;
        }

        public function getLastQuery(){
            return $this->lastQuery;
        }

        public function fetch($result){   
            $array = array();
           
            if($result instanceof mysqli_stmt)
            {
                $result->store_result();
               
                $variables = array();
                $data = array();
                $meta = $result->result_metadata();
               
                while($field = $meta->fetch_field())
                    $variables[] = &$data[$field->name]; // pass by reference
               
                call_user_func_array(array($result, 'bind_result'), $variables);
               
                $i=0;
                while($result->fetch())
                {
                    $array[$i] = array();
                    foreach($data as $k=>$v)
                        $array[$i][$k] = $v;
                    $i++;
                   
                    // don't know why, but when I tried $array[] = $data, I got the same one result in all rows
                }
            }
            elseif($result instanceof mysqli_result)
            {
                while($row = $result->fetch_assoc())
                    $array[] = $row;
            }
           
            return $array;
        }

        public function addBind( $type, &$value ){
            $this->values[] = $value;
            $this->types .= $type;
        }
       
        public function getBinds(){
            return $this->refValues(array_merge(array($this->types), $this->values));
        }

        public function refValues($arr){
            if (strnatcmp(phpversion(),'5.3') >= 0){
                $refs = array();
                foreach($arr as $key => $value)
                    $refs[$key] = &$arr[$key];
                return $refs;
            }
            return $arr;
        } 

    }