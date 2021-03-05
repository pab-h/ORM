<?php
    abstract class Link {
        protected string $host;
        protected string $user;
        protected string $pass; 
        protected string $db;

        public mysqli $link;

        public function __construct(string $host, string $user, string $pass, string $db) {
            $this->link = new mysqli(
                $host, 
                $user, 
                $pass, 
                $db
            );
        }
    }
?>