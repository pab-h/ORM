<?php
    final class Options {
        public string $primary_key;
        public array $foreign_key;
        public bool $force;
        public bool $timestamp;

        public function __construct(
            string $primary_key,
            bool $force = false,
            bool $timestamp = true,
            array $foreign_key = array(
                'field' => '',
                'references' => '',
                'change' => 'CASCADE'
            )
        ) {
            $this->primary_key = $primary_key;
            $this->force = $force;
            $this->timestamp = $timestamp;
            $this->foreign_key = $foreign_key;
            
            $ok_keys = key_exists('field', $this->foreign_key) && 
                       key_exists('references', $this->foreign_key) &&
                       key_exists('change', $this->foreign_key);
            $ok_values = $this->foreign_key['field'] !== '' && 
                         $this->foreign_key['references'] !== '' &&
                         $this->foreign_key['change'] !== '';
                       
            if(!($ok_keys && $ok_values)) {
                $this->foreign_key = array();
            }
        }
    }
?>