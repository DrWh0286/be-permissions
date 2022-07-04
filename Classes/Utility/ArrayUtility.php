<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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
