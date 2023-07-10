<?php

namespace WS\Core\Library\Preview;

class Encryption
{
    public const ALGORITHM = 'blowfish';
    public const SECRET = '3bf1e30f73c17405883d4e6bf6781f7095fc1c62';

    public static function encrypt(string $plainData, string $secret, string $algorithm): string
    {
        $iv = rand(11111111, 99999999);
        $encryptedData = openssl_encrypt($plainData, $algorithm, $secret, 0, (string) $iv);

        return base64_encode(sprintf('%s.%s', $iv, $encryptedData));
    }

    public static function decrypt(string $encryptedData, string $secret, string $algorithm): string
    {
        list($iv, $encryptedData) = explode('.', base64_decode($encryptedData), 2);

        $decrypt = openssl_decrypt($encryptedData, $algorithm, $secret, 0, $iv);
        return $decrypt !== false ? $decrypt : '';
    }
}
