<?php

namespace WS\Core\Library\Preview;

class Encryption
{
    public const string CIPHERING = 'AES-128-CBC';
    public const string SECRET = '3bf1e30f73c17405883d4e6bf6781f7095fc1c62';

    public static function encrypt(string $plainData, string $secret = self::SECRET, string $algorithm = self::CIPHERING): string
    {
        $ivLen = openssl_cipher_iv_length($algorithm);

        $iv = openssl_random_pseudo_bytes($ivLen ?: 10);

        $ciphertextRaw = openssl_encrypt($plainData, $algorithm, $secret, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertextRaw ?: '', $secret, true);

        return base64_encode($iv . $hmac . $ciphertextRaw);
    }

    public static function decrypt(string $encryptedData, string $secret = self::SECRET, string $algorithm = self::CIPHERING): string
    {
        $c = base64_decode($encryptedData);
        $ivLen = openssl_cipher_iv_length($algorithm);

        $iv = substr($c, 0, $ivLen ?: 10);
        $hmac = substr($c, $ivLen ?: 10, $sha2len=32);
        $ciphertextRaw = substr($c, $ivLen + $sha2len);

        return openssl_decrypt($ciphertextRaw, $algorithm, $secret, $options = OPENSSL_RAW_DATA, $iv) ?: '';
    }
}
