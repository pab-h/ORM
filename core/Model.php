<?php
    final class Model {
        private mysqli $link;
        public array $table;

        public function __construct(mysqli $link, array $table) {   
            $this->link = $link;
            $this->table = $table;

            if(!$this->valid_table()) {
                throw new Exception("Table format is invalid");
            }

            $this->create_table();
        }

        private function create_table(): void {
            $sql = "";
            $name = $this->table['name'];

            if($this->table['options']->force) {
                $this->link->query("DROP TABLE IF EXISTS `$name`;");
            }

            $sql .= "CREATE TABLE IF NOT EXISTS `$name`(";

            $fields_name = array_keys($this->table['fields']);

            $sql_fields = array_map(function($field_name) {
                $sql_field = "";
                $field_config = $this->table['fields'][$field_name];
                $sql_field .= "`$field_name` ";
                $sql_field .= $field_config->type." ";
    
                if(!$field_config->allow_null) {
                    $sql_field .= "NOT NULL";
                }

                if($field_config->auto_increment) {
                    $sql_field .= " AUTO_INCREMENT";
                }

                return $sql_field;
            }, $fields_name);

            $sql .= implode(', ', $sql_fields);

            if($this->table['options']->timestamp) {
                $sql .= ",
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP 
                             ON UPDATE CURRENT_TIMESTAMP,    
                ";
            }

            $primary_key = $this->table['options']->primary_key;
            $sql .= " PRIMARY KEY(`$primary_key`)";
            


            if($this->table['options']->foreign_keys != array()) {
                foreach($this->table['options']->foreign_keys as $foreign_key) {
                    $foreign_field = $foreign_key['field'];
                    $table = $foreign_key['references']['table'];
                    $field = $foreign_key['references']['field'];
                    $change = $foreign_key['change'];

                    $sql .= ", FOREIGN KEY (`$foreign_field`) REFERENCES `$table`(`$field`)
                                ON DELETE $change
                                ON UPDATE $change
                    ";
                }
            }

            $sql .= ");";

            $this->link->query($sql);
        }

        private function valid_table(): bool {
            $is_valid = true;

            $is_valid = $is_valid && key_exists('name', $this->table);

            if(key_exists('fields', $this->table)) {
                foreach($this->table['fields'] as $field) {
                    $is_valid = $is_valid && get_class($field) == 'Field';
                }
            } else {
                $is_valid = false;
            }

            $is_valid = $is_valid && key_exists('options', $this->table);

            $is_valid = $is_valid && get_class($this->table['options']) == 'Options';

            return $is_valid;
        }

        private function create_where_clasule(array $where): string {
            $conditions = array_map(function(array $condition){
                $field = $condition["field"];
                $value = $condition["value"];

                return "`$field`='$value'";
            }, $where);

            $condition = implode(" AND ", $conditions);

            return "WHERE ".$condition;
        }

        public function find_by_pk($pk): array {
            $table_name = $this->table['name'];
            $pk_field = $this->table['options']->primary_key;

            $result = $this->link->query(
                "SELECT * FROM `$table_name` 
                    WHERE `$pk_field`='$pk';"
            )
            ->fetch_assoc();

            if(is_null($result)) {
                throw new Exception("NOT FOUND", 404);   
            }

            return $result;
        }

        public function find_one(array $where): array {
            $table_name = $this->table['name'];
            $where_clasule = $this->create_where_clasule($where);

            $result = $this->link->query(
                "SELECT * FROM `$table_name`
                    $where_clasule;"
            )
            ->fetch_assoc();

            if(is_null($result)) {
                throw new Exception("NOT FOUND", 404);   
            }

            return $result;
        }

        public function find_all(array $where): array {
            $table_name = $this->table['name'];
            $where_clasule = $this->create_where_clasule($where);

            $result = $this->link->query(
                "SELECT * FROM `$table_name`
                    $where_clasule;"
            );

            if(!($this->link->affected_rows > 0)) {
                throw new Exception("NOT FOUND", 404);   
            } 

            $results = array();
            while($rows = $result->fetch_assoc()) {
                array_push($results, $rows);
            }

            return $results;
        }

        public function update($pk, string $field, $new_value): void {
            $pk_field = $this->table['options']->primary_key;
            $this->find_by_pk($pk);
            
            $table_name = $this->table['name'];
            $this->link->query(
                "UPDATE `$table_name` SET `$field`='$new_value'
                    WHERE `$pk_field`=$pk;"
            );
        }

        public function delete($pk): void {
            $pk_field = $this->table['options']->primary_key;
            $this->find_by_pk($pk);

            $table_name = $this->table['name'];
            $this->link->query(
                "DELETE FROM `$table_name` 
                    WHERE `$pk_field`='$pk';"
            );
        }

        public function create(... $need): array {
            $table_name = $this->table['name'];

            $fields = array_keys($this->table['fields']);
            $sql = "INSERT INTO `$table_name`(";

            $fields_to_insert = array_map(function(string $field){
                if($field != $this->table['options']->primary_key) {
                    return "`$field`";
                }
            }, $fields);

            for($i = 0; $i < count($fields_to_insert); $i++) {
                if(is_null($fields_to_insert[$i])) {
                    unset($fields_to_insert[$i]);
                }
            }

            $field_to_insert = implode(', ', $fields_to_insert);

            $sql .= $field_to_insert.") VALUES (";

            $values_to_insert = array_map(function($value){
                return "'$value'";
            }, $need);

            $value_to_insert = implode(', ', $values_to_insert);

            $sql .= $value_to_insert.");";

            $this->link->query($sql);
            if(!($this->link->affected_rows > 0)) {
                throw new Exception("AN ERROR OCCURRED WHILE CREATING THE NEW RECORD", 404);   
            } 

            $last_id = $this->link->insert_id;

            return $this->find_by_pk($last_id);
        }

        public function query(callable $query) {
            return $query($this->link);
        }

    }

?>
