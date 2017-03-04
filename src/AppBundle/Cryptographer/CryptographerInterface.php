<?php

namespace AppBundle\Cryptographer;

interface CryptographerInterface
{
    /**
     * @param string $raw
     *
     * @return string
     */
    public static function encrypt($raw);

    /**
     * @param string $encrypted
     *
     * @return string
     */
    public static function decrypt($encrypted);
}
