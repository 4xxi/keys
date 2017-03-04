<?php

namespace AppBundle\Doctrine\DBAL\Types;

use AppBundle\Cryptographer\Cryptographer;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * A Type which can be used for encrypting and storing text data.
 */
class EncryptedTextType extends Type
{
    const TYPE = 'encrypted_text';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return null;
        }

        return base64_encode(Cryptographer::encrypt($value));
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return array();
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        return Cryptographer::decrypt(base64_decode($value));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
