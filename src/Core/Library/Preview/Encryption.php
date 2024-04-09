<?php

namespace WS\Core\Library\Preview;

class Encryption
{
    public const CIPHERING = 'AES-128-CBC';
    public const SECRET = '3bf1e30f73c17405883d4e6bf6781f7095fc1c62';

    public static function encrypt(string $plainData, string $secret = self::SECRET, string $algorithm = self::CIPHERING): string
    {
        $ivLen = openssl_cipher_iv_length($algorithm);

        $iv = openssl_random_pseudo_bytes($ivLen);

        $ciphertextRaw = openssl_encrypt($plainData, $algorithm, $secret, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertextRaw, $secret, $binary = true);
        
        return base64_encode($iv . $hmac . $ciphertextRaw);
    }

    public static function decrypt(string $encryptedData, string $secret = self::SECRET, string $algorithm = self::CIPHERING): string
    {
        $c = base64_decode($encryptedData);
        $ivLen = openssl_cipher_iv_length($algorithm);

        $iv = substr($c, 0, $ivLen);
        $hmac = substr($c, $ivLen, $sha2len=32);
        $ciphertextRaw = substr($c, $ivLen + $sha2len);

        return openssl_decrypt($ciphertextRaw, $algorithm, $secret, $options = OPENSSL_RAW_DATA, $iv);
    }
}
