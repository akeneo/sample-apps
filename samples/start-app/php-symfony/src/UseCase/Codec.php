<?php

namespace App\UseCase;

final class Codec
{
    const CYPHER = "AES-256-CTR";

    public static function encode($payload, $key)
    {
        $iv_length = openssl_cipher_iv_length(self::CYPHER);
        $iv = random_bytes($iv_length);
        $data = openssl_encrypt($payload, self::CYPHER, $key, 0, $iv);

        return [$data, bin2hex($iv)];
    }

    public static function decode($data, $key, $encryptedIv)
    {
        $iv = hex2bin($encryptedIv);
        return openssl_decrypt($data, self::CYPHER, $key, 0, $iv);
    }
}
