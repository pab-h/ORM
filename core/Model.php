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
            $primary_key = $this->table['options']->primary_key;

            $sql .= ", PRIMARY KEY(`$primary_key`)";

            $sql .= ");";

            $sql = $this->link->real_escape_string($sql);
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


        public function find_by_pk($pk): array {
            $table_name = $this->table['name'];
            $pk_field = $this->table['options']->primary_key;

            $result = $this->link->query(
                "SELECT * FROM `$table_name` 
                    WHERE `$pk_field`='$pk';"
            )
            ->fetch_assoc();
            if(is_null($result)) {
                throw new Exception("NOT FOUND");   
            }

            return $result;
        }

    }

?>