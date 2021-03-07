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
            array $foreign_keys = array(
                array(
                    'field' => '',
                    'references' => array(
                        'table' => '',
                        'field' => ''
                    ),
                    'change' => ''
                )
            )
        ) {
            $this->primary_key = $primary_key;
            $this->force = $force;
            $this->timestamp = $timestamp;
            $this->foreign_keys = $foreign_keys;
            
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

            $ok_keys = true;
            $ok_values = true;

            foreach($this->foreign_keys as $foreign_key) {
                $ok_keys = $ok_keys && array_keys($foreign_key) == $need_keys_to_foreign_key;
                $ok_keys = $ok_keys && array_keys($foreign_key['references']) == $need_keys_to_references;

                $ok_values = $ok_values && 
                             $foreign_key['field'] != $empty_foreign_key['field'] &&
                             $foreign_key['change'] != $empty_foreign_key['change'] &&
                             $foreign_key['references']['table'] != $empty_foreign_key['references']['table'] &&
                             $foreign_key['references']['field'] != $empty_foreign_key['references']['field'];

                
            }

            if(!($ok_keys && $ok_values)) {
                $this->foreign_keys = array();
            }
        }
    }
?>