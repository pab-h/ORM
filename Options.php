<?php
    final class Options {
        public string $primary_key;
        public array $foreign_key;
        public bool $force;
        public bool $is_snake_case;
        public bool $timestamp;

        public function __construct(
            bool $force = false,
            bool $is_snake_case = true,
            bool $timestamp = true,
            string $primary_key,
            array $foreign_key = array(
                'field' => '',
                'references' => ''
            )
        ) {
            $this->primary_key = $primary_key;
            $this->force = $force;
            $this->is_snake_case = $is_snake_case;
            $this->timestamp = $timestamp;
            $this->foreign_key = $foreign_key;
            
            $ok_keys = key_exists('field', $this->foreign_key) && 
                       key_exists('references', $this->foreign_key);
            $ok_values = $this->foreign_key['field'] !== '' && 
                         $this->foreign_key['references'] !== '';
                       
            if(!($ok_keys && $ok_values)) {
                $this->foreign_key = array();
            }
        }
    }
?>