<?
    class System_Core_Entity extends Prototype {
        public static $table = null;
        public static $key = array();
        public static $fields = array();

        public static function allRows(){
            return System::getDB()->query("SELECT * FROM `" . static::$table . "`");
        }

        public static function newRow($data = array()){
            $sqlTableKeys = array_keys(static::$fields);
            $sqlTableKeys = "`" . implode("`, `", $sqlTableKeys) . "`";
            $sqlTableValues = $data;
            $sqlSTMT = '';

            $fieldsToInsert = array();
            $i = 0;
            foreach(static::$fields as $k=>$v){
                if($i===0){
                    $sqlSTMT .= "?{$v}:{$k}";
                }else{
                    $sqlSTMT .= ", ?{$v}:{$k}";
                }
                
                $fieldsToInsert[$k] = isset($data[$k]) ? $data[$k] : null;

                $i++;
            }

            $sql = "insert into `" . static::$table . "` (" . $sqlTableKeys . ") values (" . $sqlSTMT . ")";

            $res = System::getDB()->query($sql, $fieldsToInsert);
            if((int)$res>0){
                return $res;
            }else{
                self::fatal("Duplicate entry");
            }
        }

        public static function deleteRow($id = array()){
            if(is_array($id) && count(static::$key)>1){
                $sql = "delete from `" . static::$table . "` where";
                $i = 0;
                foreach(static::$key as $k){
                    if($i===0){
                        $sql .= " " . static::$key[0] . "=?s:" . static::$key[0] . "";
                    }else{
                        $sql .= " AND " . static::$key[0] . "=?s:" . static::$key[0] . "";
                    }
                    $i++;
                }
                return System::getDB()->query($sql, $id);
            }elseif(count(static::$key) === 1){
                $sql = "delete from `" . static::$table . "` where " . static::$key[0] . "=?s:" . static::$key[0] . "";
                return System::getDB()->query($sql, $id);
            }else{
                self::fatal("Incorrect keys in deleteRow method");
            }
            
        }
    }