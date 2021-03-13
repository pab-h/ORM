<?php
    class Cryption {
        private string $algo_crypt = "AES-128-CBC";
        private string $algo_hash = "sha256";

        public function encrypt(string $data, string $key): string {
            $iv_len = openssl_cipher_iv_length($this->$algo_crypt);
            $iv = openssl_random_pseudo_bytes($iv_len);

            $raw_data = openssl_encrypt($data, $this->$algo_crypt, $key, 0, $iv);

            return base64_encode("$raw_data::$iv");
        }

        public function decrypt(string $encrypted, string $key): string {
            $encrypted_raw_explode = explode('::', base64_decode($encrypted));
            
            $data_raw = $encrypted_raw_explode[0];
            $iv = $encrypted_raw_explode[1];
            
            return openssl_decrypt($data_raw, $this->$algo_crypt, $key, 0, $iv);
        }

        public function hash(string $data, string $key, int $rounds = 1): string {
            $hash = hash_hmac($this->algo_hash, $data, $key);

            for($i = 0; $i < $rounds-1; $i++){
                $hash = hash_hmac('sha256', $data, $key);
            }

            return $hash;
        }

        public function set_algo_crypt(string $algo): void {
            $this->algo_crypt = $algo;
        }

        public function set_algo_hash(string $algo): void {
            $this->algo_hash = $algo;
        }
    }
?>  