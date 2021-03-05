<?php 
    require_once __DIR__.'/core/Link.php';
    require_once __DIR__.'/core/Model.php';

    final class ORM extends Link {
        public function __construct(string $host, string $user, string $pass, string $db) {
            parent::__construct($host, $user, $pass, $db);
        }

        public function define(array $table): Model {
            return new Model($this->link, $table);
        }
    }
?>
