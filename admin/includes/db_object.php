<?php
    
    class Object_db{

        public $errors = array();
        public $upload_errors_array = array(
        UPLOAD_ERR_OK           => "There is no error.",
        UPLOAD_ERR_INI_SIZE     => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
        UPLOAD_ERR_FORM_SIZE    => "The uploaded file exceeds the MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL      => "The uploaded file was only parially uploaded.",
        UPLOAD_ERR_NO_FILE      => "No file was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR   => "Missing a temporary folder.",
        UPLOAD_ERR_CANT_WRITE   => "Failed to write file to disk.",
        UPLOAD_ERR_EXTENSION    => "A PHP extension stops the file upload."
    
    );

        protected static $db_table = "users";

        public function save_user_and_image(){
            if (!empty($this->errors)) {
                return false;
            }if (empty($this->user_image) || empty($this->tmp_path)) {
                $this->errors[] = "the file was not available";
                return false;
            }

            $target_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->user_image;


        

            if (file_exists($target_path)) {
                $this->errors[] = "The file {$this->user_image} already exists";
                return false;
            }


            // move_uploaded_file($user_image, $destination);
            if(move_uploaded_file($this->tmp_path, $target_path)){
                
                if ($this->create()) {
                    unset($this->tmp_path);
                    return true;
                }
            }else {
                $this ->errors[] = "the file directory probably does not have permission";
                return false;
            }

            $this->create();
        
    }

        public static function find_all(){
            $sql = "SELECT * FROM " . static::$db_table;
            return static::find_this_query($sql);
        }



        public static function find_by_id($id){
            $sql = "SELECT * FROM ". static::$db_table ." WHERE id = $id LIMIT 1";
            $the_result_array = static::find_this_query($sql);
            return !empty($the_result_array)? array_shift($the_result_array): false;

        //    if (!empty($the_result_array)) {
        //        $first_item = array_shift($the_result_array);
        //        return $first_item;
        //    }else {
        //        return false;
        //    }
        }

        public static function find_this_query($sql){
            global $database;
            $result_set = $database->query($sql);
            $the_object_array = array();
            while($row = mysqli_fetch_array($result_set))
            {
                $the_object_array[] = static::instantiation($row);
            }
            return $the_object_array;
        }

        public static function instantiation($the_record){
            $calling_class = get_called_class();
            $the_object = new $calling_class;
     
            // $the_object->id = $the_record['id'];
            // $the_object->username = $the_record['username'];
            // $the_object->password = $the_record['password'];
            // $the_object->first_name = $the_record['first_name'];
            // $the_object->last_name = $the_record['last_name'];

            foreach ($the_record as $the_attribute => $value) {
                if($the_object->has_the_attribute($the_attribute)){
                    $the_object->$the_attribute = $value;
                }
            }
     
            return $the_object;
        }
        
        private function has_the_attribute($the_attribute){
            $object_properties = get_object_vars($this);
            return array_key_exists($the_attribute, $object_properties);
        }


        
        public function properties(){
            // return get_object_vars($this);
            $properties = array();
            foreach (static::$db_table_fields as $db_field) {
                if (property_exists($this, $db_field)) {
                    $properties[$db_field] = $this->$db_field;

                }
            }
           return $properties;
            
        }

        protected function clean_properties(){
            global $database;
            $clean_properties = array();
            foreach ($this->properties() as $key => $value) {
                $clean_properties[$key] = $database->escape_string($value);
            }

            return $clean_properties;
        }


        public function create(){
            global $database;
            $properties = $this->clean_properties();
            $sql  = "INSERT INTO " . static::$db_table . "(" . implode(",",array_keys($properties)) . ")";
            $sql .="VALUES ('".   implode("','" ,array_values($properties))  ."')";
            // $sql .= $database->escape_string($this->username) . "', '";
            // $sql .= $database->escape_string($this->password) . "', '";
            // $sql .= $database->escape_string($this->first_name) . "', '";
            // $sql .= $database->escape_string($this->last_name) . "')";

            if ($database->query($sql)) {
                $this->id = $database->the_insert_id();
                return true;
            }else {
                return false;
            }

        }
    

        /// This function will check if there is a user and update, if not it will create.

        public function save(){
            return isset($this->id) ? $this->update() : $this-> create();
        }

        public function update(){
            global $database;

            $properties = $this->clean_properties();
            $properties_pairs = array();
            foreach ($properties as $key => $value) {
                $properties_pairs[] = "{$key}='{$value}'";
            }
            $sql  = "UPDATE " .static::$db_table . " SET ";
            $sql .= implode(", ",$properties_pairs);
            $sql .= " WHERE id= ".$database->escape_string($this->id)                    ;

            $database->query($sql);

            return (mysqli_affected_rows($database->connection) ==1 ) ? true : false;

        }
    

        public function delete(){
            global $database;
            $sql  = "DELETE FROM " . static::$db_table;
            $sql .= " WHERE id = " . $database->escape_string($this->id);     
            $sql .= " LIMIT 1";              ;


            $database->query($sql);

            return (mysqli_affected_rows($database->connection) ==1 ) ? true : false;

        }

        public static function count_all(){
            global $database;
            $sql        = "SELECT COUNT(*) FROM " . static::$db_table;
            $result_set = $database->query($sql);
            $row        = mysqli_fetch_array($result_set);

            return array_shift($row);
        }
        
    
    }

?>