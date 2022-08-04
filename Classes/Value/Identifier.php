<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
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

namespace SebastianHofer\BePermissions\Value;

final class Identifier
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        if (preg_match('/\s/', $identifier)) {
            throw new InvalidIdentifierException('Spaces are not allowed within an identifier string! "' . $identifier . '"');
        }
        $this->identifier = $identifier;
    }

    /**
     * @param string $title
     * @return Identifier
     * @throws InvalidIdentifierException
     */
    public static function buildNewFromTitle(string $title): Identifier
    {
        $originalTitle = $title;
        $title = str_replace("ä", "ae", $title);
        $title = str_replace("ü", "ue", $title);
        $title = str_replace("ö", "oe", $title);
        $title = str_replace("Ä", "Ae", $title);
        $title = str_replace("Ü", "Ue", $title);
        $title = str_replace("Ö", "Oe", $title);
        $title = str_replace("ß", "ss", $title);
        $title = strtolower($title);
        $title = preg_replace('/\s/', '_', $title);

        if (is_string($title)) {
            $title = preg_replace('/[^a-z0-9_-]/', '', $title);
        }

        if (!is_string($title)) {
            throw new InvalidIdentifierException('Something went wrong with creating an identifier from title "' . $originalTitle . '"!');
        }

        return new self($title);
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
