<?php
    final class Field {
        public string $type;
        public bool $allow_null;
        public bool $auto_increment;
        public bool $unique;

        public function __construct(
            string $type, 
            bool $allow_null,
            bool $auto_increment = false ,
            bool $unique = false
        ) {
            $this->type = $type;
            $this->allow_null = $allow_null;
            $this->unique = $unique;
            $this->auto_increment = $auto_increment;
        }
    }
?>