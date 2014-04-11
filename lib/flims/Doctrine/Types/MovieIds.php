<?php

namespace flims\Doctrine\Types;

use Doctrine\DBAL\Types\ArrayType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use flims\MovieId;

/**
 * Doctrine type used to shorten the array of movie id fields
 */
class MovieIds extends ArrayType
{
    const TYPENAME = 'movieids';
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return null;
        }

        return array_reduce($value, function (&$result, $item) { return sprintf('%s%s%s', $result, $result ? ',' : '', (string) $item);});
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return array();
        }

        return array_map(function ($item) { return MovieId::fromUuids($item); }, explode(',', $value));
    }
    
    public function getName()
    {
        return self::TYPENAME;
    }
}