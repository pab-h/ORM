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
                'references' => array(
                    'table' => '',
                    'field' => ''
                ),
                'change' => ''
            )
        ) {
            $this->primary_key = $primary_key;
            $this->force = $force;
            $this->timestamp = $timestamp;
            $this->foreign_key = $foreign_key;

            $empty_foreign_key = array(
                'field' => '',
                'references' => array(
                    'table' => '',
                    'field' => ''
                ),
                'change' => ''
            );

            $need_keys_to_foreign_key = array('field', 'references', 'change');
            $need_keys_to_references = array('table', 'field');

            $ok_keys = array_keys($this->foreign_key) == $need_keys_to_foreign_key;
            $ok_keys = $ok_keys && array_keys($this->foreign_key['references']) == $need_keys_to_references;


            $ok_values = $this->foreign_key != $empty_foreign_key;

            if(!($ok_keys && $ok_values)) {
                $this->foreign_key = array();
            }
        }
    }
?>