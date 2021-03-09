<?php
    class Cryption {
        private string $algo = "AES-128-CBC";

        public function encrypt(string $data, string $key): string {
            $iv_len = openssl_cipher_iv_length($this->algo);
            $iv = openssl_random_pseudo_bytes($iv_len);

            $raw_data = openssl_encrypt($data, $this->algo, $key, 0, $iv);

            return base64_encode("$raw_data::$iv");
        }

        public function decrypt(string $encrypted, string $key): string {
            $encrypted_raw_explode = explode('::', base64_decode($encrypted));
            
            $data_raw = $encrypted_raw_explode[0];
            $iv = $encrypted_raw_explode[1];
            
            return openssl_decrypt($data_raw, $this->algo, $key, 0, $iv);
        }

    }
?>  