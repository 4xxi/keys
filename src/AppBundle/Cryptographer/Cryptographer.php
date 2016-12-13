<?php

namespace AppBundle\Cryptographer;

class Cryptographer implements CryptographerInterface
{
    const ALGORITHM = 'aes-256-cbc';

    /* TODO: move out into parameters.yml */
    const PASS = '12345';
    const IV = 'd
??2??Ǝ?[?5???';

    /**
     * @param string $raw
     *
     * @return string
     */
    public static function encrypt($raw)
    {
        return openssl_encrypt($raw, self::ALGORITHM, self::PASS, true, self::IV);
    }

    /**
     * @param string $encrypted
     *
     * @return string
     */
    public static function decrypt($encrypted)
    {
        return openssl_decrypt($encrypted, self::ALGORITHM, self::PASS, OPENSSL_RAW_DATA, self::IV);
    }

    /**
     * @return string
     */
    private static function generateIV()
    {
        return openssl_random_pseudo_bytes(16);
    }
}