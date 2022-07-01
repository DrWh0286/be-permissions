<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Utility;

final class ArrayUtility
{
    /**
     * @param array<mixed> $array
     * @return bool
     */
    public static function recursiveKsort(array &$array): bool
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                self::recursiveKsort($value);
            }
        }
        return ksort($array);
    }

    /**
     * @param array<mixed> $array
     * @return bool
     */
    public static function ksortNestedAsort(array &$array): bool
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                asort($value);
                $value = array_values($value);
            }
        }
        return ksort($array);
    }
}
